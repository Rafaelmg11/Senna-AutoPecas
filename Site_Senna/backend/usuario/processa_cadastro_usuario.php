<?php
session_start();
require_once '../../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_usuario = $_POST['nome_usuario'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $id_perfil = $_POST['perfil'];

    try {
        $pdo->beginTransaction();

        $sql_usuario = "INSERT INTO usuario (nome_usuario, email, senha, id_perfil)
                       VALUES (:nome_usuario, :email, :senha, :id_perfil)";
        $stmt = $pdo->prepare($sql_usuario);
        $stmt->bindParam(":nome_usuario", $nome_usuario);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":senha", $senha);
        $stmt->bindParam(":id_perfil", $id_perfil);
        $stmt->execute();

        $pdo->commit();
        echo "<script>alert('Usuário cadastrado com sucesso!'); window.location.href='../../cadastrar_usuario.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    }
}
?>