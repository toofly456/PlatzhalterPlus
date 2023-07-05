<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "db_conn.php";
include "function.php";

function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data); 
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $data;
}

if (isset($_POST['email'], $_POST['password'])) {
    $email = validate($_POST['email']);
    $pass = validate($_POST['password']);
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    if (empty($email)) {
        header("Location: start.php?error=Email is required");
        exit();
    } else if (empty($pass)) {
        header("Location: start.php?error=Password is required");
        exit();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(); 

        if ($row && password_verify($pass, $row['password'])) {
            if ($row['active'] == 0) {
                header("Location: start.php?error=Account is deactivated");
                exit();
            }

            $_SESSION['email'] = $row['email'];
            $_SESSION['id'] = $row['id'];
            $currentDateTime = date('Y-m-d H:i:s');
            $user_id = $_SESSION['id'];
            
            try {
                $stmt = $pdo->prepare("UPDATE `users` SET `last_login` = ? WHERE `users`.`id` = ?");
                $stmt->execute([$currentDateTime, $user_id]);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int) $e->getCode());
            } 
            $userId = $_SESSION['id'];

$nohomenutzer = NoHome($pdo, $user_id);

// Home als Startseite für alle zugänglich machen, außer für Nutzer mit No-Homepage Rolle
if ($nohomenutzer <= 0) {
            echo "<script>window.location.href = 'home.php';</script>";
            exit();
} else if ($nohomenutzer > 0){
    echo "<script>window.location.href = 'menu.php';</script>";
    exit();
}
        } else {
            header("Location: start.php?error=Incorrect email or password");
            exit();
        }
    } 
} else {
    echo "<script>window.location.href = 'start.php';</script>";
    exit();
}

?>
