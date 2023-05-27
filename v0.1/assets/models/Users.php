<?php

class Users extends SharedModel
{


    public function registerUser(array $data)
    {

        $checkIfMailExists = $this->checkIfMailExists($data['mail']);
        if ($checkIfMailExists) {
            $this->outputData(false, 'Email already exists', null);
            return;
        }
        $token = (int) $this->token();
        $passHash = password_hash($data['pword'], PASSWORD_DEFAULT);
        #  Prepare the fields and values for the insert query
        $fields = [
            'name' => $data['name'],
            'mail' => $data['mail'],
            'pword'  => $passHash,
            'usertoken' => $token,
            'otp' => $token,
            "role" => "User",
            'time' => time()

        ];

        # Build the SQL query
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $columns = implode(', ', array_keys($fields));
        $sql = "INSERT INTO tblusers ($columns) VALUES ($placeholders)";

        #  Execute the query and handle any errors
        try {
            $stmt =  $this->conn->prepare($sql);
            $i = 1;
            foreach ($fields as $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($i,  $this->sanitizeInput($value), $type);
                $i++;
            }
            $stmt->execute();

            $mailer = new Mailer();

            try {
                $mailer->sendOTPToken($data['mail'], $data['name'], $token);
            } catch (Exception $e) {
                $errorMessage = date('[Y-m-d H:i:s] ') . 'Error sending mail  for ' . __METHOD__ . '  ' . PHP_EOL . 'Error Message: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
                 error_log($errorMessage, 3, 'mail.log');

            }
             $this->createWallet($token);

            http_response_code(201);
            $output = $this->outputData(true, 'Account created', null);
        } catch (PDOException $e) {

            $output  = $this->respondWithInternalError('Error: ' . $e->getMessage());
        } finally {
            $stmt   = null;
            $this->conn = null;
            unset($mailer);
        }

        return $output;
    }



     #Create wallet::Tgis method create wallet on the platform

     public function createWallet(int $usertoken)
     {
         $amount = '0.00';
         try {
             $sql = 'INSERT INTO tblwallet (usertoken, amount) VALUES (:usertoken, :amount)';
             $stmt = $this->conn->prepare($sql);
             $stmt->bindParam(':usertoken', $usertoken);
             $stmt->bindParam(':amount', $amount);
             $stmt->execute();
             return true;
         } catch (PDOException $e) {
             $this->respondWithInternalError('Error: ' . $e->getMessage());
             return false;
         } finally {
             $stmt = null;
             $this->conn = null;
         }
     }

    # updateUserData function updates user biodata

    public function updateUserData(int $id, array $data): int
    {

        try {
            $updateQuery = 'UPDATE users SET ';
            $params = array();
            foreach ($data as $key => $value) {
                $updateQuery .= $key . ' = ?, ';
                $params[] = $value;
            }
            $updateQuery = rtrim($updateQuery, ', ') . ' WHERE id = ?';
            $params[] = $id;

            $stmt = $this->conn->prepare($updateQuery);
            $stmt->execute($params);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            $this->respondWithInternalError($_SESSION['err']);
            return null;
        } finally {
            $stmt = null;
            $this->conn = null;
        }
    }

   
    public function tryLogin($data): array|bool
    {

        try {
            $sql = 'SELECT * FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':mail', $data['mail'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                $this->outputData(false, 'No user found', null);
                return false;
            }

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$user){
                return false;
            }

            if ($user['status'] != 1) {

                $unverifiedData = array(
                    'regStatus' => ($user['status'] == 1) ? true : false

                );
                $this->outputData(false, 'Account not verified.verify account by entering  OTP sent to yout mail', $unverifiedData);
                exit;
            }

            if (!password_verify($data['pword'], $user['pword'])) {
                $this->outputData(false, "Incorrect password for $data[mail]", null);
                exit;
            }
                $getAccountBalance =  $this->getAccountBalance($user['usertoken']);
                $userData = [
                    'name' => $user['name'],
                    'mail' => $user['mail'],
                    'usertoken' => $user['usertoken'],
                    'regStatus' => ($user['status'] == 1) ? true : false,
                    'userType' => $user['userType'],
                    'availableBalance' => ($getAccountBalance['totalBalannce']),
                    'availableBalance_thousand' => $this->formatCurrency($getAccountBalance['totalBalannce']),
                    'RegisteredOn' => $this->formatDate($user['time'])
                ];
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            $this->respondWithInternalError($_SESSION['err']);
        } finally {
            $stmt = null;
            $this->conn = null;
        }
        return $userData;
    }
    private function checkIfMailExists(string $mail): bool
    {

        try {
            $sql = 'SELECT mail FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            $this->respondWithInternalError($_SESSION['err']);
        } finally {
            $stmt = null;
        }
    }

    #Update Password:: This function updates a user Password

    public function updatePassword(array $data): void
    {

        try {
            $sql = 'SELECT pword FROM tblusers WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usertoken', $data['usertoken'], PDO::PARAM_INT);

            if ($stmt->execute()) {
                $dbPwd = $stmt->fetchColumn();
                $passHash = password_hash($data['npword'], PASSWORD_DEFAULT, [12]);

                if (password_verify($data['fpword'], $dbPwd)) {

                    if (!$this->updatePasswordInDB($passHash, $data['usertoken'])) {
                        $this->outputData(false, $_SESSION['err'], null);
                        return;
                    }
                    $this->outputData(true, 'Password Updated', null);
                    return;
                } else {

                    $this->outputData(false, 'Current password specified is not correct', null);
                    return;
                }
            }
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            $this->respondWithInternalError($_SESSION['err']);
        } finally {
            $stmt = null;
            $this->conn = null;

            #   Terminate the database connection
        }
    }

    # updatePasswordInDB::This function Updates users ppassword....

    private  function updatePasswordInDB(string $pword, int $usertoken): bool
    {
        try {
            $sql = 'UPDATE tblusers SET pword = :pword WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':pword', $pword,  PDO::PARAM_STR);
            $stmt->bindParam(':usertoken', $usertoken,  PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            return false;
        } finally {
            $stmt = null;
            $this->conn = null;
        }
    }

    public function forgetPword(array $data): bool
    {

        $checkIfMailExists = $this->checkIfMailExists($data['mail']);
        if (!$checkIfMailExists) {
            $this->outputData(false, 'Email does not exists', null);
            return false;
        }
        $token = $this->token();
        $passHash = password_hash($token, PASSWORD_DEFAULT);

        if (!$this->resetPasswordInDB($passHash, $data['mail'])) {
            $this->respondWithInternalError($_SESSION['err']);

            return false;
        }

        $mailer = new Mailer;
        $userData = $this->getUserData($data['mail']);

        try {
            if ($mailer->sendPasswordToUser($data['mail'], $userData['fname'], $token)) {
                $this->outputData(true, 'Password sent to mail', null);
                return true;
            }
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
        } finally {
            unset($mailer);
        }
    }

    private function resetPasswordInDB(string $pword, string $mail): bool
    {
        try {
            $sql = 'UPDATE tblusers SET pword = :pword WHERE mail = :mail';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':pword', $pword,  PDO::PARAM_STR);
            $stmt->bindParam(':mail', $mail,  PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            return false;
        } finally {
            $stmt = null;
        }
    }
}
