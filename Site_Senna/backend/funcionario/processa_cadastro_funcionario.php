<?php
session_start();
require_once '../../include/conexao.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_funcionario = $_POST['nome_funcionario'];
    $cargo = $_POST['cargo'];
    $salario = $_POST['salario'];
    $endereco = $_POST['endereco'];
    $imagem = $_POST['imagem'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];

    try {
        $pdo->beginTransaction();

        //Inserir usuário (perfil FUNCIONARIO)
        $nome_usuario = $nome_funcionario;
        $email = strtolower(str_replace(' ', '', $nome_funcionario)) . "@exemplo.com";
        $senha = password_hash(" ", PASSWORD_DEFAULT);
        $id_perfil = 2; // perfil FUNCIONARIO

        $sqlUsuario = "INSERT INTO usuario (nome_usuario, email, senha, id_perfil)
                   VALUES (:nome_usuario, :email, :senha, :id_perfil)";
        $stmt = $pdo->prepare($sqlUsuario);
        $stmt->bindParam(":nome_usuario", $nome_usuario);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":senha", $senha);
        $stmt->bindParam(":id_perfil", $id_perfil);
        $stmt->execute();

        $id_usuario = $pdo->lastInsertId(); // pega o id do usuário inserido

        // Inserir funcionário usando o id_usuario correto
        $sqlFuncionario = "INSERT INTO funcionario 
        (id_usuario, nome_funcionario, cargo, salario, endereco, imagem, cpf, telefone) 
        VALUES (:id_usuario, :nome_funcionario, :cargo, :salario, :endereco, :imagem, :cpf, :telefone)";

        $stmt = $pdo->prepare($sqlFuncionario);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->bindParam(":nome_funcionario", $nome_funcionario);
        $stmt->bindParam(":cargo", $cargo);
        $stmt->bindParam(":salario", $salario);
        $stmt->bindParam(":endereco", $endereco);
        $stmt->bindParam(":imagem", $imagem); // se for upload, precisa de $_FILES
        $stmt->bindParam(":cpf", $cpf);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->execute();

        $pdo->commit();
        echo "<script>alert('Funcionário cadastrado com sucesso!'); window.location.href='cadastrar_funcionario.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro: " . $e->getMessage() . "');</script>";
    }
}

?>