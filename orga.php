<!DOCTYPE html>
<html lang="de">

<head>
    <title>PlatzhalterPlus</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="bootstrap.css">
</head>
<style>
    body {
        background: linear-gradient(to bottom, #d1d1d1, #1a1a1a);
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    td.t {
        border-left: #666;
        border-right: #666;
        border-top: #666;
        border-bottom: #666;
        box-sizing: border-box;
        border-radius: 1px;
        padding-left: 20px;
        padding-right: 20px;
        margin-left: auto;
        margin-right: auto;
        width: auto;
        text-align: center;
    }

    th.h {
        border-left: #666;
        border-right: #666;
        border-top: #666;
        border-bottom: #666;
        box-sizing: border-box;
        border-radius: 1px;
        padding-left: 20px;
        padding-right: 20px;
        margin-left: auto;
        margin-right: auto;
        width: auto;

    }
</style>

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

// Verwaltungsfunktionen nur für Administratoren zugänglich machen
if (isUserAdmin($pdo, $userId) || isContentCreator($pdo, $userId)) {

    if (isset($_GET['error'])) { ?>
        <p class="error">
            <?php echo $_GET['error']; ?>
        </p>
    <?php
    }

    if (isset($_POST['hinzufügen'])) {
        $code = $_POST['code'];
        $CodeSelect = $pdo->prepare('SELECT code FROM users WHERE code = ?');
        $CodeSelect->execute([$_POST['code']]);
        $CodeSelect = $CodeSelect->fetchColumn();

        if ($CodeSelect == true) {
            try {
                $code = $_POST['code'];
                $codeuid = $pdo->prepare('SELECT id FROM users WHERE code = ?');
                $codeuid->execute([$_POST['code']]);
                $codeuid = $codeuid->fetchColumn();

                $check = $pdo->prepare('SELECT uid FROM orga WHERE code = ? AND vorgesetzter = ?');
                $check->execute([$_POST['code'], $userId]);
                $check = $check->fetchColumn();

                if (empty($check) && $userId != $check) {
                    $stmt = $pdo->prepare('INSERT INTO orga (uid, code, vorgesetzter) VALUES (?, ?, ?)');
                    $stmt->execute([$codeuid, $code, $userId]);
                    echo "<script>window.location.href = 'orga.php';</script>";
                    exit();
                } else {
                    echo "<h4 style='color: red';><b>Achtung! Dieser User ist dir bereits zugewiesen</b></h4>";
                }
            } catch (\PDOException $e) {
                // var_dump($code);
                // var_dump($codeuid);
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        } else {
            echo "<h4  style='color: red';>Der Code konnte keinem Nutzer zugeordnet werden.</h4>";
        }
    }


    if (isset($_POST['entfernen'])) {
        try {
            $user_id = $_POST['entfernen'];
            $stmt = $pdo->prepare("DELETE FROM orga WHERE `orga`.`uid` = ? AND `orga`.`vorgesetzter` = ?;");
            $stmt->execute([$user_id, $userId]);
            echo "<script>window.location.href = 'orga.php';</script>";
            exit();
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    // Überprüfung, ob die dem Content-Creator bereits Nutzer zugewiesen sind 
    $stmt = $pdo->prepare('SELECT COUNT(o.id)
    FROM orga o
    INNER JOIN users u ON u.id = o.uid
    WHERE o.vorgesetzter = ? AND u.active = 1;');
    $stmt->execute([$userId]);
    $nutzerinsgesamt = $stmt->fetchColumn();

    
    if ($nutzerinsgesamt < 1) {
        echo "<header>
    </br>
    </br>
    </br>
    </br><h2 style='color: black';>Dir sind aktuell noch keine Nutzer zugewiesen. </br>
    Bitte füge über den Code im Profil deine Kollegen zur Organisation hinzu.</h2></header>";
    } else {
    ?>

        <body>

            <header>
                </br>
                </br>
                </br>
                </br>
                </br>
                <h3>Diese Nutzer sind dir zugewiesen:</h3>
                </br>
                </br>
            </header>
            </br>


            <table class="t">
                </br>
                <tr class="t">
                    <th class="h">Vorname</th>
                    <th class="h">Nachname</th>
                    <th class="h">Code</th>
                    <th class="h">Textvorlagen Anzahl</th>
                    <th class="h">Aktionen</th>
                </tr>


                <?php

                $nutzer = getUSERcode($pdo, $user_id);

                foreach ($nutzer as $row) {
                    $user_id = $row['id'];

                ?>
                    <tr>
                        <td class="t"><?php echo $row['vorname']; ?></td>
                        <td class="t"><?php echo $row['nachname']; ?></td>
                        <td class="t"><?php echo $row['code']; ?></td>
                        <td class="t"><?php $anzahl = "SELECT COUNT(befehl) FROM sql_code WHERE user_id = '$user_id' AND visible != 1";
                                        foreach ($pdo->query($anzahl) as $row) {
                                            echo $row['COUNT(befehl)'];
                                        } ?></td>
                        <td class="t">
                            </br>
                            <div style="width: 100%; justify-content: center; align-items: center; text-align: center;">
                                <form method="GET" action="home.php" style="background-color: rgba(255, 255, 255, 0.0);
    border:rgba(255, 255, 255, 0.0);">
                                    <button class="button-17" role="button" type="submit" name="einnehmen" value="<?php echo $user_id; ?>" formnovalidate>👁️</button>

                                </form>
                                <div>
                                    </br>
                                </div>

                                <form method="POST" action="orga.php" style="background-color: rgba(255, 255, 255, 0.0);
    border:rgba(255, 255, 255, 0.0);">
                                    <button class="button-17" role="button" type="submit" name="entfernen" value="<?php echo $user_id; ?>" onclick="return confirm('Soll der Benutzer aus deiner Organisation entfernt werden?')" formnovalidate>🗑️</button>
                                </form>
                            </div>
                            </br>
                        </td>
                    </tr>


            <?php
                }
            }
            ?>
            </table>
            <div>
                </br>
                </br>
                </br>
            </div>
            <form method="POST" action="orga.php" style="width: 55%;">

                <input id="code" name="code" value="" placeholder="Gebe hier den mitgeteilten Code deines Kollegen ein"></input>
                <div style="width: 100%; justify-content: center; align-items: center; text-align: center;">
                    <button class="button-17" role="button" type="submit" name="hinzufügen" formnovalidate>Hinzufügen</button>
                </div>
                </br>
                </br>
            </form>
            <div>
                </br>
                </br>
            </div>
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
