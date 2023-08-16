<?php
require_once( '../../assets/initializer.php' );
$data = ( array ) json_decode( file_get_contents( 'php://input' ), true );

$Product = new Product( $db );

if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
    header( 'HTTP/1.1 405 Method Not Allowed' );
    header( 'Allow: POST' );
    exit();
}

#  Check for params  if matches required parametes
$validKeys = [ 'catid',  'usertoken', 'productName', 'productQuantity', 'productImage', 'delieveryPrice', 'productPrice'];
if (!$Product->validateRequiredParams($data, $validKeys)) {
    return;
}
$createProduct  = $Product->createProduct( $data );
unset( $Product );
unset( $db );



