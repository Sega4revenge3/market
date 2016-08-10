<?php
/**
 * @author Ravi Tamada
 * @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial
 */
 const DEFAULT_URL = 'https://vimarket-11dcb.firebaseio.com/';
class DB_Functions
{ 
    private $conn;
    // constructor
    function __construct()
    {
        require_once 'DB_Connect.php';
        // connecting to database
        $db         = new Db_Connect();
        $this->conn = $db->connect();
    }
    // destructor
    function __destruct()
    {
    }
    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($username, $email, $password, $phone, $address, $area, $firebaseid)
    {
        $this->conn->set_charset('utf8');
        $uuid               = uniqid('', true);
        $hash               = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt               = $hash["salt"]; // salt
        $stmt               = $this->conn->prepare("INSERT INTO users(username,email,encrypted_password,salt,phone, address,area,datecreate,firebaseid) VALUES(?,?,?,?, ?, ?, ?, NOW(),?)");
        $stmt->bind_param("ssssssss", $username, $email, $encrypted_password, $salt, $phone, $address, $area, $firebaseid);
        $result = $stmt->execute();
        $stmt->close();
        // check for successful store
        if ($result) {
		
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($userid, $email, $username, $encrypted_password, $salt, $phone, $address, $area, $userpic, $datecreate, $dateupdate, $status, $firebaseid, $rate, $count);
            $user = $stmt->fetch();
            $stmt->close();
            if ($user) {
				$sql = "UPDATE users SET firebaseid='' WHERE firebaseid = '$firebaseid' and userid!= $userid";

			if ($this->conn->query($sql) === TRUE) {
			  // use is found
                    $response["error"]              = FALSE;
                    $response["user"]["userid"]     = $userid;
                    $response["user"]["username"]       = $username;
                    $response["user"]["email"]      = $email;
                    $response["user"]["phone"]      = $phone;
                    $response["user"]["address"]    = $address;
                    $response["user"]["area"]       = $area;
                    $response["user"]["userpic"]    = $userpic;
                    $response["user"]["datecreate"] = $datecreate;
                    $response["user"]["dateupdate"] = $dateupdate;
					  $response["user"]["firebaseid"]       = $firebaseid;
                    $response["user"]["rate"]       = $rate;
                    $response["user"]["count"]      = $count;
                    echo json_encode($response, 256);
			echo "Error updating record: " . $this->conn->error;
			}
                  
            } else {
                // user failed to store
                $response["error"]     = TRUE;
                $response["error_msg"] = "Unknown error occurred in registration!";
                echo json_encode($response);
            }
        } else {
            return false;
        }
    }
    //store comments
    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password,$firebaseupdate)
    {
        $this->conn->set_charset('utf8');
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->bind_result($userid, $email, $username, $encrypted_password, $salt, $phone, $address, $area, $userpic, $datecreate, $dateupdate, $status, $firebaseid, $rate, $count);
            $user = $stmt->fetch();
            $stmt->close();
            // verifying user password
            $salt               = $salt;
            $encrypted_password = $encrypted_password;
            $hash               = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                if ($user != false) {
						$sql = "UPDATE users SET firebaseid='$firebaseupdate' WHERE userid = '$userid'";

			if ($this->conn->query($sql) === TRUE) {
			  // use is found
                    $response["error"]              = FALSE;
                    $response["user"]["userid"]     = $userid;
                    $response["user"]["name"]       = $username;
                    $response["user"]["email"]      = $email;
                    $response["user"]["phone"]      = $phone;
                    $response["user"]["address"]    = $address;
                    $response["user"]["area"]       = $area;
                    $response["user"]["userpic"]    = $userpic;
                    $response["user"]["datecreate"] = $datecreate;
                    $response["user"]["dateupdate"] = $dateupdate;
					  $response["user"]["firebaseid"]       = $firebaseupdate;
                    $response["user"]["rate"]       = $rate;
                    $response["user"]["count"]      = $count;
                    echo json_encode($response, 256);
			echo "Error updating record: " . $this->conn->error;
			}
                  
                } else {
                    // user is not found with the credentials
                    $response["error"]     = TRUE;
                    $response["error_msg"] = "Login credentials are wrong. Please try again!";
                    echo json_encode($response);
                }
            } else {
                // required post params is missing
                $response["error"]     = TRUE;
                $response["error_msg"] = "Required parameters email or password is missing!";
                echo json_encode($response);
            }
        } else {
            return NULL;
        }
    }
    public function getDetailProduct($productid)
    {
        $db = new DB_Functions();
      
       $this->conn->set_charset('utf8');
        //fetch table rows from mysql db
        $sql    = "Select users.userid,users.username,users.userpic,users.rate,comments.contentcomment,comments.time,comments.productid from users,comments where users.userid = comments.userid and comments.productid = $productid order by time desc";
        $result = $this->conn->query($sql);
        //create an array
        $encode = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $encode[] = $row;
        }
        $this->conn->set_charset('utf8');
        $stmt = $this->conn->prepare("SELECT product.productid,product.productname,product.price,users.username,users.phone,users.userpic,users.email,category.categoryname,
										product.productaddress,product.areaproduct,product.producttype,product.productstatus,product.productimage,product.productdate,product.description,product.sharecount,
										product.lat,product.lot From product,users,category Where users.userid = product.userid and category.categoryid = product.categoryid and product.productid = ?");
        $stmt->bind_param("s", $productid);
        if ($stmt->execute()) {
            $stmt->bind_result($productid, $productname, $price, $username, $phone, $userpic, $email, $categoryname, $productaddress, $areaproduct, $producttype, $productstatus, $productimage, $productdate, $description, $sharecount, $lat, $lot);
            $product = $stmt->fetch();
            $stmt->close();
            // check for password equality
            if ($product != false) {
                // use is found
                $response["product"]["productid"]      = $productid;
                $response["product"]["productname"]    = $productname;
                $response["product"]["price"]          = $price;
                $response["product"]["username"]       = $username;
                $response["product"]["phone"]          = $phone;
                $response["product"]["userpic"]        = $userpic;
                $response["product"]["email"]          = $email;
                $response["product"]["categoryname"]   = $categoryname;
                $response["product"]["productaddress"] = $productaddress;
                $response["product"]["areaproduct"]    = $areaproduct;
                $response["product"]["producttype"]    = $producttype;
                $response["product"]["productstatus"]  = $productstatus;
                $response["product"]["productimage"]   = $productimage;
                $response["product"]["productdate"]    = $productdate;
                $response["product"]["description"]    = $description;
                $response["product"]["sharecount"]     = $sharecount;
                $response["product"]["lat"]            = $lat;
                $response["product"]["lot"]            = $lot;
                $response["product"]["comments"]       = $encode;
                echo json_encode($response, 256);
            } else {
                // user is not found with the credentials
                $response["error"]     = TRUE;
                $response["error_msg"] = "Login credentials are wrong. Please try again!";
                echo json_encode($response);
            }
        } else {
            return NULL;
        }
    }
    /**
     * Check user is existed or not
     */
    public function isUserExisted($email)
    {
        $stmt = $this->conn->prepare("SELECT email from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password)
    {
        $salt      = sha1(rand());
        $salt      = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash      = array(
            "salt" => $salt,
            "encrypted" => $encrypted
        );
        return $hash;
    }
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password)
    {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }
    public function storeComment($userid, $contentcomment, $productid)
    {function send_notification ($tokens, $message)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array(
			 'registration_ids' => $tokens,
			 'data' => $message
			);

		$headers = array(
			'Authorization:key = AIzaSyCEmeHXjGFCMzqhFrSPCE9zEmBuY7A6FLM ',
			'Content-Type: application/json'
			);

	   $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
	}
		
      
        $this->conn->set_charset('utf8');
        $time = round(microtime(true) * 1000);
        $stmt = $this->conn->prepare("INSERT INTO comments(userid,productid,time,contentcomment) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss", $userid, $productid, $time, $contentcomment);
        $result = $stmt->execute();
        $stmt->close();
        // check for successful store
        if ($result) {
         	$conn = mysqli_connect("localhost","root","","vimarket");

	$sql = " Select firebaseid From users";

	$result = mysqli_query($conn,$sql);
	$tokens = array();

	if(mysqli_num_rows($result) > 0 ){

		while ($row = mysqli_fetch_assoc($result)) {
			$tokens[] = $row["firebaseid"];
		}
	}

	mysqli_close($conn);

	$message = array("message" => " có người bình luận sản phẩm của bạn ");
	$message_status = send_notification($tokens, $message);
	echo $message_status;
            
        } else {
            return false;
        }
    }
	
public function sendEmail($email,$temp_password){

  $mail = $this -> mail;
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'sega4revenge@gmail.com';
  $mail->Password = 'sega4deptrai';
  $mail->SMTPSecure = 'ssl';
  $mail->Port = 465;
 
  $mail->From = 'sega4revenge3@gmail.com';
  $mail->FromName = 'Your Name';
  $mail->addAddress($email, 'Your Name');
 
  $mail->addReplyTo('sega4revenge3@gmail.com', 'Your Name');
 
  $mail->WordWrap = 50;
  $mail->isHTML(true);
 
  $mail->Subject = 'Password Reset Request';
  $mail->Body    = 'Hi,<br><br> Your password reset code is <b>'.$temp_password.'</b> . This code expires in 120 seconds. Enter this code within 120 seconds to reset your password.<br><br>Thanks,<br>Learn2Crack.';

  if(!$mail->send()) {

   return $mail->ErrorInfo;

  } else {

    return true;

  }
}
  public function passwordResetRequest($email){

    $random_string = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 6)), 0, 6);
    $hash = $this->getHash($random_string);
    $encrypted_temp_password = $hash["encrypted"];
    $salt = $hash["salt"];

    $sql = 'SELECT * from password_reset_request WHERE email = ?';
    $query = $this -> conn -> prepare($sql);
	$query ->bind_param("s", $email);
    $query -> execute();

    if($query){

      $query->store_result();
        $row_count = $query->num_rows;
		
        if ($row_count == 0){


            $insert_sql = 'INSERT INTO password_reset_request SET email =?,encrypted_temp_password =?,
                    salt =?,created_at = ?';
            $insert_query = $this ->conn ->prepare($insert_sql);
				$insert_query ->bind_param("ssss", $email,$encrypted_temp_password,$salt,date("Y-m-d H:i:s"));
            $insert_query->execute();

            if ($insert_query) {

                $user["email"] = $email;
                $user["temp_password"] = $random_string;

                return $user;

            } else {

                return false;

            }


        } else {

            $update_sql = 'UPDATE password_reset_request SET email = ?,encrypted_temp_password = ?,
                    salt =? ,created_at = ?';
            $update_query = $this -> conn -> prepare($update_sql);
			$update_query ->bind_param("ssss", $email,$encrypted_temp_password,$salt,date("Y-m-d H:i:s"));
            $update_query -> execute();

            if ($update_query) {
        
                $user["email"] = $email;
                $user["temp_password"] = $random_string;
                 return $user;

            } else {

                return false;

            }

        }
    } else {

        return false;
    }


 }
  public function getHash($password) {

     $salt = sha1(rand());
     $salt = substr($salt, 0, 10);
     $encrypted = password_hash($password.$salt, PASSWORD_DEFAULT);
     $hash = array("salt" => $salt, "encrypted" => $encrypted);

     return $hash;

}
 public function checkUserExist($email){

    $sql = 'SELECT * from users WHERE email = ?';

    $query = $this -> conn -> prepare($sql);
	$query ->bind_param("s", $email);
    $query -> execute();
  
    if($query){
		$query->store_result();
        $row_count = $query->num_rows;

        if ($row_count == 0){

            return false;

        } else {

            return true;

        }
    } else {

        return false;
    }
 }
 public function resetPassword($email,$code,$password){


    $sql = 'SELECT * FROM password_reset_request WHERE email = :email';
    $query = $this -> conn -> prepare($sql);
    $query -> execute(array(':email' => $email));
    $data = $query -> fetchObject();
    $salt = $data -> salt;
    $db_encrypted_temp_password = $data -> encrypted_temp_password;

    if ($this -> verifyHash($code.$salt,$db_encrypted_temp_password) ) {

        $old = new DateTime($data -> created_at);
        $now = new DateTime(date("Y-m-d H:i:s"));
        $diff = $now->getTimestamp() - $old->getTimestamp();
        
        if($diff < 120) {

            return $this -> changePassword($email, $password);

        } else {

            false;
        }
        

    } else {

        return false;
    }

 }
  public function changePassword($email, $password){


    $hash = $this -> getHash($password);
    $encrypted_password = $hash["encrypted"];
    $salt = $hash["salt"];

    $sql = 'UPDATE users SET encrypted_password = :encrypted_password, salt = :salt WHERE email = :email';
    $query = $this -> conn -> prepare($sql);
    $query -> execute(array(':email' => $email, ':encrypted_password' => $encrypted_password, ':salt' => $salt));

    if ($query) {
        
        return true;

    } else {

        return false;

    }

 }

}
?>
				