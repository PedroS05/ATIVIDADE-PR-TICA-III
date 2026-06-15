<?php
session_start();
require_once "../models/post.php";

if (!isset($_SESSION['usuario'])) {
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];
$post_id = intval($_POST['post_id']);

toggle_curtida($usuario_id, $post_id);


echo total_curtidas($post_id);
