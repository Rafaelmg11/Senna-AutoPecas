<?php
session_start();
require_once 'includes/conexao.php';

// Permite apenas ADM ou Gerente cadastrar funcionário
if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1, 2, 4])) {
    echo "<script>alert('Acesso negado!');window.location.href='cadastrar_peca.php'</script>";
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoria = $_POST['categoria'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $qtde_estoque = $_POST['qtde_estoque'] ?? 0;
    $lote = $_POST['lote'] ?? '';
    $valor = $_POST['valor'] ?? 0;
    $id_fornecedor = $_POST['id_fornecedor'] ?? null;

    $peso = $_POST['peso'] ?? 0;
    $altura = $_POST['altura'] ?? 0;
    $largura = $_POST['largura'] ?? 0;
    $comprimento = $_POST['comprimento'] ?? 0;

    // Upload das imagens (BLOB)
    function getImage($name)
    {
        return (isset($_FILES[$name]) && $_FILES[$name]['error'] == 0)
            ? file_get_contents($_FILES[$name]['tmp_name'])
            : null;
    }

    $imagem_capa = getImage('imagem_capa');
    $imagem1 = getImage('imagem1');
    $imagem2 = getImage('imagem2');
    $imagem3 = getImage('imagem3');
    $imagem4 = getImage('imagem4');

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO peca 
                (categoria, nome, marca, descricao, qtde_estoque, lote, valor, id_fornecedor,
                 imagem_capa, imagem1, imagem2, imagem3, imagem4,
                 peso, altura, largura, comprimento)
                VALUES 
                (:categoria, :nome, :marca, :descricao, :qtde, :lote, :valor, :id_fornecedor,
                 :imagem_capa, :imagem1, :imagem2, :imagem3, :imagem4,
                 :peso, :altura, :largura, :comprimento)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':qtde', $qtde_estoque);
        $stmt->bindParam(':lote', $lote);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':id_fornecedor', $id_fornecedor);
        $stmt->bindParam(':imagem_capa', $imagem_capa, PDO::PARAM_LOB);
        $stmt->bindParam(':imagem1', $imagem1, PDO::PARAM_LOB);
        $stmt->bindParam(':imagem2', $imagem2, PDO::PARAM_LOB);
        $stmt->bindParam(':imagem3', $imagem3, PDO::PARAM_LOB);
        $stmt->bindParam(':imagem4', $imagem4, PDO::PARAM_LOB);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':altura', $altura);
        $stmt->bindParam(':largura', $largura);
        $stmt->bindParam(':comprimento', $comprimento);

        $stmt->execute();
        $pdo->commit();

        echo "<script>alert('Peça cadastrada com sucesso!'); window.location.href='cadastrar_peca.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro ao cadastrar peça: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// TRAZ OS FORNECEDORES EXISTENTES NO BANCO DE DADOS
$stmt = $pdo->prepare("SELECT id_fornecedor, nome FROM fornecedor");
$stmt->execute();
$fornecedores_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Peça</title>
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
            <h2>Cadastrar Peça</h2>
        </div>
        
        <form action="cadastrar_peca.php" method="post" enctype="multipart/form-data">
          <div class="formulario-linhas">
            <br><br>
            <!-- CLIENTE -->
            <div class="formulario-coluna cliente-coluna">
              <legend>Dados da Peça</legend>

              <label for="categoria">Categoria da Peça:</label>
              <input type="text" id="categoria" name="categoria" placeholder="Digite a categoria" required>

              <label for="nome">Nome da Peça:</label>
              <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" required>

              <label for="marca">Marca da Peça:</label>
              <input type="text" id="marca" name="marca" placeholder="Digite a marca" required>

              <label for="descricao">Descrição da Peça:</label>
              <textarea id="descricao" name="descricao" placeholder="Digite uma descrição" rows="3" required></textarea>
            </div> <br><br>

            <form action="cadastrar_peca.php" method="post" enctype="multipart/form-data">
                
                    <!-- 2. Descrição + Estoque + Fornecedor -->
                    <div class="formulario-coluna usuario-coluna">
                        <legend>...</legend>

                        <label for="qtde_estoque">Quantidade em Estoque:</label>
                        <input type="number" id="qtde_estoque" name="qtde_estoque" min="0" required>

                        <label for="lote">Lote:</label>
                        <input type="text" id="lote" name="lote" required>

                        <label for="valor">Valor da Peça (un.):</label>
                        <input type="text" id="valor" name="valor" required>

                        <label for="id_fornecedor">Fornecedor:</label>
                        <select name="id_fornecedor" id="id_fornecedor" required>
                            <?php foreach ($fornecedores_disponiveis as $fornecedor): ?>
                                <option value="<?= $fornecedor['id_fornecedor'] ?>"><?= $fornecedor['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div><br><br>

                <!-- LINHA 2: Informações Técnicas | Imagens -->
                <div class="formulario-linhas">

                    <!-- 3. Informações Técnicas -->
                    <div class="formulario-coluna cliente-coluna">
                        <legend>Informações Técnicas</legend>

                        <label for="peso">Peso (kg):</label>
                        <input type="number" id="peso" name="peso" step="0.01" required>

                        <label for="altura">Altura (cm):</label>
                        <input type="number" id="altura" name="altura" step="0.01" required>

                        <label for="largura">Largura (cm):</label>
                        <input type="number" id="largura" name="largura" step="0.01" required>

                        <label for="comprimento">Comprimento (cm):</label>
                        <input type="number" id="comprimento" name="comprimento" step="0.01" required>
                    </div><br><br>

                    <!-- 4. Imagens da Peça -->
                    <div class="formulario-coluna usuario-coluna">
                        <legend>Imagens da Peça</legend>

                        <label for="imagem_capa">Imagem de Capa:</label>
                        <input type="file" id="imagem_capa" name="imagem_capa" accept="image/*" required>

                        <label for="imagem1">Imagem 1:</label>
                        <input type="file" id="imagem1" name="imagem1" accept="image/*">

                        <label for="imagem2">Imagem 2:</label>
                        <input type="file" id="imagem2" name="imagem2" accept="image/*">

                        <label for="imagem3">Imagem 3:</label>
                        <input type="file" id="imagem3" name="imagem3" accept="image/*">

                        <label for="imagem4">Imagem 4:</label>
                        <input type="file" id="imagem4" name="imagem4" accept="image/*">

                        <!-- Container para exibir as imagens -->
                        <div id="preview-imagens" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;"></div>

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
        // Máscara de nome: apenas letras e espaços
        // document.getElementById('nome').addEventListener('input', function() {
        //     // Remove tudo que não for letra ou espaço
        //     this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
        // });

        // Máscara de lote: apenas letras maiúsculas e números
        document.getElementById('lote').addEventListener('input', function () {
            // Remove tudo que não for letra ou número
            this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        });

        // Validação para quantidade em estoque: apenas números
        document.getElementById('qtde_estoque').addEventListener('input', function () {
            // Remove qualquer caractere que não seja número
            this.value = this.value.replace(/\D/g, '');
        });



        // Validação em tempo real para aceitar apenas números positivos
        ['peso', 'altura', 'largura', 'comprimento'].forEach(id => {
            document.getElementById(id).addEventListener('input', function () {
                let value = parseFloat(this.value);
                if (isNaN(value) || value <= 0) {
                    this.style.borderColor = 'red';
                } else {
                    this.style.borderColor = 'green';
                }
            });
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
                    img.style.width = '100px';
                    img.style.height = '100px';
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