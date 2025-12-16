<?php
session_start();
require_once 'includes/conexao.php';

// Permite apenas ADM ou Gerente cadastrar funcionário
if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1, 2])) {
    echo "<script>alert('Acesso negado!');window.location.href='main.php'</script>";
    exit();
}


// Ativa exibição de erros para debug (remova em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = $_POST['nome'] ?? '';
    $cpf        = $_POST['cpf'] ?? '';
    $endereco   = $_POST['endereco'] ?? '';
    $telefone   = $_POST['telefone'] ?? '';
    $cargo      = $_POST['cargo'] ?? '';
    $salario    = $_POST['salario'] ?? '';
    $admissao   = $_POST['admissao'] ?? '';
    $usuario    = $_POST['nome_usuario'] ?? '';
    $senha      = $_POST['senha'] ?? '';
    $confirma   = $_POST['confirma_senha'] ?? '';
    $perfil     = $_POST['perfil'] ?? 2; // valor padrão = Funcionário
    $email      = $_POST['email'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';

    if ($data_nascimento) {
        //DataTime lê em formato de data
        $nascimento = new DateTime($data_nascimento);
        $hoje = new DateTime(); #Data Atual
        $idade = $hoje->diff($nascimento)->y; //diff calcula a diferença entre duas datas //->y = quantidade de anos completos entre as datas

        if ($idade < 16) {
            echo "<script>alert('O funcionário precisa ter no mínimo 16 anos!'); history.back();</script>";
            exit();
        }

        $ano_admissao = new DateTime($admissao);

        if ($ano_admissao->format('y') > $hoje->format('y')) {
            echo "<script>alert('O ano inserido é maior do que o ano atual!'); history.back();</script>";
            exit();
        }
    }

    if ($senha !== $confirma) {
        echo "<script>alert('As senhas não coincidem!');history.back();</script>";
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Upload da foto (opcional)
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $foto = file_get_contents($_FILES['foto']['tmp_name']);
        }

        try {
            $pdo->beginTransaction();

            //Verifica se cpf já existe
            $sqlcpf = "SELECT COUNT(*) FROM funcionario WHERE cpf = :cpf";
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


            // Cadastro do usuário
            $sqlUsuario = "INSERT INTO usuario (nome_usuario, email, senha, id_perfil)
                           VALUES (:usuario,:email, :senha, :id_perfil)";
            $stmtUsuario = $pdo->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':usuario', $usuario);
            $stmtUsuario->bindParam(':email', $email);
            $stmtUsuario->bindParam(':senha', $senhaHash);
            $stmtUsuario->bindParam(':id_perfil', $perfil);
            $stmtUsuario->execute();

            $idUsuario = $pdo->lastInsertId();

            // Cadastro do funcionário
            $sqlFuncionario = "INSERT INTO funcionario
                               (id_usuario, nome_funcionario, cpf, endereco, telefone, cargo, salario, data_admissao, imagem, data_nascimento)
                               VALUES (:id_usuario, :nome, :cpf, :endereco, :telefone, :cargo, :salario, :admissao, :foto, :data_nascimento)";
            $stmtFunc = $pdo->prepare($sqlFuncionario);
            $stmtFunc->bindParam(':id_usuario', $idUsuario);
            $stmtFunc->bindParam(':nome', $nome);
            $stmtFunc->bindParam(':cpf', $cpf);
            $stmtFunc->bindParam(':endereco', $endereco);
            $stmtFunc->bindParam(':telefone', $telefone);
            $stmtFunc->bindParam(':cargo', $cargo);
            $stmtFunc->bindParam(':salario', $salario);
            $stmtFunc->bindParam(':admissao', $admissao);
            $stmtFunc->bindParam(':data_nascimento', $data_nascimento);
            $stmtFunc->bindParam(':foto', $foto, PDO::PARAM_LOB);
            $stmtFunc->execute();

            $pdo->commit();

            echo "<script>alert('Funcionário cadastrado com sucesso!');window.location.href='cadastrar_funcionario.php';</script>";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<script>alert('Erro ao cadastrar: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Funcionário</title>
    <link rel="stylesheet" href="css/main_css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
<div class="container">

    <?php include_once 'includes/sidebar.php'; ?>

    <!--CONTEUDO!-->
    <div class="conteudo">

        <div class="titulo">
            <h2>Cadastrar Funcionário</h2>
        </div>
        
        <form action="cadastrar_funcionario.php" method="post" enctype="multipart/form-data">
          <div class="formulario-linhas">
            
            <!-- CLIENTE -->
            <div class="formulario-coluna cliente-coluna">
              <legend>Dados do Funcionário</legend>
              <label for="nome">Nome Completo:</label>
              <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" required>

              <label for="cpf">CPF:</label>
              <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
              
              <label for="data_nascimento">Data de Nascimento:</label>
              <input type="date" id="data_nascimento" name="data_nascimento" required>

              <label for="admissao">Data de Admissão:</label>
              <input type="date" id="admissao" name="admissao" required>
            </div>
<br>
            <div class="formulario-coluna cliente-coluna">
                <label for="endereco">Endereço:</label>
              <input type="text" id="endereco" name="endereco" placeholder="Digite o endereço" required>

              <label for="telefone">Telefone:</label>
              <input type="text" id="telefone" name="telefone" placeholder="(xx) xxxxx-xxxx" required>

              <label for="cargo">Cargo:</label>
              <input type="text" id="cargo" name="cargo" placeholder="Digite o cargo" required>

              <label for="salario">Salário:</label>
              <input type="number" id="salario" name="salario" placeholder="Digite o salário" required>

              <label for="perfil">Tipo de Usuário</label>
              <select name="perfil" id="perfil">
                <option value="3">Funcionário</option>
                <option value="1">Administrador</option>
                <option value="2">Gerente</option>
                <option value="4">Almoxarife</option>
              </select>
            </div>
<br><br>
            <!-- USUÁRIO -->
            <div class="formulario-coluna usuario-coluna" id="usuario-coluna">
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

                <!-- FOTO DO FUNCIONÁRIO -->
                <div class="formulario-coluna usuario-coluna">
                    <legend>Foto do Funcionário</legend>
                    <label for="foto">Selecionar Imagem:</label>
                    <input type="file" id="foto" name="foto" accept="image/*">

                    <?php if (!empty($funcionario['imagem'])): ?>
                        <p>Foto atual:</p>
                        <img src="../../uploads/<?= htmlspecialchars($funcionario['imagem']) ?>"
                            alt="Foto" style="max-width:100px; border-radius:5px;">
                    <?php endif; ?>

                    <!-- Container para pré-visualização -->
                    <div id="preview-imagens" style="display:flex; gap:10px; flex-wrap:wrap; margin:auto; justify-content:center; text-align:center;"></div>
                </div>
            </div>
<br>
          </div>

          <!-- BOTÕES -->
          <div class="botoes">
            <button type="submit" class="botao">Cadastrar</button>
            <button type="reset" class="botao">Cancelar</button>
          </div>
        </form>
    </div>
</div>


<script>
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
</script>

<script>
    function mostrarSenha(){
        var senha1 = document.getElementById("senha");
        var senha2 = document.getElementById("confirma_senha");
        var tipo = senha1.type === "password" ? "text" : "password";
        senha1.type=tipo;
        senha2.type=tipo;
    }

// Máscara de nome: apenas letras e espaços
document.getElementById('nome').addEventListener('input', function() {
    // Remove tudo que não for letra ou espaço
    this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
});
</script>

<script src="js/javascript.js"></script>

<script>
    const inputs = document.querySelectorAll('input[type="file"]');
    const previewContainer = document.getElementById('preview-imagens');

    inputs.forEach(input => {
        input.addEventListener('change', () => {
            // Limpa a imagem anterior
            const existingImg = previewContainer.querySelector(`#preview-${input.id}`);
            if (existingImg) existingImg.remove();

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.id = `preview-${input.id}`;
                    img.style.width = '300px';
                    img.style.height = '300px';
                    img.style.objectFit = 'cover';
                    img.style.border = '1px solid #ccc';
                    img.style.borderRadius = '5px';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
</script>

</body>
</html>
