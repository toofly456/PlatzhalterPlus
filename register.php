<?php
session_start();

include "db_conn.php";
include "logo.php";

if (isset($_POST['senden'])) {
	// Daten aus dem Formular verarbeiten
	$vorname = $_POST['vorname'];
	$nachname = $_POST['nachname'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$password_confirm = $_POST['password_confirm'];
	$hash = password_hash($password, PASSWORD_DEFAULT);

	// Validierung der Eingaben
	$fehler = array();
	if (empty($vorname)) {
		$fehler[] = 'Bitte geben Sie Ihren Vornamen ein.';
		header("Location: register.php?error=firstname is required");
		exit();
	}
	if (empty($nachname)) {
		$fehler[] = 'Bitte geben Sie Ihren Nachnamen ein.';
		header("Location: register.php?error=lastname is required");
		exit();
	}
	// Überprüfung, ob die E-Mail-Adresse bereits registriert ist
	$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
	$stmt->execute([$email]);
	$existingEmailCount = $stmt->fetchColumn();

	if ($existingEmailCount > 0) {
		$fehler[] = 'Die E-Mail-Adresse ist bereits registriert.';
		header("Location: register.php?error=Email is already registered");
		exit();
	}
	if (empty($email)) {
		$fehler[] = 'Bitte geben Sie eine E-Mail-Adresse ein.';
		header("Location: register.php?error=Email is required");
		exit();
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$fehler[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
		header("Location: register.php?error=Email is required");
		exit();
	}
	if (empty($password)) {
		$fehler[] = 'Bitte ein Passwort eingeben.';
		header("Location: register.php?error=password is required");
		exit();
	} elseif (strlen($password) < 8) {
		$fehler[] = 'Das Passwort muss mindestens 8 Zeichen enthalten.';
		header("Location: register.php?error=password is too short");
		exit();
	}
	if (empty($password_confirm)) {
		$fehler[] = 'Bitte das Passwort erneut eingeben.';
		header("Location: register.php?error=varification of password is required");
		exit();
	} elseif ($password !== $password_confirm) {
		$fehler[] = 'Die Passwörter stimmen nicht überein.';
		header("Location: register.php?error=password is not identical");
		exit();
	}

	// Daten in die Datenbank einfügen
	if (empty($fehler)) {
		$code = base64_encode(random_bytes(18));
		try {
			$stmt = $pdo->prepare('INSERT INTO users (vorname, nachname, email, code, password, erstellung_datum, active, last_login) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
			$stmt->execute([$vorname, $nachname, $email, $code, $hash, date('Y-m-d H:i:s'), '1', date('Y-m-d H:i:s')]);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int) $e->getCode());
		}
		echo "<script>window.location.href = 'start.php';</script>";
		exit;
	}
}

if (isset($_POST['anmelden'])) {
	echo "<script>window.location.href = 'start.php';</script>";
	exit;
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
	<title>PlatzhalterPlus</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="bootstrap.css">

	<style>
		.cursor {
			display: flex;
			flex-wrap: wrap;
		}

		.cursor>div {
			flex: 120px;
			padding: 10px 2px;
			white-space: nowrap;
			border: 1px solid #666;
			border-radius: 5px;
			margin: 0 5px 5px 0;
		}

		.move {
			cursor: move;
		}

		.grab {
			cursor: -webkit-grab;
			cursor: grab;
		}

		.grabbing {
			cursor: -webkit-grabbing;
			cursor: grabbing;
		}

		.image-container {
			display: flex;
			justify-content: center;
			align-items: center;
			text-align: center;
			cursor: pointer;
		}

		h1 {
			text-align: center;
			margin-bottom: 40px;
		}

		input {
			display: block;
			border: 2px solid #ccc;
			width: 90%;
			padding: 10px;
			margin: 30px auto;
			border-radius: 5px;
			margin-left: auto;
			margin-right: auto;
		}

		.platzhalter-color {
			color: grey;
		}

		.highlight {
			border: none;
			box-shadow: 0 0 10px 5px rgb(55, 171, 82);
			background: linear-gradient(45deg, rgb(255, 255, 255), rgb(255, 255, 255));
		}

		table {
			width: 100%;
		}

		#navbar {
			background-color: #333;
			z-index: 1;
			margin: 0 auto;
			position: fixed;
			top: 0;
			left: 0;
			float: left;
			overflow: hidden;
			height: auto;
			width: 100%;
		}

		#navbar a {
			float: left;
			color: #f2f2f2;
			text-align: center;
			padding: 14px 16px;
			text-decoration: none;
			font-size: 16px;
		}

		#navbar a:hover {
			background-color: #ffffff;
			color: black;
		}

		#navbar .image-container {
			float: left;
			padding: 8px 8px;
		}

		#navbar #logo {
			vertical-align: middle;
		}

		#navbar .logout {
			float: right;
		}

		.content {
			padding-top: 60px;
			padding-left: 16px;
			padding-right: 16px;
		}

		body {
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
			width: 100%;
			flex-direction: column;
			background: linear-gradient(to bottom, #d1d1d1, #1a1a1a);
		}

		.table-container {
			width: 100%;
			margin: 0 auto;
			display: flex;
			justify-content: space-between;
		}

		.table1,
		.table2 {
			display: inline-block;
			width: 48%;
			border-collapse: collapse;
		}

		* {
			font-family: sans-serif;
			box-sizing: border-box;
			margin-left: auto;
			margin-right: auto;
			width: auto;
		}

		h1.a {
			text-align: left;
			margin-bottom: 40px;
		}

		form {
			border: 2px solid #ccc;
			background: #fff;
			border-radius: 15px;
			width: 90% auto;
		}

		form.a {
			justify-content: left;
			align-items: left;
			float: left;
			border: 2px solid #ccc;
			margin-left: 10px;
			padding-left: 20px;
			background: #fff;
			border-radius: 15px;
			width: 90%;
		}

		td {
			background-image: linear-gradient(#e3e3e3b2, #ababab76);
			box-shadow: none;
			border-left: #666;
			border-right: #666;
			border-top: #666;
			border-bottom: #666;
			border-radius: 20px;
			box-sizing: border-box;
			padding-left: 20px;
			padding-right: 20px;
			margin-left: auto;
			margin-right: auto;
			width: 100% auto;
		}

		#div1 {
			float: left;
			width: 100px;
			height: 35px;
			margin: 10px;
			padding: 10px;
			border: 1px solid black;
		}

		.error {
			background: #F2DEDE;
			color: #A94442;
			padding: 10px;
			width: 95%;
			border-radius: 5px;
			margin: 20px auto;
		}

		.button-17 {
			align-items: center;
			appearance: none;
			background-color: #fcfcfc;
			border-radius: 24px;
			border-style: none;
			box-shadow: rgba(0, 0, 0, .2) 0 3px 5px -1px, rgba(0, 0, 0, .14) 0 6px 10px 0, rgba(0, 0, 0, .12) 0 1px 18px 0;
			box-sizing: border-box;
			color: #3c4043;
			cursor: pointer;
			display: inline-flex;
			fill: currentcolor;
			font-family: "Google Sans", Roboto, Arial, sans-serif;
			font-size: 14px;
			font-weight: 500;
			height: 48px;
			justify-content: center;
			letter-spacing: .25px;
			line-height: normal;
			max-width: 100%;
			overflow: visible;
			padding: 2px 24px;
			position: relative;
			text-align: center;
			text-transform: none;
			transition: box-shadow 280ms cubic-bezier(.4, 0, .2, 1), opacity 15ms linear 30ms, transform 270ms cubic-bezier(0, 0, .2, 1) 0ms;
			user-select: none;
			-webkit-user-select: none;
			touch-action: manipulation;
			width: auto;
			will-change: transform, opacity;
			z-index: 0;
		}

		.button-17:active {
			box-shadow: 0 4px 4px 0 rgb(60 64 67 / 30%), 0 8px 12px 6px rgb(60 64 67 / 15%);
			outline: none;
		}

		.button-17:focus {
			outline: none;
			border: 2px solid #4285f4;
		}

		.button-17:not(:disabled) {
			box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
		}

		.button-17:not(:disabled):hover {
			box-shadow: rgba(60, 64, 67, .3) 0 2px 3px 0, rgba(60, 64, 67, .15) 0 6px 10px 4px;
		}

		.button-17:not(:disabled):focus {
			box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
		}

		.button-17:not(:disabled):active {
			box-shadow: rgba(60, 64, 67, .3) 0 4px 4px 0, rgba(60, 64, 67, .15) 0 8px 12px 6px;
		}

		.button-17:disabled {
			box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
		}

		form {
			border: 2px solid #ccc;
			background: #fff;
			border-radius: 15px;
			width: 90%;
		}

		footer * {
			margin: 0 0 1em;
			justify-content: center;
			align-items: center;
			text-align: center;
		}

		footer {
			text-align: center;
			border-radius: 0px 0.5em 0.5em;
			padding: 30px;
			width: 100%;
			justify-content: center;
			align-items: center;
			text-align: center;
		}

		.error {
			background: #F2DEDE;
			color: #A94442;
			padding: 10px;
			width: 95%;
			border-radius: 5px;
			margin: 20px auto;
		}
	</style>
	<?php
	if (isset($_GET['error'])) {
	?>
		<p class="error">
			<?php
			echo $_GET['error'];
			?>
		</p>
	<?php
	}
	?>
</head>

<body>
	</br>
	<form method="POST">
		<a href="index.php">
			<div class="image-container">
				<img id="logo" height="60" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="PLatzhalterPlus">
			</div>
		</a>
		<header>
			</br>
			<h1>Registrierung</h1>
		</header>


		<label for="inputVorname">&nbsp Vorname:</label>
		<input type="text" id="inputVorname" size="30" name="vorname" placeholder="Vorname" required>

		<label for="inputNachname">&nbsp Nachname:</label>
		<input type="text" id="inputNachname" size="30" name="nachname" placeholder="Nachname" required>

		<label for="inputEmail">&nbsp E-Mail:</label>
		<input type="email" id="inputEmail" size="30" name="email" placeholder="E-Mail" required>

		<label for="inputPasswort">&nbsp Passwort:</label></br>
		<small>&nbsp min. 8 Zeichen</small>
		<input type="password" id="inputPasswort" size="30" name="password" placeholder="Passwort" required>

		<label for="inputPasswort2">&nbsp Passwort wiederholen:</label>
		<input type="password" id="inputPasswort2" size="30" name="password_confirm" placeholder="Passwort wiederholden" required>

		<div style="width: 100%;
    justify-content: center;
    align-items: center;
    text-align: center;">
			<label for="terms">Ich akzeptiere die <a href="terms.html">Nutzungsvereinbarung</a></label>
			<input type="checkbox" id="terms" name="terms" value="1" required>
		</div>
		</br>

		<div style="text-align: center;">
			<button type="submit" class="button-17" role="button" name="senden" value="Speichern">Registrieren</button>
			</br>
			</br>
		</div>
	</form>
	<div>
		</br>
	</div>
	<form action="start.php" method="POST" style="background-color: rgba(255, 255, 255, 0.0);
	border:rgba(255, 255, 255, 0.0); text-align: center;">
		</br> <button type="submit" class="button-17" role="button" name="anmelden" value="Login" formnovalidate>Anmelden</button>
	</form>
	</br>

	<?php
	include "footer.html";
	?>
</body>

</html>
