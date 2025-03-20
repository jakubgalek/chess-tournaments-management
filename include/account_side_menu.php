<nav class="side-nav">
    <ul class="side-menu">
        <li id="account_statistics"><a href="../pages/account_statistics.php">Moje statystyki</a></li>
        <?php if (isset($_SESSION['Role']) && $_SESSION['Role'] === 'arbiter'): ?>
            <li id ="account_manage_tournament"><a href="../pages/account_manage_tournament.php">Zarządzanie turniejami</a></li>
        <?php endif; ?>
        <li class="spacer"></li>
        <li id="account_settings"><a href="../pages/account_settings.php">Ustawienia konta</a></li>
        <li ><a href="../scripts/logout.php">Wyloguj się</a></li>
    </ul>
</nav>
