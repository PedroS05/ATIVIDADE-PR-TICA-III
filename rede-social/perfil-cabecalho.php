<?php
/**
 * Cabeçalho de perfil no estilo X (Twitter).
 *
 * Variáveis esperadas:
 * - $usuario_perfil (array do usuário exibido)
 * - $usuario_logado (array do usuário logado)
 * - $seguidores, $seguindo (int)
 * - $eh_proprio_perfil (bool)
 */
global $eh_proprio_perfil, $usuario_perfil, $usuario_logado, $seguidores, $seguindo;
$capa = !empty($usuario_perfil['capa'])
    ? htmlspecialchars($usuario_perfil['capa'])
    : '';

$meses = [
    1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
    5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
    9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
];

$data_ingresso = '';

if (!empty($usuario_perfil['criado_em'])) {
    $timestamp = strtotime($usuario_perfil['criado_em']);
    $mes = $meses[(int) date('n', $timestamp)] ?? '';
    $data_ingresso = 'Ingressou em ' . $mes . ' de ' . date('Y', $timestamp);
}
?>

<div class="perfil-x">
    <div class="perfil-capa<?= $capa ? '' : ' perfil-capa-padrao' ?>"<?= $capa ? ' style="background-image:url(' . $capa . ')"' : '' ?>></div>

    <div class="perfil-acoes-topo">
        <?php if ($eh_proprio_perfil): ?>
            <a href="meu-perfil.php?editar=1" class="button botao-perfil-acao">Editar perfil</a>
        <?php else: ?>
            <button
                type="button"
                id="btn-seguir-<?= $usuario_perfil['id'] ?>"
                onclick="seguir(<?= $usuario_perfil['id'] ?>)"
                class="button botao-perfil-acao <?= ja_segue($usuario_logado['id'], $usuario_perfil['id']) ? 'is-light' : 'botao-seguir' ?>"
            >
                <?= ja_segue($usuario_logado['id'], $usuario_perfil['id']) ? 'Seguindo' : 'Seguir' ?>
            </button>
        <?php endif; ?>
    </div>

    <div class="perfil-avatar-wrap">
        <?php if (!empty($usuario_perfil['foto'])): ?>
            <img
                class="perfil-avatar"
                src="data:image/jpeg;base64,<?= base64_encode($usuario_perfil['foto']) ?>"
                alt="Foto de <?= htmlspecialchars($usuario_perfil['nome_completo']) ?>"
            >
        <?php else: ?>
            <img
                class="perfil-avatar"
                src="https://via.placeholder.com/134"
                alt="Foto de perfil"
            >
        <?php endif; ?>
    </div>

    <div class="perfil-info">
        <h1 class="perfil-nome">
            <?= htmlspecialchars($usuario_perfil['nome_completo']) ?>
        </h1>

        <p class="perfil-username">@<?= htmlspecialchars($usuario_perfil['username']) ?></p>

        <?php if (!empty($usuario_perfil['bio'])): ?>
            <p class="perfil-bio"><?= nl2br(htmlspecialchars($usuario_perfil['bio'])) ?></p>
        <?php endif; ?>

        <?php if ($data_ingresso): ?>
            <p class="perfil-meta">
                <span class="perfil-meta-icone">📅</span>
                <?= $data_ingresso ?>
            </p>
        <?php endif; ?>

        <div class="perfil-stats">
            <a href="#" class="perfil-stat">
                <strong><?= number_format($seguindo, 0, ',', '.') ?></strong>
                <span>Seguindo</span>
            </a>
            <a href="#" class="perfil-stat">
                <strong><?= number_format($seguidores, 0, ',', '.') ?></strong>
                <span>Seguidores</span>
            </a>
        </div>
    </div>
</div>

<div class="perfil-abas">
    <span class="perfil-aba ativa">Posts</span>
</div>
