<?php
    session_start();

    require_once '../../includes/conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='../principal.php'; </script>";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $busca = $_POST['busca'];

        if (is_numeric($busca)) {
            $query = "SELECT f.*, u.id_usuario, u.email FROM funcionario AS f INNER JOIN usuario AS u ON u.id_usuario = f.id_usuario WHERE f.id_funcionario = :id";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":id", $busca, PDO::PARAM_INT);
        } else {
            $query = "SELECT f.*, u.id_usuario, u.email FROM funcionario AS f INNER JOIN usuario AS u ON u.id_usuario = f.id_usuario WHERE f.nome_funcionario LIKE :nome";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindValue(":nome", "$busca%", PDO::PARAM_STR);
        }
        
        try {
            $stmt -> execute();
            
            $funcionario = $stmt -> fetch();

            if ($funcionario) {
                ?>
                <?php include_once '../includes/navbar.php'; ?>

                <head>
                    <link rel="stylesheet" href="../../styles.css">
                </head>

                <table border style="text-align: center;">
            <tr>
                <th>ID Funcionário</th>
                <th>Usuário Vinculado</th>
                <th>Nome</th>
                <th>Cargo</th>
                <th>Salário</th>
                <th>Endereço</th>
                <th>CPF</th>
                <th>Telefone</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>

            <tr>
                <td><?= htmlspecialchars($funcionario['id_funcionario']) ?></td>
                <td>ID: <?= htmlspecialchars($funcionario['id_usuario']) ?> - <?= htmlspecialchars($funcionario['email']) ?></td>
                <td><?= htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                <td><?= htmlspecialchars($funcionario['cargo']) ?></td>
                <td><?= htmlspecialchars($funcionario['salario']) ?></td>
                <td><?= htmlspecialchars($funcionario['endereco']) ?></td>
                <td><?= htmlspecialchars($funcionario['cpf']) ?></td>
                <td><?= htmlspecialchars($funcionario['telefone']) ?></td>
                <td><?php echo $funcionario['imagem'] ?></td>

                <td>
                    <a href="../../alterar_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>">Editar</a>
                    <a href="processa_exclusao_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>">Excluir</a>
                </td>
            </tr>
        </table>
                
                <a href="../buscar_funcionario.php">Todos os funcionários</a>
            <?php
            } else {
                echo "<script> alert('Funcionário não encontrado!'); window.location.href='../buscar_funcionario.php'; </script>";
            }
            
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao buscar Funcionário!'); window.location.href='../buscar_funcionario.php'; </script>";
            error_log("Erro: ".$e -> getMessage());
        }
    }
?>