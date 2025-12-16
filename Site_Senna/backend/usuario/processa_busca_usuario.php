<?php
    session_start();

    require_once '../../includes/conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='../principal.php'; </script>";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $busca = $_POST['busca'];

        if (is_numeric($busca)) {
            $query = "SELECT * FROM usuario WHERE id_usuario = :id";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":id", $busca, PDO::PARAM_INT);
        } else {
            $query = "SELECT * FROM usuario WHERE nome_usuario LIKE :nome";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindValue(":nome", "$busca%", PDO::PARAM_STR);
        }
        
        try {
            $stmt -> execute();
            
            $usuario = $stmt -> fetch();

            if ($usuario) {
                ?>
                <head>
                    <link rel="stylesheet" href="../../styles.css">
                </head>

                <?php include_once '../../includes/navbar.php'; ?>

                <table border style="text-align: center;">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                    
                    <tr>
                        <td><?= htmlspecialchars($usuario['id_usuario'])?></td>
                        <td><?= htmlspecialchars($usuario['nome_usuario'])?></td>
                        <td><?= htmlspecialchars($usuario['email'])?></td>
                        <td><?= htmlspecialchars($usuario['id_perfil'])?></td>

                        <td>
                            <a href="../../alterar_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>">Alterar</a>
                            <a href="processa_exclusao_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>">Excluir</a>
                        </td>
                    </tr>
                </table>
                
                <a href="../../buscar_usuario.php">Todos os usuários</a>
            <?php
            } else {
                echo "<script> alert('Usuário não encontrado!'); window.location.href='../buscar_usuario.php'; </script>";
            }
            
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao buscar usuário!'); window.location.href='../buscar_usuario.php'; </script>";
            error_log("Erro: ".$e -> getMessage());
        }
    }
?>