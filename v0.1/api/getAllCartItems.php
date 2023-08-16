<?php
require_once( '../assets/initializer.php' );
$data = (array) json_decode(file_get_contents('php://input'), true);
$Product = new Product( $db );


$validKeys = ['usertoken'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit();
}

if (!$Product->validateRequiredParams($data, $validKeys)) {
    return;
}

#Your method should be here
$getAllCategories = $Product->getAllCartItems($data['usertoken']);
if ( $getAllCategories ) {
    $Product->outputData( true, 'Fetched cart items', $getAllCategories );
} else {

    $Product->outputData( false,  $_SESSION[ 'err' ], null );
}

unset( $Product );
unset( $db );

