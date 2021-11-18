<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");


session_start();

require_once "connect.php";
require_once "checkViews.php";

$returnArray = array();
$returnObj = (object)null;

$response = (object) null;
$response->status = 1;
$response->alreadyLogged = false;

if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true && isset($_SESSION['userID']))) {
    $response->alreadyLogged = true;
    if ($link) {
        $autorID = $_SESSION['userID'];
        $result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.dataUtworzenia, artykuly.status, artykuly.tagi, kategorie.kategoria, wyswietlenia.wyswietlenia, wyswietleniadzis.wyswietlenia as wyswietleniaDzis 
            FROM artykuly, kategorie, wyswietlenia, wyswietleniadzis WHERE artykuly.autorID=$autorID AND kategorie.id=artykuly.kategorie AND wyswietlenia.idArtykulu=artykuly.id AND wyswietleniadzis.idArtykulu = artykuly.id ORDER BY artykuly.dataUtworzenia DESC");
        if ($result) {
            $row = [];
            while ($row = mysqli_fetch_assoc($result))
                $return[] = $row;
            $response->body=$return;
            echo json_encode($response);
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
