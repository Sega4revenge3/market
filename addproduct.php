<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'connection.php';
    addProduct();
}
/**
 *
 */
function addProduct()
{
    global $connect;

    $productname = $_POST["productname"];
    $price = $_POST["price"];
    $userid = $_POST["userid"];
    $categoryid = $_POST["categoryid"];
    $productaddress = $_POST["productaddress"];
    $areaproduct = $_POST["areaproduct"];
    $producttype = $_POST["producttype"];
    $productstatus = $_POST["productstatus"];
    $productimage = $_POST["productimage"];
    $description = $_POST["description"];
    $lat = $_POST["lat"];
    $lot = $_POST["lot"];

    // $now = NOW();
    $productdate = strtotime('now');
    $connect->set_charset('utf8');
    $query = "Insert into product(productname,price,userid,categoryid,productaddress,areaproduct,producttype,productstatus,productimage,productdate,description,lat,lot) values ('$productname','$price','$userid','$categoryid',
            '$productaddress','$areaproduct','$producttype','$productstatus','$productimage','$productdate','$description','$lat','$lot');";
    mysqli_query($connect, $query) or die (mysqli_error($connect));
    mysqli_close($connect);
}

?>