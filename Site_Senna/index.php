<?php
session_start();
require_once 'includes/conexao.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = '';
$senha_temp_mostrar = '';
$etapa = $_GET['etapa'] ?? 'login';

// ==================== LOGIN ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'login') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT u.* FROM usuario AS u
                           WHERE u.email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($usuario) {
        if ($usuario['senha_temporaria'] == 1){
            $_SESSION['senha_temp_usuario'] = $usuario['id_usuario'];
            $_SESSION['senha_temp_mostrar'] = $senha; // opcional exibir
            header("Location: redefinir_senha.php");
            exit();
        } elseif (password_verify($senha, $usuario['senha'])) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario'] = $usuario['nome_usuario'];
            $_SESSION['id_perfil'] = $usuario['id_perfil'];

            if ($usuario['id_perfil'] == 5){
                $stmt = $pdo->prepare("SELECT c.id_carrinho FROM carrinho AS c JOIN usuario ON usuario.id_usuario = c.id_usuario WHERE usuario.id_usuario = :id");
                $stmt->bindParam(':id', $usuario['id_usuario']);
                $stmt->execute();
                $u_carrinho = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['id_carrinho'] = $u_carrinho['id_carrinho'];
                echo "<script>alert('Login bem-sucedido!'); window.location.href='auto_pecas/loja.php';</script>";//Cliente
            }else{
                echo "<script>alert('Login bem-sucedido!'); window.location.href='principal.php';</script>"; //Admin ou outros perfis
            }
            exit();
        } else {
            $msg = "Senha incorreta! Insira novamente.";
        }
    } else {
        $msg = "E-mail não encontrado!";
    }
} 

// ==================== CADASTRO ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendCadastro'])) {
    $nome           = trim($_POST['nome_cliente'] ?? '');
    $cpf            = trim($_POST['cpf'] ?? '');
    $endereco       = trim($_POST['endereco'] ?? '');
    $telefone       = trim($_POST['telefone'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $usuario        = trim($_POST['nome_usuario'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $confirma       = $_POST['confirma_senha'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';

    // Validação idade mínima
    if ($data_nascimento) {
        $nascimento = new DateTime($data_nascimento);
        $hoje = new DateTime();
        $idade = $hoje->diff($nascimento)->y;
        if ($idade < 18) {
            echo "<script>alert('O cliente precisa ter no mínimo 18 anos!'); history.back();</script>";
            exit();
        }
    }

    if ($senha !== $confirma) {
        echo "<script>alert('As senhas não coincidem!'); history.back();</script>";
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        $sqlUsuario = "INSERT INTO usuario (nome_usuario, email, senha, id_perfil) VALUES (:nome_usuario, :email, :senha, 5)";
        $stmt = $pdo->prepare($sqlUsuario);
        $stmt->execute([':nome_usuario'=>$usuario, ':email'=>$email, ':senha'=>$senhaHash]);
        $idUsuario = $pdo->lastInsertId();

        $sqlCliente = "INSERT INTO cliente (id_usuario, nome_cliente, cpf, endereco, telefone, data_nascimento)
                       VALUES (:id_usuario, :nome, :cpf, :endereco, :telefone, :data_nascimento)";
        $stmtCliente = $pdo->prepare($sqlCliente);
        $stmtCliente->execute([
            ':id_usuario'=>$idUsuario,
            ':nome'=>$nome,
            ':cpf'=>$cpf,
            ':endereco'=>$endereco,
            ':telefone'=>$telefone,
            ':data_nascimento'=>$data_nascimento
        ]);

        $sqlCarrinho = "INSERT INTO carrinho (id_usuario) VALUES (:id_usuario)";
        $stmtCarrinho = $pdo->prepare($sqlCarrinho);
        $stmtCarrinho->execute([':id_usuario'=>$idUsuario]);

        $pdo->commit();
        echo "<script>alert('Cliente cadastrado com sucesso!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro ao cadastrar: ".addslashes($e->getMessage())."');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="css/login_style.css">
</head>
<body>
<div class="container_pagina_login">
    <div class="pagina_login_container">
        <div class="container_informacoes_login">
            <img src="img/logo_branca.png" alt="">
                <pre>Peças certas, viagens seguras.
    Sempre à frente com você.</pre>
        </div>

        <div class="container_login">
            <div class="formulario">
                <div class="btn-form">
                    <span onclick="Entrar()">Entrar</span>
                    <span onclick="Cadastro()">Cadastro</span>
                    <hr id="Indicador">
                </div>

                <?php if($msg) echo "<p style='color:red;text-align:center;'>$msg</p>"; ?>

                <!-- FORM LOGIN -->
                <form action="index.php" method="post" id="EntrarPainel">
                    <input type="hidden" name="acao" value="login">
                    <input type="email" name="email" placeholder="E-mail de acesso" required>
                    <input type="password" name="senha" placeholder="Digite sua senha" required>
                    <button type="submit" class="btn">Entrar</button>
                    <a href="esqueci_senha.php">Esqueceu sua senha?</a>
                </form>

                <!-- FORM CADASTRO -->
                <form action="" method="post" id="CadastroSite">
                    <label>Nome:</label>
                    <input type="text" name="nome_cliente" required>
                    <label>CPF:</label>
                    <input type="text" name="cpf" maxlength="14" required>
                    <label>Telefone:</label>
                    <input type="text" name="telefone" maxlength="15" required>
                    <label>Endereço:</label>
                    <input type="text" name="endereco" required>
                    <label>Data de Nascimento:</label>
                    <input type="date" name="data_nascimento" required>
                    <label>Nome de Usuário:</label>
                    <input type="text" name="nome_usuario" required>
                    <label>Email:</label>
                    <input type="email" name="email" required>
                    <label>Senha:</label>
                    <input type="password" name="senha" required>
                    <label>Confirme Senha:</label>
                    <input type="password" name="confirma_senha" required>
                    <div class="mostrar-senha">
                        <input type="checkbox" onclick="mostrarSenha()">
                        <label>Mostrar Senha</label>
                    </div>
                    <button type="submit" name="sendCadastro" class="btn">Cadastre-se</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarSenha(){
    const s1 = document.querySelector("#CadastroSite input[name='senha']");
    const s2 = document.querySelector("#CadastroSite input[name='confirma_senha']");
    const tipo = s1.type === "password" ? "text" : "password";
    s1.type = s2.type = tipo;
}
</script>
<script>
// Função para aplicar máscara
function mascara(input, tipo) {
    let v = input.value.replace(/\D/g, ''); // Remove tudo que não é número

    if (tipo === 'cpf') {
        // Formato: 000.000.000-00
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } 
    else if (tipo === 'telefone') {
        // Formato: (00) 0000-0000 ou (00) 00000-0000
        if (v.length <= 10) {
            v = v.replace(/(\d{2})(\d)/, '($1) $2');
            v = v.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            v = v.replace(/(\d{2})(\d)/, '($1) $2');
            v = v.replace(/(\d{5})(\d)/, '$1-$2');
        }
    } 
    else if (tipo === 'cep') {
        // Formato: 00000-000
        v = v.replace(/(\d{5})(\d)/, '$1-$2');
    }

    input.value = v;
}

// Adicionando eventos aos inputs
document.querySelector("input[name='cpf']").addEventListener('input', function() {
    mascara(this, 'cpf');
});

document.querySelector("input[name='telefone']").addEventListener('input', function() {
    mascara(this, 'telefone');
});

document.querySelector("input[name='cep']").addEventListener('input', function() {
    mascara(this, 'cep');
});
</script>

<script src="js/login.js"></script>
</body>
</html>
