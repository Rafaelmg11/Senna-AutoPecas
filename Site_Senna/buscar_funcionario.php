<?php
session_start();
require_once 'includes/conexao.php'; 

$funcionarios = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (strlen($busca) == 14) {
        //Busca por CPF exato
        $sql = "SELECT f.*, u.id_usuario, u.email
                FROM funcionario f
                INNER JOIN usuario u ON f.id_usuario = u.id_usuario
                WHERE f.cpf = :cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cpf', $busca, PDO::PARAM_STR);

    }elseif (is_numeric($busca)) {
        $sql = "SELECT f.*, u.id_usuario, u.email
                FROM funcionario f
                INNER JOIN usuario u ON f.id_usuario = u.id_usuario
                WHERE f.id_funcionario = :busca
                ORDER BY f.nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT f.*, u.id_usuario, u.email
                FROM funcionario f
                INNER JOIN usuario u ON f.id_usuario = u.id_usuario
                WHERE f.nome_funcionario LIKE :busca_nome
                ORDER BY f.nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT f.*, u.id_usuario, u.email
            FROM funcionario f
            INNER JOIN usuario u ON f.id_usuario = u.id_usuario
            ORDER BY f.nome_funcionario ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$funcionarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buscar Funcionário</title>
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
        <div class="titulo">
            <h2>Buscar Funcionário</h2>
        </div>

        <div class="formulario-coluna cliente-coluna">
            <form action="buscar_funcionario.php" method="POST">
                <label for="busca">Digite o ID, Nome ou CPF:</label>
                <input type="text" id="busca" name="busca" placeholder="Ex: Maria Souza">
                <button type="submit" class="botao">Pesquisar</button>
            </form>
        </div>

<br>
        <div class="formulario-coluna tabela">
            <?php if (!empty($funcionarios)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $f): ?>
                            <tr>
                                <td><?= htmlspecialchars($f['id_funcionario']) ?></td>
                                <td><?= htmlspecialchars($f['nome_funcionario']) ?></td>
                                <td><?= htmlspecialchars($f['email']) ?></td>
                                <td><?= htmlspecialchars($f['telefone']) ?></td>
                                <td>
                                    <button class="ver-mais-btn" style="padding: 12px 10px; font-size: 12px; width: auto;"
                                        data-id="<?= htmlspecialchars($f['id_funcionario']) ?>"
                                        data-nome="<?= htmlspecialchars($f['nome_funcionario']) ?>"
                                        data-cpf="<?= htmlspecialchars($f['cpf']) ?>"
                                        data-nascimento="<?= htmlspecialchars($f['data_nascimento']) ?>"
                                        data-endereco="<?= htmlspecialchars($f['endereco']) ?>"
                                        data-telefone="<?= htmlspecialchars($f['telefone']) ?>"
                                        data-cargo="<?= htmlspecialchars($f['cargo']) ?>"
                                        data-salario="<?= number_format($f['salario'], 2, ',', '.') ?>"
                                        data-admissao="<?= htmlspecialchars($f['data_admissao']) ?>"
                                        data-usuario-id="<?= htmlspecialchars($f['id_usuario']) ?>"
                                        data-email="<?= htmlspecialchars($f['email']) ?>">
                                        <i class="fa-solid fa-eye"></i> Ver Mais
                                    </button>

                                                <a href="alterar_funcionario.php?busca=<?= htmlspecialchars($f['id_funcionario']) ?>">
                                                    <i class="fa-solid fa-pen-to-square"></i> Alterar
                                                </a>

                                                <?php if ($_SESSION['id_perfil'] == 1 && $_SESSION['id_usuario'] != $f['id_usuario']): ?>
                                                    <a href="backend/funcionario/processa_exclusao_funcionario.php?id=<?= htmlspecialchars($f['id_funcionario']) ?>" 
                                                        onclick="return confirm('Tem certeza que deseja excluir este funcionário?');" class="btn-excluir">
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
                    <p>Nenhum funcionário encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<br>
<!-- Modal -->
<div id="modal-funcionario" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Detalhes do Funcionário</h2>

        <div class="cliente-card">
            <div class="cliente-info">
                <p><strong>ID:</strong> <span id="modal-id"></span></p>
                <p><strong>Usuário Vinculado:</strong> <span id="modal-usuario"></span></p>
                <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
                <p><strong>CPF:</strong> <span id="modal-cpf"></span></p>
                <p><strong>Data Nascimento:</strong> <span id="modal-nascimento"></span></p>
                <p><strong>Endereço:</strong> <span id="modal-endereco"></span></p>
                <p><strong>Telefone:</strong> <span id="modal-telefone"></span></p>
                <p><strong>Cargo:</strong> <span id="modal-cargo"></span></p>
                <p><strong>Salário:</strong> <span id="modal-salario"></span></p>
                <p><strong>Data Admissão:</strong> <span id="modal-admissao"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById("modal-funcionario");
const span = modal.querySelector(".close");

document.querySelectorAll(".ver-mais-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("modal-id").textContent = btn.dataset.id;
        document.getElementById("modal-usuario").textContent = `ID: ${btn.dataset.usuarioId} - ${btn.dataset.email}`;
        document.getElementById("modal-nome").textContent = btn.dataset.nome;
        document.getElementById("modal-cpf").textContent = btn.dataset.cpf;
        document.getElementById("modal-nascimento").textContent = btn.dataset.nascimento;
        document.getElementById("modal-endereco").textContent = btn.dataset.endereco;
        document.getElementById("modal-telefone").textContent = btn.dataset.telefone;
        document.getElementById("modal-cargo").textContent = btn.dataset.cargo;
        document.getElementById("modal-salario").textContent = `R$ ${btn.dataset.salario}`;
        document.getElementById("modal-admissao").textContent = btn.dataset.admissao;

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
