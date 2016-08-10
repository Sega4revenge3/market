<?php

/**
 * @author Ravi Tamada
 * @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial
 */

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['userid']) && isset($_POST['productid']) && isset($_POST['contentcomment'])) {

    // receiving the post params
  $userid = $_POST['userid'];
   $productid = $_POST['productid'];
    $contentcomment = $_POST['contentcomment'];
    // check if user is already existed with the same email
  
        // create a new user
        $comment = $db->storeComment($userid,$contentcomment,$productid);
      
    
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (name, email or password) is missing!";
    echo json_encode($response);
}
?>

