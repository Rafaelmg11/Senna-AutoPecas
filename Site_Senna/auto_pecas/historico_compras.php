<?php
    session_start();
    require_once 'conexao.php';

    //Inicializa a Variavel
    $pecas_compradas = [];

    // VARIAVEIS USADAS
    $id_usuario = $_SESSION['id_usuario'];
    $id_carrinho = $_SESSION['id_carrinho'];

    $stmt = $pdo -> prepare('SELECT ci.*, p.imagem_capa, p.valor, p.nome FROM compra_item AS ci 
                             INNER JOIN compra ON ci.id_compra = compra.id_compra 
                             INNER JOIN peca AS p ON p.id_peca = ci.id_peca
                             WHERE compra.id_usuario = :id_usuario');

    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $pecas_compradas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/carrinho.css">
</head>
<body>

    <!--MENU NAV BAR-->
    <?php include 'menu_nav.php';?>

    <div class="container_pagina_carrinho">

        <div class="container_carrinho">

            <div class="carrinho_itens">

                <div class="tabela-container-carrinho">
                    <table class="listar-tabela-carrinho">
                        <?php foreach($pecas_compradas as $item):?>
                            <tr style="height: 180px;">
                                <td>
                                    <div style="display:flex; align-items:center; gap:15px;">

                                            <div class="col-img">
                                                <img src="data:image/jpeg;base64,<?= !empty($item['imagem_capa']) ? base64_encode($item['imagem_capa']) : '' ?>" alt="Sem Imagem">
                                            </div>

                                            <div class="produto-info">
                                                <span class="nome"><?= htmlspecialchars($item['nome']) ?></span>

                                                
                                
                                                <div class="quantidade_carrinho">
                                                    <label>Quantidade:</label>
                                                
                                                    <span><?=htmlspecialchars($item['quantidade'])?></span>
                                                </div>

                                                <!-- Preço unitário -->
                                                <span class="preco_unit">R$ <?= number_format($item['valor'], 2, ',', '.') ?></span>
                                            </div>
                                    </div>
                                </td>

                                <td class="valor_total_produto">
                                    <span class="preco_total">R$ <?= number_format($item['valor'] * $item['quantidade'], 2, ',', '.') ?></span>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <script>
        // quando a página carregar, ajusta subtotais e total
        document.addEventListener('DOMContentLoaded', function () {
            
        });

        function formatBRL(number) {
            return 'R$ ' + Number(number).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function continuarCompra() {
            var confirma = confirm('Tem certeza que deseja finalizar a compra?');
            if (confirma) {
                window.location.href='finalizar_compra.php';
            } else {
                alert('Não tenha pressa, ela é inimiga da perfeição... A nossa inimiga...')
            }
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>