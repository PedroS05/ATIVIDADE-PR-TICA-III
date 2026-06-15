<?php
header("Location: meu-perfil.php" . (isset($_GET['editar']) ? '?editar=1' : ''));
exit;
