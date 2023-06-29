<!DOCTYPE html>
<html lang="de">

<head>
    <title>PlatzhalterPlus</title>
    <link rel="stylesheet" type="text/css" href="bootstrap.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
</head>


<?php
session_start();
include "navbar.php";
include "db_conn.php";
include "logout.php";
  if (!isset($_SESSION['id'])) {
    echo "<script>window.location.href = 'start.php';</script>";
    exit();
  }
?>

<body>
    <header>
        </br>
        </br>
        </br>
        </br>
        <h1>Edit</h1>
        <h2>Bearbeite hier deine Texte</h2>
    </header>
        <form method="post" style="width:100%;">
    <table style="width: 100%;
    justify-content: center;
    align-items: center;
    text-align: center;"> 
            <?php

            if (isset($_GET['text']) || isset($_POST['speichern'])) {
                $user_id = $_SESSION['id'];
                $id = $_GET['text'];
                $output = getSqltitle2($pdo, $user_id, $id);
                ?>
                <tr>
                    <td>
                        <h3><?php echo $output[0]; ?></h3>
                        <textarea id="text" name="befehl" cols="100" rows="15" placeholder="SELECT * FROM..." minlength="1"
                            maxlength="1500"><?php
                            $user_id = $_SESSION['id'];
                            $idBefehl = $_GET['text'];
                            try {
                                $stmt = $pdo->prepare('SELECT befehl FROM sql_code WHERE `sql_code`.`user_id` = ? AND `sql_code`.`id` = ?');
                                $stmt->execute([$user_id, $idBefehl]);
                                $befehl = $stmt->fetchColumn();
                                echo $befehl;
                            } catch (\PDOException $e) {
                                throw new \PDOException($e->getMessage(), (int) $e->getCode());
                            }
                            ?></textarea>
                        </br>
                        </br>
                    </td>
                </tr>

        </table>
        </br>
        <button class="button-17" type="submit" name="speichern" value="Senden" style="width: 100%;
    justify-content: center;
    align-items: center;
    text-align: center;">Speichern</button>
        <?php
        if (isset($_POST['speichern'])) {
            $myTextValue = $_POST['befehl']; 
            try {
                $stmt = $pdo->prepare('UPDATE `sql_code` SET `befehl` = ? WHERE `sql_code`.`user_id` = ? AND `sql_code`.`id` = ?');
                $stmt->execute([$myTextValue, $user_id, $idBefehl]);
                $befehlNeu = $stmt->fetchColumn();
    echo "<script>window.location.href = 'sammlung.php';</script>";
    exit();
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        }
            } else {
                echo "</br><h2 style = 'text-align: center;'> Es wurde keine ID übergeben. </h2>";
            }
            ?>
    </form>
    </br>
    </br>
    </br>
<?php
include "footer.html";
  ?>
</body>

</html>