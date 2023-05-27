<?php

abstract class SharedModel
{

    public   $conn;


    public function __construct(Database $database)
    {
        $this->conn = $database->connect();
    }


    public function validateRequiredParams($data, $validKeys)
    {
        $errors = [];

        #   Check for invalid keys
        $invalidKeys = array_diff(array_keys($data), $validKeys);
        if (!empty($invalidKeys)) {
            foreach ($invalidKeys as $key) {
                $errors[] = "$key is not a valid input field";
            }
        }

        #   Check for empty fields
        foreach ($validKeys as $key) {
            if (empty($data[$key])) {
                $errors[] = ucfirst($key) . ' is required';
            }
        }

        if (!empty($errors)) {
            $this->respondUnprocessableEntity($errors);
            return;
        }

        #   Sanitize input
        foreach ($validKeys as $key) {
            $data[$key] = $this->sanitizeInput($data[$key]);
        }

        return $data;
    }

    public function saveProfileImage(array $profileImage): array
    {
        $imageInfo = [];

        #   Get the image file information
        $profileImageName = $profileImage['name'];
        $profileImageTmp = $profileImage['tmp_name'];

        #   Check if at least profile image file is present
        if (!isset($profileImageName) || empty($profileImageName)) {
            http_response_code(400);
            $this->outputData(false, 'Please select an image to upload', null);
            return null;
        }

        #   Valid file extensions
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        #   Test for profile image file extension
        if (isset($profileImageName) && !empty($profileImageName)) {
            $profileImageExt = strtolower(pathinfo($profileImageName, PATHINFO_EXTENSION));
            if (!in_array($profileImageExt, $validExtensions)) {
                http_response_code(422);
                $this->outputData(false, 'Only JPG, JPEG, PNG and GIF files are allowed.', null);
                return null;
            } else {
                #   Save the profile image file
                $newProfileImageName = time() . '_' . $profileImageName;
                $profileImagePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $newProfileImageName;
                if (!file_exists($profileImagePath) || !is_readable($profileImageTmp)) {
                    http_response_code(422);
                    $this->outputData(false, 'Unable to upload the profile image. Please try again later.', null);
                    return null;
                } else if (move_uploaded_file($profileImageTmp, $profileImagePath)) {
                    $imageInfo['profileImage'] = $newProfileImageName;
                } else {
                    $imageInfo = null;
                }
            }
        }
        http_response_code(200);
        return $imageInfo;
    }

    public  function getMemoryUsage()
    {
        $mem_usage = memory_get_usage(true);
        if ($mem_usage < 1024)
            return $mem_usage . ' bytes';
        elseif ($mem_usage < 1048576)
            return round($mem_usage / 1024, 2) . ' KB';
        else
            return round($mem_usage / 1048576, 2) . ' MB';
    }

    public  function checkSize()
    {
        $memory_usage = $this->getMemoryUsage();
        echo 'Memory usage: ' . $memory_usage;
    }

    #   validate email

    public function validateEmail($value)
    {
        #   code...
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * sanitizeInput Parameters
     *
     * @param [ type ] $input
     * @return string
     */
    public  function sanitizeInput($input)
    {
        # Remove white space from beginning and end of string
        $input = trim($input);
        # Remove slashes
        $input = stripslashes($input);
        # Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $input;
    }

    #  resourceNotFound::Check for id if exists

    private function resourceNotFound(int $id): void
    {

        echo json_encode(['message' => "Resource with id $id not found"]);
    }

    /**
     * respondUnprocessableEntity alert of errors deteced
     *
     * @param array $errors
     * @return void
     */

    public function respondUnprocessableEntity(array $errors): void
    {

        $this->outputData(false,  'Kindly review your request parameters to ensure they comply with our requirements.',  $errors);
    }

    public function connectToThirdPartyAPI(array $payload, string $url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            $this->outputData(false, 'Unable to process request, try again later', null);
        }

        curl_close($ch);

        return $response;
    }

    public function respondWithInternalError($errors): void
    {

        $this->outputData(false,  'Unable to process request, try again later',  $errors);
    }

    public function token()
    {

        $defaultPassword = mt_rand(100000, 999999);
        return $defaultPassword;
    }

    #getUserdata::This method fetches All info related to a user

    public function getUserdata(mixed $identifier)
    {
        try {
            $db = new Database();
            $sql = 'SELECT * FROM tblusers WHERE ';
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $sql .= 'mail = :mail';
            } else if (is_int($identifier)) {
                $sql .= 'usertoken = :usertoken';
            } else {
                throw new \InvalidArgumentException('Invalid identifier type');
            }

            $stmt = $this->conn->prepare($sql);
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $stmt->bindParam(':mail', $identifier);
            } else {
                $stmt->bindParam(':usertoken', $identifier);
            }
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stmt->rowCount() === 0) {
                $this->outputData(false, 'No user found', null);
                exit;
            }

            $array = [
                'fname' => $user['name'],
                'mail' => $user['mail'],
                'usertoken' => $user['usertoken'],
                'phone' => $user['phone'],
            ];
        } catch (PDOException $e) {
            $_SESSION['err'] = 'Error retrieving user details: ' . $e->getMessage();
            exit;
            #   $this->respondWithInternalError( false, 'Unable to retrieve user details: ' . $e->getMessage(), null );
            return false;
        } finally {
            $stmt = null;
        }
        return $array;
    }

    #authenticateUser:: This method authencticates User data

    public function authenticateUser($usertoken)
    {

        try {

            $sql = 'SELECT usertoken FROM tblusers WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usertoken', $usertoken);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {

                $_SESSION['err'] = 'User does not exists';
                return false;
                exit;
            }

            return true;
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            return false;
        } finally {
            $stmt =  null;
        }
    }

      #This method gets  a user account balance

      public function getAccountBalance( $usertoken )
      {
             try {
                 $sql = 'SELECT amount AS totalBalance FROM tblwallet WHERE usertoken = :userToken';
                 $stmt = $this->conn->prepare($sql);
                 $stmt->bindParam( ':userToken', $usertoken, PDO::PARAM_INT );
                 $stmt->execute( [ $usertoken ] );
     
                 if ( $stmt->rowCount() == 0 ) {
                 
                     return false;
                     exit;
                 }
     
                 $totalBalance = $stmt->fetch( PDO::FETCH_ASSOC );
                 $array = [
                     'totalBalannce' => $totalBalance[ 'totalBalance' ]
                 ];
             } catch ( PDOException $e ) {
                 echo 'Error: ' . $e->getMessage();
             }
             finally {
                 $stmt = null;
                 $this->conn  = null;
             }
             return $array;
         }

    #formatDate::This method format date to humna readable format

    public function formatDate($time)
    {

        return date('D d M, Y: H', $time);
    }

    #v::This method format date to amount readable fomat

    public function formatCurrency($amonut)
    {

        return number_format($amonut, 2);
    }

    public function outputData($success = null, $message = null, $data = null)
    {

        $arr_output = array(
            'success' => $success,
            'message' => $message,
            'data' => $data,
        );
        echo json_encode($arr_output);
    }
}
