<?php
    require_once 'includes/conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='principal.php'; </script>";
    }

    $stmt = $pdo -> prepare("SELECT p.*, f.nome AS 'nome_fornecedor', f.id_fornecedor FROM peca AS p INNER JOIN fornecedor AS f WHERE p.id_fornecedor = f.id_fornecedor");
    $stmt -> execute();
    $pecas = $stmt -> fetchAll();

    return $pecas;
?>