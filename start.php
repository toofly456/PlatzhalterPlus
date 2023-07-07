<!DOCTYPE html>
<html lang="en">

<head>
	<title>PlatzhalterPlus</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="bootstrap.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
	<style>
		* {
			height: auto;
		}

		form {
			margin-top: 20px;
			border: 2px solid #ccc;
			background: #fff;
			border-radius: 15px;
			width: 80%;
			height: 100%;
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
</head>

<body>
	<script>
		if (typeof $_POST !== 'undefined') {
			if ($_POST.hasOwnProperty('anmelden')) {
				window.location.href = 'login.php';
				return;
			}
			if ($_POST.hasOwnProperty('neu')) {
				window.location.href = 'register.php';
				return;
			}
		}
	</script>

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

	<?php
	include "logo.php";
	?>
	<form action="login.php" method="POST">
		<a href="index.php">
			<div class="image-container">
				<img id="logo" height="60" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="PLatzhalterPlus" style="height: 60px;">
			</div>
		</a>
		</br>
		<header>
			<h1>LOGIN</h1>
			</br>
			</br>
			</br>
		</header>

		<label for="Email: ">&nbsp Email: </label>
		<input type="text" name="email" placeholder="E-Mail"><br>

		<label for="Passwort: ">&nbsp Passwort: </label>
		<input type="password" name="password" placeholder="Passwort"><br>

		<div style="text-align: center;	justify-content: center;
	align-items: center;
	text-align: center;">
			<button type="submit" class="button-17" role="button" name="anmelden">Anmelden</button></br></br>
			<a href="terms.html">Nutzungsvereinbarung</a>
		</div>
		</br>
	</form>

	<div style="text-align: center;	justify-content: center;
	align-items: center;
	text-align: center;">
		</br>
		</br>
		<form action="register.php" method="POST" style="background-color: rgba(255, 255, 255, 0.0);
	border:rgba(255, 255, 255, 0.0);">
			<button type="submit" class="button-17" role="button" name="neu" value="Neues Benutzerkonto" formnovalidate>Neues Benutzerkonto</button>
		</form>
		</br>
	</div>


	<?php
	include "footer.html";
	?>
</body>

</html>
