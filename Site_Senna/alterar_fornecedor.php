<?php
session_start();
require_once 'includes/conexao.php';

$fornecedor = [];
$fornecedores = [];

// Se tiver ID na URL (GET), busca os dados
if (!empty($_GET['busca'])) {
    $busca = trim($_GET['busca']);

    if (is_numeric($busca)) {
        // Busca por ID
        $stmt = $pdo->prepare("SELECT * FROM fornecedor WHERE id_fornecedor = :id");
        $stmt->execute([':id' => (int)$busca]);
        $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Busca por nome (parcial)
        $stmt = $pdo->prepare("SELECT * FROM fornecedor WHERE nome LIKE :nome");
        $stmt->execute([':nome' => "$busca%"]);
        $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($fornecedores) {
            $qnt_linhas = count($fornecedores);
    
            if ($qnt_linhas === 1) {
                $fornecedor = $fornecedores[0];
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Fornecedor</title>
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
                <h2>Alterar Fornecedor</h2>
            </div>

            <!-- Barra de pesquisa -->
            <div class="formulario-coluna">
                <form method="get" action="alterar_fornecedor.php">
                    <label for="busca">Buscar Fornecedor (ID ou Nome):</label>
                    <input type="text" id="busca" name="busca" required>
                    <button type="submit" class="botao"><i class="fa-solid fa-search"></i> Buscar</button>
                </form>

            </div>

            <br>

            <!-- Formulário principal -->
            <div class="formulario-coluna cliente-coluna">
                <form action="backend/fornecedor/processa_alteracao_fornecedor.php" method="post">
                    <input type="hidden" name="id_fornecedor" value="<?= htmlspecialchars($fornecedor['id_fornecedor'] ?? '') ?>">

                    <legend>Dados da Empresa</legend>

                    <label for="nome">Nome da Empresa:</label>
                    <input type="text" id="nome" name="nome"
                        placeholder="Digite o nome completo"
                        value="<?= htmlspecialchars($fornecedor['nome'] ?? '') ?>">

                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="telefone"
                        placeholder="(xx) xxxxx-xxxx"
                        value="<?= htmlspecialchars($fornecedor['telefone'] ?? '') ?>">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                        placeholder="Digite o email"
                        value="<?= htmlspecialchars($fornecedor['email'] ?? '') ?>">

                    <label for="cnpj">CNPJ:</label>
                    <input type="text" id="cnpj" name="cnpj"
                        placeholder="Digite o CNPJ"
                        value="<?= htmlspecialchars($fornecedor['cnpj'] ?? '') ?>">

                    <label for="insc_estadual">Inscrição Estadual:</label>
                    <input type="text" id="insc_estadual" name="insc_estadual"
                        placeholder="xxx.xxx.xxx.xxx"
                        value="<?= htmlspecialchars($fornecedor['insc_estadual'] ?? '') ?>">

                    <label for="endereco">Endereço:</label>
                    <input type="text" id="endereco" name="endereco"
                        placeholder="Rua , 123, Bairro, Cidade - UF"
                        value="<?= htmlspecialchars($fornecedor['endereco'] ?? '') ?>">


                    <!-- Botões -->
                    <div class="botoes">
                        <button type="submit"><i class="fa-solid fa-pen-to-square"></i> Alterar</button>
                        <button type="reset"><i class="fa-solid fa-xmark"></i> Cancelar</button>
                    </div>
                </form>

                <!-- TABELA SE BUSCAR MAIS DE UM FORNECEDOR -->
                <?php if(count($fornecedores) > 1): ?>
                    <div class="formulario-coluna tabela">
                        <?php if (!empty($fornecedores)): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Endereço</th>
                                        <th>E-mail</th>
                                        <th>CNPJ</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fornecedores as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['id_fornecedor']) ?></td>
                                            <td><?= htmlspecialchars($p['nome']) ?></td>
                                            <td style="width: 180px !important;"><?= htmlspecialchars($p['endereco']) ?></td>
                                            <td><?= htmlspecialchars($p['email']) ?></td>
                                            <td><?= htmlspecialchars($p['cnpj']) ?></td>

                                            <td>
                                                <a href="alterar_fornecedor.php?busca=<?= htmlspecialchars($p['id_fornecedor']) ?>">
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

    <script>
        // Nome: apenas letras e espaços
        document.getElementById('nome').addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
        });

        // Telefone com máscara
        document.getElementById('telefone').addEventListener('input', function() {
            let x = this.value.replace(/\D/g, '').slice(0, 11);
            let r = '';
            if (x.length > 0) r = '(' + x.slice(0, 2);
            if (x.length >= 3) r += ') ' + x.slice(2, 7);
            if (x.length >= 8) r += '-' + x.slice(7, 11);
            this.value = r;
        });

        // CNPJ com máscara
        document.getElementById('cnpj').addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '').slice(0, 14);
            let r = '';
            if (v.length > 0) r = v.slice(0, 2);
            if (v.length > 2) r += '.' + v.slice(2, 5);
            if (v.length > 5) r += '.' + v.slice(5, 8);
            if (v.length > 8) r += '/' + v.slice(8, 12);
            if (v.length > 12) r += '-' + v.slice(12, 14);
            this.value = r;
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