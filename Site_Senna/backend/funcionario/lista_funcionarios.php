<?php
    require_once 'includes/conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='principal.php'; </script>";
    }

    $query  = "SELECT f.*, u.id_usuario, u.email FROM funcionario AS f INNER JOIN usuario AS u ON u.id_usuario = f.id_usuario";

    $stmt = $pdo -> prepare($query);
    $stmt -> execute();
    $funcionarios = $stmt -> fetchAll();

    return $funcionarios;
?>