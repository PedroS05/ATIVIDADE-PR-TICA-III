<?php
session_start();
require_once "models/usuario.php";

$usuario_id = $_SESSION['usuario']['id'];

$resultados = null;

if (!empty($_GET['busca'])) {
    $busca = $_GET['busca'];
    $resultados = buscar_usuarios($busca, $usuario_id);
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>
<?php include "includes/header.php"; ?>


<div class="container">

<form method="GET">
<input class="input" name="busca" placeholder="Buscar usuários">
</form>

<hr>

<?php if ($resultados): ?>

<?php while ($u = mysqli_fetch_assoc($resultados)): ?>

<div class="box" style="display:flex; align-items:center; gap:15px;">

    <!-- 📸 FOTO -->
    <?php if (!empty($u['foto'])): ?>
        <img 
            src="data:image/jpeg;base64,<?= base64_encode($u['foto']) ?>"
            style="width:50px;height:50px;border-radius:50%;object-fit:cover;"
        >
    <?php else: ?>
        <img 
            src="https://via.placeholder.com/50"
            style="border-radius:50%;"
        >
    <?php endif; ?>

    <!-- 👤 DADOS -->
    <div>
        <p><strong><?= $u['nome_completo'] ?></strong></p>
        <p>@<?= $u['username'] ?></p>
    </div>

    <!-- 🎯 AÇÕES -->
    <div style="margin-left:auto; display:flex; gap:10px;">

        <!-- 👁️ VER PERFIL -->
        <a href="usuario.php?id=<?= $u['id'] ?>" 
           class="button is-info is-small">
            Ver perfil
        </a>

        <!-- 👥 SEGUIR (AJAX) -->
        <button
            type="button"
            id="btn-seguir-<?= $u['id'] ?>"
            onclick="seguir(<?= $u['id'] ?>)"
            class="button <?= ja_segue($usuario_id, $u['id']) ? 'is-light' : 'is-primary' ?> is-small">

            <?= ja_segue($usuario_id, $u['id']) ? 'Seguindo' : 'Seguir' ?>

        </button>
    </div>

</div>

<?php endwhile; ?>

<?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>