<?php
session_start();
require_once 'includes/conexao.php';

// Permite apenas ADM
if (!isset($_SESSION['id_perfil']) || $_SESSION['id_perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='main.php';</script>";
    exit();
}

try {
    $sql = "SELECT id_fornecedor, nome, telefone, email, cnpj, insc_estadual, endereco
            FROM fornecedor
            ORDER BY id_fornecedor ASC";
    $stmt = $pdo->query($sql);
    $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar fornecedores: " . $e->getMessage());
}


$fornecedores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (is_numeric($busca)) {
        $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :busca ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM fornecedor WHERE nome LIKE :busca_nome ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM fornecedor ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Fornecedor</title>

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
            <h2>Excluir Fornecedor</h2>
        </div>

        <div class="formulario-coluna cliente-coluna">
            <form action="excluir_fornecedor.php" method="POST">
                <label for="busca">Digite o ID ou Nome do Fornecedor:</label>
                <input type="text" id="busca" name="busca" placeholder="Ex: João Silva">
                <button type="submit" class="botao">Buscar</button>
            </form>
        </div><br>

        <div class="formulario-coluna tabela">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome da Empresa</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>CNPJ</th>
                    <th>Inscrição Estadual</th>
                    <th>Endereço</th>
                    <th>Ações</th>
                </tr>

                <tr>
                    <?php if ($fornecedores): ?>
                        <?php foreach($fornecedores as $fornecedor): ?>
                            <tr>
                                <td><?= htmlspecialchars($fornecedor['id_fornecedor']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['nome']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['telefone']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['email']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['cnpj']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['insc_estadual']) ?></td>
                                <td><?= htmlspecialchars($fornecedor['endereco']) ?></td>
                                <td>
                                    <a href="backend/fornecedor/processa_exclusao_fornecedor.php?id=<?= $fornecedor['id_fornecedor'] ?>" 
                                    class="btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir esse fornecedor?');">
                                    <i class="fa-solid fa-trash"></i>Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;">Nenhum fornecedor encontrado.</td></tr>
                    <?php endif; ?>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="js/javascript.js"></script>
</body>
</html>
