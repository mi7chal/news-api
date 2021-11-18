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
$response->info = [];
$response->alreadyLogged = false;


if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
    $response->alreadyLogged = true;
    if (isset($_GET['id'])) {
        $idArt = $_GET['id'];
        $check = mysqli_query($link, "SELECT autorId FROM artykuly WHERE id=$idArt");
        $row = mysqli_fetch_assoc($check);
        if ($row['autorId'] == $_SESSION['userID']) {
            $delete = mysqli_query($link, "DELETE from artykuly WHERE id=$idArt");
            if ($delete) {
                $response->info = "Usunięto artykuł";
                array_map('unlink', glob("./articleImages/$idArt/*.*"));
                rmdir("./articleImages/$idArt");
                echo json_encode($response);
                exit();
            } else {
                $response->status = 0;
                $response->info = "Nieznany błąd";
                echo json_encode($response);
                exit();
            }
        } else {
            $response->status = 0;
            $response->info = "Nieznany błąd";
            echo json_encode($response);
            exit();
        }
    } else {
        $response->status = 0;
        $response->info = "Nieznany błąd";
        echo json_encode($response);
        exit();
    }
} else {
    $response->status = 0;
    $response->info = "Nie jesteś zalogowany";
    echo json_encode($response);
    exit();
}


mysqli_close($link);
