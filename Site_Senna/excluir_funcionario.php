<?php
session_start();
require_once 'includes/conexao.php';

// Permite apenas ADM
if (!isset($_SESSION['id_perfil']) || $_SESSION['id_perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='main.php';</script>";
    exit();
}

try {
    $sql = "SELECT f.id_funcionario, f.nome_funcionario, f.cpf,
                   f.endereco, f.telefone, f.cargo, f.salario, f.data_admissao,
                   u.nome_usuario, u.id_usuario
            FROM funcionario f
            LEFT JOIN usuario AS u ON f.id_usuario = u.id_usuario
            ORDER BY f.id_funcionario ASC";
    $stmt = $pdo->query($sql);
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar funcionários: " . $e->getMessage());
}

$funcionarios = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (strlen($busca) == 14) {
        //Busca por CPF exato
        $sql = "SELECT f.*, u.id_usuario, u.email, u.nome_usuario
                FROM funcionario f
                INNER JOIN usuario u ON f.id_usuario = u.id_usuario
                WHERE f.cpf = :cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cpf', $busca, PDO::PARAM_STR);

    }elseif (is_numeric($busca)) {
        $sql = "SELECT f.*, u.id_usuario, u.email, u.nome_usuario
                FROM funcionario f
                INNER JOIN usuario u ON f.id_usuario = u.id_usuario
                WHERE f.id_funcionario = :busca
                ORDER BY f.nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT f.*, u.id_usuario, u.email, u.nome_usuario
                FROM funcionario f
                INNER JOIN usuario u ON f.id_usuario = u.id_usuario
                WHERE f.nome_funcionario LIKE :busca_nome
                ORDER BY f.nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT f.*, u.id_usuario, u.email, u.nome_usuario
            FROM funcionario f
            INNER JOIN usuario u ON f.id_usuario = u.id_usuario
            ORDER BY f.nome_funcionario ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$funcionarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Funcionário</title>

    <link rel="stylesheet" href="css/main_css.css">
    <link rel="stylesheet" href="css/tabela.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php include_once 'includes/sidebar.php'; ?>

<div class="container">
    <div class="conteudo">
        <div class="titulo">
            <h2>Excluir Funcionário</h2>
        </div>

        <div class="formulario-coluna cliente-coluna">
            <form action="excluir_funcionario.php" method="POST">
                <label for="busca">Digite o ID, Nome ou CPF:</label>
                <input type="text" id="busca" name="busca" placeholder="Ex: Maria Souza">
                <button type="submit" class="botao">Pesquisar</button>
            </form>
        </div>

<br>

        <div class="formulario-coluna tabela">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Cargo</th>
                    <th>Salário</th>
                    <th>Admissão</th>
                    <th>Usuário</th>
                    <th>Ações</th>
                </tr>

                <tr>
                    <?php if ($funcionarios): ?>
                        <?php foreach ($funcionarios as $funcionario): if($_SESSION['id_usuario'] != $funcionario['id_usuario']):?>
                            <tr>
                                <td><?= htmlspecialchars($funcionario['id_funcionario']) ?></td>
                                <td><?= htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                                <td><?= htmlspecialchars($funcionario['cpf']) ?></td>
                                <td><?= htmlspecialchars($funcionario['endereco']) ?></td>
                                <td><?= htmlspecialchars($funcionario['telefone']) ?></td>
                                <td><?= htmlspecialchars($funcionario['cargo']) ?></td>
                                <td>R$ <?= htmlspecialchars(number_format($funcionario['salario'], 2, ',', '.')) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($funcionario['data_admissao']))) ?></td>
                                <td><?= htmlspecialchars($funcionario['nome_usuario'] ?? '') ?></td>
                                <td>
                                    <a href="processa_exclusao_funcionario.php?id=<?= $funcionario['id_funcionario'] ?>" 
                                    class="btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir este funcionário?');">
                                    <i class="fa-solid fa-trash"></i>Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" style="text-align:center;">Nenhum funcionário encontrado.</td></tr>
                    <?php endif; ?>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="js/javascript.js"></script>
</body>
</html>
