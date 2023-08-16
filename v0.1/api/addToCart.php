<?php
require_once('../assets/initializer.php');
$data = (array) json_decode(file_get_contents('php://input'), true);

$Product = new Product($db);


$validKeys = ['productToken','usertoken'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit();
}

if (!$Product->validateRequiredParams($data, $validKeys)) {
    return;
}

$getProductdata = $Product->addItemsToCart($data);
if($getProductdata){

    $Product->outputData(true, "Item Added", null);
}else{

    $Product->outputData(false, $_SESSION['err'], null);
}

#Your method should be here
unset( $Product );
unset( $db );
