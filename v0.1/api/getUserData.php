<?php
require_once('../assets/initializer.php');
$data = (array) json_decode(file_get_contents('php://input'), true);

$user = new Users($db);


$validKeys = ['usertoken'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit();
}

if (!$user->validateRequiredParams($data, $validKeys)) {
    return;
}

$getUserdata = $user->getUserdata($data['usertoken']);
if($getUserdata){

    $user->outputData(true, "Fecthed User Data", $getUserdata);
}else{

    $user->outputData(false, $_SESSION['err'], null);
}

#Your method should be here
unset( $user );
unset( $db );
