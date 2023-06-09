<?php
$user_id = $_SESSION['id'];

#No-Home Rolle 
function NoHome($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(rolle_id) FROM role WHERE uid = ? AND rolle_id = '3' AND active = '1'");
    $stmt->execute([$user_id]);
    //einmal ausgeben
    return $stmt->fetchColumn();
}

#Admin
function isUserAdmin($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT rolle_id FROM role WHERE uid = ? AND rolle_id = '1' AND active = '1'");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

#Content-Creator
function isContentCreator($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT rolle_id FROM role WHERE uid = ? AND rolle_id = '2' AND active = '1'");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


function getSqlCommands($pdo, $user_id)
{
    #visible = 1 meint, dass die SQLs NICHT angezeigt werden!
    $stmt = $pdo->prepare("SELECT befehl FROM sql_code WHERE user_id = ? AND visible != 1");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getSqlId($pdo, $command, $user_id)
{
    $stmt = $pdo->prepare("SELECT id FROM sql_code WHERE befehl = ? AND user_id = ? AND visible != 1");
    $stmt->execute([$command, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getSqlTitel($pdo, $command, $user_id)
{
    $stmt = $pdo->prepare("SELECT titel FROM sql_code WHERE befehl = ? AND user_id = ? AND visible != 1");
    $stmt->execute([$command, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
function getSqltitle2($pdo, $user_id, $id)
{
    $stmt = $pdo->prepare("SELECT titel FROM sql_code WHERE user_id = ? AND id = ? AND visible != 1");
    $stmt->execute([$user_id, $id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function Vorgsetzteranzahl($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(vorgesetzter) FROM orga WHERE uid = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

function MitarbeiteruidActiv($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT o.uid FROM orga o INNER JOIN users u ON u.id = o.uid WHERE o.vorgesetzter = ? AND u.active = 1");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function MitarbeiteruidInactiv($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT o.uid FROM orga o INNER JOIN users u ON u.id = o.uid WHERE o.vorgesetzter = ? AND u.active = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function Code(PDO $pdo, $id)
{
    $stmt = $pdo->prepare("SELECT code FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getUSERcode($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT u.id, u.vorname, u.nachname, u.code FROM users u INNER JOIN orga o ON o.uid = u.id WHERE o.vorgesetzter = ? AND u.active = 1 ORDER BY u.id DESC;");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEmail($pdo, $benutzer)
{
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$benutzer]);
    //einmal ausgeben
    return $stmt->fetchColumn();
}

function generatePassword() {
    $length = 16; // Länge des Kennworts (Sie können dies anpassen)
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%*~;';
    
    $password = '';
    while (strlen($password) < $length) {
        $password .= $characters[random_int(0, strlen($characters) - 1)];
    }
    
    if (isPasswordValid($password)) {
        return $password;
    } else {
        return generatePassword();
    }
}

function isPasswordValid($password) {
    // Überprüfen, ob das Kennwort die Anforderungen erfüllt
    if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%*~;])[A-Za-z\d!@#$%*~;]{8,}$/', $password)) {
        return true;
    } else {
        return false;
    }
}

function searchForKeywords($item, $pdo)
{
    $passwort = generatePassword();


    $words = explode(' ', $item);

    $usedPlatzhalter = array();

    $id = $_SESSION['id'];
    $stmt = $pdo->prepare("SELECT PlatzhalterID FROM platzhalter WHERE user_id = ?");
    $stmt->execute([$id]);
    $keywords = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($words as $word) {
        foreach ($keywords as $keyword) {
            if (strpos($word, $keyword) !== false) { // Groß- und Kleinschreibung bei der Überprüfung
                $stmt = $pdo->prepare("SELECT name FROM platzhalter WHERE user_id = ? AND  PlatzhalterID = ? AND  name NOT LIKE '%passwort%' AND name NOT LIKE '%datum%';");
                $stmt->execute([$id, $keyword]);
                $platzhalter = $stmt->fetchAll(PDO::FETCH_COLUMN);

                foreach ($platzhalter as $Platzhalterx) {
                    if (strpos($word, $Platzhalterx) !== false && !in_array($Platzhalterx, $usedPlatzhalter)) {
                        array_push($usedPlatzhalter, $Platzhalterx);
                        if (strtolower($Platzhalterx)) {
?>
                            <table>
                                <tr>
                                    <td>
                                        <input id="text" name="<?php echo strtolower($Platzhalterx) ?>" value="" minlength="1" maxlength="100" <?php if (isset($_POST['menu']) == false) { ?> required <?php } ?> style="width:80%;"></input>

                                    </td>
                                    <td>
                                        <input id="<?php echo $keyword ?>" value="<?php echo strtolower($Platzhalterx) ?>" readonly="readonly" style="width:auto;"></input>
                                    </td>
                                </tr>
                            </table>
                        <?php
                        } else {

                        ?>
                            <table>
                                <tr>
                                    <td>
                                        <input id="text" name="<?php echo $Platzhalterx ?>" size="70" value="" minlength="1" maxlength="100" <?php
                                                                                                                                                if (isset($_POST['menu']) == false) { ?> required <?php } ?> style="width:80%;"></input>

                                    </td>
                                    <td>
                                        <input id="<?php echo $keyword ?>" size="30" value="<?php echo $Platzhalterx ?>" readonly="readonly" style="width:auto;"></input>
                                    </td>
                                </tr>
                            </table>

                    <?php
                        }
                    }
                }
            }
        }
    }


    foreach ($words as $word) {
        foreach ($keywords as $keyword) {

            //datum / passwort Kleinschreibung
            $stmt = $pdo->prepare("SELECT name FROM platzhalter WHERE user_id = ? AND  (name = 'passwort' OR name LIKE '%datum%');");
            $stmt->execute([$id]);
            $platzhalter3 = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($platzhalter3 as $Platzhalterx3) {
                if (strpos($word, strtolower($Platzhalterx3)) !== false && !in_array($Platzhalterx3, $usedPlatzhalter)) {
                    array_push($usedPlatzhalter, $Platzhalterx3);
                    // Array mit ähnlichen Begriffen
                    $aehnlicheBegriffe = array('aenderung_datum', 'datum');
                    ?>
                    <table>
                        <tr>
                            <td>
                                <input name="<?php echo $Platzhalterx3 ?>" size="70" <?php
                                                                                        if ($Platzhalterx3 === 'passwort') {
                                                                                            echo 'type="text" value="' . $passwort . '"';
                                                                                        } else if (in_array($Platzhalterx3, $aehnlicheBegriffe)) {
                                                                                            $currentDateTime = date('Y-m-d'); // Aktuelles Datum und Uhrzeit
                                                                                            echo 'type="date" value="' . $currentDateTime . '"';
                                                                                        } ?> style="width:auto;">
                                </input>

                            </td>
                            <td>
                                <input id="<?php echo "[" . $Platzhalterx3 . "]" ?>" value="<?php echo $Platzhalterx3 ?>" size="30" readonly="readonly" style="width:auto;"></input>
                            </td>
                        </tr>
                    </table>

<?php
                }
            }
        }
    }
    echo "<i><h5> Falls ein oder mehrere Platzhalter nicht mehr als Eingabefeld angezeigt werden, </br>
solltest du deine vorhandenen Platzhalter überprüfen. </h5></i>";
}

function Verlauf(PDO $pdo, $id)
{
    $stmt = $pdo->prepare("SELECT text FROM verlauf WHERE usr = ? AND datum > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY `verlauf`.`datum` DESC");
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function Verlaufdatum(PDO $pdo, $id)
{
    $stmt = $pdo->prepare("SELECT datum FROM verlauf WHERE usr = ? AND datum > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY `verlauf`.`datum` DESC");
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

#verwaltung.php
function getRole($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT rolle_id FROM role WHERE uid = ? AND active = 1;");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getActive($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT active FROM role WHERE uid = ?;");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function active($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT active FROM users WHERE id = ? AND active = 1;");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getUSER($pdo)
{
    $stmt = $pdo->prepare("SELECT u.id, u.vorname, u.nachname, u.email, u.code, u.erstellung_datum, u.last_login FROM users u ORDER BY u.vorname DESC;");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function usr($pdo, $benutzer)
{
    $stmt = $pdo->prepare("SELECT u.vorname FROM users u WHERE id = ?;");
    $stmt->execute([$benutzer]);
    //einmal ausgeben
    return $stmt->fetchColumn();
}
?>
