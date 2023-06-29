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
?> 