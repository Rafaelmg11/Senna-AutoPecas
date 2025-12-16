<?php
    require_once 'includes/conexao.php';

    $stmt = $pdo -> prepare("SELECT * FROM perfil");
    $stmt -> execute();
    $perfis = $stmt -> fetchAll();

    return $perfis;
?>