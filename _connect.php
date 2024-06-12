<?php
//กำหนดค่า Access-Control-Allow-Origin ให้ เครื่อง อื่น ๆ สามารถเรียกใช้งานหน้านี้ได้
header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");

header("Access-Control-Max-Age: 3600");

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// รับข้อมูลจาก LINE Webhook
$input = file_get_contents('php://input');
$events = json_decode($input, true);

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == 'POST' && substr($_GET['id'], 0, 4) == 'imed') {
} else {
    //ตั้งค่าการเชื่อมต่อฐานข้อมูล
    $dbHost = "xxxxxxxx";
    $dbPort = "xxxxxxxx";
    $dbUser = "xxxxxxx";
    $dbPassword = "xxxxxxx";
    $dbName = "xxxxxxxxxxx";
}


// Data source name
$dsn = "pgsql:host=" . $dbHost . ";port=" . $dbPort . ";dbname=" . $dbName . "";
$conn = null;


try {
    $conn = new PDO($dsn, $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>