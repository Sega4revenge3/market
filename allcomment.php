<?php
echo "{";
echo "comments:";
//open connection to mysql db
$connection = mysqli_connect("localhost", "root", "", "vimarket") or die("Error " . mysqli_error($connection));
$connection->set_charset('utf8');

//fetch table rows from mysql db

if (isset($_POST['productid'])) {
    $productid = $_POST['productid'];
    $sql = "Select users.userid,users.username,users.userpic,users.rate,comments.contentcomment,comments.time,comments.productid from users,comments where users.userid = comments.userid and comments.productid = $productid order by time desc";
    $result = $connection->query($sql);

    //create an array
    $encode = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $encode[] = $row;
    }

    echo json_encode($encode, 256);

    //close the db connection
    mysqli_close($connection);
    echo "}";
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>		