<!DOCTYPE html>
<html lang="de">

<head>
    <title>PlatzhalterPlus</title>
    <link rel="stylesheet" type="text/css" href="bootstrap.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">

    <style>
        th.t {
            font-size: 11px;
            border-left: #666;
            border-right: #666;
            border-top: #666;
            border-bottom: #666;
            border-radius: 1px;
            width: 100%;
            height: 100%;
            vertical-align: top;
        }

        td.t {
            font-size: 11px;
            border-left: #666;
            border-right: #666;
            border-top: #666;
            border-bottom: #666;
            border-radius: 1px;
            width: 100%;
            height: 100%;
            vertical-align: top;
        }
    </style>
</head>

<?php
session_start();
include "navbar.php";
include "db_conn.php";
include "logout.php";

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    echo "<script>window.location.href = 'start.php';</script>";
    exit();
}

$userId = $_SESSION['id'];

// Verwaltungsfunktionen nur für Administratoren zugänglich machen
if (isUserAdmin($pdo, $userId)) {


    if (isset($_POST['senden'])) {
        $user_id = $_POST['senden'];
        $nohomeid = "nohome_" . $user_id;
        $userid = "user_" . $user_id;
        $adminid = "admin_" . $user_id;
        $activeid = "active_" . $user_id;


        // Überprüfen Sie die Checkbox-Werte, indem Sie den korrekten Namen der Checkboxen verwenden
        if (isset($_POST[$nohomeid])) {
            // Die Active-Checkbox wurde angeklickt
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `role` WHERE `uid` = ? AND `rolle_id` = '3' AND `active` = '1';");
                $stmt->execute([$user_id]);
                $count = $stmt->fetchColumn();

                if ($count == 0) {
                    // Die Rolle existiert noch nicht, also hinzufügen
                    $stmt = $pdo->prepare("INSERT INTO `role` (`id`, `rolle_id`, `name`, `uid`, `active`) VALUES (NULL, '3', 'Far-From-Home', ?, '1');");
                    $stmt->execute([$user_id]);
                }
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM `role` WHERE `uid` = ? AND `rolle_id` = '3' AND `active` = '1';");
                $stmt->execute([$user_id]);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        }


        if (isset($_POST[$userid])) {
            // Die User-Checkbox wurde angeklickt
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `role` WHERE `uid` = ? AND `rolle_id` = '2' AND `active` = '1';");
                $stmt->execute([$user_id]);
                $count = $stmt->fetchColumn();

                if ($count == 0) {
                    // Die Rolle existiert noch nicht, also hinzufügen
                    $stmt = $pdo->prepare("INSERT INTO `role` (`id`, `rolle_id`, `name`, `uid`, `active`) VALUES (NULL, '2', 'Content-Creator', ?, '1');");
                    $stmt->execute([$user_id]);
                }
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM `role` WHERE `uid` = ? AND `rolle_id` = '2' AND `active` = '1';");
                $stmt->execute([$user_id]);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        }


        if (isset($_POST[$adminid])) {
            // Die Admin-Checkbox wurde angeklickt
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `role` WHERE `uid` = ? AND `rolle_id` = '1' AND `active` = '1';");
                $stmt->execute([$user_id]);
                $count = $stmt->fetchColumn();

                if ($count == 0) {
                    $stmt = $pdo->prepare("INSERT INTO `role` (`id`, `rolle_id`, `name`, `uid`, `active`) VALUES (NULL, '1', 'Admin', ?, '1');");
                    $stmt->execute([$user_id]);
                }
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM `role` WHERE `uid` = ? AND `rolle_id` = '1' AND `active` = '1';");
                $stmt->execute([$user_id]);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        }


        if (isset($_POST[$activeid])) {
            // Die Active-Checkbox wurde angeklickt
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `id` = ? AND `active` = '0';");
                $stmt->execute([$user_id]);
                $count = $stmt->fetchColumn();

                if ($count == 1) {
                    $stmt = $pdo->prepare("UPDATE users SET active = 1 WHERE id = ?;");
                    $stmt->execute([$user_id]);
                }
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET active = 0 WHERE id = ?;");
                $stmt->execute([$user_id]);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        }


        echo "<script>window.location.href = 'verwaltung.php';</script>";
        exit();
    }
?>




    <body style="height: 100%;">
        <header>
            </br>
            </br>
            </br>
            </br>
            <h1> Verwaltung </h1>
        </header>
        </br>


        <form method="POST" style="width: auto;">
    <table class="t">
        <tr>
            <th class="t" style="width: 20px;">Zeile</th>
            <th class="t">Vorname</th>
            <th class="t">Nachname</th>
            <th class="t">Email</th>
            <th class="t">Code</th>
            <th class="t">Letzte Aktivität</th>
            <th class="t">Rolle: No-Homepage</th>
            <th class="t">Rolle: Content-Creator</th>
            <th class="t">Rolle: Admin</th>
            <th class="t">Aktiv</th>
            <th class="t">Aktion</th>
        </tr>

        <?php
        $nutzer = getUSER($pdo);
        $zeilennummer = 1;

        foreach ($nutzer as $row) {
            $user_id = $row['id'];
            $nohomeid = "nohome_" . $user_id;
            $userid = "user_" . $user_id;
            $adminid = "admin_" . $user_id;
            $activeid = "active_" . $user_id;

            $active = getActive($pdo, $user_id);
            $isActive = in_array('1', $active);

            $active2 = active($pdo, $user_id);
            $ActivUser = in_array('1', $active2);

            $roles = getRole($pdo, $user_id);
            $isNoHomeChecked = in_array('3', $roles);
            $isUserChecked = in_array('2', $roles);
            $isAdminChecked = in_array('1', $roles);
        ?>
            <tr>
                <td class="t" style="width: 20px;"><?php echo $zeilennummer; ?></td>
                <td class="t"><?php echo $row['vorname']; ?></td>
                <td class="t"><?php echo $row['nachname']; ?></td>
                <td class="t"><?php echo $row['email']; ?></td>
                <td class="t"><?php echo $row['code']; ?></td>
                <td class="t">
                    <?php
                    echo $row['last_login'];
                    $lastLogin = strtotime($row['last_login']);
                    $twoWeeksAgo = strtotime('-2 weeks');
                    if ($ActivUser == true) {
                        if ($lastLogin > $twoWeeksAgo) {
                            echo "</br></br>Status: 🟢";
                        }
                        if ($lastLogin < $twoWeeksAgo) {
                            echo "</br></br>Status: 🟡";
                        }
                    } else if ($ActivUser == false) {
                        echo "</br></br>Status: 🔴";
                    }
                    ?>
                </td>
                <td class="t">
                    <input type="checkbox" name="<?php echo $nohomeid; ?>" value="yes" <?php if ($isNoHomeChecked && $isActive == true) {
                                                                                                echo "checked";
                                                                                            } ?> style="width: auto;">
                </td>
                <td class="t">
                    <input type="checkbox" name="<?php echo $userid; ?>" value="yes" <?php if ($isUserChecked && $isActive == true) {
                                                                                            echo "checked";
                                                                                        } ?> style="width: auto;">
                </td>
                <td class="t">
                    <input type="checkbox" name="<?php echo $adminid; ?>" value="yes" <?php if ($isAdminChecked && $isActive == true) {
                                                                                                echo "checked";
                                                                                            } ?> style="width: auto;">
                </td>
                <td class="t">
                    <input type="checkbox" name="<?php echo $activeid; ?>" value="yes" <?php if ($ActivUser == true) {
                                                                                                echo "checked";
                                                                                            } ?> style="width: auto;">
                </td>

                <td class="t" style="vertical-align: middle;">
                    <button class="button-17" role="button" type="submit" name="senden" value="<?php echo $row['id']; ?>">Speichern</button>
                </td>
            </tr>
        <?php
            $zeilennummer++;
        }
        ?>
    </table>
</form>

        <div>
            </br>
            </br>
            </br>
        </div>

        <form method="POST" style="width: 90%;" enctype="multipart/form-data">
            </br>
            <label for="bilder" style="margin-left: 5%;">Neue Bilder in Datenbank hinterlegen:</label>
            <input id="text" name="titel" value="" minlength="1" maxlength="255" placeholder="Bezeichnung"></input>
            <input type="file" id="bilder" name="bilder">
            <button class="button-17" role="button" type="submit" name="bild" style="margin-left: 5%;">Zu Bilderpool hinzufügen</button>
            </br>
            </br>
        </form>

    <?php
    if (isset($_POST['bild'])) {
        $titel = strtolower(trim($_POST['titel']));
        $currentDateTime = date('Y-m-d H:i:s');
        $BILD = $_FILES['bilder']['tmp_name'];

        // Lese den binären Inhalt der Datei aus
        $binData = file_get_contents($BILD);

        try {
            $stmt = $pdo->prepare('INSERT INTO bilderpool (name, bild, filetype, uid, active, datum) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$titel, $binData, '.jpeg', $userId, '1', $currentDateTime]);
            echo "<script>window.location.href = 'verwaltung.php';</script>";
            exit();
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
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