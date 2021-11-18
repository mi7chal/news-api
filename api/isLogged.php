<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

session_start();

$response = (object) null;
$response->status=1;
$response->alreadyLogged=false;
if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
    $response->alreadyLogged=true;
    echo json_encode($response);
}
else{
    $response->alreadyLogged=false;
    $response->status=0;
    echo json_encode($response);
}