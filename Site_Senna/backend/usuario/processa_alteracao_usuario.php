<?php
    session_start();

    require_once '../../includes/conexao.php';
if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1])) {
    echo "<script>alert('Acesso negado!');window.location.href='main.php'</script>";
    exit();
}

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_usuario = $_POST['id_usuario'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $perfil = $_POST['id_perfil'];
        $nova_senha = !empty($_POST['nova_senha']) ? password_hash($_POST['nova_senha'], PASSWORD_DEFAULT) : null;

        // ATUALIZA OS DADOS DO USUÁRIO
        if($nova_senha) {
            $query = "UPDATE usuario SET nome_usuario = :nome, email = :email, id_perfil = :id_perfil, senha = :senha WHERE id_usuario = :id_usuario";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":senha", $nova_senha);
        } else {
            $query = "UPDATE usuario SET nome_usuario = :nome, email = :email, id_perfil = :id_perfil WHERE id_usuario = :id_usuario";

            $stmt = $pdo -> prepare($query);
        }

        $stmt -> bindParam(":nome", $nome, PDO::PARAM_STR);
        $stmt -> bindParam(":email", $email, PDO::PARAM_STR);
        $stmt -> bindParam(":id_perfil", $perfil, PDO::PARAM_INT);
        $stmt -> bindParam("id_usuario", $id_usuario, PDO::PARAM_INT);

        try {
            $stmt -> execute();

            echo "<script> alert('Usuário alterado com sucesso!'); window.location.href='../../buscar_usuario.php'; </script>";
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao alterar usuário!'); window.location.href='../../alterar_usuario.php'; </script>";
        }
    }
?>