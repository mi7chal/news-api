<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");


session_start();
require_once "connect.php";
require_once "checkViews.php";

$response = (object)null;
$response->alreadyLogged = false;
$response->status = 1;
$response->info='';
$result = mysqli_query($link, "SELECT * FROM kategorie");
if ($result) {
    $return = [];
    while ($row = mysqli_fetch_assoc($result))
        $return[] = $row;

    if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
        $response->alreadyLogged = true;
    }
    $response->body = $return;

    echo json_encode($response);
} else {
    $response->alreadyLogged = false;
    $respone->status = 0;
    echo json_encode($response);
}
mysqli_close($link);
