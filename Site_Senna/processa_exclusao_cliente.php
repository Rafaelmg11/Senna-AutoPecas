<?php
session_start();
require_once 'includes/conexao.php';


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo "<script>alert('ID inválido!'); window.location.href='excluir_cliente.php';</script>";
    exit();
}

try {
    $pdo->beginTransaction();

    // Buscar id_usuario vinculado
    $stmtBusca = $pdo->prepare("SELECT id_usuario FROM cliente WHERE id_cliente = :id");
    $stmtBusca->execute([':id' => $id]);
    $row = $stmtBusca->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $pdo->rollBack();
        echo "<script>alert('Cliente não encontrado!'); window.location.href='excluir_cliente.php';</script>";
        exit();
    }

    // Excluir cliente
    $stmtDelCli = $pdo->prepare("DELETE FROM cliente WHERE id_cliente = :id");
    $stmtDelCli->execute([':id' => $id]);

    // Se houver usuário vinculado, excluir também
    if (!empty($row['id_usuario'])) {
        $stmtDelUser = $pdo->prepare("DELETE FROM usuario WHERE id_usuario = :idu");
        $stmtDelUser->execute([':idu' => $row['id_usuario']]);
    }

    $pdo->commit();

    echo "<script>alert('Cliente excluído com sucesso!'); window.location.href='excluir_cliente.php';</script>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<script>alert('Erro ao excluir: " . addslashes($e->getMessage()) . "'); window.location.href='excluir_cliente.php';</script>";
}
