<?php 


class Payment extends SharedModel{




    public function processWallet( $reference )
    {
           $transaction_reference = $reference;
           // 		$paystack_public_key = 'sk_test_476c7d23197ff39c9d7cfd1dd3384d6e5e9f46ce';
   
           $url = 'https://api.paystack.co/transaction/verify/' . rawurlencode( $reference );
   
           $ch = curl_init();
           curl_setopt( $ch, CURLOPT_URL, $url );
           curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
           curl_setopt( $ch, CURLOPT_HTTPHEADER, [
               'Authorization: Bearer sk_test_476c7d23197ff39c9d7cfd1dd3384d6e5e9f46ce', //replace this with your own test key
           ] );
   
           $request = curl_exec( $ch );
           $status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
   
           return ( $request );
       }
   
      










}