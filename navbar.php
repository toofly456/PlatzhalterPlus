<style>
  nav {
    background-color: #3339;
    width: 100%;
    position: absolute;
    top: 0;
    z-index: 999;
  }

  nav ul {
    margin: 0;
    padding: 0;
    list-style: none;
    overflow: hidden;
  }

  nav ul li {
    float: left;
  }

  nav ul li a {
    display: block;
    color: #fff;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
  }

  nav ul li a:hover {
    background-color: white;
    color: black;
  }

  nav ul li.logout {
    float: right;
  }

  @media (max-width: 600px) {
    nav ul {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      display: none; /* Änderung: standardmäßig ausgeblendet */
      position: absolute;
      top: 100%; /* Änderung: unterhalb der Navbar */
      left: 0;
      width: 100%;
      background-color: #3339;
      padding: 10px;
    }

    nav ul li {
      float: none;
      margin-bottom: 10px; /* Änderung: Abstand zwischen den Elementen */
    }

    nav ul li a {
      padding: 14px;
      width: 100%; /* Änderung: volle Breite für anklickbare Fläche */
    }

    nav ul li a:hover {
      background-color: white;
      color: black;
    }

    nav.active ul {
      display: block; /* Änderung: bei Aktivierung anzeigen */
    }
  }

  /* Zusätzliche CSS-Regel für das Menü-Symbol */
  .menu-icon {
    display: none; /* Änderung: standardmäßig ausgeblendet */
    color: #fff;
    font-size: 24px;
    float: right;
    cursor: pointer;
    padding: 10px;
  }

  @media (max-width: 600px) {
    .menu-icon {
      display: block; /* Änderung: bei Handyansicht anzeigen */
    }
  }
</style>

<nav>
  <?php
  include "logo.php";
  include "function.php";
  ?>
  <ul>
    <li style="background-color: white;">
      <a>
        <div class="logo">
          <img id="logo" height="30" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="PlatzhalterPlus">
        </div>
      </a>
    </li>
    <?php
    $userId = $_SESSION['id'];
    $nohomenutzer = NoHome($pdo, $user_id);
    if ($nohomenutzer <= 0) {
    ?>
      <li><a href="home.php">Home</a></li>
    <?php
    }
    ?>
    <li><a href="menu.php">Menu</a></li>
    <li><a href="sammlung.php">Sammlung</a></li>

    <?php
    $userId = $_SESSION['id'];
    if (isUserAdmin($pdo, $userId) || isContentCreator($pdo, $userId)) {
    ?>
      <li><a href="orga.php">Organisation</a></li>
    <?php
    }
    ?>

    <?php
    $userId = $_SESSION['id'];
    if (isUserAdmin($pdo, $userId)) {
    ?>
      <li><a href="verwaltung.php">Verwaltung</a></li>
    <?php
    }
    ?>

    <li class="logout"><a href="start.php">Abmelden</a></li>
    <li class="logout"><a href="profil.php">
        <div class="logo">
          <img id="logo" height="30" src="data:image/jpeg;base64,<?php echo $imageDataProfil; ?>" alt="Profil">
        </div>
      </a></li>
  </ul>
  <div class="menu-icon" onclick="toggleMenu()">&#9776;</div> <!-- Hinzugefügt: Menü-Symbol -->
</nav>

<script>
  function toggleMenu() {
    var nav = document.querySelector('nav ul');
    nav.classList.toggle('active');
  }

  document.addEventListener('click', function(e) {
    var nav = document.querySelector('nav ul');
    var menuIcon = document.querySelector('.menu-icon');
    if (e.target !== nav && e.target !== menuIcon && !nav.contains(e.target)) {
      nav.classList.remove('active');
    }
  });
</script>
