<?php
    echo "{";
    echo "feed:";
    //open connection to mysql db
   $connection = mysqli_connect("localhost","root","","vimarket") or die("Error " . mysqli_error($connection));
	$connection->set_charset('utf8');

    //fetch table rows from mysql db
  
if (isset($_POST['page'])) {
    $page = $_POST['page'];
	if ($page < 1) {
    $page = 1;
	}
	$limit = 10;
	$start = ($limit * $page) - $limit;
  $sql = "Select product.productid,product.productname,product.price,users.username,category.categoryname,product.productaddress,product.areaproduct,product.producttype,
  product.productstatus,product.productimage,product.productdate,product.description,product.sharecount,product.lot,product.lat 
			From product,users,category Where users.userid = product.userid and category.categoryid = product.categoryid LIMIT $start,$limit";
    $result = $connection->query($sql);

    //create an array
   $encode = array();

while($row = mysqli_fetch_assoc($result)) {
   $encode[] = $row;
}

echo json_encode($encode,256);  

    //close the db connection
    mysqli_close($connection);
    echo "}";
}	
  else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>		