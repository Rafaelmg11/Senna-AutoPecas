<?php
session_start();
require_once 'includes/conexao.php';

// Verificação de acesso
if ($_SESSION['id_perfil'] != 1 && $_SESSION['id_perfil'] != 2) {
    echo "<script> alert('Acesso Negado!'); window.location.href='main.php' </script>";
    exit();
}

// Buscar estoque
$estoque = [];
$query = "SELECT p.*, f.nome AS nome_fornecedor FROM peca AS p
          JOIN fornecedor AS f ON f.id_fornecedor = p.id_fornecedor";
$stmt = $pdo->prepare($query);
try {
    $stmt->execute();
    $estoque = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar estoque: " . $e->getMessage();
}

// Reabastecer peça
if (isset($_GET['id_peca']) && !empty($_GET['id_peca'])) {
    $peca = $_GET['id_peca'];
    $qtde = $_GET['qtde'];

    $query = "UPDATE peca SET qtde_estoque = qtde_estoque + :qtde WHERE id_peca = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':qtde', $qtde, PDO::PARAM_INT);
    $stmt->bindParam(':id', $peca, PDO::PARAM_INT);

    try {
        $stmt->execute();
        echo "<script> alert('Estoque da peça reabastecido com sucesso!'); window.location.href='visualizar_estoque.php'; </script>";
    } catch (PDOException $e) {
        echo "Erro ao reabastecer: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Estoque</title>
    <link rel="stylesheet" href="css/main_css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <?php include_once 'includes/sidebar.php'; ?>

    <div class="container">
        <h1 style="margin-bottom:20px;">Estoque de Peças</h1>

        <div class="formulario-coluna tabela">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Marca</th>
                        <th>Quantidade</th>
                        <th>Valor</th>
                        <th>Fornecedor</th>
                        <th>Reabastecer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($estoque as $peca): ?>
                        <tr>
                            <td><?= htmlspecialchars($peca['nome']) ?></td>
                            <td><?= htmlspecialchars($peca['categoria']) ?></td>
                            <td><?= htmlspecialchars($peca['marca']) ?></td>
                            <td><?= htmlspecialchars($peca['qtde_estoque']) ?></td>
                            <td>R$ <?= number_format($peca['valor'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($peca['nome_fornecedor']) ?></td>
                            <td>
                                <form action="visualizar_estoque.php" method="GET" class="reabastecer-form">
                                    <input type="hidden" name="id_peca" value="<?= htmlspecialchars($peca['id_peca']) ?>">
                                    <input type="number" name="qtde" min="1" placeholder="Qtd" required>
                                    <button type="submit" class="botao">Reabastecer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/javascript.js"></script>
</body>
</html>
