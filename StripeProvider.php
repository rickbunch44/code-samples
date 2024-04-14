<?php

namespace App\Providers\Custom;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Sku;
use Illuminate\Support\ServiceProvider;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeProvider extends ServiceProvider
{
    public static function getHostEnv()
    {
        return config('app.env') === 'production' ? config('app.stripe.production.publishable') : config('app.stripe.development.publishable');
    }

    private static function getStripeClient()
    {
        $secret_key = config('app.env') === 'production' ? config('app.stripe.production.secret_key') : config('app.stripe.development.secret_key');
        return new StripeClient($secret_key);
    }

    public static function getAllPayments($range = "week")
    {
        $stripe = self::getStripeClient();
        $response = $stripe->charges->all([
            'limit' => 15,
            'created' => [
                'gt' => $range === "week" ? (new \DateTime("now - 6 month"))->getTimestamp() : (new \DateTime("now - 3 days"))->getTimestamp()
            ]
        ]);

        $payments = [];
        foreach ($response as $payment) {
            $payments[] = [
                'id' => $payment['id'],
                'amount' => $payment['amount'],
                'billing_details' => $payment['billing_details'],
                'description' => $payment['description'],
                'paid' => $payment['paid'],
                'date' => $payment['created'], // UTC to PST adjustment
            ];
        }
        return $payments;
    }

    public static function getOrders()
    {
        $after = null;
        $limit = 100;
        $iterations = 0;
        $paidSessions = [];

        // find Stripe Checkout Sessions that have been paid

        while ($iterations <= 3) {
            $checkoutSessions = self::getCheckoutSessions($limit, $after);

            foreach ($checkoutSessions['data'] as $index => $session) {
                if ($session['payment_status'] !== 'unpaid') {
                    $paidSessions[] = $session['id'];
                }

                if ($index === $limit - 1) {
                    $after = $session['id'];
                }
            }
            $iterations++;

            if (!$checkoutSessions['has_more']) {
                break;
            }
        }

        // compile Checkout Sessions into Orders

        $orders = [];
        foreach ($paidSessions as $session) {
            $orders[] = self::getCheckoutSession($session);
        }

        // submit orders that do not exist yet

        if (config('app.env') === 'production') {
            $compiled_orders = self::compileOrders($orders);
            foreach ($compiled_orders as $order) {
                $exists = OrderProvider::orderExists($order['order_id']);
                if (!$exists) {
                    $shopifyOrder = ShopifyProvider::submitDraftOrder($order);
                    if ($shopifyOrder) {
                        OrderProvider::createOrder($order, $shopifyOrder);
                    }
                }
            }
        }


        return $orders;
    }

    private static function compileOrders($orders)
    {
        $order_details = [];
        foreach ($orders as $index => $order) {

            $order_details[$index]['order_id'] = $order['id'];

            $order_details[$index]['totals'] = [
                'amount_total' => (float)$order['amount_total'] / 100,
                'amount_subtotal' => (float)$order['amount_subtotal'] / 100,
                'amount_discount' => (float)$order['total_details']['amount_discount'] / 100,
                'amount_shipping' => (float)$order['total_details']['amount_shipping'] / 100,
                'amount_tax' => (float)$order['total_details']['amount_tax'] / 100,
                'status' => $order['payment_status'],
            ];

            $order_details[$index]['customer'] = [
                'name' => $order['shipping_details']['name'],
                'email' => $order['customer_details']['email'],
                'line1' => $order['shipping_details']['address']['line1'],
                'line2' => $order['shipping_details']['address']['line2'],
                'city' => $order['shipping_details']['address']['city'],
                'state' => $order['shipping_details']['address']['state'],
                'postal_code' => $order['shipping_details']['address']['postal_code'],
                'country' => $order['shipping_details']['address']['country'],
            ];

            $order_details[$index]['line_items'] = [];
            foreach ($order['line_items'] as $line_item) {

                $sku = Sku::where('shopify_sku', $line_item['price']['product'])
                    ->first();

                $item = [
                    'variant_id' => $sku->sku_id,
                    'price_id' => $line_item['price']['id'],
                    'amount_subtotal' => (float)$line_item['amount_subtotal'] / 100,
                    'quantity' => $line_item['quantity'],
                ];
                $order_details[$index]['line_items'][] = $item;
            }
        }

        return $order_details;
    }

    private static function getCheckoutSessions($limit, $after = null)
    {
        $stripe = self::getStripeClient();
        $params = [
            'limit' => $limit,
        ];

        if ($after) {
            $params['starting_after'] = $after;
        }

        $sessions = $stripe->checkout->sessions->all($params);

        return $sessions;
    }

    private static function getCheckoutSession($id)
    {
        $stripe = self::getStripeClient();
        $session = $stripe->checkout->sessions->retrieve(
            $id, []
        );

        $line_items = $stripe->checkout->sessions->allLineItems($id, []);
        $session['line_items'] = $line_items['data'];

        return $session;
    }

    public static function createStripeSession($skus)
    {
        $line_items = [];
        $total_price = 0;
        $total_quantity = 0;

        if (!$skus || count($skus) === 0) {
            return false;
        }

        foreach ($skus as $sku) {
            $line_items[] = [
                'quantity' => $sku['purchase_quantity'],
                'price' => $sku['stripe_price_id'],
            ];
            $total_price += ($sku['purchase_quantity'] * $sku['price']);
            $total_quantity += $sku['purchase_quantity'];
        }

        $stripe = StripeProvider::getStripeClient();
        try {
            $session = $stripe->checkout->sessions->create([
                'success_url' => (config('app.url') . '/checkout_success'),
                'cancel_url' => (config('app.url') . '/checkout'),
                'mode' => 'payment',
                'line_items' => $line_items,
                'shipping_address_collection' => [
                    'allowed_countries' => ['US'],
                ],
                'allow_promotion_codes' => true,
                'automatic_tax' => ['enabled' => true],
                'shipping_options' => [self::retrieveShippingRates($total_quantity, $total_price)],
            ]);
        } catch (ApiErrorException $e) {
            return $e->getError();
        }
        return $session;
    }

    public static function retrieveStripeSession($sessionId)
    {
        $stripe = self::getStripeClient();
        $stripe->checkout->sessions->all(['limit' => 15]);
    }

    public static function submitProductToStripe($product)
    {
        $stripe = self::getStripeClient();
        $images = ProductImage::query()
            ->select("url")
            ->where("product", "=", $product->uuid)
            ->get()
            ->toArray();

        if ($images && count($images) > 8) {
            $images = array_slice($images, 0, 8);
        }

        $images = array_column($images, "url");

        foreach ($product->skus as $sku) {
            try {
                $created_product = $stripe->products->create([
                    'id' => $sku->sku_id,
                    'name' => $product->short_name . " - " . $sku['title'],
                    'active' => true,
                    'shippable' => true,
                    'description' => $product->description ? strip_tags($product->description) : $product->short_name,
                    'default_price_data' => [
                        'currency' => 'usd',
                        'tax_behavior' => 'exclusive',
                        'unit_amount' => intval($sku->price * 100)
                    ],
                    'images' => $images,
                    'url' => ItemProvider::getProductURLBySlug($product->slug)
                ]);

                if (!$created_product) {
                    return false;
                }
                $sku->stripe_id = $sku->sku_id;
                $sku->stripe_price_id = $created_product['default_price'];
                $sku->save();

            } catch (\Exception $exception) {
                return false;
            }
        }

        return true;
    }

    public static function checkStripeId($product)
    {
        foreach ($product->skus as $sku) {
            try {
                $stripe = self::getStripeClient();
                $response = $stripe->products->retrieve(
                    $sku->sku_id,
                    []
                );

                if ($response instanceof \Stripe\Product) {
                    $sku->stripe_id = $sku->sku_id;
                    $sku->stripe_price_id = $response['default_price'];
                    $sku->save();
                }

            } catch (\Exception $exception) {
                var_dump($exception->getMessage());
                continue;
            }
        }
    }

    public static function updateSkuPrice($newSku, $price)
    {
        $stripe = self::getStripeClient();
        $priceId = $stripe->prices->create([
            'product' => $newSku['stripe_id'],
            'unit_amount' => (float)$price * 100,
            'currency' => 'usd',
            'tax_behavior' => 'exclusive'
        ]);

        if (!$priceId['id']) {
            return false;
        }

        $sku = Sku::find($newSku['id']);
        $sku->stripe_price_id = $priceId['id'];
        $sku->price = (float)$price;
        $sku->save();
        return true;
    }

    public static function deleteProduct($stripeId)
    {
        $stripe = StripeProvider::getStripeClient();
        try {
            $result = $stripe->products->delete(
                $stripeId,
//            ['active' => false]
            );
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function deleteAllProducts()
    {
        $stripe = StripeProvider::getStripeClient();

        $limit = 100;
        $starting_after = null;

        do {
            $params = ['limit' => $limit];

            if ($starting_after) {
                $params['starting_after'] = $starting_after;
            }
            $products = $stripe->products->all($params);

            foreach ($products as $product) {
                $productObj = Product::find($product->id)
                    ->with('skus')
                    ->get();
                self::deleteProduct($product->id);
                ItemProvider::deleteProduct($productObj);
            }
            $starting_after = $products->data[$limit - 1]->id;
        } while (count($products) === $limit);
    }

    public static function refreshProductID($product, $newProductId)
    {
        $stripe = StripeProvider::getStripeClient();
        try {
            $result = $stripe->products->update(
                $product->skus[0]['stripe_id'],
                ['active' => true],
            );
        } catch (\Exception $exception) {
            return false;
        }
    }

    private static function retrieveShippingRates($itemCount, $totalPrice)
    {
        $final_shipping_rates = [];
        $shipping_rates = CheckoutProvider::calculateShippingRatesByProductCount($itemCount, $totalPrice);

        foreach ($shipping_rates as $index => &$rate) {
            unset($rate['additional_amount']);
            unset($rate['base_amount']);
            $rate['fixed_amount']['amount'] = (int)$rate['fixed_amount']['amount'] * 100;
            $final_shipping_rates[$index]['shipping_rate_data'] = $rate;
        }
        return $final_shipping_rates;
    }
}
