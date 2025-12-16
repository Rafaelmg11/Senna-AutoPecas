<?php
session_start();
require_once 'includes/conexao.php'; 

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
    <title>Buscar Fornecedor</title>
    <link rel="stylesheet" href="css/main_css.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
<?php include_once 'includes/sidebar.php'; ?>

<div class="container">
    <div class="conteudo">
        <div class="titulo">
            <h2>Buscar Fornecedor</h2>
        </div>

        <div class="formulario-coluna cliente-coluna">
            <form action="buscar_fornecedor.php" method="POST">
                <label for="busca">Digite o ID ou Nome do Fornecedor:</label>
                <input type="text" id="busca" name="busca" placeholder="Ex: João Silva">
                <button type="submit" class="botao">Buscar</button>
            </form>
        </div>

<br>
        <div class="formulario-coluna tabela">
            <?php if (!empty($fornecedores)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>E-mail</th>
                            <th>CNPJ</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fornecedores as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['id_fornecedor']) ?></td>
                                <td><?= htmlspecialchars($p['nome']) ?></td>
                                <td style="width: 180px !important;"><?= htmlspecialchars($p['endereco']) ?></td>
                                <td><?= htmlspecialchars($p['email']) ?></td>
                                <td><?= htmlspecialchars($p['cnpj']) ?></td>

                                <td>
                                    <button class="ver-mais-btn" style="padding: 12px 10px; font-size: 12px; width: auto;" 
                                        data-id="<?= htmlspecialchars($p['id_fornecedor']) ?>"
                                        data-nome="<?= htmlspecialchars($p['nome']) ?>"
                                        data-endereco="<?= htmlspecialchars($p['endereco']) ?>"
                                        data-telefone="<?= htmlspecialchars($p['telefone']) ?>"
                                        data-email="<?= htmlspecialchars($p['email']) ?>"
                                        data-cnpj="<?= htmlspecialchars($p['cnpj']) ?>"
                                        data-insc_estadual="<?= htmlspecialchars($p['insc_estadual']) ?>">
                                        <i class="fa-solid fa-eye"></i> Ver Mais
                                    </button>

                                    <a href="alterar_fornecedor.php?busca=<?= htmlspecialchars($p['id_fornecedor']) ?>">
                                        <i class="fa-solid fa-pen-to-square"></i> Alterar
                                    </a>

                                    <?php if ($_SESSION['id_perfil'] == 1): ?>
                                        <a href="backend/fornecedor/processa_exclusao_fornecedor.php?id=<?= htmlspecialchars($p['id_fornecedor']) ?>" 
                                            onclick="return confirm('Deseja realmente excluir este fornecedor?');">
                                            <i class="fa-solid fa-trash"></i> Excluir
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhuma peça encontrada.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal-peca" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Detalhes do Fornecedor</h2>

        <div class="cliente-card">
            <div class="cliente-info">
                <p><strong>ID:</strong> <span id="modal-id"></span></p>
                <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
                <p><strong>Endereço:</strong> <span id="modal-endereco"></span></p>
                <p><strong>Telefone:</strong> <span id="modal-telefone"></span></p>
                <p><strong>E-mail:</strong> <span id="modal-email"></span></p>
                <p><strong>CNPJ:</strong> <span id="modal-cnpj"></span></p>
                <p><strong>Inscrição Estadual:</strong> <span id="modal-insc_estadual"></span></p>
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
        document.getElementById("modal-endereco").textContent = btn.dataset.endereco;
        document.getElementById("modal-telefone").textContent = btn.dataset.telefone;
        document.getElementById("modal-email").textContent = btn.dataset.email;
        document.getElementById("modal-cnpj").textContent = btn.dataset.cnpj;
        document.getElementById("modal-insc_estadual").textContent = btn.dataset.insc_estadual;

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
