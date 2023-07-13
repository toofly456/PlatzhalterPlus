<!DOCTYPE html>
<html lang="de">

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
    input,
    textarea {
      caret-color: red;
      border-width: 2px;
    }
  </style>
</head>


<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "db_conn.php";
include "navbar.php";
include "logout.php";

if (!isset($_SESSION['id'])) {
  echo "<script>window.location.href = 'start.php';</script>";
  exit();
}

$userId = $_SESSION['id'];

$nohomenutzer = NoHome($pdo, $user_id);

// Home für alle zugänglich machen, außer für Nutzer mit No-Home Rolle
if ($nohomenutzer <= 0) {

  if (isset($_GET['einnehmen'])) {
    $benutzer = $_GET['einnehmen'];

    function getUSERvorgesetzter($pdo, $userId, $benutzer)
    {
      $stmt = $pdo->prepare("SELECT uid FROM orga o WHERE o.vorgesetzter = ? AND o.uid = ?");
      $stmt->execute([$userId, $benutzer]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $NutzerInOrga = getUSERvorgesetzter($pdo, $userId, $benutzer);
    $NutzerInOrga2 = array();

    foreach ($NutzerInOrga as $row) {
      $NutzerInOrga2[] = $row['uid'];
    }


    foreach ($NutzerInOrga as $row) {
      $NutzerInOrga2 = $row['uid'];
    }


    $stmt = $pdo->prepare('SELECT COUNT(*) FROM orga o WHERE o.vorgesetzter = ? AND o.uid = ?');
    $stmt->execute([$userId, $benutzer]);
    $anzahl1 = $stmt->fetchColumn();

    if ($anzahl1 > 0) {
      $benutzername = usr($pdo, $benutzer);
      $email = getEmail($pdo, $benutzer);
      echo "<header>
        </br>
        </br>
        </br>
        </br><h3>Du bist jetzt in der Benutzeransicht von " . $benutzername . "</h3></header>";



      if (isset($_POST['senden'])) {
        $titel = trim($_POST['titel']);
        $befehl = trim($_POST['befehl']);

        // Check if the text already exists
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM sql_code WHERE user_id = ? AND befehl = ? AND visible != 1');
        $stmt->execute([$benutzer, $befehl]);
        $result = $stmt->fetchColumn();
        if ($result > 0) {
          $_SESSION['error'] = 'Dieser Text existiert bereits.';
          echo "<script>window.location.href = 'home.php?error=Dieser Text existiert bereits';</script>";
          exit();
        } else if (empty($titel) || empty($befehl)) {
          $_SESSION['error'] = 'Titel und Text sind erforderlich.';
          echo "<script>window.location.href = 'home.php?error=Titel und Text sind erforderlich';</script>";
          exit();
        } else {
          $null = 0;
          try {
            $stmt = $pdo->prepare('INSERT INTO sql_code (user_name, user_id, titel, befehl, visible, datum) VALUES (?, ?, ?, ?, ?, ?)');
            $currentDateTime = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit
            $modifiedDateTime = date('Y-m-d H:i:s', strtotime($currentDateTime . ' +2 hours')); // Datum um 2 Stunden nach hinten versetzt

            $stmt->execute([$email, $benutzer, $titel, $befehl, $null, $modifiedDateTime]);
            echo '<h3>Die neue Textvorlage wurde erfolgreich gespeichert.</h3>';
            // echo "<script>window.location.href = 'menu.php';</script>";
            // exit();
          } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
          }
        }
      }


      if (isset($_POST['platz'])) {
        $neu = strtolower(trim($_POST['neu']));

          // Check if the placeholder contains only one word
  if (strpos($neu, ' ') !== false) {
    $_SESSION['error'] = 'Ein Wort pro Platzhalter.';
    echo "<script>window.location.href = 'home.php?error=Ein Wort pro Platzhalter';</script>";
    exit();
  } else { 

        // Check if the Platzhalter already exists
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM platzhalter WHERE user_id = ? AND name LIKE ?');
        $stmt->execute([$benutzer, $neu]);
        $result = $stmt->fetchColumn();
        if ($result <= 0) {
          try {
            $stmt = $pdo->prepare('INSERT INTO platzhalter (user_id, user, name, platzhalterID) VALUES (?, ?, ?, ?)');
            $stmt->execute([$benutzer, $email, $neu, '[' . $neu . ']']);
            // echo "<script>window.location.href = 'home.php';</script>";
            // exit();
          } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
          }
        } else {
          $_SESSION['error'] = 'Platzhalter existiert bereits.';
          echo "<script>window.location.href = 'home.php?error=Platzhalter existiert bereits';</script>";
          exit();
        }
      }
    }
    }
  } else {

    if (isset($_POST['senden'])) {
      $titel = trim($_POST['titel']);
      $befehl = trim($_POST['befehl']);

      // Check if the text already exists
      $stmt = $pdo->prepare('SELECT COUNT(*) FROM sql_code WHERE user_id = ? AND befehl = ? AND visible != 1');
      $stmt->execute([$_SESSION['id'], $befehl]);
      $result = $stmt->fetchColumn();
      if ($result > 0) {
        $_SESSION['error'] = 'Dieser Text existiert bereits.';
        echo "<script>window.location.href = 'home.php?error=Dieser Text existiert bereits';</script>";
        exit();
      } else if (empty($titel) || empty($befehl)) {
        $_SESSION['error'] = 'Titel und Text sind erforderlich.';
        echo "<script>window.location.href = 'home.php?error=Titel und Text sind erforderlich';</script>";
        exit();
      } else {
        $null = 0;
        try {
          $stmt = $pdo->prepare('INSERT INTO sql_code (user_name, user_id, titel, befehl, visible, datum) VALUES (?, ?, ?, ?, ?, ?)');
          $currentDateTime = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit
          $modifiedDateTime = date('Y-m-d H:i:s', strtotime($currentDateTime . ' +2 hours')); // Datum um 2 Stunden nach hinten versetzt

          $stmt->execute([$_SESSION['email'], $_SESSION['id'], $titel, $befehl, $null, $modifiedDateTime]);
          echo "<script>window.location.href = 'menu.php';</script>";
          exit();
        } catch (\PDOException $e) {
          throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
      }
    }

    if (isset($_POST['platz'])) {
      $neu = strtolower(trim($_POST['neu']));
      // Check if the placeholder contains only one word
if (strpos($neu, ' ') !== false) {
$_SESSION['error'] = 'Ein Wort pro Platzhalter.';
echo "<script>window.location.href = 'home.php?error=Ein Wort pro Platzhalter';</script>";
exit();
} else { 
      // Check if the Platzhalter already exists
      $stmt = $pdo->prepare('SELECT COUNT(*) FROM platzhalter WHERE user_id = ? AND name LIKE ?');
      $stmt->execute([$_SESSION['id'], $neu]);
      $result = $stmt->fetchColumn();
      if ($result <= 0) {
        try {
          $stmt = $pdo->prepare('INSERT INTO platzhalter (user_id, user, name, platzhalterID) VALUES (?, ?, ?, ?)');
          $stmt->execute([$_SESSION['id'], $_SESSION['email'], $neu, '[' . $neu . ']']);
          // echo "<script>window.location.href = 'home.php';</script>";
          // exit();
        } catch (\PDOException $e) {
          throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
      } else {
        $_SESSION['error'] = 'Platzhalter existiert bereits.';
        echo "<script>window.location.href = 'home.php?error=Platzhalter existiert bereits';</script>";
        exit();
      }
    }
    }
  }
?>



  <body>


    <header>
      </br>
      </br>
      </br>
      </br>
      <?php
      //$NutzerInOrga2 = alle Nutzer anzeigen, wo ich der Vorgesetzte bin.
      if (isset($_GET['einnehmen']) && $anzahl1 < 1) {
        echo "<header>
          </br>
          </br>
          </br>
          </br><h3 style='color: red';>Warnung: Dieser Nutzer ist dir nicht zugeordnet!</h3></header>";
      } else {
        $vorname = $pdo->prepare('SELECT vorname FROM users WHERE id = ?');
        $vorname->execute([$_SESSION['id']]);
        $vorname = $vorname->fetchColumn();
        $hour = date('H');
        if ($hour >= 4 && $hour < 12) {
          echo "<h1> Guten Morgen $vorname</br><p>Herzlich Willkommen!</p> </h1>";
        } elseif ($hour >= 12 && $hour < 18) {
          echo "<h1> Guten Tag $vorname</br><p>Herzlich Willkommen!</p> </h1>";
        } else {
          echo "<h1> Guten Abend $vorname</br><p>Herzlich Willkommen!</p> </h1>";
        }
      }
      ?>
    </header>
    </br>
    <?php if (isset($_GET['error'])) { ?>
      <p class="error">
        <?php echo $_GET['error']; ?>
      </p>
    <?php
    }
    ?>
    <form method="POST">
      <table style="width:95%;">
        <td>
          </br>
          <label>Titel*:</label>
          <input id="text" name="titel" value="" minlength="1" maxlength="70" required></input>

          <label>Neue Textvorlage*: </label>
          </br>
          <?php
          if (isset($_GET['einnehmen']) && $anzahl1 < 1) {
            $benutzer = $_GET['einnehmen'];
            $id = $benutzer;
          } else {
            $id = $_SESSION['id'];
          }
          $anzahl = "SELECT COUNT(id) FROM platzhalter WHERE user_id = '$id'";
          foreach ($pdo->query($anzahl) as $row) {
            $rows = 12 + $row['COUNT(id)'] * 3;
          }
          ?>
          <textarea id="text" name="befehl" rows="<?php echo $rows; ?>" value="''" placeholder="SELECT * FROM... " cols=69 minlength="1" maxlength="2000" required></textarea>
          </br>
          </br>
          <button class="button-17" role="button" type="submit" name="senden" value="Senden">Speichern</button>
          </br>
          </br>
        </td>
        </br>
        <td style="width: 40%;">
          <h2>&nbsp Platzhalter: &nbsp</h2>
          <?php
          #Anzahl der Platzhalter
          if (isset($_GET['einnehmen']) &&  $anzahl1 >= 1) {
            $benutzer = $_GET['einnehmen'];
            $id = $benutzer;
          } else {
            $id = $_SESSION['id'];
          }
          $anzahl = "SELECT COUNT(id) FROM platzhalter WHERE user_id = '$id'";
          foreach ($pdo->query($anzahl) as $row) {
            echo "<h3> Gesamtanzahl an Platzhaltern: " . $row['COUNT(id)'] . "</h3>";
          }
          ?>
          </br>

          <!--alle Platzhalter anzeigen-->
          <?php
          if (isset($_GET['einnehmen']) && $anzahl1 >= 1) {
            $benutzer = $_GET['einnehmen'];
            if ($benutzer == $NutzerInOrga2) {
              $id = $benutzer;
            }
          } else if (empty($benutzer)) {
            $id = $_SESSION['id'];
          }
          function getSqlPlatzhalter2($pdo, $id)
          {

            $stmt = $pdo->prepare("SELECT name FROM platzhalter WHERE user_id = ?");
            $stmt->execute([$id]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
          }
          $platzhalter = getSqlPlatzhalter2($pdo, $id);


          foreach ($platzhalter as $platzhalter) {
          ?>
            <table style="width: auto;">
              <tr>
                <td>
                  <input class="move platzhalter-color" value="<?php echo "$platzhalter" ?>" readonly="readonly" draggable="true" ondragstart="drag(event)" id="<?php echo "[" . $platzhalter . "]" ?>" style="width: auto;">
                  </input>
                </td>

                <td>
                  <button type="submit" class="button-17" role="button" name="loeschen" value="<?php echo $platzhalter; ?>" onclick="return confirm('Achtung: Dieser Platzhalter könnte in einer Vorlage enthalten sein. Möchtest du trotzdem mit dem Löschen fortfahren?')" formnovalidate>🗑️</button>
                </td>
              </tr>
            </table>
          <?php
          }

          if (isset($_POST['loeschen']) && isset($_GET['einnehmen'])) {
            // Der Code zum Löschen des Platzhalters von anderen Nutzer
            $benutzer = $_GET['einnehmen'];
            $name = $_POST['loeschen'];

            try {
              $stmt = $pdo->prepare('DELETE FROM platzhalter WHERE `platzhalter`.`user_id` = ? AND `platzhalter`.`name` = ?;');
              $stmt->execute([$benutzer, $name]);
              echo "<script>window.location.href = 'home.php';</script>";
              exit();
            } catch (\PDOException $e) {
              throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
          }


          if (isset($_POST['loeschen']) && empty(($_GET['einnehmen']))) {
            // Der Code zum Löschen des Platzhalters bei sich selbst
            $name = $_POST['loeschen'];
            try {
              $stmt = $pdo->prepare('DELETE FROM platzhalter WHERE `platzhalter`.`user_id` = ? AND `platzhalter`.`name` = ?;');
              $stmt->execute([$_SESSION['id'], $name]);
              echo "<script>window.location.href = 'home.php';</script>";
              exit();
            } catch (\PDOException $e) {
              throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
          }

          $stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 2 AND active = 1");
          $stmt->execute();
          $image = $stmt->fetch()['bild'];

          $imageData = base64_encode($image);
          ?>

          </br>
          <div class="image-container">
            <img id="bild" height="30" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="Plus" onclick="scrollIntoView()">
          </div>

          <script>
            var image = document.getElementById("bild");
            image.onclick = function() {
              var neu = document.getElementById("neu");
              if (neu) {
                neu.scrollIntoView();
                neu.classList.add("highlight");
              }
            };

            function allowDrop(ev) {
              ev.preventDefault();
            }

            function drag(ev) {
              ev.dataTransfer.setData("text", ev.target.id);
            }

            function drop(ev) {
              ev.preventDefault();
              var data = ev.dataTransfer.getData("text");
              ev.target.appendChild(document.getElementById(data));
            }
          </script>
          </br>
        </td>
        </tr>
      </table>

      </br>
      </br>
      <!--Neue Platzhalter hinzufügen-->
      <table style="width:80%;">
        <tr>
          <td>
            <input id="neu" name="neu" value="" placeholder="Neuer Platzhalter"></input>
            <button class="button-17" role="button" type="submit" name="platz" formnovalidate>Hinzufügen</button>
            </br>
            </br>
          </td>
        </tr>
      </table>
      </br>
    </form>

  <?php
} else {
  echo "<header>
    </br>
    </br>
    </br>
    </br><h2 style='color: red';>Kein Zugriff möglich, bitte wenden Sie sich an den Admin.</h2></header>";
}
include "footer.html";
  ?>
  </body>

</html>
