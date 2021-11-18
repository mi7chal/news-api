<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");


session_start();

$data = json_decode(file_get_contents('php://input'));
$response = (object) null;
$response->status = 1;
$response->info = [];
$response->alreadyLogged = false;
if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
	$response->status = 0;
	$response->info[] = "Uzytkownik zalogowany";
	$response->alreadyLogged = true;
	echo json_encode($response);
	exit();
}

require_once "connect.php";



if (
	isset($data->name) && isset($data->surname)
	&& isset($data->email) && isset($data->login)
	&& isset($data->password1) && isset($data->password2)
) {

	$login = $data->login;


	if ((strlen($login) < 6)) {
		$response->status = 0;
		$response->info[] = "Podano za krótki login";
	}

	if (ctype_alnum($login) == false) {
		$response->status = 0;
		$response->info[] = "Login może zawierać tylko litery i cyfry";
	}


	$email = $data->email;
	$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

	if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
		$response->status = 0;
		$response->info[] = "Niepoprawny email";
	}


	$password1 = $data->password1;
	$password2 = $data->password2;

	if ((strlen($password1) < 8)) {
		$response->status = 0;
		$response->info[] = "Hasło jest za krótkie";
	}

	if ($password1 != $password2) {
		$response->status = 0;
		$response->info[] = "Podane hasła nie są identyczne!";
	}

	$password_hash = password_hash($password1, PASSWORD_DEFAULT);

	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);

	try {
		if (mysqli_error($link)) {
			throw new Exception(mysqli_connect_errno());
		} else {

			$result = mysqli_query($link, "SELECT id FROM uzytkownicy WHERE email='$email'");

			if (!$result) throw new Exception(mysqli_connect_errno());

			if (mysqli_num_rows($result) > 0) {
				$response->status = 0;
				$response->info[] = "Podany email jest już zarejestrowany";
			}


			$result = mysqli_query($link, "SELECT id FROM uzytkownicy WHERE login='$login'");

			if (!$result) throw new Exception(mysqli_connect_errno());

			if (mysqli_num_rows($result) > 0) {
				$response->status = 0;
				$response->info[] = "Podany login jest już zajęty";
			}

			if ($response->status == 1) {

				$toSend = (object)null;
				$toSend->name = mysqli_real_escape_string($link, $data->name);
				$toSend->surname = mysqli_real_escape_string($link, $data->surname);
				$toSend->email = mysqli_real_escape_string($link, $data->email);
				$toSend->login = mysqli_real_escape_string($link, $data->login);

				$result = mysqli_query($link, "INSERT INTO uzytkownicy 
				VALUES ('', '$toSend->name', '$toSend->surname','$toSend->login', '$toSend->email', NOW(),'$password_hash', NOW())");
				if ($result) {
					$reqId = mysqli_query($link, "SELECT id FROM uzytkownicy WHERE login = '$toSend->login'");
					$row = mysqli_fetch_assoc($reqId);
					$dbUserId=$row['id'];
					$_SESSION['logged'] = true;
					$_SESSION['userID'] = $dbUserId;
					$response->info="Zarejestrowano pomyślnie!";
					echo json_encode($response);
				} else {
					throw new Exception(mysqli_connect_errno());
				}
			} else {
				echo json_encode($response);
			}

			unset($result);
			mysqli_close($link);
		}
	} catch (Exception $e) {
		$response->status = 0;
		$response->info[] = "Nieznany błąd";
		$response->error = $e;
		echo json_encode($response);
	}
} else {
	$response->status = 0;
	$response->info[] = "Nieznany błąd";
	echo json_encode($response);
}
