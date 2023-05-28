<?php
require_once( '../../assets/initializer.php' );
$data = ( array ) json_decode( file_get_contents( 'php://input' ), true );

$Category = new Category( $db );

if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
    header( 'HTTP/1.1 405 Method Not Allowed' );
    header( 'Allow: POST' );
    exit();
}

#  Check for params  if matches required parametes
$validKeys = [ 'catname'];
if (!$Category->validateRequiredParams($data, $validKeys)) {
    return;
}
$createCategory  = $Category->createCategory( $data );
unset( $Category );
unset( $db );



