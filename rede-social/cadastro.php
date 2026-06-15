<?php
session_start();
require_once "models/usuario.php";

$erro = "";
$sucesso = "";

// manter valores preenchidos
$dados = [
    "nome_completo" => "",
    "username" => "",
    "email" => "",
    "confirmar_email" => "",
    "data_nascimento" => "",
    "genero" => ""
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // sanitização
    foreach ($dados as $campo => $valor) {
        if (isset($_POST[$campo])) {
            $dados[$campo] = sanitizar($_POST[$campo]);
        }
    }

    $senha = $_POST['senha'] ?? "";
    $confirmar_senha = $_POST['confirmar_senha'] ?? "";

    // 🔴 validações

    // campos obrigatórios
    foreach ($dados as $campo => $valor) {
        if (empty($valor)) {
            $erro = "Todos os campos são obrigatórios.";
            break;
        }
    }

    // email válido
    if (!$erro && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    }

    // confirmação email
    if (!$erro && $dados['email'] !== $dados['confirmar_email']) {
        $erro = "Emails não coincidem.";
    }

    // senha
    if (!$erro && !preg_match('/^(?=.*[A-Z])(?=.*\d).{6,}$/', $senha)) {
        $erro = "Senha deve ter mínimo 6 caracteres, 1 maiúscula e 1 número.";
    }

    // confirmação senha
    if (!$erro && $senha !== $confirmar_senha) {
        $erro = "Senhas não coincidem.";
    }

    // data válida
    if (!$erro && !strtotime($dados['data_nascimento'])) {
        $erro = "Data de nascimento inválida.";
    }

    // gênero válido
    $generos_validos = ["Feminino", "Masculino", "Outro"];
    if (!$erro && !in_array($dados['genero'], $generos_validos)) {
        $erro = "Gênero inválido.";
    }

    // 🔵 cadastro
    if (!$erro) {

        $dados['senha'] = $senha;

        if (criar_usuario($dados)) {
            header("Location: index.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar. Tente outro email/username.";
        }
    }
}
?>

<?php include "includes/header.php"; ?>

<div class="container" style="max-width:500px; margin-top:40px;">

    <h1 class="title txt-centralizado">Cadastro</h1>

    <?php if ($erro): ?>
        <div class="notification is-danger">
            <?= $erro ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="field">
            <label class="label">Nome completo</label>
            <input class="input" name="nome_completo" value="<?= $dados['nome_completo'] ?>">
        </div>

        <div class="field">
            <label class="label">Username</label>
            <input class="input" name="username"value="<?= $dados['username'] ?>">
        </div>

        <div class="field">
            <label class="label">Email</label>
            <input class="input" name="email" type="email" value="<?= $dados['email'] ?>">
        </div>

        <div class="field">
            <label class="label">Confirmar Email</label>
            <input class="input" name="confirmar_email" type="email" value="<?= $dados['confirmar_email'] ?>">
        </div>

        <div class="field">
            <label class="label">Senha</label>
            <input class="input" name="senha" type="password">
        </div>

        <div class="field">
            <label class="label">Confirmar Senha</label>
            <input class="input" name="confirmar_senha" type="password">
        </div>

        <div class="field">
            <label class="label">Data de nascimento</label>
            <input class="input" type="date" name="data_nascimento" value="<?= $dados['data_nascimento'] ?>">
        </div>

        <div class="field">
            <label class="label">Gênero</label>
            <div class="select is-fullwidth">
                <select name="genero">
                    <option value="">Selecione</option>
                    <option <?= $dados['genero']=="Feminino"?"selected":"" ?>>Feminino</option>
                    <option <?= $dados['genero']=="Masculino"?"selected":"" ?>>Masculino</option>
                    <option <?= $dados['genero']=="Outro"?"selected":"" ?>>Outro</option>
                </select>
            </div>
        </div>

        <div class="field">
            <button class="button botao-bordo is-fullwidth">Cadastrar</button>
        </div>

    </form>

    <div class="txt-centralizado">
        <a href="index.php">Já tenho conta</a>
    </div>

</div>

<?php include "includes/footer.php"; ?>