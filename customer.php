<?php

$shopify = new ShopifyRequest();

$shopify->updateCustomer('3828379320480');
echo "<br /><br />";
$shopify->getCustomers(2);

class ShopifyRequest {
    const API_KEY = 'XXX';
    const API_PRIVATE = 'XXX';
    const BASE_URL = 'ricks-jewelry-shop.myshopify.com';

    public function getCustomers($limit) {
        $endpoint = '/admin/api/2020-07/customers.json?limit=' . $limit;
        $url = $this->createURL($endpoint);

        self::sendRequest($url, 'GET');
    }

    public function updateCustomer($cid) {
        $endpoint = '/admin/api/2020-07/customers/' . $cid . '.json';
        $props = [
            "customer" => [
                "id" => $cid,
                "email" => "rick.a.bunch@gmail.com",
                "phone" => "+15304005982",
                "note" => "Rick is awesome!"
            ]
        ];

        $url = $this->createURL($endpoint);

        self::sendRequest($url, 'PUT', $props);
    }

    private function sendRequest($url, $method, $props = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

        switch ($method) {
            case 'PUT': {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($props));
                break;
            }
        }

        try {
            $response = curl_exec($ch);
            var_dump(json_decode($response, true));
            return json_decode($response, true);
        } catch (Exception $exception) {
            var_dump($exception->getMessage());
            return false;
        }
    }

    private function createURL($endpoint) {
        return 'https://' . self::API_KEY . ':' . self::API_PRIVATE . '@' . self::BASE_URL . $endpoint;
    }
}
