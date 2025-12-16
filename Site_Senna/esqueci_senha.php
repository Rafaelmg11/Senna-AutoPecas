<?php
session_start();
require_once 'includes/conexao.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = '';
$codigo_gerado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $msg = "Usuário não encontrado!";
    } else {
        // Gera código único de 6 dígitos
        $codigo = rand(100000, 999999);

        // Salva na sessão
        $_SESSION['redef_email'] = $email;
        $_SESSION['redef_codigo'] = $codigo;
        $_SESSION['redef_usuario'] = $usuario['id_usuario'];

        // Mostra o código na tela (apenas para desenvolvimento local)
        $codigo_gerado = $codigo;

        // Redireciona após 5 segundos para a página de redefinir senha
        header("Refresh:5; url=redefinir_senha.php");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Esqueci a Senha</title>
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
                <h2>Recuperação de Senha</h2> 
                <?php if($msg) echo "<p style='color:red; text-align:center;'>$msg</p>"; ?>
                
                <?php if($codigo_gerado): ?>
                    <p style="color:green; text-align:center;">
                        Código de redefinição gerado: <strong><?php echo $codigo_gerado; ?></strong>
                    </p>
                    <p style="text-align:center;">Você será redirecionado para a página de redefinir senha em 5 segundos...</p>
                <?php else: ?>
                    <form method="POST"> <br>
                        <label>E-mail:</label>
                        <input type="email" name="email" required>
                        <button type="submit" class="gerar_redefinir_senha">Gerar Código</button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
</body>
</html>
