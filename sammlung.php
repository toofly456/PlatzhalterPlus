<!DOCTYPE html>
<html lang="de">

<head>
    <title>PlatzhalterPlus</title>
    <link rel="stylesheet" type="text/css" href="bootstrap.css" />
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
            <h1 class="a">Sammlung</h1> </br>
            <?php
            $uid = $_SESSION['id'];
            $anzahl = "SELECT COUNT(befehl) FROM sql_code WHERE user_id = '$uid' AND visible != 1";
            foreach ($pdo->query($anzahl) as $row) {
                echo "<h2 class='a'>&nbsp Vorlagen: " . $row['COUNT(befehl)'] . "</h2>";
            }
            if (isset($_POST['loeschen'])) {
                $titleList = $_POST['loeschen'];
                echo "<h3 class='a'>&nbsp Die Textvorlage [" . $titleList . "] wurde gelöscht.</h3>";
            }
            ?>

    </header>
    <main>

        <div style="width: 100%;
    justify-content: center;
    align-items: center;
    text-align: center; width:auto;">
            <table>

                <?php
                $commands = getSqlCommands($pdo, $_SESSION['id']);

                foreach ($commands as $command) {
                ?>


                    <?php
                    $user_id = $_SESSION['id'];
                    $ids = getSqlId($pdo, $command, $user_id);
                    $titles = getSqlTitel($pdo, $command, $user_id);

                    $idList = implode(', ', $ids);
                    $titleList = implode(', ', $titles);

                    $output = "[$idList] <b> $titleList </b>";

                    if (!empty($idList)) {
                    ?>
                        <label type="text" name="auswahl">
                            <pre>
                                    <tr>
                <td style="vertical-align: top; height: 100%; width: auto">
                                <?php
                                echo ("</br>$output </br></br> ");
                                print_r($command);
                                ?>
                                                </pre>
                            </br>
                            </br>

                            <div style="display: flex;">
                                <div style="margin-right: 30px;">
                                    <form action="edit.php" method="GET" style="background-color: rgba(255, 255, 255, 0.0); border:rgba(255, 255, 255, 0.0);">
                                        <button class="button-17" role="button" type="submit" name="text" id=<?php echo $idList; ?> value=<?php echo $idList; ?>>⚙️</button>
                                    </form>
                                </div>
                                <div style="margin-left: -10px;">
                                    <form action="sammlung.php" method="POST" style="background-color: rgba(255, 255, 255, 0.0); border:rgba(255, 255, 255, 0.0);">
                                        <button class="button-17" role="button" type="submit" name="loeschen" value="<?php echo $idList; ?>" onclick="return confirm('Soll dieser Eintrag wirklich gelöscht werden?')">🗑️</button>
                                    </form>
                                </div>
                            </div>

                            </br>
                            </td>
                            </tr>
                        </label>
                <?php
                    }
                }
                ?>

            </table>
            </br>
            </br>
        </div>


        <?php
        if (isset($_POST['loeschen'])) {
            $user_id = $_SESSION['id'];
            $idList = $_POST['loeschen'];
            try {
                $stmt = $pdo->prepare('UPDATE sql_code s SET s.visible = 1 WHERE s.user_id = ? AND s.id = ?');
                $stmt->execute([$user_id, $idList]);
                echo "<script>window.location.href = 'sammlung.php';</script>";
                exit();
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            }
        }
        ?>
    </main>
    </br>
    </br>
    </br>
<?php
include "footer.html";
  ?>
</body>

</html>