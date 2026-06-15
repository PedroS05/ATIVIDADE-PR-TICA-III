<?php
session_start();
require_once "models/usuario.php";

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizar($_POST['email'] ?? '');

    if (empty($email)) {
        $erro = "Informe o email cadastrado.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } else {
        $usuario = buscar_usuario_por_email($email);

        if ($usuario) {
            $token = criar_token_recuperacao($usuario['id']);

            if ($token) {
                header("Location: redefinir-senha.php?token=" . urlencode($token));
                exit;
            }

            $erro = "Não foi possível iniciar a recuperação. Tente novamente.";
        } else {
            $erro = "Email não encontrado.";
        }
    }
}
?>

<?php include "includes/header.php"; ?>

<div class="columns is-centered mt-6">
    <div class="column is-4">
        <div class="card">
            <div class="card-content">

        <div class="container" style="max-width:400px; margin-top:40px;">

    <h1 class="title txt-centralizado">Esqueceu a senha?</h1>
        <p class="txt-centralizado mb-4">
            Informe o email da sua conta para redefinir a senha.
        </p>

    <?php if ($erro): ?>
        <div class="notification is-danger">
            <?= $erro ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="field">
            <label class="label">Email</label>
                <div class="control">
                    <input class="input" type="email" name="email" placeholder="Digite seu email" required>
                </div>
        </div>

        <div class="field">
            <button class="button botao-bordo is-fullwidth">
                Continuar
            </button>
        </div>
    </form>

                    <div class="txt-centralizado mt-4">
                        <a href="index.php">Voltar ao login</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
