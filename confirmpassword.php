<?php

/**
 * @author Ravi Tamada
 * @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial
 */

require_once 'include/Functions.php';

$fun = new Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['email']) && isset($_POST['code']) && isset($_POST['password'])) {
	$email = $_POST['email'];
    $code = $_POST['code'];
    $password = $_POST['password'];
    echo $fun -> resetPassword($email,$code,$password);
   
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (name, email or password) is missing!";
    echo json_encode($response);
}
?>

