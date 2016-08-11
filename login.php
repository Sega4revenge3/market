<?php

/**
 * @author Ravi Tamada
 * @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial
 */

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['firebaseid'])) {

    // receiving the post params
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firebaseid = $_POST['firebaseid'];
    // get the user by email and password
    $user = $db->getUserByEmailAndPassword($email, $password,$firebaseid);
}
else {
    echo $_POST['email'];
    echo $_POST['password'];
    echo $_POST['firebaseid'];
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>

