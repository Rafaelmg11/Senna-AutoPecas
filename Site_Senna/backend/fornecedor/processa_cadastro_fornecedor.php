<?php
session_start();
require_once '../../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $insc_estadual = $_POST['insc_estadual'];
    $endereco = $_POST['endereco'];

    try {
        $pdo->beginTransaction();

        $sql_fornecedor = "INSERT INTO fornecedor 
            (nome, telefone, email, endereco, insc_estadual, cnpj) 
            VALUES (:nome, :telefone, :email, :endereco, :insc_estadual, :cnpj)";

        $stmt = $pdo->prepare($sql_fornecedor);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":endereco", $endereco);
        $stmt->bindParam(":insc_estadual", $insc_estadual);
        $stmt->bindParam(":cnpj", $cnpj);
        $stmt->execute();

        $pdo->commit();
        echo "<script>alert('Fornecedor cadastrado com sucesso!'); window.location.href='cadastrar_fornecedor.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    }
}
?>