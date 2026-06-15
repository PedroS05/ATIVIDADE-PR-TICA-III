<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item logo-rede">

            </a>
        </div>
        <div class="navbar-menu is-active">
            <div class="navbar-start">
                <a href="feed.php" class="navbar-item">
                    Início
                </a>
                <a href="buscar.php" class="navbar-item">
                    Pesquisa
                </a>
                <?php if (isset($_SESSION['usuario'])): ?>
                <a href="meu-perfil.php" class="navbar-item">
                    Meu Perfil
                </a>
                <?php endif; ?>
            </div>
            <div class="navbar-end">
                <a href="logout.php" class="navbar-item">
                    Sair
                </a>
            </div>
        </div>
    </div>
</nav>