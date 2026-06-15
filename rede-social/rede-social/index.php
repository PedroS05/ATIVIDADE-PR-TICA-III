<?php
session_start();
require_once "models/usuario.php";

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = sanitizar($_POST['email']);
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {

        $usuario = autenticar_usuario($email, $senha);

        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            header("Location: feed.php");
            exit;
        } else {
            $erro = "Email ou senha inválidos.";
        }
    }
}
?>

<?php include "includes/header.php"; ?>
<div class="columns is-centered mt-6">
<div class="column is-4">
<div class="card">
<div class="card-content">

<div class="container" style="max-width:400px; margin-top:60px;">

    <h1 class="title txt-centralizado">Login</h1>

    <?php if ($erro): ?>
        <div class="notification is-danger">
            <?= $erro ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['sucesso'])): ?>
        <div class="notification is-success">
            <?= $_SESSION['sucesso'] ?>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <form method="POST">

        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" placeholder="Digite seu email">
            </div>
        </div>

        <div class="field">
            <label class="label">Senha</label>
            <div class="control">
                <input class="input" type="password" name="senha" placeholder="Digite sua senha">
            </div>
        </div>

        <div class="field">
            <button class="button botao-bordo is-fullwidth">
                Entrar
            </button>
        </div>

    </form>

    <div class="txt-centralizado mt-4">
        <a href="esqueci-senha.php">Esqueceu a senha?</a>
    </div>

    <div class="txt-centralizado mt-2">
        <a href="cadastro.php">Criar conta</a>
    </div>

</div>
</div>
</div>
</div>
</div>


<?php include "includes/footer.php"; ?>