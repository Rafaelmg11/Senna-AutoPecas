<?php
session_start();
require_once 'includes/conexao.php';

$clientes = [];

$cliente = [];
$usuario = [];

// Se tiver ID na URL (GET), busca os dados
if (!empty($_GET['busca_cliente'])) {
    $busca_cliente = $_GET['busca_cliente'];

    if (is_numeric($busca_cliente)) {
        $stmt = $pdo->prepare("SELECT * FROM cliente WHERE id_cliente = :id");
        $stmt->execute([':id' => $busca_cliente]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $stmt2 = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = :id_usuario");
            $stmt2->execute([':id_usuario' => $cliente['id_usuario']]);
            $usuario = $stmt2->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "<script>alert('Cliente não encontrado!');</script>";
        }
    } else { // Se o cliente não foi buscado por um ID (ou valor inteiro)
        $stmt = $pdo->prepare("SELECT * FROM cliente WHERE nome_cliente LIKE :nome");
        $stmt->execute([':nome' => "$busca_cliente%"]);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($clientes) {
            $qnt_linhas = count($clientes);

            if ($qnt_linhas === 1) {
                $cliente = $clientes[0];

                $stmt2 = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = :id_usuario");
                $stmt2->execute([':id_usuario' => $cliente['id_usuario']]);
                $usuario = $stmt2->fetch(PDO::FETCH_ASSOC);
            }
        } else {
            echo "<script>alert('Cliente não encontrado!');</script>";
        }
    }
}

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM cliente WHERE id_cliente = :id");
    $stmt->execute([':id' => $id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $stmt2 = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = :id_usuario");
        $stmt2->execute([':id_usuario' => $cliente['id_usuario']]);
        $usuario = $stmt2->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<script>alert('Cliente não encontrado!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alterar Cliente</title>
<link rel="stylesheet" href="css/main_css.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php include_once 'includes/sidebar.php'; ?>

<div class="container">
    <div class="conteudo">

        <h2>Alterar Cliente</h2>

        <!-- Barra de pesquisa -->
        <div class="formulario-coluna cliente-coluna">
            <form action="alterar_cliente.php" method="get" class="formulario-linhas">
                <label for="busca_cliente">Pesquise o cliente (nome ou id):</label>
                <input type="text" id="busca_cliente" name="busca_cliente" placeholder="Digite o ID ou NOME do cliente" min="1" required>
                <button type="submit" class="botao"><i class="fa-solid fa-search"></i> Buscar</button> 
            </form>
        </div>
        <br>
        
        <br>
        <!-- Formulário de Alteração -->
        <form action="backend/cliente/processa_alteracao_cliente.php" method="post" class="formulario-linhas">

            <!-- Dados do Cliente -->
            <div class="formulario-coluna cliente-coluna">
                <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente'] ?? '') ?>">
                <legend>Dados do Cliente</legend>

                <label for="nome_cliente">Nome Completo:</label>
                <input type="text" id="nome_cliente" name="nome_cliente" 
                    value="<?= htmlspecialchars($cliente['nome_cliente'] ?? '') ?>" 
                    placeholder="Digite o nome completo">

                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" 
                    value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>" 
                    placeholder="000.000.000-00">

                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" 
                    value="<?= htmlspecialchars($cliente['endereco'] ?? '') ?>" 
                    placeholder="Rua, 123, Bairro, Cidade - UF">

                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" 
                    value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>" 
                    placeholder="(xx) xxxxx-xxxx">
            </div>
<br>
            <!-- Dados do Usuário -->
            <div class="formulario-coluna usuario-coluna">
                <legend>Usuário</legend>

                <label for="nome_usuario">Nome de Usuário:</label>
                <input type="text" id="nome_usuario" name="nome_usuario" 
                    value="<?= htmlspecialchars($usuario['nome_usuario'] ?? '') ?>" 
                    placeholder="Digite o usuário">

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" 
                    value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" 
                    placeholder="Digite o email">

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite a nova senha">

                <label for="confirma_senha">Confirme a Senha:</label>
                <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme a senha">

                <div class="mostrar-senha">
                    <input type="checkbox" id="mostrar-senha" onclick="mostrarSenha()">
                    <label for="mostrar-senha">Mostrar Senha</label>
                </div>
            </div>

            <!-- Botões -->
            <div class="botoes">
                <button type="submit" class="botao"><i class="fa-solid fa-pen-to-square"></i> Alterar</button>
                <button type="reset" class="botao"><i class="fa-solid fa-xmark"></i> Cancelar</button>
            </div>
        </form>

        <!-- TABELA SE RETORNAR VAROS CLIENTES -->
        <?php if(count($clientes) > 1): ?>
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
                                                <a href="alterar_cliente.php?id=<?= htmlspecialchars($cliente['id_cliente']) ?>">
                                                    <i class="fa-solid fa-pen-to-square"></i> Alterar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Mostrar senha
function mostrarSenha(){
    const s1 = document.getElementById("senha");
    const s2 = document.getElementById("confirma_senha");
    const tipo = s1.type === "password" ? "text" : "password";
    s1.type = s2.type = tipo;
}

// Máscara CPF
document.getElementById('cpf').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    let r = '';
    if (v.length > 3) r = v.slice(0, 3) + '.' + v.slice(3, 6);
    else r = v;
    if (v.length > 6) r += '.' + v.slice(6, 9);
    if (v.length > 9) r += '-' + v.slice(9, 11);
    this.value = r;
});

// Máscara telefone
document.getElementById('telefone').addEventListener('input', function() {
    let x = this.value.replace(/\D/g, '').slice(0, 11);
    let r = '';
    if (x.length > 0) r = '(' + x.slice(0, 2);
    if (x.length >= 3) r += ') ' + x.slice(2, 7);
    if (x.length >= 8) r += '-' + x.slice(7, 11);
    this.value = r;
});

// Apenas letras nos nomes
document.getElementById('nome_cliente').addEventListener('input', function() {
    this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
});
document.getElementById('nome_usuario').addEventListener('input', function() {
    this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
});
</script>

<script src="js/javascript.js"></script>
</body>
</html>
