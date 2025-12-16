<?php
session_start();
require_once '../../includes/conexao.php';

if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1,2])) {
    echo "<script>alert('Acesso negado!');window.location.href='main.php'</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_funcionario = $_POST['id_funcionario'] ?? null;
    $nome           = $_POST['nome'] ?? '';
    $cargo          = $_POST['cargo'] ?? '';
    $salario        = $_POST['salario'] ?? '';
    $endereco       = $_POST['endereco'] ?? '';
    $cpf            = $_POST['cpf'] ?? '';
    $telefone       = $_POST['telefone'] ?? '';
    $data_nascimento= $_POST['nascimento'] ?? '';
    $data_admissao  = $_POST['admissao'] ?? '';
    $foto           = $_FILES['foto'] ?? null;

    if (!$id_funcionario) {
        echo "<script>alert('ID do funcionário não informado!'); window.history.back();</script>";
        exit();
    }

    // Upload da imagem
    $nome_arquivo = null;
    if ($foto && $foto['tmp_name']) {
        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

        // Validação simples de extensão
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $ext_permitidas)) {
            echo "<script>alert('Extensão de imagem inválida! Use JPG, PNG ou GIF.'); window.history.back();</script>";
            exit();
        }

        $nome_arquivo = uniqid('foto_') . '.' . $ext;
        move_uploaded_file($foto['tmp_name'], "../../uploads/" . $nome_arquivo);
    }

    try {
        // Atualizar funcionário
        $query = "UPDATE funcionario 
                  SET nome_funcionario = :nome, cargo = :cargo, salario = :salario,
                      endereco = :endereco, cpf = :cpf, telefone = :telefone,
                      data_nascimento = :nascimento, data_admissao = :admissao"
                  . ($nome_arquivo ? ", imagem = :imagem" : "") .
                  " WHERE id_funcionario = :id_funcionario";

        $stmt = $pdo->prepare($query);

        $params = [
            ':nome' => $nome,
            ':cargo' => $cargo,
            ':salario' => $salario,
            ':endereco' => $endereco,
            ':cpf' => $cpf,
            ':telefone' => $telefone,
            ':nascimento' => $data_nascimento,
            ':admissao' => $data_admissao,
            ':id_funcionario' => $id_funcionario
        ];

        if ($nome_arquivo) {
            $params[':imagem'] = $nome_arquivo;
        }

        $stmt->execute($params);

        // Buscar id_usuario vinculado ao funcionário
        $query = "SELECT id_usuario FROM funcionario WHERE id_funcionario = :id";
        $stmtUser = $pdo->prepare($query);
        $stmtUser->bindParam(':id', $id_funcionario, PDO::PARAM_INT);
        $stmtUser->execute();
        $id_usuario = $stmtUser->fetchColumn(); // já retorna o valor

        // Atualizar foto do usuário apenas se houve upload
        if ($nome_arquivo && $id_usuario) {
            $query = "UPDATE usuario 
                      SET imagem_usuario = :foto 
                      WHERE id_usuario = :id_usuario";

            $stmtFoto = $pdo->prepare($query);
            $stmtFoto->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
            $stmtFoto->bindValue(":foto", $nome_arquivo);
            $stmtFoto->execute();
        }

        echo "<script>alert('Funcionário alterado com sucesso!'); window.location.href='../../alterar_funcionario.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Erro ao alterar funcionário: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Requisição inválida!'); window.location.href='../../alterar_funcionario.php';</script>";
}
?>
