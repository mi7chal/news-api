<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

session_start();

$data = json_decode(file_get_contents('php://input'));
$response = (object) null;
$response->status=1;
$response->alreadyLogged=false;
if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
    $response->status=0;
    $response->info="Użytkownik zalogowany";
    $response->alreadyLogged=true;
    echo json_encode($response);
    exit();
}

require_once "connect.php";


// if ($link && isset($_POST['login']) && isset($_POST['password'])) {
//     $login = $_POST['login'];
//     $password = $_POST['password'];

if ($link && isset($data->login) && isset($data->password)) {
    $login = $data->login;
    $password = $data->password;

    $login = htmlentities($login, ENT_QUOTES, "UTF-8");


    $result = mysqli_query(
        $link,
        sprintf(
            "SELECT login, email, haslo, id FROM uzytkownicy WHERE login='%s' OR email='%s'",
            mysqli_real_escape_string($link, $login),
            mysqli_real_escape_string($link, $login)
        )
    );

    if ($result && mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['haslo'])) {
            $_SESSION['logged'] = true;
            $_SESSION['userID']=$row['id'];
            $updateDate = mysqli_query($link, "UPDATE uzytkowncy SET (dataOstatniegoLogowania = now()");
            $response->info="Zalogowano pomyślnie!";
            echo json_encode($response);
        }
    } else {
        $response->status=0;
        $response->info = "Podano złe dane";
        echo json_encode($response);
    }
} else {
    $response->status=0;
    $response->error = "Błąd";
    echo json_encode($response);
}
unset($result);
mysqli_close($link);
