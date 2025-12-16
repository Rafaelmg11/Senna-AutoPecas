<?php
session_start();
require_once '../../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoria = $_POST['categoria'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $qtde_estoque = $_POST['qtde_estoque'];
    $lote = $_POST['lote'];
    $valor = $_POST['valor'];
    $id_fornecedor = $_POST['id_fornecedor'];

    // Tratamento do upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    } else {
        $imagem = null; // ou trate erro
    }

    try {
        $pdo->beginTransaction();

        $sql_peca = "INSERT INTO peca 
            (categoria, nome, descricao, qtde_estoque, lote, valor, id_fornecedor, imagem) 
            VALUES (:categoria, :nome, :descricao, :qtde_estoque, :lote, :valor, :id_fornecedor, :imagem)";

        $stmt = $pdo->prepare($sql_peca);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->bindParam(":qtde_estoque", $qtde_estoque);
        $stmt->bindParam(":lote", $lote);
        $stmt->bindParam(":valor", $valor);
        $stmt->bindParam(":id_fornecedor", $id_fornecedor);
        $stmt->bindParam(":imagem", $imagem, PDO::PARAM_LOB);

        $stmt->execute(); // <-- faltava isso

        $pdo->commit();
        echo "<script>alert('Peça cadastrada com sucesso!'); window.location.href='cadastrar_peca.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    }
}
?>