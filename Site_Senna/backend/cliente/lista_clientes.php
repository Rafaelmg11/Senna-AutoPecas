<?php
    require_once 'conexao.php';

    $query = "SELECT c.*, u.id_usuario, u.email FROM cliente AS c INNER JOIN usuario AS u ON u.id_usuario = c.id_usuario";

    $stmt = $pdo -> prepare($query);
    $stmt -> execute();
    $clientes = $stmt -> fetchAll();

    return $clientes;
?>