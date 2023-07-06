<?php
include "db_conn.php";

$stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 1 AND active = 1");
$stmt->execute();
$image = $stmt->fetch()['bild'];
$imageData = base64_encode($image);


$stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 4 AND active = 1");
$stmt->execute();
$image = $stmt->fetch()['bild'];
$imageDataProfil = base64_encode($image);




$stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 7 AND active = 1");
$stmt->execute();
$image = $stmt->fetch()['bild'];
$imageDataGif1 = base64_encode($image);

$stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 8 AND active = 1");
$stmt->execute();
$image = $stmt->fetch()['bild'];
$imageDataGif2 = base64_encode($image);

$stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 9 AND active = 1");
$stmt->execute();
$image = $stmt->fetch()['bild'];
$imageDataGif3 = base64_encode($image);

$stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 10 AND active = 1");
$stmt->execute();
$image = $stmt->fetch()['bild'];
$imageDataGif4 = base64_encode($image);

$stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 11 AND active = 1");
$stmt->execute();
$image = $stmt->fetch()['bild'];
$imageDataGif5 = base64_encode($image);
?> 
