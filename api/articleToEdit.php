<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");


session_start();
require_once "connect.php";

$response = (object) null;
$response->status = 1;
$response->info = '';

if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $autorID = $_SESSION['userID'];
        $result = mysqli_query($link, "SELECT * FROM artykuly WHERE id=$id");
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if ($row['autorId'] == $autorID) {
                    $response->body = $row;
                    echo json_encode($response);
                } else {
                    $response->status=0;
                    $response->info="Nie jesteś autorem artykułu";
                    echo json_encode($response);
                }
            } else {
                $response->status = 0;
                $response->info = "Artykuł nie istnieje";
                echo json_encode($response);
            }
        } else {
            $response->status = 0;
            $response->info = "Nieznany błąd";
            echo json_encode($response);
        }
    } else {
        $response->status = 0;
        $response->info = "Błędne zapytanie";
        echo json_encode($response);
    }
} else {
    $response->status = 0;
    $response->info = "Błędne zapytanie";
    echo json_encode($response);
}

mysqli_close($link);
