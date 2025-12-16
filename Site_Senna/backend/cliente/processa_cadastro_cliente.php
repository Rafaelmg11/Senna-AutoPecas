<?php

session_start();
require_once '../../includes/conexao.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_cliente = $_POST['nome_cliente'];
    $endereco     = $_POST['endereco'];
    $cpf          = $_POST['cpf'];
    $telefone     = $_POST['telefone'];


    try {
        $pdo->beginTransaction();

        // Inserir usuário
        $email = strtolower(str_replace(' ', '', $nome_cliente)) . "@exemplo.com"; // exemplo
        $senha = password_hash(" ", PASSWORD_DEFAULT); // senha padrão
        $id_perfil = 2; // perfil cliente

        $sqlUsuario = "INSERT INTO usuario (nome_usuario, email, senha, id_perfil) 
                VALUES (:nome_usuario, :email, :senha, 5)";
        $stmt = $pdo->prepare($sqlUsuario);
        $stmt->bindParam(":nome_usuario", $nome_cliente);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":senha", $senha);
        $stmt->execute();

        $id_usuario = $pdo->lastInsertId();

        // Inserir cliente
        $sqlCliente = "INSERT INTO cliente (id_usuario, nome_cliente, endereco, cpf, telefone)   
                VALUES (:id_usuario, :nome_cliente, :endereco, :cpf, :telefone)";
        $stmt = $pdo->prepare($sqlCliente);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->bindParam(":nome_cliente", $nome_cliente);
        $stmt->bindParam(":endereco", $endereco);
        $stmt->bindParam(":cpf", $cpf);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->execute();

        //Criar carrinho para o cliente
        $sqlCarrinho = "INSERT INTO carrinho (id_usuario) VALUES (:id_usuario)";
        $stmtCarrinho = $pdo->prepare($sqlCarrinho);
        $stmtCarrinho->bindParam(':id_usuario', $idUsuario);
        $stmtCarrinho->execute();


        $pdo->commit();
        echo "<script>alert('Cliente cadastrado com sucesso!'); window.location.href='cadastro_cliente.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    }
}
?>