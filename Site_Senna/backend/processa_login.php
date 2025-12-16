<?php
    session_start(); // Inicializa a variável superglobal '$_SESSION', aquilo que carrega as sessões salvas na página

    require_once '../includes/conexao.php'; // Requer o arquivo de conexão via PDO

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Se o pedido que foi requisitado veio por 'POST'
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $query = "SELECT * FROM usuario WHERE email LIKE :email";

        $stmt = $pdo -> prepare($query);
        $stmt -> bindParam(":email", $email, PDO::PARAM_STR);
        $stmt -> execute();
        $usuario = $stmt -> fetch();

        if ($usuario) {
            if ($usuario['email'] == 'adm@master') {
                if ($senha == $usuario['senha']) {
                    if ($usuario['senha_temporaria'] == TRUE) {
                        header("Location: recuperar_senha.php");
                        exit();
                    } else {
                        $_SESSION['perfil'] = $usuario['id_perfil'];
                        $_SESSION['id_usuario'] = $usuario['id_usuario'];
                        $_SESSION['usuario'] = $usuario['nome_usuario'];

                        echo "<script> alert('Login bem-sucedido! Seja bem-vindo!'); window.location.href='../principal.php'; </script>";
                    }
                } else {
                    echo "<script> alert('Senha incorreta! Insira novamente sua senha.'); window.location.href='../index.php'; </script>";
                }
            } else {
                if (password_verify($senha, $usuario['senha'])) {
                    if ($usuario['senha_temporaria'] == TRUE) {
                        header("Location: recuperar_senha.php");
                        exit();
                    } else {
                        $_SESSION['perfil'] = $usuario['id_perfil'];
                        $_SESSION['id_usuario'] = $usuario['id_usuario'];
                        $_SESSION['usuario'] = $usuario['nome_usuario'];
            
                        echo "<script> alert('Login bem-sucedido! Seja bem-vindo!'); window.location.href='../principal.php'; </script>";
                    }
                } else {
                    echo "<script> alert('Senha incorreta! Insira novamente sua senha.'); window.location.href='../index.php'; </script>";
                }
            }
        } else {
            echo "<script> alert('Usuário não encontrado! Verifique seu e-mail.'); window.location.href='../index.php'; </script>";
        }
    }
?>