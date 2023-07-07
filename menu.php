<!DOCTYPE html>
<html lang="de">

<head>
    <title>PlatzhalterPlus</title>
    <link rel="stylesheet" type="text/css" href="bootstrap.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
</head>

<?php
session_start();
include "navbar.php";
include "db_conn.php";
include "logout.php";
?>

<?php
if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
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
        <h1> Menu </h1>
    </header>
    </br>


    <div>
        </br>
        </br>
        </br>
    </div>

    <form method="POST" style="width:100%;">
        <div style="width: 100%;
    justify-content: center;
    align-items: center;
    text-align: center;">


            </br>
            <label for="menu">Wähle einen Datensatz aus:</label></br>
            <select style="width: 80% auto;" name="menu" id="menu" class="select2">
                <option value="auswahl">Auswählen...</option>
                <script>
                    $(document).ready(function() {
                        $('.select2').select2();
                    });
                </script>

                <?php
                $commands = getSqlCommands($pdo, $_SESSION['id']);
                foreach ($commands as $command) {
                    $ids = getSqlId($pdo, $command, $user_id);
                    $titles = getSqlTitel($pdo, $command, $user_id);

                    $idList = implode(', ', $ids);
                    $titleList = implode(', ', $titles);

                    $output = "[$idList] <b> $titleList </b>";
                ?>
                    <option value="<?php echo $idList; ?>" <?php if (isset($_POST['menu']) && $_POST['menu'] == $idList)
                                                                echo 'selected="selected"'; ?>>
                        <?php echo $output; ?>
                    </option>
                <?php
                }
                if (isset($_POST['menu'])) {
                    $menu = $_POST['menu'];

                    $stmt = $pdo->prepare("SELECT titel FROM sql_code WHERE id = ?");
                    $stmt->execute([htmlspecialchars($menu, ENT_QUOTES, 'UTF-8')]);
                    $selectedItem = $stmt->fetchColumn();
                    $titel = htmlspecialchars($selectedItem);

                    $stmt = $pdo->prepare("SELECT befehl FROM sql_code WHERE id = ?");
                    $stmt->execute([htmlspecialchars($menu, ENT_QUOTES, 'UTF-8')]);
                    $selectedItem = $stmt->fetchColumn();
                    $item = htmlspecialchars($selectedItem);
                }
                ?>
            </select>
            </br>
            </br>
            <button class="button-17" name="auswahl" role="button" type="submit">Vorlage anzeigen</button>
            </br>
            </br>

            <table>
                <tr>
                    <td>
                        </br>
                        <?php
                        if (isset($_POST['menu'])) {
                            echo "<label>Textvorlage:" . "<b>  $titel </b>" . "</label>";
                        }
                        ?>
                        </br>
                        <textarea id="vorlage" name="vorlage" value="" rows="15" placeholder="Vorlage auswählen..." readonly style="width: 60%;"><?php if (isset($_POST['menu']) || isset($_POST['übernehmen'])) {
                                                                                                                                            echo $item;
                                                                                                                                        } ?></textarea>
                        </br>
                        </br>
                    </td>
                </tr>
            </table>

            <?php
            if (isset($_POST['übernehmen'])) {
                error_reporting(0);

                $Vorlage = $_POST['vorlage'];

                $words = explode(' ', $item);

                $id = $_SESSION['id'];
                $stmt = $pdo->prepare("SELECT name FROM platzhalter WHERE user_id = ?");
                $stmt->execute([$id]);
                //keywords = Eingabe
                $keywords = $stmt->fetchAll(PDO::FETCH_COLUMN);

                foreach ($words as $word) {
                    foreach ($keywords as $keyword) {
                        if (strpos($word, $keyword) !== false) {

                            $keywordSchleife = array($_POST[$keyword]);
                            $implodeKey = implode(', ', $keywordSchleife);
                            $Eingabe[] = htmlspecialchars($implodeKey, ENT_QUOTES, 'UTF-8');

                            $stmt = $pdo->prepare("SELECT PlatzhalterID FROM platzhalter WHERE user_id = ? AND  name = ?");
                            $stmt->execute([$id, $keyword]);
                            $platzhalter = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            foreach ($platzhalter as $Platzhalterx) {
                                $myArray = array(
                                    $Platzhalterx => $Platzhalterx
                                );
                                $implodeP = implode(',', $myArray);
                                $Alt[] = htmlspecialchars($implodeP, ENT_QUOTES, 'UTF-8');
                            }
                        }
                    }
                }
                // var_dump($Eingabe);
                // var_dump($Alt);

                if (!empty($Alt) && !empty($Eingabe)) {

                    $Ergebnis = str_replace($Alt, $Eingabe, $Vorlage);
                    $currentDateTime = date('Y-m-d H:i:s'); // Aktuelles Datum und Uhrzeit
                    $modifiedDateTime = date('Y-m-d H:i:s', strtotime($currentDateTime . ' +2 hours')); // Datum um 2 Stunden nach hinten versetzt

                    $pp = '[passwort]';
                    if (strpos($item, $pp) === false) {
                        try {
                            $stmt = $pdo->prepare('INSERT INTO verlauf (usr, text, datum) VALUES (?, ?, ?)');
                            $stmt->execute([$_SESSION['id'], $Ergebnis, $modifiedDateTime]);
                        } catch (\PDOException $e) {
                            throw new \PDOException($e->getMessage(), (int) $e->getCode());
                        }
                    } else {
                        echo "<h5> Textvorlagen mit Passwörtern werden nicht im Verlauf gespeichert. </h5>";
                    }
                } else {
                    echo "<h5> Nur Textvorlagen mit Platzhaltern werden im Verlauf dokumentiert. </h5>";
                }
            }


            if (isset($_POST['übernehmen'])) {
            ?>
                </br>
                <table>
                    <?php
                    $stmt = $pdo->prepare("SELECT bild FROM bilderpool WHERE id = 3 AND active = 1");
                    $stmt->execute();
                    $image = $stmt->fetch()['bild'];

                    $imageData = base64_encode($image);
                    ?>
                    <div class="image-container">
                        <img id="bild" height="30" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="Plus" onclick="scrollIntoView()">
                    </div>
                    </br>
                    <tr>
                        <td>
                            </br>
                            <label>Ausgabe:</label>
                            </br>
                            <textarea id="copy_text_hidden" name="copy_text_hidden" value="" rows="15" placeholder="Ergebnis" style="width: 60%;"><?php
                                                                                                                                        if (isset($Ergebnis)) {
                                                                                                                                            echo $Ergebnis;
                                                                                                                                        } ?></textarea></br>
                        <?php
                    }
                        ?>
                        </br>
                        </br>

                        </td>
                    </tr>
                </table>
                </br>
                </br>
                <?php
                #Nur enthaltende Platzhalter anzeigen
                if (isset($_POST['menu'])) {
                    function getSqlPlatzhalterID($pdo)
                    {
                        $id = $_SESSION['id'];
                        $stmt = $pdo->prepare("SELECT PlatzhalterID FROM platzhalter WHERE user_id = ?");
                        $stmt->execute([$id]);
                        return $stmt->fetchAll(PDO::FETCH_COLUMN);
                    }
                    $platzhalter = getSqlPlatzhalterID($pdo);
                    if (!empty($platzhalter) && isset($_POST['menu'])) {
                        searchForKeywords($item, $pdo);
                    }
                }
                if (isset($_POST['menu']) && (!empty($titel))) {
                ?>

                    </br>
                    </br>
                    <div style="display: inline-block;">
                        <button class="button-17" name="übernehmen" role="button" type="submit">Übernehmen</button>

                        <button class="button-17" name="kopieren" role="button" onclick="copyToClipboard()">Kopieren</button>

                        <script>
                            function copyToClipboard() {
                                var text = "";
                                text = document.getElementById("copy_text_hidden").value;
                                navigator.clipboard.writeText(text);
                            }
                        </script>
                    <?php
                }
                    ?>
                    </div>
                    </br>
                    </br>
        </div>
    </form>
    <div>
        </br>
        </br>
        </br>
    </div>


    <details>
    <summary style="font-size: 24px; color: #fff;cursor: pointer;">Verlauf:</summary>
    <div style="display: flex; width: 80%">
        <table style="
  text-align: center;
  display: flex;
  justify-content: center;
  align-items: center;">
            <?php
            $text = Verlauf($pdo, $_SESSION['id']);
            $datum = Verlaufdatum($pdo, $_SESSION['id']);
            foreach ($text as $index => $text) {
                $currentDatum = isset($datum[$index]) ? $datum[$index] : ''; // Aktuelles Datum im Verlauf
                echo "<tr><td>";
                echo "<input placeholder='$currentDatum' readonly style='width: auto'>";
                echo "<textarea rows='15' cols='40' readonly>$text</textarea></br></br>";
                echo "</td></tr>";
            }
            ?>
        </table>
    </div>
</details>




    <div>
        </br>
        </br>
        </br>
    </div>

    <?php
    include "footer.html";
    ?>
</body>

</html>
