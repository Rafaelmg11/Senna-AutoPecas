<?php
session_start();
require_once '../../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']); // pega o ID digitado

    try {
        $pdo->beginTransaction();

        $sql = "DELETE FROM usuario WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $pdo->commit();
            echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='../../buscar_usuario.php';</script>";
        } else {
            $pdo->rollBack();
            echo "<script>alert('Erro ao excluir usuário!');</script>";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    }
} else {
    if(!empty($_GET['id'])) {
        $id = $_GET['id'];

        $query = "DELETE FROM usuario WHERE id_usuario = :id";

        $stmt = $pdo -> prepare($query);
        $stmt -> bindParam(":id", $id, PDO::PARAM_INT);

        try {
            $stmt -> execute();
            echo "<script> alert('Usuário excluído com sucesso!'); window.location.href='../../buscar_usuario.php'; </script>";
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao excluir usuário!'); window.location.href='../../excluir_usuario.php'; </script>";
            error_log("Erro: " . $e -> getMessage());
        }
    }
}
?>