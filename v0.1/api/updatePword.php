<?php
require_once( '../assets/initializer.php' );
$data = ( array ) json_decode( file_get_contents( 'php://input' ), true );

$user = new Users( $db );

$validKeys = [ 'usertoken', 'fpword', 'npword' ];
#  Check for params  if matches required parametes
if (!$user->validateRequiredParams($data, $validKeys)) {
    return;
}
    
$updatePassword = $user->updatePassword( $data );
unset($user);
unset($db);

