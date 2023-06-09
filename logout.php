<?php 

// Überprüfe, ob die letzte Aktivitätszeit vorhanden ist
if (isset($_SESSION['last_activity'])) {
    // Überprüfe, ob die Inaktivitätsdauer überschritten wurde
    $inactive_duration = 3600; // 1 Stunde in Sekunden
    $current_time = time();
    $last_activity_time = $_SESSION['last_activity'];

    if (($current_time - $last_activity_time) > $inactive_duration) {
        // Nutzer als inaktiv betrachten und abmelden
        session_unset();
        session_destroy();
        echo "<script>window.location.href = 'start.php?error=User inactive for over 60 minutes';</script>";
        exit();
    }
}

// Aktualisiere die letzte Aktivitätszeit
$_SESSION['last_activity'] = time();

?> 
