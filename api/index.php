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

if (isset($_SESSION['logged']) && ($_SESSION['logged'] == true)) {
	$resultToday = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie,
	 wyswietleniadzis.wyswietlenia
	 FROM `artykuly`, `wyswietleniadzis` 
	 WHERE wyswietleniadzis.idArtykulu=artykuly.id 
	 ORDER BY wyswietleniadzis.wyswietlenia DESC
	 LIMIT 9");


	$resultAll = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie,
	  wyswietlenia.wyswietlenia
	 FROM `artykuly`, `wyswietlenia` 
	 WHERE wyswietlenia.idArtykulu=artykuly.id 
	 ORDER BY wyswietlenia.wyswietlenia DESC
	 LIMIT 9");

	$resultDate = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie
		FROM `artykuly` 
		ORDER BY artykuly.dataUtworzenia DESC
		LIMIT 9");
} else {
	$resultToday = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie,
	 wyswietleniadzis.wyswietlenia
	 FROM `artykuly`, `wyswietleniadzis` 
	 WHERE wyswietleniadzis.idArtykulu=artykuly.id AND artykuly.status=1
	 ORDER BY wyswietleniadzis.wyswietlenia DESC
	 LIMIT 9");


	$resultAll = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie,
	  wyswietlenia.wyswietlenia
	 FROM `artykuly`, `wyswietlenia` 
	 WHERE wyswietlenia.idArtykulu=artykuly.id AND artykuly.status=1
	 ORDER BY wyswietlenia.wyswietlenia DESC
	 LIMIT 9");

	$resultDate = mysqli_query($link, "SELECT artykuly.id, artykuly.tytul, artykuly.zdjecie
		FROM `artykuly` 
		WHERE artykuly.status=1
		ORDER BY artykuly.dataUtworzenia DESC
		LIMIT 9");
}

$return = [];

while ($row = mysqli_fetch_assoc($resultToday))
	$return[] = $row;

$returnObj->title = "Popularne dziś";
$returnObj->id = 0;
$returnObj->body = $return;
$returnArray[] = $returnObj;
$returnObj = (object)null;

$return = [];
while ($row = mysqli_fetch_assoc($resultAll))
	$return[] = $row;

$returnObj->title = "Najpopularniejsze";
$returnObj->id = 1;
$returnObj->body = $return;
$returnArray[] = $returnObj;
$returnObj = (object)null;

$return = [];
while ($row = mysqli_fetch_assoc($resultDate))
	$return[] = $row;

$returnObj->title = "Najnowsze";
$returnObj->id = 2;
$returnObj->body = $return;
$returnArray[] = $returnObj;
$returnObj = (object)null;




echo json_encode($returnArray);

mysqli_close($link);
