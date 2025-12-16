<?php
session_start();
require_once 'includes/conexao.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = '';

// Verifica se o usuário está na etapa de redefinição
if (!isset($_SESSION['redef_usuario']) || !isset($_SESSION['redef_codigo'])) {
    header("Location: esqueci_senha.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    $nova = $_POST['nova_senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';

    if ($codigo != $_SESSION['redef_codigo']) {
        $msg = "Código inválido!";
    } elseif ($nova !== $confirma) {
        $msg = "As senhas não coincidem!";
    } else {
        // Atualiza a senha
        $hash = password_hash($nova, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuario SET senha = :senha, senha_temporaria = 0 WHERE id_usuario = :id");
        $stmt->execute([
            ':senha' => $hash,
            ':id' => $_SESSION['redef_usuario']
        ]);

        // Limpa sessão
        unset($_SESSION['redef_usuario'], $_SESSION['redef_codigo'], $_SESSION['redef_email']);

        echo "<script>alert('Senha redefinida com sucesso!'); window.location.href='index.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Redefinir Senha</title>
<link rel="stylesheet" href="css/login_style.css">
</head>
<body>
<div class="container_pagina_login">
    <div class="pagina_login_container">
        <div class="container_informacoes_login">
            <img src="img/logo_branca.png" alt="Logo">
        </div>

        <div class="container_login">
            <div class="formulario">
                <h2>Redefinir Senha</h2>

                <?php if($msg) echo "<p style='color:red; text-align:center;'>$msg</p>"; ?>

                <form method="POST" class="form_redefinir_senha">
                    <label>Código de Redefinição:</label>
                    <input type="text" name="codigo" required>

                    <label>Nova Senha:</label>
                    <input type="password" name="nova_senha" required>

                    <label>Confirme a Nova Senha:</label>
                    <input type="password" name="confirma_senha" required>

                    <button type="submit" class="gerar_redefinir_senha">Redefinir Senha</button>
                                    <p style="text-align:center;">
                    <a href="index.php" style="font-size: 15px; margin-top: 30px; margin-left: -30px;">Voltar ao Login</a>
                    </p>
                </form>


            </div>
        </div>
    </div>
</div>
</body>
</html>
