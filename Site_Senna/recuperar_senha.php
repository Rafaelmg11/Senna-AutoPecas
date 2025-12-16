<?php
session_start();
require_once 'includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? null;
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';

    if ($nova_senha !== $confirma) {
        echo "<script>alert('As senhas não coincidem!'); window.history.back();</script>";
        exit();
    }

    if ($email) {
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email AND senha_temporaria = 1");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuario SET senha = :senha, senha_temporaria = 0 WHERE id_usuario = :id");
            $stmt->execute([
                ':senha' => $hash,
                ':id' => $usuario['id_usuario']
            ]);

            echo "<script>alert('Senha alterada com sucesso! Faça login.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Email não encontrado ou senha não temporária.'); window.location.href='esqueci_senha.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Recuperar senha</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h2>Recuperar Senha</h2>
<form method="POST" action="">
    <label for="email">Digite seu e-mail:</label>
    <input type="email" name="email" id="email" required>

    <label for="nova_senha">Nova senha:</label>
    <input type="password" name="nova_senha" id="nova_senha" required>

    <label for="confirma_senha">Confirme a nova senha:</label>
    <input type="password" name="confirma_senha" id="confirma_senha" required>

    <button type="submit">Alterar senha</button>
</form>
<a href="index.php">Voltar ao login</a>
</body>
</html>
