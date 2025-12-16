<?php
session_start();
require_once 'includes/conexao.php'; 

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
<title>Buscar Peça</title>
<link rel="stylesheet" href="css/modal.css">
<link rel="stylesheet" href="css/main_css.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    
<?php include_once 'includes/sidebar.php'; ?>

<div class="container">
    <div class="conteudo">

        <div class="formulario-coluna cliente-coluna">
                    <div class="titulo">
            <h2>Buscar Peça</h2>
        </div>

        <form action="buscar_peca.php" method="POST">
            <label for="busca">Digite o ID, Nome ou Categoria:</label>
            <input type="text" id="busca" name="busca" placeholder="Ex: Motor, acessórios ...">
            <button type="submit" class="botao">Pesquisar</button>
        </form>

        </div>
<br>
        <div class="formulario-coluna tabela">
            <?php if (!empty($pecas)): ?>
                <!-- Container da tabela -->
                <div class="tabela-container">
                    <table class="listar-tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Fornecedor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pecas as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['id_peca']) ?></td>
                                    <td><?= htmlspecialchars($p['nome']) ?></td>
                                    <td><?= htmlspecialchars($p['categoria']) ?></td>
                                    <td><?= htmlspecialchars($p['nome_fornecedor']) ?></td>
                                    <td>
                                        <div class="acoes">
                                            <button class="ver-mais-btn"
                                                data-id="<?= htmlspecialchars($p['id_peca']) ?>"
                                                data-nome="<?= htmlspecialchars($p['nome']) ?>"
                                                data-categoria="<?= htmlspecialchars($p['categoria']) ?>"
                                                data-descricao="<?= htmlspecialchars($p['descricao']) ?>"
                                                data-estoque="<?= htmlspecialchars($p['qtde_estoque']) ?>"
                                                data-lote="<?= htmlspecialchars($p['lote']) ?>"
                                                data-valor="<?= number_format($p['valor'], 2, ',', '.') ?>"
                                                data-fornecedor="<?= htmlspecialchars($p['nome_fornecedor']) ?>">
                                                <i class="fa-solid fa-eye"></i> Ver Mais
                                            </button>

                                            <?php if ($_SESSION['id_perfil'] != 3): ?>
                                                <a href="alterar_peca.php?busca=<?= htmlspecialchars($p['id_peca']) ?>">
                                                    <i class="fa-solid fa-pen-to-square"></i> Alterar
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($_SESSION['id_perfil'] == 1): ?>
                                                <a href="processa_exclusao_peca.php?id=<?= htmlspecialchars($p['id_peca']) ?>" 
                                                    onclick="return confirm('Deseja realmente excluir esta peça?');" class="btn-excluir">
                                                    <i class="fa-solid fa-trash"></i> Excluir
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Nenhuma peça encontrada.</p>
            <?php endif; ?>
        </div>


<!-- Modal -->
<div id="modal-peca" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Detalhes da Peça</h2>

        <div class="cliente-card">
            <div class="cliente-info">
                <p><strong>ID:</strong> <span id="modal-id"></span></p>
                <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
                <p><strong>Categoria:</strong> <span id="modal-categoria"></span></p>
                <p><strong>Descrição:</strong> <span id="modal-descricao"></span></p>
                <p><strong>Estoque:</strong> <span id="modal-estoque"></span></p>
                <p><strong>Lote:</strong> <span id="modal-lote"></span></p>
                <p><strong>Valor:</strong> <span id="modal-valor"></span></p>
                <p><strong>Fornecedor:</strong> <span id="modal-fornecedor"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById("modal-peca");
const span = modal.querySelector(".close");

document.querySelectorAll(".ver-mais-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("modal-id").textContent = btn.dataset.id;
        document.getElementById("modal-nome").textContent = btn.dataset.nome;
        document.getElementById("modal-categoria").textContent = btn.dataset.categoria;
        document.getElementById("modal-descricao").textContent = btn.dataset.descricao;
        document.getElementById("modal-estoque").textContent = btn.dataset.estoque;
        document.getElementById("modal-lote").textContent = btn.dataset.lote;
        document.getElementById("modal-valor").textContent = `R$ ${btn.dataset.valor}`;
        document.getElementById("modal-fornecedor").textContent = btn.dataset.fornecedor;

        modal.style.display = "block";
    });
});

span.onclick = () => modal.style.display = "none";
window.onclick = event => {
    if (event.target == modal) modal.style.display = "none";
};
</script>

<script src="js/javascript.js"></script>
</body>
</html>
