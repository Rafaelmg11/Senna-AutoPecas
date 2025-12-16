<?php
session_start();
require_once 'includes/conexao.php'; 

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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Cliente</title>
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
                <h2>Buscar Cliente</h2>
            </div>
    
            <div class="formulario-coluna cliente-coluna">
            <form action="buscar_cliente.php" method="POST">
                <label for="busca">Digite o ID ou Nome:</label>
                <input type="text" id="busca" name="busca" placeholder="Ex: João Silva">
                <button type="submit" class="botao">Pesquisar</button>
            </form>
            
            </div>
<br>
            <div class="formulario-coluna tabela">
                <?php if (!empty($clientes)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Cliente</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cliente['id_cliente']) ?></td>
                                    <td><?= htmlspecialchars($cliente['nome_cliente']) ?></td>
                                    <td><?= htmlspecialchars($cliente['cpf']) ?></td>
                                    <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                                    
                                    <td>
                                        <button class="ver-mais-btn" style="padding: 12px 10px; font-size: 12px; width: auto;"
                                            data-id="<?= htmlspecialchars($cliente['id_cliente']) ?>"
                                            data-nome="<?= htmlspecialchars($cliente['nome_cliente']) ?>"
                                            data-endereco="<?= htmlspecialchars($cliente['endereco']) ?>"
                                            data-cpf="<?= htmlspecialchars($cliente['cpf']) ?>"
                                            data-telefone="<?= htmlspecialchars($cliente['telefone']) ?>"
                                            data-usuario-id="<?= htmlspecialchars($cliente['id_usuario']) ?>"
                                            data-email="<?= htmlspecialchars($cliente['email']) ?>">
                                            <i class="fa-solid fa-eye"></i> Ver Mais
                                        </button>

                                                    <a href="alterar_cliente.php?id=<?= htmlspecialchars($cliente['id_cliente']) ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i> Alterar
                                                    </a>

                                                    <?php if ($_SESSION['id_perfil'] == 1): ?>
                                                        <a href="excluir_cliente.php?id=<?= htmlspecialchars($cliente['id_cliente']) ?>" 
                                                        onclick="return confirm('Deseja realmente excluir este cliente?');" class="btn-excluir">
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
                        <p>Nenhum cliente encontrado.</p>
                    <?php endif; ?>
                </div>

        </div>
    </div>

    

        <!-- Modal -->
        <div id="modal-cliente" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Detalhes do Cliente</h2>

                <div class="cliente-card">
                    <div class="cliente-info">
                        <p><strong>ID Cliente:</strong> <span id="modal-id"></span></p>
                        <p><strong>Usuário Vinculado:</strong> <span id="modal-usuario"></span></p>
                        <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
                        <p><strong>Endereço:</strong> <span id="modal-endereco"></span></p>
                        <p><strong>CPF:</strong> <span id="modal-cpf"></span></p>
                        <p><strong>Telefone:</strong> <span id="modal-telefone"></span></p>
                    </div>
                </div>
            </div>
        </div>

    <script>
        const modal = document.getElementById("modal-cliente");
        const span = document.querySelector(".close");

        document.querySelectorAll(".ver-mais-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                document.getElementById("modal-id").textContent = btn.dataset.id;
                document.getElementById("modal-usuario").textContent = `ID: ${btn.dataset.usuarioId} - ${btn.dataset.email}`;
                document.getElementById("modal-nome").textContent = btn.dataset.nome;
                document.getElementById("modal-endereco").textContent = btn.dataset.endereco;
                document.getElementById("modal-cpf").textContent = btn.dataset.cpf;
                document.getElementById("modal-telefone").textContent = btn.dataset.telefone;

                modal.style.display = "block";
            });
        });

        span.onclick = () => modal.style.display = "none";
        window.onclick = event => {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    </script>

    <script src="js/javascript.js"></script>
</body>
</html>
