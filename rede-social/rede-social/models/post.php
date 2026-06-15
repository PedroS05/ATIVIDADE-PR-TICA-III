<?php
require_once __DIR__ . '/../config/conexao.php';

function salvar_midia_post($arquivo, $pasta, $prefixo, $extencoes){
    if (!$arquivo || $arquivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $extencoes, true)) {
        return null;
    }

    $dir = __DIR__ . '/../public/uploads/' . $pasta;

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $nome = uniqid($prefixo, true) . '.' . $ext;

    if (move_uploaded_file($arquivo['tmp_name'], $dir . '/' . $nome)) {
        return 'public/uploads/' . $pasta . '/' . $nome;
    }

    return null;
}

function criar_post($usuario_id, $conteudo, $video_arquivo = null, $imagem_arquivo = null){
    global $conexao;

    $usuario_id = intval($usuario_id);
    $conteudo = mysqli_real_escape_string($conexao, $conteudo ?? '');

    $imagem_path = salvar_midia_post(
        $imagem_arquivo,
        'imagens',
        'imagem_',
        ['jpg', 'jpeg', 'png', 'gif', 'webp']
    );

    $video_path = salvar_midia_post(
        $video_arquivo,
        'videos',
        'video_',
        ['mp4', 'webm', 'ogg', 'mov']
    );

    $imagem_sql = $imagem_path
        ? "'" . mysqli_real_escape_string($conexao, $imagem_path) . "'"
        : "NULL";

    $video_sql = $video_path
        ? "'" . mysqli_real_escape_string($conexao, $video_path) . "'"
        : "NULL";

    $sql = "INSERT INTO posts (usuario_id, conteudo, imagem, video)
            VALUES ($usuario_id, '$conteudo', $imagem_sql, $video_sql)";

    return mysqli_query($conexao, $sql);
}

function feed($usuario_id){
    global $conexao;

    $usuario_id = intval($usuario_id);

    $sql = "
    SELECT p.*, u.nome_completo, u.username, u.foto
    FROM posts p
    JOIN usuarios u ON u.id = p.usuario_id
    WHERE p.usuario_id = $usuario_id
    OR p.usuario_id IN (
        SELECT seguindo_id FROM seguidores WHERE seguidor_id = $usuario_id
    )
    ORDER BY p.criado_em DESC";

    return mysqli_query($conexao,$sql);
}

function toggle_curtida($usuario_id,$post_id){
    global $conexao;

    $usuario_id = intval($usuario_id);
    $post_id = intval($post_id);

    $sql = "SELECT * FROM curtidas 
            WHERE usuario_id=$usuario_id AND post_id=$post_id";

    $res = mysqli_query($conexao,$sql);

    if(mysqli_num_rows($res)>0){
        mysqli_query($conexao,"DELETE FROM curtidas 
        WHERE usuario_id=$usuario_id AND post_id=$post_id");
    }else{
        mysqli_query($conexao,"INSERT INTO curtidas 
        (usuario_id,post_id) VALUES ($usuario_id,$post_id)");
    }
}

function total_curtidas($post_id){
    global $conexao;

    $post_id = intval($post_id);

    $sql = "SELECT COUNT(*) total FROM curtidas WHERE post_id=$post_id";
    $res = mysqli_query($conexao,$sql);

    $d = mysqli_fetch_assoc($res);
    return $d['total'];
}

function contar_posts($usuario_id){
    global $conexao;

    $usuario_id = intval($usuario_id);
    $sql = "SELECT COUNT(*) total FROM posts WHERE usuario_id = $usuario_id";
    $res = mysqli_query($conexao, $sql);
    $d = mysqli_fetch_assoc($res);

    return (int) $d['total'];
}
?>
