<?php

use Classes\Database;

require_once 'vendor/autoload.php';
define('MAX', 1000);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if(!$data || !$data->numbers) return;
    $db = new Database();
    $gen = new \Classes\Generator();
    $db->firstStart();
    $products = $db->getData($data->numbers);
    $result = [];
    if(isset($products['Error'])){
        switch ($products['Error']) {
            case 1:
                $result['Error'] = [
                    'Description' => 'Allow less ' . MAX . ' numbers',
                    'Code' => 1
                ];
                break;
            case 2:
                $result['Error'] = [
                    'Description' => 'No match numbers',
                    'Code' => 2
                ];
                break;
        }
        \Classes\Response::returnResponse($result);
    }
    foreach ($products as $product) {
        $result[$product['Number']] = [
            'Description' => $product['Description'],
            'Number' => $gen->getBarcode($product['Number'])
        ];
    }
    \Classes\Response::returnResponse($result);
} else {
    echo $_SERVER['REQUEST_METHOD'];
}
