<?php
    session_start();
    require_once '../../includes/conexao.php';

if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1,2])) {
    echo "<script>alert('Acesso negado!');window.location.href='main.php'</script>";
    exit();
}


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_fornecedor = $_POST['id_fornecedor'];
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        $cnpj = $_POST['cnpj'];
        $insc_estadual = $_POST['insc_estadual'];
        $endereco = $_POST['endereco'];

        $query = "UPDATE fornecedor SET nome = :nome, email = :email, cnpj = :cnpj, telefone = :telefone, insc_estadual = :insc_estadual, endereco = :endereco WHERE id_fornecedor = :id_fornecedor";

        $stmt = $pdo -> prepare($query);

        $stmt -> bindParam(":nome", $nome, PDO::PARAM_STR);
        $stmt -> bindParam("telefone", $telefone, PDO::PARAM_STR);
        $stmt -> bindParam(":email", $email, PDO::PARAM_STR);
        $stmt -> bindParam("cnpj", $cnpj, PDO::PARAM_STR);
        $stmt -> bindParam("insc_estadual", $insc_estadual, PDO::PARAM_STR);
        $stmt -> bindParam("endereco", $endereco, PDO::PARAM_STR);
        $stmt -> bindParam("id_fornecedor", $id_fornecedor, PDO::PARAM_INT);

        try {
            $stmt -> execute();

            echo "<script> alert('Fornecedor alterado com sucesso!'); window.location.href='../../alterar_fornecedor.php'; </script>";
        } catch (PDOException $e) {
            // echo "<script> alert('Erro ao alterar fornecedor!'); window.location.href='../../alterar_fornecedor.php'; </script>";
            echo $e->getMessage();
        }
    } else {
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
    
            $query = "SELECT * FROM cliente WHERE id_cliente = :id";
    
            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":id", $id, PDO::PARAM_INT);
    
            $stmt -> execute();
    
            $cliente = $stmt -> fetch();
    
            if (!$cliente) {
                echo "<script> alert('Fornecedor não encontrado!'); </script>";
            }
        }
    }
?>