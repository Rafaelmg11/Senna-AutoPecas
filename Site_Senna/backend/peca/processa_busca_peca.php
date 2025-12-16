<?php
    session_start();

    require_once '../../includes/conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='../principal.php'; </script>";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $busca = $_POST['busca'];

        if (is_numeric($busca)) {
            $query = "SELECT * FROM peca WHERE id_peca = :id";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":id", $busca, PDO::PARAM_INT);
        } else {
            $query = "SELECT * FROM peca WHERE nome LIKE :nome";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindValue(":nome", "$busca%", PDO::PARAM_STR);
        }
        
        try {
            $stmt -> execute();
            
            $peca = $stmt -> fetch();

            if ($peca) {
                ?>
                <?php include_once '../includes/navbar.php'; ?>

                <head>
                    <link rel="stylesheet" href="../../styles.css">
                </head>

                <table border style="text-align: center;">
            <tr>
                <th>ID</th>
                <th>Categoria</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Qtde. em estoque</th>
                <th>Lote</th>
                <th>Valor</th>
                <th>Fornecedor</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>

            <tr>
                <td><?= htmlspecialchars($peca['id_peca']) ?></td>
                <td><?= htmlspecialchars($peca['categoria']) ?></td>
                <td><?= htmlspecialchars($peca['nome']) ?></td>
                <td><?= htmlspecialchars($peca['descricao']) ?></td>
                <td><?= htmlspecialchars($peca['qtde_estoque']) ?></td>
                <td><?= htmlspecialchars($peca['lote']) ?></td>
                <td><?= htmlspecialchars($peca['valor']) ?></td>
                <td><?= htmlspecialchars($peca['id_fornecedor']) ?></td>
                <td><?php echo $peca['imagem'] ?></td>

                <td>
                    <a href="../../alterar_peca.php?id=<?= htmlspecialchars($peca['id_peca']) ?>">Editar</a>
                    <a href="processa_exclusao_peca.php?id=<?= htmlspecialchars($peca['id_peca']) ?>">Excluir</a>
                </td>
            </tr>
        </table>
                
            <a href="../buscar_peca.php">Todos as peças</a>
            <?php
            } else {
                echo "<script> alert('Peça não encontrada!'); window.location.href='../buscar_peca.php'; </script>";
            }
            
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao buscar peça!'); window.location.href='../buscar_peca.php'; </script>";
            error_log("Erro: ".$e -> getMessage());
        }
    }
?>