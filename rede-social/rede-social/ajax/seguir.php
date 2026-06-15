<?php
session_start();

require_once "../config/conexao.php";
require_once "../models/usuario.php";

if (!isset($_SESSION['usuario'])) {
    exit;
}

$seguidor = $_SESSION['usuario']['id'];
$seguindo = intval($_POST['usuario_id']);

if ($seguidor == $seguindo) {
    exit;
}

if (ja_segue($seguidor, $seguindo)) {

    deixar_de_seguir($seguidor, $seguindo);

    echo "nao_seguindo";

} else {

    seguir($seguidor, $seguindo);

    echo "seguindo";
}