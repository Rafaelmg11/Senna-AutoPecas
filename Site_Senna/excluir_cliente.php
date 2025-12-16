<?php
require_once 'includes/conexao.php';
session_start();

// Permite apenas ADM
if (!isset($_SESSION['id_perfil']) || $_SESSION['id_perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='main.php';</script>";
    exit();
}

try {
    $sql = "SELECT c.id_cliente, c.nome_cliente, c.cpf, c.endereco, c.telefone, u.nome_usuario
            FROM cliente c
            LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
            ORDER BY c.id_cliente ASC";
    $stmt = $pdo->query($sql);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar clientes: " . $e->getMessage());
}

$clientes = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (is_numeric($busca)) {
        $sql = "SELECT c.id_cliente, c.nome_cliente, c.endereco, c.cpf, c.telefone, 
                       u.id_usuario, u.email
                FROM cliente c
                INNER JOIN usuario u ON c.id_usuario = u.id_usuario
                WHERE c.id_cliente = :busca
                ORDER BY c.nome_cliente ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT c.id_cliente, c.nome_cliente, c.endereco, c.cpf, c.telefone, 
                       u.id_usuario, u.email
                FROM cliente c
                INNER JOIN usuario u ON c.id_usuario = u.id_usuario
                WHERE c.nome_cliente LIKE :busca_nome
                ORDER BY c.nome_cliente ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT c.id_cliente, c.nome_cliente, c.endereco, c.cpf, c.telefone, 
                   u.id_usuario, u.email
            FROM cliente c
            INNER JOIN usuario u ON c.id_usuario = u.id_usuario
            ORDER BY c.nome_cliente ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Excluir Cliente</title>
<link rel="stylesheet" href="css/main_css.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php include_once 'includes/sidebar.php'; ?>

<div class="container">
    <div class="conteudo">
        <h2>Excluir Cliente</h2>

        <div class="formulario-coluna cliente-coluna">
            <form action="excluir_cliente.php" method="POST">
                <label for="busca">Digite o ID ou Nome:</label>
                <input type="text" id="busca" name="busca" placeholder="Ex: João Silva">
                <button type="submit" class="botao">Pesquisar</button>
            </form>
            
            </div>
<br>

        <div class="formulario-coluna tabela">
            <table>
                    <tr>
                        <th>ID</th>
                        <th>Nome Completo</th>
                        <th>CPF</th>
                        <th>Endereço</th>
                        <th>Telefone</th>
                        <th>Usuário</th>
                        <th>Ações</th>
                    </tr>
                <tbody>
                    <?php if ($clientes): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= htmlspecialchars($cliente['id_cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['nome_cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['cpf']) ?></td>
                                <td><?= htmlspecialchars($cliente['endereco']) ?></td>
                                <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                                <td><?= htmlspecialchars($cliente['nome_usuario'] ?? '') ?></td>
                                <td>
                                    <a href="processa_exclusao_cliente.php?id=<?= $cliente['id_cliente'] ?>" 
                                       class="btn-excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este cliente?');">
                                       <i class="fa-solid fa-trash"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 20px;">Nenhum cliente encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="js/javascript.js"></script>
</body>
</html>
