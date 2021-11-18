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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query(
        $link,
        "SELECT artykuly.id, artykuly.tytul, artykuly.tresc, artykuly.zdjecie, artykuly.tagi, 
          artykuly.dataUtworzenia, artykuly.status, kategorie.kategoria, CONCAT(uzytkownicy.imie,' ', uzytkownicy.nazwisko) as autor 
          FROM artykuly, kategorie, uzytkownicy WHERE artykuly.id=$id 
          AND kategorie.id=artykuly.kategorie AND uzytkownicy.id=artykuly.autorId"
    );
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
                $row = mysqli_fetch_assoc($result);
                $response->data = $row;
                $response->alreadyLogged = true;
                echo json_encode($response);
                $incrementViews = mysqli_query($link,"UPDATE wyswietlenia SET wyswietlenia=(wyswietlenia+1) WHERE idArtykulu=$id");
                $incrementTodayViews = mysqli_query($link,"UPDATE wyswietleniadzis SET wyswietlenia=(wyswietlenia+1) WHERE idArtykulu=$id");
            } else {
                $row = mysqli_fetch_assoc($result);
                if ($row['status'] == 1) {
                    $response->data = $row;
                    echo json_encode($response);
                    $incrementViews = mysqli_query($link,"UPDATE wyswietlenia SET wyswietlenia=(wyswietlenia+1) WHERE idArtykulu=$id");
                    $incrementTodayViews = mysqli_query($link,"UPDATE wyswietleniadzis SET wyswietlenia=(wyswietlenia+1) WHERE idArtykulu=$id");


                } else {
                    $response->status = 0;
                    $response->info[] = "Zaloguj się, aby zobaczyć ten artykuł";
                    echo json_encode($response);
                }
            }
        } else {
            $response->status = 0;
            $response->info[] = "Artykuł nie istnieje";
            echo json_encode($response);
        }
    } else {
        $response->status = 0;
        $response->info[] = "Nieznany błąd";
        echo json_encode($response);
    }
} else {
    $response->status = 0;
    $response->info[] = "Błędne zapytanie";
    echo json_encode($response);
}



mysqli_close($link);
