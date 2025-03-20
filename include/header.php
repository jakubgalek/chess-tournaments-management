<!-- Sekcja HEADER -->
<nav class="navigation">

  <span class="logo">
    <img src="../img/logo.png" alt="logo"> 
    <a href="../pages/list_tournaments.php">TurniejeSzachowe</a>

    <!-- Ikona rozwijanego menu (widoczna na urządzeniach mobilnych) -->
    <div class="hamburger" onclick="toggleMenu()">
      &#9776;
    </div>
  </span>

  <ul class="menu">
    <li class="menu__item"><a href="../pages/list_tournaments.php">Lista turniejów</a></li>
    <li class="menu__item"><a href="../pages/list_players.php">Rejestr członków</a></li>
    <li class="menu__item"><a href="../pages/panel_login.php">Moje konto</a></li>
  </ul>

</nav>

<script>
  function toggleMenu() {
    var menu = document.querySelector('.menu');
    menu.classList.toggle('show');
  }
</script>
