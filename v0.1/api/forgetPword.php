<?php
require_once('../assets/initializer.php');
$data = (array) json_decode(file_get_contents('php://input'), true);

$user = new Users($db);


$validKeys = ['mail'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit();
}

if (!$user->validateRequiredParams($data, $validKeys)) {
    return;
}


$forgetPword = $user->forgetPword($data);
unset($user);
unset($db);
