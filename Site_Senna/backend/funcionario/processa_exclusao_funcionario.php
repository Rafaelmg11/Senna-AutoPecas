<?php
session_start();
require_once '../../includes/conexao.php';

if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1])) {
    echo "<script>alert('Acesso negado!');window.location.href='main.php'</script>";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']); // pega o ID digitado

    try {
        $pdo->beginTransaction();

        $sql = "DELETE FROM funcionario WHERE id_funcionario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, type: PDO::PARAM_INT);

        if ($stmt->execute()) {
            $pdo->commit();
            echo "<script>alert('funcionario excluído com sucesso!'); window.location.href='../../excluir_funcionario.php';</script>";
        } else {
            $pdo->rollBack();
            echo "<script>alert('Erro ao excluir funcionario!');</script>";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    }
} else {
    if(!empty($_GET['id'])) {
        $id = $_GET['id'];

        $query = "DELETE FROM funcionario WHERE id_funcionario = :id";

        $stmt = $pdo -> prepare($query);
        $stmt -> bindParam(":id", $id, PDO::PARAM_INT);

        try {
            $stmt -> execute();
            echo "<script> alert('Funcionário excluído com sucesso!'); window.location.href='../../buscar_funcionario.php'; </script>";
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao excluir funcionário!'); window.location.href='../../excluir_funcionario.php'; </script>";
            error_log("Erro: " . $e -> getMessage());
        }
    }
}
?>