<?php
    require_once 'includes/conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='principal.php'; </script>";
    }

    $stmt = $pdo -> prepare("SELECT usuario.*, perfil.* FROM usuario INNER JOIN perfil WHERE usuario.id_perfil = perfil.id_perfil");
    $stmt -> execute();
    $usuarios = $stmt -> fetchAll();

    return $usuarios;
?>