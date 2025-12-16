<?php
require_once 'includes/conexao.php';
session_start();

//  Controle de acesso: apenas ADM (Master) e Almoxarife podem cadastrar fornecedor

if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1, 2, 4])) {
    echo "<script>alert('Acesso negado!');window.location.href='main.php'</script>";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome          = $_POST['nome'] ?? '';
    $telefone      = $_POST['telefone'] ?? '';
    $email         = $_POST['email'] ?? '';
    $cnpj          = $_POST['cnpj'] ?? '';
    $insc_estadual = $_POST['insc_estadual'] ?? '';
    $endereco      = $_POST['endereco'] ?? '';

    try {
        //Verifica se o email já existe
        $sqlcnpj = "SELECT COUNT(*) FROM fornecedor WHERE cnpj = :cnpj";
        $stmtcnpj = $pdo->prepare($sqlcnpj);
        $stmtcnpj -> bindParam(':cnpj', $cnpj);
        $stmtcnpj->execute();
        $cnpj_verificacao = $stmtcnpj->fetchColumn();
        
        if ($cnpj_verificacao > 0) {
            echo "<script>alert('CNPJ já cadastrado!'); history.back();</script>";
            exit();
        }

        $query = "INSERT INTO fornecedor 
                    (nome, telefone, email, cnpj, insc_estadual, endereco)
                  VALUES
                    (:nome, :telefone, :email, :cnpj, :insc_estadual, :endereco)";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":cnpj", $cnpj);
        $stmt->bindParam(":insc_estadual", $insc_estadual);
        $stmt->bindParam(":endereco", $endereco);

        if ($stmt->execute()) {
            echo "<script> alert('Fornecedor cadastrado com sucesso!'); window.location.href='cadastrar_fornecedor.php'; </script>";
        } else {
            echo "<script> alert('Erro ao cadastrar fornecedor!'); </script>";
        }
    } catch (Exception $e) {
        echo "<script> alert('Erro: " . $e->getMessage() . "'); </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fornecedor</title>
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
            <h2>Cadastrar Fornecedor</h2>
        </div>
        
        <form action="cadastrar_fornecedor.php" method="post" enctype="multipart/form-data">
          <div class="formulario-linhas">
            
            <!-- CLIENTE -->
            <div class="formulario-coluna cliente-coluna">
              <legend>Dados do Fornecedor</legend>
              <label for="nome">Nome da Empresa/Pessoa:</label>
              <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" required>

              <label for="cnpj">CNPJ:</label>
              <input type="text" id="cnpj" name="cnpj" placeholder="xx.xxx.xxx/0001-xx" required>

              <label for="insc_estadual">Inscrição Estadual:</label>
              <input type="text" id="insc_estadual" name="insc_estadual" placeholder="xxx.xxx.xxx.xxx" maxlength=15 required>

              <label for="telefone">Telefone:</label>
              <input type="text" id="telefone" name="telefone" placeholder="(xx) xxxxx-xxxx" required>

              <label for="email">E-mail:</label>
              <input type="email" id="email" name="email" placeholder="Digite o e-mail para contato" required>

              <label for="endereco">Endereço:</label>
              <input type="text" id="endereco" name="endereco" placeholder="Digite o endereço" required>
            </div>

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
        // Máscara de CNPJ (apenas formata para 00.000.000/0000-00)
        document.getElementById('cnpj').addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '').slice(0, 14); // só números, máximo 14 dígitos
            let r = '';
            if (v.length > 2) r = v.slice(0, 2) + '.' + v.slice(2, 5);
            else r = v;
            if (v.length > 5) r += '.' + v.slice(5, 8);
            if (v.length > 8) r += '/' + v.slice(8, 12);
            if (v.length > 12) r += '-' + v.slice(12, 14);
            this.value = r;
        });

        // Máscara de telefone
        document.getElementById('telefone').addEventListener('input', function() {
            let x = this.value.replace(/\D/g, '').slice(0, 11); // no máx. 11 números
            let r = '';
            if (x.length > 0) r = '(' + x.slice(0, 2);
            if (x.length >= 3) r += ') ' + x.slice(2, 7);
            if (x.length >= 8) r += '-' + x.slice(7, 11);
            this.value = r;
        });

        // Validação simples antes de enviar o formulário
        function validarFornecedor() {
            const nome = document.getElementById('nome').value.trim();
            const telefone = document.getElementById('telefone').value.trim();
            const email = document.getElementById('email').value.trim();
            const cnpj = document.getElementById('cnpj').value.trim();
            const insc = document.getElementById('insc_estadual').value.trim();
            const endereco = document.getElementById('endereco').value.trim();

            if (!nome || !telefone || !email || !cnpj || !insc || !endereco) {
                alert("Por favor, preencha todos os campos!");
                return false;
            }

            // Validação simples de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("Digite um email válido!");
                return false;
            }

            // Validação simples de formato de telefone (apenas tamanho, pois já tem máscara)
            if (telefone.replace(/\D/g, '').length < 10) {
                alert("Digite um telefone válido!");
                return false;
            }

            // Validação simples de CNPJ (apenas tamanho, não matemática)
            if (cnpj.replace(/\D/g, '').length < 14) {
                alert("Digite um CNPJ válido!");
                return false;
            }

            return true; // se tudo certo, envia o formulário
        }


        // Máscara de nome: apenas letras e espaços
        document.getElementById('nome').addEventListener('input', function() {
            // Remove tudo que não for letra ou espaço
            this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
        });

        // Máscara para Inscrição Estadual (000.000.000.000)
        document.getElementById('insc_estadual').addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '').slice(0, 12); // Apenas números, no máximo 12
            let formatted = '';

            if (val.length > 3) formatted = val.slice(0, 3) + '.';
            else formatted = val;
            if (val.length > 6) formatted += val.slice(3, 6) + '.';
            else if (val.length > 3) formatted += val.slice(3, 6);
            if (val.length > 9) formatted += val.slice(6, 9) + '.';
            else if (val.length > 6) formatted += val.slice(6, 9);
            if (val.length > 9) formatted += val.slice(9, 12);

            this.value = formatted;
        });
        </script>

        <script src="js/javascript.js"></script>
</body>

</html>