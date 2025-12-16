<?php
session_start();
require_once 'includes/conexao.php';


$peca = [];
$pecas = [];
$fornecedores = [];

// Pega a peça pelo ID, se informado
if (!empty($_GET['busca'])) {
    $busca = $_GET['busca'];

    if (is_numeric($busca)) {
        // Busca por ID
        $stmt = $pdo->prepare("SELECT * FROM peca WHERE id_peca = :busca");
        $stmt->execute([':busca' => (int)$busca]);

        $peca = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Busca por NOME (parcial)
        $stmt = $pdo->prepare("SELECT p.*, f.nome as nome_fornecedor FROM peca p JOIN fornecedor f ON p.id_fornecedor = f.id_fornecedor WHERE p.nome LIKE :busca");
        $stmt->execute([':busca' => "%$busca%"]);
        $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($pecas) {
            $qnt_linhas = count($pecas);
    
            if ($qnt_linhas === 1) {
                $peca = $pecas[0];
            }
        }
    }
}


// Pega todos os fornecedores
$stmt = $pdo->query("SELECT id_fornecedor, nome FROM fornecedor ORDER BY nome");
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para exibir imagens existentes
function exibeImagem($imagem) {
    if ($imagem) {
        $base64 = base64_encode($imagem);
        return "<br><img src='data:image/jpeg;base64,{$base64}' style='max-width:150px; max-height:150px; margin-bottom:5px;'><br>";
    }
    return "";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alterar Peça</title>
<link rel="stylesheet" href="css/main_css.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php include_once 'includes/sidebar.php'; ?>

<div class="container">


    <!-- CONTEÚDO -->
    <div class="conteudo">

        <!-- Título -->
        <div class="titulo">
            <h2>Alterar Peça</h2>
        </div>

        <!-- Barra de pesquisa -->
        <div class="formulario-coluna">
        <form action="alterar_peca.php" method="get">
            <label for="busca_peca">Pesquisar Peça (ID ou NOME):</label>
            <input type="text" id="busca_peca" name="busca" placeholder="Digite o ID ou NOME" required>
            <button type="submit" class="botao"><i class="fa-solid fa-search"></i> Buscar</button>
        </form>

        </div>
<br>
        <!-- Formulário principal -->
        <form action="backend/peca/processa_alteracao_peca.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_peca" value="<?= htmlspecialchars($peca['id_peca'] ?? '') ?>">

            <!-- LINHA 1: Dados da Peça | Estoque/Fornecedor -->
            <div class="formulario-linhas">

                <!-- 1. Dados da Peça -->
                <div class="formulario-coluna cliente-coluna">
                    <legend>Dados da Peça</legend>

                    <label for="categoria">Categoria da Peça:</label>
                    <input type="text" id="categoria" name="categoria" 
                           placeholder="Motor, Freios..." 
                           value="<?= htmlspecialchars($peca['categoria'] ?? '') ?>">

                    <label for="nome">Nome da Peça:</label>
                    <input type="text" id="nome" name="nome" 
                           placeholder="Nome da peça" 
                           value="<?= htmlspecialchars($peca['nome'] ?? '') ?>">

                    <label for="descricao">Descrição da Peça:</label>
                    <textarea id="descricao" name="descricao" rows="3"><?= htmlspecialchars($peca['descricao'] ?? '') ?></textarea>
                </div>
 <br>
                <!-- 2. Estoque + Lote + Valor + Fornecedor -->
                <div class="formulario-coluna usuario-coluna">
                    <legend>Estoque e Fornecedor</legend>

                    <label for="qtde_estoque">Quantidade em Estoque:</label>
                    <input type="number" id="qtde_estoque" name="qtde_estoque" 
                           value="<?= htmlspecialchars($peca['qtde_estoque'] ?? '') ?>">

                    <label for="lote">Lote:</label>
                    <input type="text" id="lote" name="lote" 
                           value="<?= htmlspecialchars($peca['lote'] ?? '') ?>">

                    <label for="valor">Valor da Peça (un.):</label>
                    <input type="number" step="0.01" id="valor" name="valor" 
                           value="<?= htmlspecialchars($peca['valor'] ?? '') ?>">

                    <label for="id_fornecedor">Fornecedor:</label>
                    <select id="id_fornecedor" name="id_fornecedor" required>
                        <option value="">Selecione</option>
                        <?php foreach($fornecedores as $f): ?>
                            <option value="<?= $f['id_fornecedor'] ?>" 
                                <?= ($peca['id_fornecedor'] == $f['id_fornecedor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
 <br>
            <!-- LINHA 2: Informações Técnicas | Imagens -->
            <div class="formulario-linhas">

                <!-- 3. Informações Técnicas -->
                <div class="formulario-coluna cliente-coluna">
                    <legend>Informações Técnicas</legend>

                    <label for="peso">Peso (kg):</label>
                    <input type="number" step="0.01" id="peso" name="peso" 
                           value="<?= htmlspecialchars($peca['peso'] ?? '') ?>">

                    <label for="altura">Altura (cm):</label>
                    <input type="number" step="0.01" id="altura" name="altura" 
                           value="<?= htmlspecialchars($peca['altura'] ?? '') ?>">

                    <label for="largura">Largura (cm):</label>
                    <input type="number" step="0.01" id="largura" name="largura" 
                           value="<?= htmlspecialchars($peca['largura'] ?? '') ?>">

                    <label for="comprimento">Comprimento (cm):</label>
                    <input type="number" step="0.01" id="comprimento" name="comprimento" 
                           value="<?= htmlspecialchars($peca['comprimento'] ?? '') ?>">
                </div>
 <br>
                <!-- 4. Imagens -->
                <div class="formulario-coluna usuario-coluna">
                    <legend>Imagens da Peça</legend>
                    <?php foreach (['imagem_capa','imagem1','imagem2','imagem3','imagem4'] as $img): ?>
                        <label for="<?= $img ?>"><?= ucfirst(str_replace('_',' ',$img)) ?>:</label>
                        <?= exibeImagem($peca[$img] ?? null) ?>
                        <input type="file" id="<?= $img ?>" name="<?= $img ?>" accept="image/*">
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- BOTÕES -->
            <div class="botoes">
                <button type="submit"><i class="fa-solid fa-pen-to-square"></i> Alterar</button>
                <button type="reset"><i class="fa-solid fa-xmark"></i> Cancelar</button>
            </div>
        </form>

        <!-- tabela para se aparecer mais de uma peça -->
        <?php if(count($pecas) > 1): ?>
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
                                            <div class="acoes"></div>

                                                    <a href="alterar_peca.php?busca=<?= htmlspecialchars($p['id_peca']) ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i> Alterar
                                                    </a>
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
        <?php endif; ?>
    </div>
</div>

<script>
// Lote: apenas letras maiúsculas e números
document.getElementById('lote').addEventListener('input', function() {
    this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
});

// Estoque: apenas números
document.getElementById('qtde_estoque').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
});
</script>

<script src="js/javascript.js"></script>
</body>
</html>
