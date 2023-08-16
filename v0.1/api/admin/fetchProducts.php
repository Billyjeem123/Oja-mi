<?php
require_once( '../../assets/initializer.php' );

$Product = new Product( $db );

#  Check for rge requests method
if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
    header( 'HTTP/1.1 405 Method Not Allowed' );
    header( 'Allow: POST' );
    exit();
}

 
#Your method should be here
$getAllCategories = $Product->getAllProducts();
if ( $getAllCategories ) {
    $Product->outputData( true, 'Fetched Product', $getAllCategories );
} else {

    $Product->outputData( false,  $_SESSION[ 'err' ], null );
}

unset( $Product );
unset( $db );

