<?php
session_start();
include "navbar.php";
include "db_conn.php";
include "logout.php";

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    echo "<script>window.location.href = 'start.php';</script>";
    exit();
}

// Hole Benutzerinformationen aus der Datenbank
$user_id = $_SESSION['id'];
$profil = $pdo->prepare("SELECT email, password FROM users WHERE id = ?");
$profil->execute([$user_id]);
$row = $profil->fetch();
$email = $row['email'];

if (isset($_POST['senden'])) {
    // Aktualisiere die E-Mail-Adresse
    if (!empty($_POST['newEmail'])) {
        $newEmail = $_POST['newEmail'];
    
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$newEmail]);
        $result = $stmt->fetchColumn();
    
        if ($result > 0) {
            echo "<header>
            </br>
            </br>
            </br>
            </br><h2 style='color: red;'>Diese E-Mail-Adresse wird bereits verwendet. Bitte wähle eine andere.</h2></header>";
        } else {
            $updateEmail = "UPDATE users SET email = ? WHERE id = ?";
            $updateEmailSQL = "UPDATE `sql_code` SET `user_name` = ? WHERE `sql_code`.`user_id` = ?;";
            $updateEmailPlatzhalter = "UPDATE `platzhalter` SET `user` = ? WHERE `platzhalter`.`user_id` = ?;";
    
            $stmt1 = $pdo->prepare($updateEmail);
            $stmt1->execute([$newEmail, $_SESSION['id']]);
    
            $stmt2 = $pdo->prepare($updateEmailSQL);
            $stmt2->execute([$newEmail, $_SESSION['id']]);
    
            $stmt3 = $pdo->prepare($updateEmailPlatzhalter);
            $stmt3->execute([$newEmail, $_SESSION['id']]);
    
            if ($stmt1->rowCount() > 0) {
                echo "<header>
                </br>
                </br>
                </br>
                </br><h2 style='color: green;'>E-Mail-Adresse erfolgreich aktualisiert!</h2></header>";
                echo "<script>window.location.href = 'profil.php';</script>";
                exit();
            } else {
                echo "<header>
                </br>
                </br>
                </br>
                </br><h2 style='color: red;'>Fehler beim Aktualisieren der E-Mail-Adresse.</h2></header>";
            }
        }
    }
    

    // Aktualisiere das Passwort
    if (!empty($_POST['newPassword'])) {
        $newPassword = $_POST['newPassword'];
        $pass = validate($newPassword);
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $updatePassword = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $pdo->prepare($updatePassword);
        $stmt->execute([$hashed_pass, $_SESSION['id']]);
        if ($stmt->rowCount() > 0) {
            echo "<header>
            </br>
            </br>
            </br>
            </br><h2 style='color: green';>Passwort erfolgreich aktualisiert!</h2></header>";
            echo "<script>window.location.href = 'profil.php';</script>";
            exit();
        } else if ($stmt->rowCount() == 0) {
            echo "<header>
            </br>
            </br>
            </br>
            </br><h2 style='color: red';>Fehler beim Aktualisieren des Passworts.</h2></header>";
        }
    }
}


if (isset($_POST['deaktivieren'])) {
    try {
            $stmt = $pdo->prepare("UPDATE users SET active = 0 WHERE id = ?;");
            $stmt->execute([$user_id]);
            session_unset();
            session_destroy();
            echo "<script>window.location.href = 'start.php?error=Konto wurde erfolgreich gelöscht!';</script>";
            exit();
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
}


function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $data;
}
?>

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

<body style="height:auto;">
    <header>
        </br>
        </br>
        </br>
        </br>
        <h1>Profil</h1></br>
    </header>
    <!-- HTML-Formular zum Ändern der E-Mail-Adresse und des Passworts -->
    <form method="POST" style="width:95%;">
        </br>

        <div style="width: 100%;
    justify-content: center;
    align-items: center;
    text-align: center;">
                            <label>
                            <div style="text-align: right; color: gray; margin-right: 10%;">

                                <?php
                                $id = $_SESSION['id'];
                                $code = Code($pdo, $id);

                                echo "<p><u>Code</u>:  ";
                                foreach ($code as $value) {
                                    echo $value . " ";
                                }
                                echo "</p>";
                                ?>

                            </div>
                        </label>
                        </br>
            Aktuelle Email:<input name="oldEmail" readonly="readonly" value="<?php echo htmlspecialchars($email); ?>"><br>
            Neue E-Mail-Adresse:<input type="email" name="newEmail"><br>
            Neues Passwort:<input type="password" name="newPassword"><br>

            <button class="button-17" role="button" type="submit" name="senden" value="Senden">Profil aktualisieren</button>
            <button class="button-17" role="button" type="submit" name="deaktivieren" value="Deaktivieren"  onclick="return confirm('Achtung: Soll das Konto wirklich deaktiviert werden?')" formnovalidate style="color: red;">Profil löschen</button><br><br>
        </div>
    </form>
    <div>
        </br>
        </br>
    </div>
<?php
include "footer.html";
  ?>
</body>
</html>
