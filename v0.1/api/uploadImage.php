<?php  
require_once( '../assets/initializer.php' );

$User = new Users( $db );

if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
    $User->respondMethodNotAllowed( 'POST' );
    exit();
}

if ( empty( $_FILES['image'] ) || !is_array( $_FILES['image']['name'] ) ) {
    $User->respondUnprocessableEntity( 'Please select at least one image to upload' );
    exit();
}

$imageInfo = array();
$validExtensions = array( 'jpg', 'jpeg', 'png', 'gif' );

foreach ( $_FILES['image']['error'] as $key => $error ) {
    if ( $error !== UPLOAD_ERR_OK ) {
        continue;
    }

    $imageName = $_FILES['image']['name'][$key];
    $imageTmp = $_FILES['image']['tmp_name'][$key];
    $imageName_ext = strtolower( pathinfo( $imageName, PATHINFO_EXTENSION ) );

    if ( !in_array( $imageName_ext, $validExtensions ) ) {
       
        $User->respondUnprocessableEntity("Invalid image file extension for $imageName ");
        exit();
    }

    $mixImageNameWithTime = time() . '_' . $imageName;
    $newImageName = $_ENV[ 'APP_NAME' ] . '_' . $mixImageNameWithTime;
    $pathToImageFolder = ( $_SERVER[ 'DOCUMENT_ROOT' ] . '/Oja-mi/uploads/' . $newImageName );

    if ( !move_uploaded_file( $imageTmp, $pathToImageFolder ) ) {
        $User->respondUnprocessableEntity("Unable to upload image '$imageName'. Please try again later.");
        exit();
    }

    $imageInfo[] = array( 'image' => $newImageName ) ?? null;
}

$User->outputData( true, 'Fetched images', $imageInfo );
unset( $User );
unset( $db );
