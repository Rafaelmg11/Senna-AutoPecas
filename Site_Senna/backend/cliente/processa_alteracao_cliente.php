<?php
    session_start();
    require_once '../../includes/conexao.php';

if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1,2])) {
    echo "<script>alert('Acesso negado!');window.location.href='main.php'</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pegando os dados do POST
    $id_cliente    = $_POST['id_cliente'] ?? null;
    $nome_cliente  = $_POST['nome_cliente'] ?? '';
    $cpf           = $_POST['cpf'] ?? '';
    $telefone      = $_POST['telefone'] ?? '';
    $endereco      = $_POST['endereco'] ?? '';

    $nome_usuario  = $_POST['nome_usuario'] ?? '';
    $email_usuario = $_POST['email'] ?? '';
    $senha         = $_POST['senha'] ?? '';
    $confirma      = $_POST['confirma_senha'] ?? '';

    if (!$id_cliente) {
        echo "<script>alert('ID do cliente não informado!'); window.history.back();</script>";
        exit();
    }

    if ($senha !== $confirma) {
        echo "<script>alert('As senhas não coincidem!'); window.history.back();</script>";
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Atualiza cliente
        $query_cliente = "UPDATE cliente 
                           SET nome_cliente = :nome_cliente, cpf = :cpf, telefone = :telefone, endereco = :endereco
                           WHERE id_cliente = :id_cliente";
        $stmt_cliente = $pdo->prepare($query_cliente);
        $stmt_cliente->execute([
            ':nome_cliente' => $nome_cliente,
            ':cpf' => $cpf,
            ':telefone' => $telefone,
            ':endereco' => $endereco,
            ':id_cliente' => $id_cliente
        ]);

        // Busca id_usuario vinculado
        $stmt = $pdo->prepare("SELECT id_usuario FROM cliente WHERE id_cliente = :id_cliente");
        $stmt->execute([':id_cliente' => $id_cliente]);
        $id_usuario = $stmt->fetchColumn();

        if (!$id_usuario) {
            echo "<script>alert('Usuário vinculado não encontrado para este cliente.'); window.history.back();</script>";
            exit();
        }

        // Atualiza usuário
        $query_usuario = "UPDATE usuario 
                          SET nome_usuario = :nome_usuario, email = :email_usuario" .
                          (!empty($senha) ? ", senha = :senha" : "") .
                          " WHERE id_usuario = :id_usuario";
        $stmt_usuario = $pdo->prepare($query_usuario);

        $params = [
            ':nome_usuario' => $nome_usuario,
            ':email_usuario' => $email_usuario,
            ':id_usuario' => $id_usuario
        ];

        if (!empty($senha)) {
            $params[':senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        $stmt_usuario->execute($params);

        $pdo->commit();
        echo "<script>alert('Cliente alterado com sucesso!'); window.location.href='../../alterar_cliente.php';</script>";

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro ao alterar cliente: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }

} else {
    echo "<script>alert('Requisição inválida!'); window.location.href='../../alterar_cliente.php';</script>";
}
