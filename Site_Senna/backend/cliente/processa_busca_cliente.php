<?php
    session_start();

    require_once '../../includes/conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='../principal.php'; </script>";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $busca = $_POST['busca'];

        if (is_numeric($busca)) {
            $query = "SELECT c.*, u.id_usuario, u.email FROM cliente AS c INNER JOIN usuario AS u ON u.id_usuario = c.id_usuario WHERE id_cliente = :id";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":id", $busca, PDO::PARAM_INT);
        } else {
            $query = "SELECT c.*, u.id_usuario, u.email FROM cliente AS c INNER JOIN usuario AS u ON u.id_usuario = c.id_usuario WHERE nome_cliente LIKE :nome";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindValue(":nome", "$busca%", PDO::PARAM_STR);
        }
        
        try {
            $stmt -> execute();
            
            $cliente = $stmt -> fetch();

            if ($cliente) {
                ?>
                <?php include_once '../includes/navbar.php'; ?>

                <head>
                    <link rel="stylesheet" href="../../styles.css">
                </head>

                <table border style="text-align: center;">
                    <tr>
                        <th>ID Cliente</th>
                        <th>Usuário Vinculado</th>
                        <th>Nome</th>
                        <th>Endereco</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>

                    <tr>
                        <td><?= htmlspecialchars($cliente['id_cliente']) ?></td>
                        <td>ID: <?= htmlspecialchars($cliente['id_usuario']) ?> - <?= htmlspecialchars($cliente['email']) ?></td>
                        <td><?= htmlspecialchars($cliente['nome_cliente']) ?></td>
                        <td><?= htmlspecialchars($cliente['endereco']) ?></td>
                        <td><?= htmlspecialchars($cliente['cpf']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefone']) ?></td>

                        <td>
                            <a href="../../alterar_cliente.php?id=<?= htmlspecialchars($cliente['id_cliente']) ?>">Editar</a>
                            <a href="processa_exclusao_cliente.php?id=<?= htmlspecialchars($cliente['id_cliente']) ?>">Excluir</a>
                        </td>
                    </tr>
                </table>
                
                <a href="../buscar_cliente.php">Todos os clientes</a>
            <?php
            } else {
                echo "<script> alert('Cliente não encontrado!'); window.location.href='../buscar_cliente.php'; </script>";
            }
            
        } catch (PDOException $e) {
            // echo "<script> alert('Erro ao buscar cliente!'); window.location.href='../buscar_cliente.php'; </script>";
            echo $e->getMessage();
            error_log("Erro: ".$e -> getMessage());
        }
    }
?>