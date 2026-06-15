<?php
session_start();
require_once "models/post.php";
require_once "config/conexao.php";

// Variável de global chamada conexao para que não cause erros no futuro.
global $conexao;

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];

// Atualiza dados da sessão (principalmente foto)
$id_usuario = intval($usuario['id']);
$sql_user = "SELECT * FROM usuarios WHERE id = $id_usuario";
$res_user = mysqli_query($conexao, $sql_user);
$usuario = mysqli_fetch_assoc($res_user);
$_SESSION['usuario'] = $usuario;

// Criar post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conteudo = trim($_POST['conteudo'] ?? '');
    $tem_video = isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK;
    $tem_imagem = isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK;

    if ($conteudo !== '' || $tem_video || $tem_imagem) {
        criar_post(
            $usuario['id'],
            $conteudo,
            $_FILES['video'] ?? null,
            $_FILES['imagem'] ?? null
        );
        header("Location: feed.php");
        exit;
    }
}

$icone_imagem = file_exists(__DIR__ . '/public/images/icons/enviar-imagem.png')
    ? 'public/images/icons/enviar-imagem.png'
    : 'public/images/icons/enviar-imagem.svg';

// Buscar feed
$posts = feed($usuario['id']);
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container">

<!-- BLOCO DO USUÁRIO LOGADO -->
<div class="box" style="display:flex; align-items:center; gap:15px;">

    <?php if (!empty($usuario['foto'])): ?>
        <img 
            src="data:image/jpeg;base64,<?= base64_encode($usuario['foto']) ?>" 
            style="width:60px; height:60px; border-radius:50%; object-fit:cover;"
        >
    <?php else: ?>
        <img 
            src="https://via.placeholder.com/60" 
            style="border-radius:50%;"
        >
    <?php endif; ?>

    <div>
        <p><strong><?= $usuario['nome_completo'] ?></strong></p>
        <p>@<?= $usuario['username'] ?></p>
    </div>

</div>

<!--FORM POST -->
<form method="POST" enctype="multipart/form-data" id="form-post">
    <input class="input" name="conteudo" placeholder="O que você está pensando?">
    <input type="file" name="imagem" id="input-imagem" accept="image/*" hidden>
    <input type="file" name="video" id="input-video" accept="video/*" hidden>

    <div class="post-acoes">
        <button type="button" class="button botao-bordo is-light botao-midia" id="btn-enviar-imagem">
            <img src="<?= htmlspecialchars($icone_imagem) ?>" alt="" class="icone-midia">
            Enviar imagem
        </button>
        <button type="button" class="button botao-bordo is-light" id="btn-enviar-video">
            Enviar vídeo
        </button>
        <button type="submit" class="button botao-bordo">Postar</button>
    </div>

    <p id="nome-imagem" class="nome-midia"></p>
    <p id="nome-video" class="nome-midia"></p>
</form>

<hr>

<!--POSTS -->
<?php while ($p = mysqli_fetch_assoc($posts)): ?>

<div class="card post">
<div class="card-content" style="display:flex; gap:15px;">

    <!--FOTO DO AUTOR DO POST -->
    <?php if (!empty($p['foto'])): ?>
        <img 
            src="data:image/jpeg;base64,<?= base64_encode($p['foto']) ?>" 
            style="width:50px; height:50px; border-radius:50%; object-fit:cover;"
        >
    <?php else: ?>
        <img 
            src="https://via.placeholder.com/50" 
            style="border-radius:50%;"
        >
    <?php endif; ?>

    <div>

        <p>
            <strong><?= $p['nome_completo'] ?></strong> 
            @<?= $p['username'] ?>
        </p>

        <?php if (!empty($p['conteudo'])): ?>
            <p><?= htmlspecialchars($p['conteudo']) ?></p>
        <?php endif; ?>

        <?php if (!empty($p['imagem'])): ?>
            <img
                src="<?= htmlspecialchars($p['imagem']) ?>"
                alt="Imagem do post"
                class="post-imagem"
            >
        <?php endif; ?>

        <?php if (!empty($p['video'])): ?>
            <video class="post-video" controls>
                <source src="<?= htmlspecialchars($p['video']) ?>">
            </video>
        <?php endif; ?>

    <button onclick="curtir(<?= $p['id'] ?>)">
        Curtir (
        <span id="curtidas-<?= $p['id'] ?>">
            <?= total_curtidas($p['id']) ?>
        </span>
        )
    </button>
    </div>

</div>
</div>

<?php endwhile; ?>

</div>

<?php include "includes/footer.php"; ?>