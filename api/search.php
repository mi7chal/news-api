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
$response->alreadyLogged = false;

$data = json_decode(file_get_contents('php://input'));

if ($link && isset($data->phrase) && isset($data->long)) {
    if($data->phrase==''){
        $response->body=[];
        echo json_encode($response);
        exit();
    }
    $search = $data->phrase;
    $query = "SELECT counter.id, counter.tytul FROM(SELECT id, tytul, status, IF( `tytul` LIKE '%$search%', 12, 0) as tytulCount,
     IF( `tresc` LIKE '%$search%', 2, 0) as trescCount, IF( `kategorie` LIKE '%$search%', 10, 0) as kategorieCount,
      IF( `tagi` LIKE '%$search%', 10, 0) as tagiCount FROM artykuly) counter, wyswietlenia WHERE wyswietlenia.idArtykulu=counter.id AND (counter.tytulCount+counter.trescCount+counter.kategorieCount+counter.tagiCount)!=0";

    //ORDER BY (counter.tytulCount+counter.trescCount+counter.kategorieCount+counter.tagiCount) DESC, wyswietlenia.wyswietlenia DESC";
    if (!(isset($_SESSION['logged']) && ($_SESSION['logged'] == true))) {
        $response->alreadyLogged = true;
        $query .= " AND status=1";
    }
    $query .= " ORDER BY (counter.tytulCount+counter.trescCount+counter.kategorieCount+counter.tagiCount) DESC, wyswietlenia.wyswietlenia DESC";
    if ($data->long != true) {
        $query .= " LIMIT 10";
    }

    $result = mysqli_query($link, $query);
    $return = [];
    while ($row = mysqli_fetch_assoc($result))
        $return[] = $row;
    $response->body=$return;
    echo json_encode($response);
} else {
    echo json_encode("blad");
}







mysqli_close($link);
