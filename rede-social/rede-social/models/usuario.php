<?php
require_once __DIR__ . '/../config/conexao.php';

function sanitizar($dado){
    return htmlspecialchars(trim($dado));
}

function criar_usuario($d){
    global $conexao;

    $nome = mysqli_real_escape_string($conexao,$d['nome_completo']);
    $user = mysqli_real_escape_string($conexao,$d['username']);
    $email = mysqli_real_escape_string($conexao,$d['email']);
    $senha = password_hash($d['senha'], PASSWORD_DEFAULT);
    $data = $d['data_nascimento'];
    $genero = $d['genero'];



    $sql = "INSERT INTO usuarios 
    (nome_completo,username,email,senha,data_nascimento,genero)
    VALUES ('$nome','$user','$email','$senha','$data','$genero')";

    return mysqli_query($conexao,$sql);
}

function autenticar_usuario($email, $senha){
    global $conexao;

    $email = mysqli_real_escape_string($conexao, trim($email));

    $sql = "SELECT * FROM usuarios WHERE email='$email'";
    $res = mysqli_query($conexao, $sql);

    $u = mysqli_fetch_assoc($res);

    if ($u && password_verify($senha, $u['senha'])) {
        return $u;
    }

    return false;
}

function atualizar_usuario($id, $d){
    global $conexao;

    $id = intval($id);

    $nome = mysqli_real_escape_string($conexao, $d['nome_completo']);
    $user = mysqli_real_escape_string($conexao, $d['username']);
    $email = mysqli_real_escape_string($conexao, $d['email']);
    $bio = mysqli_real_escape_string($conexao, $d['bio'] ?? '');
    $data = mysqli_real_escape_string($conexao, $d['data_nascimento'] ?? '');
    $genero = mysqli_real_escape_string($conexao, $d['genero'] ?? '');

    $sql = "UPDATE usuarios SET
    nome_completo='$nome',
    username='$user',
    email='$email',
    bio='$bio',
    data_nascimento='$data',
    genero='$genero'";

    if (!empty($d['senha'])) {
        $senha = password_hash($d['senha'], PASSWORD_DEFAULT);
        $senha = mysqli_real_escape_string($conexao, $senha);
        $sql .= ", senha='$senha'";
    }

    $sql .= " WHERE id=$id";

    return mysqli_query($conexao, $sql);
}

function atualizar_foto($id,$foto){
    global $conexao;

    $id = intval($id);
    $foto = mysqli_real_escape_string($conexao,$foto);

    $sql = "UPDATE usuarios SET foto='$foto' WHERE id=$id";
    return mysqli_query($conexao,$sql);
}

function atualizar_capa($id, $caminho){
    global $conexao;

    $id = intval($id);
    $caminho = mysqli_real_escape_string($conexao, $caminho);

    $sql = "UPDATE usuarios SET capa='$caminho' WHERE id=$id";
    return mysqli_query($conexao, $sql);
}

function contar_seguidores($usuario_id){
    global $conexao;

    $usuario_id = intval($usuario_id);
    $sql = "SELECT COUNT(*) total FROM seguidores WHERE seguindo_id = $usuario_id";
    $res = mysqli_query($conexao, $sql);
    $d = mysqli_fetch_assoc($res);

    return (int) $d['total'];
}

function contar_seguindo($usuario_id){
    global $conexao;

    $usuario_id = intval($usuario_id);
    $sql = "SELECT COUNT(*) total FROM seguidores WHERE seguidor_id = $usuario_id";
    $res = mysqli_query($conexao, $sql);
    $d = mysqli_fetch_assoc($res);

    return (int) $d['total'];
}

function buscar_usuarios($busca, $usuario_logado_id){
    global $conexao;

    $busca = mysqli_real_escape_string($conexao, $busca);
    $usuario_logado_id = intval($usuario_logado_id);

    $sql = "SELECT id, nome_completo, username, foto
            FROM usuarios
            WHERE (nome_completo LIKE '%$busca%' 
               OR username LIKE '%$busca%')
            AND id != $usuario_logado_id";

    return mysqli_query($conexao, $sql);
}

function seguir($seguidor,$seguindo){
    global $conexao;

    $seguidor = intval($seguidor);
    $seguindo = intval($seguindo);

    $sql = "INSERT IGNORE INTO seguidores (seguidor_id,seguindo_id)
            VALUES ($seguidor,$seguindo)";

    return mysqli_query($conexao,$sql);
}


function ja_segue($seguidor, $seguindo){
    global $conexao;

    $seguidor = intval($seguidor);
    $seguindo = intval($seguindo);

    $sql = "SELECT * FROM seguidores 
            WHERE seguidor_id = $seguidor 
            AND seguindo_id = $seguindo";

    $res = mysqli_query($conexao, $sql);

    return mysqli_num_rows($res) > 0;
}

function deixar_de_seguir($seguidor, $seguindo){
    global $conexao;

    $seguidor = intval($seguidor);
    $seguindo = intval($seguindo);

    $sql = "DELETE FROM seguidores 
            WHERE seguidor_id = $seguidor 
            AND seguindo_id = $seguindo";

    return mysqli_query($conexao, $sql);
}

function buscar_usuario_por_email($email){
    global $conexao;

    $email = mysqli_real_escape_string($conexao, trim($email));
    $sql = "SELECT * FROM usuarios WHERE email='$email'";
    $res = mysqli_query($conexao, $sql);

    return mysqli_fetch_assoc($res);
}

function criar_token_recuperacao($usuario_id){
    global $conexao;

    $usuario_id = intval($usuario_id);
    $token = bin2hex(random_bytes(32));
    $token_sql = mysqli_real_escape_string($conexao, $token);

    mysqli_query($conexao, "DELETE FROM recuperacao_senha WHERE usuario_id = $usuario_id");

    $sql = "INSERT INTO recuperacao_senha (usuario_id, token, expira_em)
            VALUES ($usuario_id, '$token_sql', DATE_ADD(NOW(), INTERVAL 1 HOUR))";

    if (mysqli_query($conexao, $sql)) {
        return $token;
    }

    return false;
}

function validar_token_recuperacao($token){
    global $conexao;

    $token = mysqli_real_escape_string($conexao, trim($token));

    $sql = "SELECT r.*, u.email
            FROM recuperacao_senha r
            JOIN usuarios u ON u.id = r.usuario_id
            WHERE r.token = '$token'
            AND r.expira_em > NOW()";

    $res = mysqli_query($conexao, $sql);

    return mysqli_fetch_assoc($res);
}

function redefinir_senha_por_token($token, $nova_senha){
    global $conexao;

    $recuperacao = validar_token_recuperacao($token);

    if (!$recuperacao) {
        return false;
    }

    $usuario_id = intval($recuperacao['usuario_id']);
    $senha = password_hash($nova_senha, PASSWORD_DEFAULT);
    $senha = mysqli_real_escape_string($conexao, $senha);
    $token_sql = mysqli_real_escape_string($conexao, $token);

    mysqli_query($conexao, "UPDATE usuarios SET senha='$senha' WHERE id=$usuario_id");
    mysqli_query($conexao, "DELETE FROM recuperacao_senha WHERE token='$token_sql'");

    return true;
}

function senha_valida($senha){
    return preg_match('/^(?=.*[A-Z])(?=.*\d).{6,}$/', $senha);
}
?>