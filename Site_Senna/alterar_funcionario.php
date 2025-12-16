<?php
session_start();
require_once 'includes/conexao.php';

$funcionario = [];
$funcionarios = [];

// Se houver ID, buscar dados do funcionário
if (!empty($_GET['busca'])) {
    $busca = $_GET['busca'];

    if (is_numeric($busca)) {
        $stmt = $pdo->prepare("SELECT * FROM funcionario WHERE id_funcionario = :id");
        $stmt->execute([':id' => $busca]);
        $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
    } else { // Se o cliente não foi buscado por um ID (ou valor inteiro)
        $stmt = $pdo->prepare("SELECT * FROM funcionario WHERE nome_funcionario LIKE :nome");
        $stmt->execute([':nome' => "$busca%"]);
        $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($funcionarios) {
            $qnt_linhas = count($funcionarios);

            if ($qnt_linhas === 1) {
                $funcionario = $funcionarios[0];
            }
        } else {
            echo "<script>alert('Funcionário não encontrado!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Funcionário</title>
    <link rel="stylesheet" href="css/main_css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <?php include_once 'includes/sidebar.php'; ?>

    <div class="container">
        <div class="conteudo">

            <!-- Título -->
            <div class="titulo">
                <h2>Alterar Funcionário</h2>
            </div>

            <!-- Barra de pesquisa -->
            <!-- Barra de pesquisa -->
            <div class="formulario-coluna">
                <form action="alterar_funcionario.php" method="get">
                    <label for="busca_funcionario">Pesquisar Funcionário (ID ou NOME):</label>
                    <input type="text" id="busca" name="busca" placeholder="Digite o ID ou NOME" min="1" required>
                    <button type="submit" class="botao"><i class="fa-solid fa-search"></i> Buscar</button>
                </form>
            </div>

<br>
            <!-- Formulário principal -->
            <form action="backend/funcionario/processa_alteracao_funcionario.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_funcionario" value="<?= htmlspecialchars($funcionario['id_funcionario'] ?? '') ?>">

                <!-- LINHA 1: Dados Pessoais | Contato -->
                <div class="formulario-linhas">

                    <!-- Dados Pessoais -->
                    <div class="formulario-coluna cliente-coluna">
                        <legend>Dados Pessoais</legend>

                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome"
                            value="<?= htmlspecialchars($funcionario['nome_funcionario'] ?? '') ?>">

                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00"
                            value="<?= htmlspecialchars($funcionario['cpf'] ?? '') ?>">

                        <label for="nascimento">Data de Nascimento:</label>
                        <input type="date" id="nascimento" name="nascimento"
                            value="<?= htmlspecialchars($funcionario['data_nascimento'] ?? '') ?>">

                        <label for="endereco">Endereço:</label>
                        <input type="text" id="endereco" name="endereco" placeholder="Rua, 123, Bairro, Cidade - UF"
                            value="<?= htmlspecialchars($funcionario['endereco'] ?? '') ?>">

                    </div>
<br>
                    <!-- Dados Profissionais -->
                    <div class="formulario-coluna cliente-coluna">
                        <legend>Dados Profissionais</legend>

                        <label for="cargo">Cargo:</label>
                        <input type="text" id="cargo" name="cargo"
                            value="<?= htmlspecialchars($funcionario['cargo'] ?? '') ?>">

                        <label for="salario">Salário:</label>
                        <input type="text" id="salario" name="salario"
                            value="<?= htmlspecialchars($funcionario['salario'] ?? '') ?>">

                        <label for="admissao">Data de Admissão:</label>
                        <input type="date" id="admissao" name="admissao"
                            value="<?= htmlspecialchars($funcionario['data_admissao'] ?? '') ?>">

                                                
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" placeholder="(xx) xxxxx-xxxx"
                            value="<?= htmlspecialchars($funcionario['telefone'] ?? '') ?>">
                    </div>
<br>
<br>
<br>
                </div>
                <br>


                    <!-- LINHA 2: Dados Profissionais | Foto -->
                    <br>
                    
                    <div class="formulario-linhas">
                        <!-- Foto -->
                        <div class="formulario-coluna usuario-coluna">
                            <legend>Foto do Funcionário</legend>

                            <label for="foto">Selecionar Imagem:</label>
                            <input type="file" id="foto" name="foto" accept="image/*">

                            <!-- Foto atual -->
                            <?php if (!empty($funcionario['imagem'])): ?>
                                <div style="margin-top:10px; text-align:center;">
                                    <p><strong>Foto atual:</strong></p>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($funcionario['imagem']) ?>"
                                        alt="Foto Atual"
                                        style="max-width:150px; border-radius:5px; border:1px solid #ccc;">
                                </div>
                            <?php endif; ?>

                            <!-- Nova foto (preview) -->
                            <div id="preview-container" style="margin-top:15px; text-align:center; display:none;">
                                <p id="nova-foto-texto" style="font-weight:bold; margin-bottom:5px;">Nova foto:</p>
                                <div id="preview-imagens"
                                    style="display:flex; justify-content:center; align-items:center;"></div>
                            </div>
                        </div>
                    </div>

<br>
                <!-- BOTÕES -->
                <div class="botoes">
                    <button type="submit"><i class="fa-solid fa-pen-to-square"></i> Alterar</button>
                    <button type="reset"><i class="fa-solid fa-xmark"></i> Cancelar</button>
                </div>
            </form>

            <?php if(count($funcionarios) > 1): ?>
                <div class="formulario-coluna tabela">
                    <?php if (!empty($funcionarios)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Telefone</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($funcionarios as $f): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($f['id_funcionario']) ?></td>
                                        <td><?= htmlspecialchars($f['nome_funcionario']) ?></td>
                                        <td><?= htmlspecialchars($f['cpf']) ?></td>
                                        <td><?= htmlspecialchars($f['telefone']) ?></td>
                                        <td>
                                            <a href="alterar_funcionario.php?busca=<?= htmlspecialchars($f['id_funcionario']) ?>">
                                                <i class="fa-solid fa-pen-to-square"></i> Alterar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
                </div>
            </div>
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

        // Nome e cargo: apenas letras e espaços
        ['nome', 'cargo'].forEach(id => {
            document.getElementById(id).addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
            });
        });

        // Salário: apenas números, ponto ou vírgula
        document.getElementById('salario').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });
    </script>

    <script src="js/javascript.js"></script>

<script>
    const input = document.getElementById('foto');
    const previewContainer = document.getElementById('preview-container');
    const previewImagens = document.getElementById('preview-imagens');

    input.addEventListener('change', () => {
        // Limpa preview anterior
        previewImagens.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '150px';
                img.style.height = '150px';
                img.style.objectFit = 'cover';
                img.style.border = '1px solid #ccc';
                img.style.borderRadius = '5px';
                previewImagens.appendChild(img);

                // Exibe o bloco de nova foto
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            // Se não escolher nada, esconde o preview
            previewContainer.style.display = 'none';
        }
    });
</script>


</body>

</html>