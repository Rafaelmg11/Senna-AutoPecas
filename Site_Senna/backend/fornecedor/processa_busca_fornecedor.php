<?php
    session_start();

    require_once '../../includes/conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='../principal.php'; </script>";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $busca = $_POST['busca'];

        if (is_numeric($busca)) {
            $query = "SELECT * FROM fornecedor WHERE id_fornecedor = :id";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":id", $busca, PDO::PARAM_INT);
        } else {
            $query = "SELECT * FROM fornecedor WHERE nome LIKE :nome";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindValue(":nome", "$busca%", PDO::PARAM_STR);
        }
        
        try {
            $stmt -> execute();
            
            $fornecedor = $stmt -> fetch();

            if ($fornecedor) {
                ?>
                <?php include_once '../includes/navbar.php'; ?>

                <head>
                    <link rel="stylesheet" href="../../styles.css">
                </head>

                <table border style="text-align: center;">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>CNPJ</th>
                        <th>Inscrição Estadual</th>
                        <th>Endereço</th>
                        <th>Ações</th>
                    </tr>

                    <tr>
                        <td><?= htmlspecialchars($fornecedor['id_fornecedor']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['nome']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['telefone']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['email']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['cnpj']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['insc_estadual']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['endereco']) ?></td>

                        <td>
                            <a href="../../alterar_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">Editar</a>
                            <a href="processa_exclusao_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">Excluir</a>
                        </td>
                    </tr>
                </table>
                
                <a href="../buscar_fornecedor.php">Todos os fornecedores</a>
            <?php
            } else {
                echo "<script> alert('Fornecedor não encontrado!'); window.location.href='../buscar_fornecedor.php'; </script>";
            }
            
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao buscar Fornecedor!'); window.location.href='../buscar_fornecedor.php'; </script>";
            error_log("Erro: ".$e -> getMessage());
        }
    }
?>