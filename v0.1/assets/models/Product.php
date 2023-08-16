<?php

class Product extends SharedModel
{


    public function createProduct(array $data)
    {

        $productQuantity = (int) $data['productQuantity'];
        $price = floatval($data['productPrice']);
        $usertoken = (int) $data['usertoken'];
        $catid = (int) $data['catid'];
        $token = (int) $this->token();

        $imageUrl = $_ENV['IMAGE_PATH'] . "/$data[productImage]";
        #  Prepare the fields and values for the insert query
        $fields = [
            'usertoken' => $usertoken,
            'productName' => $data['productName'],
            'catid' => $catid,
            'productQuantity' => $productQuantity,
            'productImage'  => $data['productImage'],
            'delieveryPrice	' => $data['delieveryPrice'],
            'productPrice' => $price,
            'imageUrl' => $imageUrl,
            'token' => $token,
            'time' => time()

        ];

        # Build the SQL query
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $columns = implode(', ', array_keys($fields));
        $sql = "INSERT INTO tblproducts ($columns) VALUES ($placeholders)";

        #  Execute the query and handle any errors
        try {
            $stmt =  $this->conn->prepare($sql);
            $i = 1;
            foreach ($fields as $value) {
                $type = is_int($value) ? PDO::PARAM_INT : (is_float($value) ? PDO::PARAM_STR : PDO::PARAM_STR);
                $stmt->bindValue($i,  $value, $type);
                $i++;
            }
            $stmt->execute();

            http_response_code(201);
            $output = $this->outputData(true, 'Product uploaded', null);
        } catch (PDOException $e) {

            $output  = $this->respondWithInternalError('Error: ' . $e->getMessage());
        } finally {
            $stmt = null;
            $this->conn = null;
        }

        return $output;
    }


     #FertchProducts:: This method fetches all  available products

     public function getAllProducts()
     {
         try {
             $dataArray = array();
             $sql = 'SELECT * FROM tblproducts ORDER BY id DESC';
             $stmt = $this->conn->query($sql);
             $stmt->execute();
             $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
             $count = count($products);
 
             if ($count === 0) {
                 $_SESSION['err'] = 'No products available';
                 return;
             }
 
             foreach ($products as $allProducts) {
 
                $array = [
                    'productName' => $allProducts['productName'],
                    'productQuantity' => $allProducts['productQuantity'],
                    'delieveryPrice' => $allProducts['delieveryPrice'],
                    'productToken' => $allProducts['token'],
                    'productPrice_thousand' => $this->formatCurrency($allProducts['productPrice']),
                    'productPrice' => ($allProducts['productPrice']),
                    'productImage' => ($allProducts['imageUrl']),
                ];
 
                 array_push($dataArray, $array);
             }
 
             return $dataArray;
         } catch (Exception $e) {
             $_SESSION['err'] =  'Error: ' . $e->getMessage();
             return false;
         } finally {
             $stmt = null;
             $this->conn = null;
         }
 
         return $dataArray;
     }
 

      #FertchProducts:: This method fetches all  available products

      public function getProductsByToken(int $token)
      {
          try {
              $dataArray = array();
              $sql = 'SELECT * FROM tblproducts WHERE token = :token';
              $stmt = $this->conn->prepare($sql);
              $stmt->bindParam(':token', $token);
              $stmt->execute();
              
              $allProducts = $stmt->fetch(PDO::FETCH_ASSOC);
              $count = count($allProducts);
  
              if ($count === 0) {
                  $_SESSION['err'] = 'No products available';
                  return;
              }
                  $array = [
                      'productName' => $allProducts['productName'],
                      'productQuantity' => $allProducts['productQuantity'],
                      'productUrl' => $allProducts['imageUrl'],
                      'delieveryPrice' => $allProducts['delieveryPrice'],
                      'productToken' => $allProducts['token'],
                      'productPrice_thousand' => $this->formatCurrency($allProducts['productPrice']),
                      'productPrice' => ($allProducts['productPrice']),
                      'productImage' => ($allProducts['imageUrl']),
                  ];
  
  
             
          } catch (Exception $e) {
              $_SESSION['err'] =  'Error: ' . $e->getMessage();
              return false;
          } finally {
              $stmt = null;
              $this->conn = null;
          }
  
          return $array;
      }


       #addProductToCart This emethod adds  a product to cart..

    public function addItemsToCart(array $data)
    {
        try {
            if ($this->checkIfItemExistInCart($data['usertoken'], $data['productToken'])) {
                $this->outputData(false, 'Item already exists', null);
                exit;
            }
            $isAddedToCart = false;
            $productQuantity = 1;
            $sql = "INSERT INTO tblcarts(usertoken, productToken, productQuantity) 
                    VALUES(:usertoken, :productToken, :productQuantity)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usertoken', $data['usertoken'],);
            $stmt->bindParam(':productToken', $data['productToken']);
            $stmt->bindParam(':productQuantity', $productQuantity);
            $stmt->execute();
            $_SESSION['err'] = 'Item added to cart';
            $isAddedToCart = true;
        
        } catch (PDOException $e) {
            $_SESSION['err'] = 'Error adding item to cart: ' . $e->getMessage();
            $isAddedToCart = false;
        } finally {
            $stmt = null;
            $this->conn = null;
        }

        return  $isAddedToCart;
    }

     #checkIfItemExistInCart::This method checks if item alreay exists in cart

     public function checkIfItemExistInCart(int $userToken, int $productToken)
     {
         try {
             $sql = 'SELECT COUNT(*) AS count FROM tblcarts 
             WHERE usertoken = :userToken AND productToken = :productToken';
             $stmt = $this->conn->prepare($sql);
             $stmt->bindParam(':userToken', $userToken, PDO::PARAM_INT);
             $stmt->bindParam(':productToken', $productToken, PDO::PARAM_INT);
             $stmt->execute();
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
             if ($result['count'] == 1) {
                 return true;
             } else {
                 return false;
             }
         } catch (PDOException $e) {
             $_SESSION['err'] = 'Error confirming cart: ' . $e->getMessage();
             $this->outputData(false, $_SESSION['err'], null);
             return false;
         } finally {
             $stmt = null;
             #  $this->conn  = null;
         }
     }


     #getAllCartItems:: This method fetches all available cart items in the cart...

    public function getAllCartItems(int $usertoken)
    {

        try {
            $sql = 'SELECT usertoken, productToken, productQuantity FROM tblcarts WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usertoken', $usertoken, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                $this->outputData(false, 'No item found', null);
                exit;
            }

            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dataArrays = array();
            #  Initialize a variable to hold the total price
            $total = 0;
            foreach ($cartItems as $allCartItems) {
                #  Retrieve the product token and quantity for the current cart item
                $productToken = $allCartItems['productToken'];
                $productQuantity = $allCartItems['productQuantity'];

                #  Retrieve the product data for the current product token
                $getProductByItRelatedToken = $this->getProductByItRelatedToken($productToken);

                #  Calculate the subtotal for the current cart item and add it to the total price
                $productPrice = $getProductByItRelatedToken['productPrice'];
                $subtotal = $productQuantity * $productPrice;
                $total += $subtotal;

                #  Create an array to hold the current product data and add it to the cart data array
                $cartItem = [
                    'productname' => $getProductByItRelatedToken['productName'],
                    'productToken' => $getProductByItRelatedToken['productToken'],
                    'maximumQuantity' => intval($getProductByItRelatedToken['productQuantity']),
                    'productQuantity' => intval($productQuantity),
                    'productPrice' => ($productPrice),
                    'productPrice_thousand' => $this->formatCurrency($subtotal),
                    'productImage' => $getProductByItRelatedToken['imageUrl']
                ];
                array_push($dataArrays, $cartItem);
            }

            #  Create an array to hold the final cart data
            $dataResult = [
                'Products' => $dataArrays,
                'TotalPrice_thousand' => $this->formatCurrency($total),
                'TotalPrice' => $total
            ];
        } catch (PDOException $e) {
            #  Handle any PDO exceptions
            $_SESSION['err'] = 'PDO Exception: ' . $e->getMessage();
            $this->respondWithInternalError($_SESSION['err']);
            exit;
        } catch (Exception $e) {
            #  Handle any other exceptions
            $_SESSION['err'] = ' Exception: ' . $e->getMessage();
            $this->respondWithInternalError($_SESSION['err']);
            exit;
        } finally {

            $stmt = null;
            $this->conn = null;
        }
        #  Return the final cart data array
        return $dataResult;
    }


    public function getProductByItRelatedToken( $productToken )
    {
           $dataArray = array();
   
           try {
               $sql = 'SELECT * FROM tblproducts WHERE token = :pToken';
               $stmt = $this->conn->prepare( $sql );
               $stmt->bindParam( ':pToken', $productToken, PDO::PARAM_INT );
               $stmt->execute();
               $relatedProducts = $stmt->fetch( PDO::FETCH_ASSOC );
   
               $array = [
                'productName' => $relatedProducts['productName'],
                'productQuantity' => $relatedProducts['productQuantity'],
                'productUrl' => $relatedProducts['imageUrl'],
                'delieveryPrice' => $relatedProducts['delieveryPrice'],
                'productToken' => $relatedProducts['token'],
                'productPrice_thousand' => $this->formatCurrency($relatedProducts['productPrice']),
                'productPrice' => ($relatedProducts['productPrice']),
                'imageUrl' => ($relatedProducts['imageUrl']),
            ];
           } catch ( PDOException $e ) {
               $this->respondWithInternalError( 'Unable to get product related items: ' . $e->getMessage() );
               return false;
           }
           finally {
               $stmt = null;
           }
   
           return $array;
       }
  
}
