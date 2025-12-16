<?php
    require_once 'conexao.php';

    $stmt = $pdo -> prepare("SELECT * FROM fornecedor");
    $stmt -> execute();
    $fornecedores = $stmt -> fetchAll();

    return $fornecedores;
?>