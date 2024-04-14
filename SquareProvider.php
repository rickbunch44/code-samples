<?php

namespace App\Providers\Custom;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\ServiceProvider;
use Ramsey\Uuid\Uuid;
use Square\Models\CatalogImage;
use Square\Models\CatalogItem;
use Square\Models\CatalogItemVariation;
use Square\Models\CatalogObject;
use Square\Models\CreateCatalogImageRequest;
use Square\Models\Money;
use Square\Models\UpsertCatalogObjectRequest;
use Square\SquareClient;
use Square\Environment;
use Square\Exceptions\ApiException;
use Square\Utils\FileWrapper;

class SquareProvider extends ServiceProvider
{
    public static function getSquareClient()
    {
        return new SquareClient([
            'accessToken' => config('app.env') === 'production' ? config('app.square.production.secret_key') : config('app.square.development.secret_key'),
            'environment' => Environment::PRODUCTION,
        ]);
    }

    public static function uploadItems()
    {
        $client = self::getSquareClient();

        /** @var Product $product */
        $products = Product::take(250)
            ->with('images')
            ->get();

        foreach ($products as $productObj) {

            $product = $productObj->toArray();
            $object_id = $product['square_id'] ? $product['square_id'] : '#' . $product['slug'];
            $price_money = new Money();
            $price_money->setAmount($product['normal_price'] * 100);
            $price_money->setCurrency('USD');

            $item_variation_data = new CatalogItemVariation();
            $item_variation_data->setItemId($object_id);
            $item_variation_data->setName($product['full_name']);
            $item_variation_data->setPricingType('FIXED_PRICING');
            $item_variation_data->setPriceMoney($price_money);

            $catalog_object = new CatalogObject('ITEM_VARIATION', $object_id . "_var1");
            $catalog_object->setItemVariationData($item_variation_data);

            $variations = [$catalog_object];
            $item_data = new CatalogItem();
            $item_data->setName($product['full_name']);
            $item_data->setDescription($product['description']);
            $item_data->setAbbreviation(substr($product['full_name'], 0, 2));
            $item_data->setVariations($variations);

            $object = new CatalogObject('ITEM', $object_id);
            $object->setItemData($item_data);

            $body = new UpsertCatalogObjectRequest($product['uuid'], $object);

            $api_response = $client->getCatalogApi()->upsertCatalogObject($body);

            $productObj->square_id = $product['slug'];
            $productObj->save();

        }
    }
}
