<?php

class Utility
{
  #  Function to generate OTP 
  public function generateNumericOTP($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "1357902468";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }

  public  static function generateAlphaNumericOTP($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "1357902468ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }

  public  static function generateAlphaNumericOTP_case($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "1357902468ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }

  public static function generateAlphaNumericOTP_symbol($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "1357902468ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_@!";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }

  public static function generateAlphaOTP($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }


  #  validate email
  public  static function validateEmail(string $mail)
  {
    #  code...

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
  }


  public static  function token()
  {

    $defaultPassword = mt_rand(100000, 999999);
    return $defaultPassword;
  }

  #formatDate::This method format date to humna readable format

  public static  function formatDate($time)
  {

    return date('D d M, Y: H', $time);
  }

  #v::This method format date to amount readable fomat

  public static function formatCurrency($amonut)
  {

    return number_format($amonut, 2);
  }



  public static function diffForHumans($timestamp)
  {

    $current_time = time();

    $difference_in_seconds = $current_time - $timestamp;

    if ($difference_in_seconds < 60) {
      return "Just now";
    } elseif ($difference_in_seconds < 3600) {
      return floor($difference_in_seconds / 60) . " minutes ago";
    } elseif ($difference_in_seconds < 86400) {
      return floor($difference_in_seconds / 3600) . " hours ago";
    } elseif ($difference_in_seconds < 604800) {
      return floor($difference_in_seconds / 86400) . " days ago";
    } elseif ($difference_in_seconds < 2592000) {
      $weeks = floor($difference_in_seconds / 604800);
      return $weeks . " " . ($weeks === 1 ? "week" : "weeks") . " ago";
    } elseif ($difference_in_seconds < 31104000) {
      return floor($difference_in_seconds / 2592000) . " months ago";
    } else {
      return floor($difference_in_seconds / 31104000) . " years ago";
    }
  }
}
