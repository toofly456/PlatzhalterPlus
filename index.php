<!DOCTYPE html>
<html lang="de">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="bootstrap.css">


	<title>PlatzhalterPlus</title>
	<style>
		body {
			background: linear-gradient(to bottom, #d1d1d1, #1a1a1a);
			text-align: center;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
		}

		* {
			height: auto;
		}

		#slideshow {
			position: relative;
		}

		.slide {
			display: none;
		}

		.slide img {
			width: 100%;
			height: auto;
		}

		.description {
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			background-color: rgba(0, 0, 0, 0.5);
			color: white;
			padding: 10px;
		}

		.dot {
  height: 10px;
  width: 10px;
  background-color: white;
  border-radius: 50%;
  display: inline-block;
  margin: 0 5px;
  transition: background-color 0.3s ease;
}

.active {
  background-color: grey;
}

	</style>
</head>
<?php
	include "logo.php";

?>
<body>
<a href="index.php">
			<div class="image-container">
				<img id="logo" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="PLatzhalterPlus" style="width: 40%;">
			</div>
		</a>
	<p>
	</br>
	</br>
	</br>
		Wie wird <b>PlatzhalterPlus</b> verwendet?
	</p>
	<div style="text-align: center; margin-top: 10px;">
  <span class="dot" onclick="currentSlide(1)"></span>
  <span class="dot" onclick="currentSlide(2)"></span>
  <span class="dot" onclick="currentSlide(3)"></span>
  <span class="dot" onclick="currentSlide(4)"></span>
  <span class="dot" onclick="currentSlide(5)"></span>
</div>
	<form style="margin-left: 20px; margin-right: 20px; width: auto;">
	</br>
	<div id="slideshow">
		<div class="slide">
			<img src="data:image/jpeg;base64,<?php echo $imageDataGif1; ?>" alt="Bild 1" style="height: auto;">
			<div class="description">1. Platzhalter hinzufügen</div>
		</div>
		<div class="slide">
			<img src="data:image/jpeg;base64,<?php echo $imageDataGif2; ?>" alt="Bild 2" style="height: auto;">
			<div class="description">2. Platzhalter per Drag & Drop in die Textvorlage ziehen und speichern</div>
		</div>
		<div class="slide">
			<img src="data:image/jpeg;base64,<?php echo $imageDataGif3; ?>" alt="Bild 3" style="height: auto;">
			<div class="description">3. Textvorlage auswählen und Eingabefelder ausfüllen</div>
		</div>
		<div class="slide">
			<img src="data:image/jpeg;base64,<?php echo $imageDataGif4; ?>" alt="Bild 4" style="height: auto;">
			<div class="description">4. Der Inhalt der Eingabefelder wird für die Platzhalter übernommen</div>
		</div>
		<div class="slide">
			<img src="data:image/jpeg;base64,<?php echo $imageDataGif5; ?>" alt="Bild 5" style="height: auto;">
			<div class="description">Alle Einträge werden automatisch dokumentiert</div>
		</div>
	</div>
	</br>
	</form>


	<script>
  var slideIndex = 0;
  showSlides(slideIndex);

  function plusSlides(n) {
    showSlides((slideIndex += n));
  }

  function currentSlide(n) {
    showSlides((slideIndex = n));
  }

  function showSlides(n) {
    var i;
    var slides = document.getElementsByClassName("slide");
    var dots = document.getElementsByClassName("dot");
    if (n > slides.length) {
      slideIndex = 1;
    }
    if (n < 1) {
      slideIndex = slides.length;
    }
    for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
    }
    for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex - 1].style.display = "block";
    dots[slideIndex - 1].className += " active";
  }
</script>



	
	<p>
	</br>
	</br>
		<b>PlatzhalterPlus</b> soll als Innovation den Kunden-Support erleichtern</br>
		und setzt sich aus 5 Besonderheiten zusammen:
	</p>
	<div>
		</br>
		</br>
	</div>
	<details>
		<summary style="font-size: 24px; color: snow;">Automatisierung 🔧</summary>
		<p>Durch das Tool <b>PlatzhalterPlus</b> können Platzhalter in Texten mit individuellen Eingaben ersetzt
			werden.</br>
			Das spart Zeit und reduziert Fehler bei der manuellen Eingabe von Standardtexten.</br>
			Standard Kundenanfragen können viel schneller beantwortet werden.</br>
			Deine Standardtexte sind vorbereitet? Dann kannst du bereits loslegen!</p>
	</details>
	<div>
		</br>
		</br>
	</div>
	<details>
		<summary style="font-size: 24px; color: snow;">Priorisierung 🎯</summary>
		<p>Vermeide unnötige Zeitverschwendung mit immer wiederkehrenden Aufgaben.</br>
			Fokussiere dich auf die Aufgaben, die mehr Zeit in Anspruch nehmen.</br>
			<b>PlatzhalterPlus</b> hilft dir und verschafft Abhilfe.
		</p>
	</details>
	<div>
		</br>
		</br>
	</div>
	<details>
		<summary style="font-size: 24px; color: snow;">Teamwork 🤝</summary>
		<p>Mehrere Mitarbeiter können gleichzeitig das Tool benutzen und als 'Content-Creator' neue Einträge anlegen.
		</p>
	</details>
	<div>
		</br>
		</br>
	</div>
	<details>
		<summary style="font-size: 24px; color: snow;">Wissensdatenbank 💾</summary>
		<p>Alle Vorlagen und Platzhalter können immer hinzugefügt, entsprechend angepasst und oder gelöscht werden.</br>
			Alle umgeschriebenen Texte werden dokumentiert und im Verlauf gespeichert.</br>
			<b>PlatzhalterPlus</b> kann so auch als Wissensdatenbank für neue Mitarbeiter genutzt werden.</br>
		</p>
	</details>
	<div>
		</br>
		</br>
	</div>
	<details>
		<summary style="font-size: 24px; color: snow;">IT-Sicherheit 🛡️</summary>
		<p>Alle Angaben sind geschützt und können nur vom Benutzer selbst aufgerufen werden.</br>
			<b>PlatzhalterPlus</b> speichert keine eingegebenen personenbezogenen Passwörter in Textvorlagen ab!</br>
		</p>
	</details>
	<div>
		</br>
		</br>
	</div>


	<div>
		<p style="color: snow;">
			Die Nutzung von <b>PlatzhalterPlus</b> ist völlig kostenlos!</br>
		</p>
	</div>

	<form method="POST" action="start.php"
		style="background-color: rgba(255, 255, 255, 0.0); border:rgba(255, 255, 255, 0.0); justify-content: center; align-items: center; text-align: center;">
		<button type="submit" class="button-17" role="button" name="start" value="Start">Jetzt loslegen!</button>
	</form>
	</br>
	</br>
	<footer>
		</br>
		<small>
			<p style="color: snow;">©2023 PlatzhalterPlus</p></br>
		</small>
	</footer>
</body>

</html>
