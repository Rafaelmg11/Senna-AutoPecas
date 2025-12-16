<?php
session_start();
require_once 'includes/conexao.php';

// Permite apenas ADM
if (!isset($_SESSION['id_perfil']) || $_SESSION['id_perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='main.php';</script>";
    exit();
}

try {
    $sql = "SELECT id_peca, categoria, nome, descricao, qtde_estoque, valor, lote
            FROM peca
            ORDER BY id_peca ASC";
    $stmt = $pdo->query($sql);
    $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar peças: " . $e->getMessage());
}


$pecas = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (is_numeric($busca)) {
        $sql = "SELECT p.*, f.nome AS nome_fornecedor
                FROM peca p
                INNER JOIN fornecedor f ON p.id_fornecedor = f.id_fornecedor
                WHERE p.id_peca = :busca
                ORDER BY p.nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT p.*, f.nome AS nome_fornecedor
                FROM peca p
                INNER JOIN fornecedor f ON p.id_fornecedor = f.id_fornecedor
                WHERE p.nome LIKE :busca_nome OR p.categoria LIKE :busca_nome
                ORDER BY p.nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT p.*, f.nome AS nome_fornecedor
            FROM peca p
            INNER JOIN fornecedor f ON p.id_fornecedor = f.id_fornecedor
            ORDER BY p.nome ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$pecas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Peça</title>

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
            <h2>Excluir Peça</h2>
        </div>

        <div class="formulario-coluna cliente-coluna">
        <form action="excluir_peca.php" method="POST">
            <label for="busca">Digite o ID, Nome ou Categoria:</label>
            <input type="text" id="busca" name="busca" placeholder="Ex: Motor, acessórios ...">
            <button type="submit" class="botao">Pesquisar</button>
        </form>

        </div>
<br>

        <div class="formulario-coluna tabela">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Categoria</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Quantidade</th>
                    <th>Lote</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>

                <tr>
                    <?php if ($pecas): ?>
                        <?php foreach ($pecas as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['id_peca']) ?></td>
                                <td><?= htmlspecialchars($p['categoria']) ?></td>
                                <td><?= htmlspecialchars($p['nome']) ?></td>
                                <td><?= htmlspecialchars($p['descricao']) ?></td>
                                <td><?= htmlspecialchars($p['qtde_estoque']) ?></td>
                                <td><?= htmlspecialchars($p['lote']) ?></td>
                                <td>R$ <?= number_format($p['valor'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="processa_exclusao_peca.php?id=<?= $p['id_peca'] ?>"
                                    class="btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir esta peça?');">
                                    <i class="fa-solid fa-trash"></i>Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;">Nenhuma peça encontrada.</td></tr>
                    <?php endif; ?>
                </tr>
            </table>
        </div>
    </div>
</div>  

<script src="js/javascript.js"></script>
</body>
</html>
