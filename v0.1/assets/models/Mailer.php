<?php

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
 {

    public function sendOTPToken( $email, $fname, $otp )
    {
   
           require_once( $_SERVER[ 'DOCUMENT_ROOT' ] . '/Oja-mi/vendor/autoload.php' );
   
           $mail = new PHPMailer( true );
   
           $mail->isSMTP();
   
           $mail->Host = $_ENV[ 'HOST_NAME' ];
   
           $mail->SMTPAuth = true;
   
           $mail->Username = $_ENV[ 'SMTP_USERNAME' ];
   
           $mail->Password = $_ENV[ 'SMTP_PWORD' ];
   
           $mail->SMTPSecure = 'ssl';
   
           $mail->Port = 465;
   
           $mail->setFrom( $_ENV[ 'APP_MAIL' ], $_ENV[ 'APP_NAME' ] );
   
           $mail->addAddress($email, $fname );
   
           $mail->isHTML( true );
   
           $mail->Subject = ' [Oja-Mi] Account Verification';
           
           $body =  "Dear $fname ,<br><br>";
           
           $body .= "Thank you for registering with us! We're thrilled to have you as a part of our community. To ensure the security and validity of your account, we kindly ask you to complete the email verification process. <br><br>";
           
            $body .=  "Your OTP is: $otp<br><br>";
            
           $body .=  "To finalize your registration and gain full access to our platform, kindly enter the OTP in the designated field on our website. This verification step helps us ensure that your email address is correct and that you have control over the account.<br><br>";
           
           $body .= "<h4> $otp</h4><br><br>";
           
           $body .= "Thank you for your cooperation in completing the email verification process. We look forward to providing you with a fantastic experience on our platform. Should you need any assistance or have any feedback, feel free to reach out to us<br><br>";
           
           $body .= 'Best regards, <br><br>';
   
           $body .= "Team  {$_ENV['APP_NAME']}" ;
   
           $mail->Body = $body;
   
          
   
           if ( !$mail->send() ) {
               echo 'sent';
           } else {
               return true;
           }
       }



   
}
