<?php
session_start();
require_once 'includes/conexao.php';

// Permite apenas ADM, Gerente ou outro perfil autorizado
if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1, 2, 3])) {
    echo "<script>alert('Acesso negado!'); window.location.href='main.php';</script>";
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome           = trim($_POST['nome_cliente'] ?? '');
    $cpf            = trim($_POST['cpf'] ?? '');
    $endereco       = trim($_POST['endereco'] ?? '');
    $telefone       = trim($_POST['telefone'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $usuario        = trim($_POST['nome_usuario'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $confirma       = $_POST['confirma_senha'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';

    // Validação de idade mínima
    if ($data_nascimento) {
        $nascimento = new DateTime($data_nascimento);
        $hoje = new DateTime();
        $idade = $hoje->diff($nascimento)->y;
        if ($idade < 18) {
            echo "<script>alert('O cliente precisa ter no mínimo 18 anos!'); history.back();</script>";
            exit();
        }
    }

    // Validação de senha
    if ($senha !== $confirma) {
        echo "<script>alert('As senhas não coincidem!'); history.back();</script>";
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        //Verifica se cpf já existe
        $sqlcpf = "SELECT COUNT(*) FROM cliente WHERE cpf = :cpf";
        $stmtcpf = $pdo->prepare($sqlcpf);
        $stmtcpf -> bindParam(':cpf', $cpf);
        $stmtcpf->execute();
        $cpf_verificacao = $stmtcpf->fetchColumn();
        
        if ($cpf_verificacao > 0) {
            echo "<script>alert('CPF já cadastrado!'); history.back();</script>";
            exit();
        }

        //Verifica se o email já existe
        $sqlemail = "SELECT COUNT(*) FROM usuario WHERE email = :email";
        $stmtemail = $pdo->prepare($sqlemail);
        $stmtemail -> bindParam(':email', $email);
        $stmtemail->execute();
        $email_verificacao = $stmtemail->fetchColumn();
        
        if ($email_verificacao > 0) {
            echo "<script>alert('E-mail já cadastrado!'); history.back();</script>";
            exit();
        }

        // Inserir usuário
        $sqlUsuario = "INSERT INTO usuario (nome_usuario, email, senha, id_perfil) VALUES (:nome_usuario, :email, :senha, 5)";
        $stmt = $pdo->prepare($sqlUsuario);
        $stmt->bindParam(':nome_usuario', $usuario);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->execute();
        $idUsuario = $pdo->lastInsertId();

        // Inserir cliente
        $sqlCliente = "INSERT INTO cliente (id_usuario, nome_cliente, cpf, endereco, telefone, data_nascimento)
                       VALUES (:id_usuario, :nome, :cpf, :endereco, :telefone, :data_nascimento)";
        $stmtCliente = $pdo->prepare($sqlCliente);
        $stmtCliente->bindParam(':id_usuario', $idUsuario);
        $stmtCliente->bindParam(':nome', $nome);
        $stmtCliente->bindParam(':cpf', $cpf);
        $stmtCliente->bindParam(':endereco', $endereco);
        $stmtCliente->bindParam(':telefone', $telefone);
        $stmtCliente->bindParam(':data_nascimento', $data_nascimento);
        $stmtCliente->execute();

        // Criar carrinho para o cliente
        $sqlCarrinho = "INSERT INTO carrinho (id_usuario) VALUES (:id_usuario)";
        $stmtCarrinho = $pdo->prepare($sqlCarrinho);
        $stmtCarrinho->bindParam(':id_usuario', $idUsuario);
        $stmtCarrinho->execute();

        $pdo->commit();
        echo "<script>alert('Cliente cadastrado com sucesso!'); window.location.href='cadastrar_cliente.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro ao cadastrar: ".addslashes($e->getMessage())."');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente</title>
    <link rel="stylesheet" href="css/main_css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include_once 'includes/sidebar.php'; ?>

        <!-- Conteúdo principal -->
        <div class="conteudo">
            <form action="cadastrar_cliente.php" method="post">
                <h2>Cadastrar Cliente</h2>
                <div class="formulario-linhas">

                    <!-- Dados do Cliente -->
                    <div class="formulario-coluna cliente-coluna">
                        <legend>Dados do Cliente</legend>
                        <label for="nome_cliente">Nome Completo:</label>
                        <input type="text" id="nome_cliente" name="nome_cliente" placeholder="Digite o nome completo" required>

                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>

                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" placeholder="(xx) xxxxx-xxxx" required>

                        <label for="endereco">Endereço:</label>
                        <input type="text" id="endereco" name="endereco" placeholder="Digite o endereço" required>

                        <label for="data_nascimento">Data de Nascimento:</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" required>
                    </div> <br><br><br><br><br>

                    <!-- Usuário -->
                    <div class="formulario-coluna usuario-coluna">
                        <legend>Usuário</legend>
                        <label for="nome_usuario">Nome de Usuário:</label>
                        <input type="text" id="nome_usuario" name="nome_usuario" placeholder="Digite o usuário" required>

                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" placeholder="Digite o email" required>

                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" placeholder="Digite a senha" required>

                        <label for="confirma_senha">Confirme a Senha:</label>
                        <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme a senha" required>

                        <div class="mostrar-senha">
                            <input type="checkbox" id="mostrar-senha" onclick="mostrarSenha()">
                            <label for="mostrar-senha">Mostrar Senha</label>
                        </div>
                    </div>

                </div>

                <div class="botoes">
                    <button type="submit" class="botao">Cadastrar</button>
                    <button type="reset" class="botao">Cancelar</button>
                </div>
            </form>
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
document.getElementById('cpf').addEventListener('input', function(){
    let v = this.value.replace(/\D/g,'').slice(0,11);
    if(v.length>9) this.value = v.slice(0,3)+'.'+v.slice(3,6)+'.'+v.slice(6,9)+'-'+v.slice(9,11);
    else if(v.length>6) this.value = v.slice(0,3)+'.'+v.slice(3,6)+'.'+v.slice(6);
    else if(v.length>3) this.value = v.slice(0,3)+'.'+v.slice(3);
    else this.value = v;
});

// Máscara Telefone
document.getElementById('telefone').addEventListener('input', function(){
    let x = this.value.replace(/\D/g,'').slice(0,11);
    if(x.length>6) this.value = '('+x.slice(0,2)+') '+x.slice(2,7)+'-'+x.slice(7,11);
    else if(x.length>2) this.value = '('+x.slice(0,2)+') '+x.slice(2);
    else this.value = x;
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
