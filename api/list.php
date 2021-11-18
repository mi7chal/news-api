<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");


session_start();
require_once "connect.php";
require_once "checkViews.php";

$response = (object) null;
$response->status = 1;
$response->info = [];
$response->alreadyLogged = false;

if (isset($_GET['offset']) && isset($_GET['order'])) {

	$offset = $_GET['offset'];
	$offset = $offset * 10;
	$order = $_GET['order'];
	$result = null;
	if (isset($_GET['category'])) {
		$category = $_GET['category'];
		$categoryIdRes = mysqli_query($link, "SELECT id from kategorie WHERE kategoria='$category'");
		if ($categoryIdRes) {
			if (mysqli_num_rows($categoryIdRes) == 1) {
				$row = mysqli_fetch_assoc($categoryIdRes);
				$categoryId = $row['id'];
			} else {
				$response->status = 0;
				$response->info[] = "Niepoprawna kategoria";
				echo json_encode($response);
				exit();
			}
		} else {
			$response->status = 0;
			$response->info[] = "Nieznany błąd1";
			echo json_encode($response);
			exit();
		}

		if ($order == "yes") {
			if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
				$result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie, artykuly.tresc, wyswietlenia.idArtykulu, wyswietlenia.wyswietlenia 
			FROM `artykuly`, `wyswietlenia` 
			WHERE wyswietlenia.idArtykulu=artykuly.id AND artykuly.kategorie=$categoryId 
			ORDER BY wyswietlenia.wyswietlenia DESC LIMIT 10 OFFSET $offset");
			} else {
				$result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie, artykuly.tresc, wyswietlenia.idArtykulu, wyswietlenia.wyswietlenia 
				FROM `artykuly`, `wyswietlenia` 
				WHERE wyswietlenia.idArtykulu=artykuly.id AND artykuly.kategorie=$categoryId AND artykuly.status=1
				ORDER BY wyswietlenia.wyswietlenia DESC LIMIT 10 OFFSET $offset");
			}
		} else {
			if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
				$result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie, artykuly.tresc, wyswietlenia.idArtykulu, wyswietlenia.wyswietlenia 
			FROM `artykuly`, `wyswietlenia` 
			WHERE wyswietlenia.idArtykulu=artykuly.id AND artykuly.kategorie=$categoryId 
			ORDER BY artykuly.dataUtworzenia DESC LIMIT 10 OFFSET $offset");
			} else {
				$result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie, artykuly.tresc, wyswietlenia.idArtykulu, wyswietlenia.wyswietlenia 
				FROM `artykuly`, `wyswietlenia` 
				WHERE wyswietlenia.idArtykulu=artykuly.id AND artykuly.kategorie=$categoryId AND artykuly.status=1
				ORDER BY artykuly.dataUtworzenia DESC LIMIT 10 OFFSET $offset");
			}
		}
	} else {
		if ($order == "yes") {
			if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
				$result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie, artykuly.tresc, wyswietlenia.idArtykulu, wyswietlenia.wyswietlenia 
			FROM `artykuly`, `wyswietlenia` 
			WHERE wyswietlenia.idArtykulu=artykuly.id  
			ORDER BY wyswietlenia.wyswietlenia DESC LIMIT 10 OFFSET $offset");
			} else {
				$result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie, artykuly.tresc, wyswietlenia.idArtykulu, wyswietlenia.wyswietlenia 
				FROM `artykuly`, `wyswietlenia` 
				WHERE wyswietlenia.idArtykulu=artykuly.id  AND artykuly.status=1
				ORDER BY wyswietlenia.wyswietlenia DESC LIMIT 10 OFFSET $offset");
			}
		} else {
			if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
				$result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie, artykuly.tresc, wyswietlenia.idArtykulu, wyswietlenia.wyswietlenia 
			FROM `artykuly`, `wyswietlenia` 
			WHERE wyswietlenia.idArtykulu=artykuly.id 
			ORDER BY artykuly.dataUtworzenia DESC LIMIT 10 OFFSET $offset");
			} else {
				$result = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie, artykuly.tresc, wyswietlenia.idArtykulu, wyswietlenia.wyswietlenia 
				FROM `artykuly`, `wyswietlenia` 
				WHERE wyswietlenia.idArtykulu=artykuly.id  AND artykuly.status=1
				ORDER BY artykuly.dataUtworzenia DESC LIMIT 10 OFFSET $offset");
			}
		}
	}
	if ($result != null) {
		$return=[];
		while ($row = mysqli_fetch_assoc($result))
			$return[] = $row;

		if ($return)
			$response->data = $return;
		else
			$response->data = [];

		echo json_encode($response);
		exit();
	} else {
		$response->status = 0;
		$response->info[] = "Nieznany błąd2";
		$response->data = [];
		echo json_encode($response);
		exit();
	}
}

mysqli_close($link);
