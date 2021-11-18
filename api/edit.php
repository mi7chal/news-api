<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");


session_start();

require_once('connect.php');

$response = (object) null;
$response->status = 1;
$response->info = '';
$response->alreadyLogged = false;

if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
    $response->alreadyLogged = true;
    if (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['category']) && isset($_POST['tags']) && isset($_POST['status'])&& isset($_POST['id'])) {
        try {
            $id = $_POST['id'];
            $title = mysqli_real_escape_string($link, $_POST['title']);
            $content = mysqli_real_escape_string($link, $_POST['content']);
            $category = mysqli_real_escape_string($link, $_POST['category']);
            $tags = mysqli_real_escape_string($link, $_POST['tags']);
            $status = mysqli_real_escape_string($link, $_POST['status']);
            $autorID = mysqli_real_escape_string($link, $_SESSION['userID']);
            if (!isset($_FILES['image'])) {
                $result = mysqli_query($link, "UPDATE artykuly SET tytul='$title', tresc='$content', kategorie =  $category, tagi = '$tags', status =  $status WHERE id = $id");
                if ($result) {
                    $response->status = 1;
                    $response->info = "Dodano pomyślnie";
                    echo json_encode($response);
                } else {
                    $response->status = 0;
                    $response->info = "Nieznany błąd";
                    echo json_encode($response);
                }
            } else {
                $imageName = $_FILES['image']['name'];
                array_map('unlink', glob("./articleImages/$id/*.*"));
                $result = mysqli_query($link, "UPDATE artykuly SET tytul='$title', tresc='$content', kategorie=$category, tagi ='$tags', status =  $status, zdjecie ='$imageName' WHERE id =$id");
                move_uploaded_file($_FILES['image']['tmp_name'], "./articleImages/$id/$imageName");
                if ($result) {
                    $response->status = 1;
                    $response->info = "Edytowano pomyślnie";
                    echo json_encode($response);
                } else {
                    $response->status = 0;
                    $response->info = "Nieznany błąd";
                    echo json_encode($response);
                }
            }
            // $id = mysqli_insert_id($link);
            // mkdir("./articleImages/$id");
            // move_uploaded_file($_FILES['image']['tmp_name'], "./articleImages/$id/$imageName");
        } catch (Exception $e) {
        }
    } else {
        $response->status = 0;
        $response->info = "Nieznany błąd";
        echo json_encode($response);
    }
} else {
    $response->status = 0;
    $response->info = "Nie jesteś zalogowany";
    echo json_encode($response);
}

mysqli_close($link);
