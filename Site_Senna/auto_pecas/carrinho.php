<?php
    session_start();
    include_once "conexao.php";
    $id_usuario = $_SESSION['id_usuario'];

    $_SESSION['produtos_selecionados'][] = [];

    // Busca a peça e o nome de fornecedor de acordo com as FK
    $sql = "SELECT car.id_carrinho, ci.id_item, ci.id_peca, ci.quantidade, p.nome, p.qtde_estoque, p.imagem_capa, p.valor
            FROM carrinho AS car
            INNER JOIN carrinho_item AS ci ON car.id_carrinho = ci.id_carrinho
            INNER JOIN peca AS p ON ci.id_peca = p.id_peca
            WHERE car.id_usuario = :id_usuario";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_usuario',  $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['exclui_peca']) && !empty($_GET['exclui_peca'])) {
        $id_item = $_GET['exclui_peca'];
        $id_peca = $_GET['id_peca'];
        $id_carrinho = $_SESSION['id_carrinho'];

        $query = "DELETE FROM carrinho_item WHERE id_item = :id_item AND id_carrinho = :id_carrinho AND id_peca = :id_peca";
        $stmt = $pdo -> prepare($query);

        $stmt -> bindParam(':id_item', $id_item);
        $stmt -> bindParam(':id_carrinho', $id_carrinho);
        $stmt -> bindParam(':id_peca', $id_peca);

        try {
            $stmt -> execute();
            header("Location: carrinho.php");
            exit();
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao excluir do carrinho!') </script>";
        }
    }

    if (isset($_GET['peca_add']) && !empty($_GET['peca_add'])) {
        $id_peca = $_GET['peca_add'];
        $id_carrinho = $_SESSION['id_carrinho'];
        $qtde = $_GET['qtde'];

        $query = "INSERT INTO carrinho_item (id_carrinho, id_peca, quantidade) VALUES (:id_carrinho, :id_peca, :qtde)";
        $stmt = $pdo -> prepare($query);

        $stmt -> bindParam(':id_carrinho', $id_carrinho);
        $stmt -> bindParam(':id_peca', $id_peca);
        $stmt -> bindParam(':qtde', $qtde);

        try {
            $stmt -> execute();
            header("Location: carrinho.php");
            exit();
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao adicionar no carrinho!'); history.back(); </script>";
        }
    }

    if (isset($_GET['alterar_qtde']) && !empty($_GET['alterar_qtde'])) {
        $id_item = $_GET['id_item'];
        $id_carrinho = $_SESSION['id_carrinho'];
        $id_peca = $_GET['alterar_qtde'];
        $qtde = $_GET['qtde'];

        $query = "UPDATE carrinho_item SET quantidade = :qtde WHERE id_item = :id_item AND id_carrinho = :id_carrinho AND id_peca = :id_peca";
        $stmt = $pdo -> prepare($query);

        $stmt -> bindParam(':qtde', $qtde);
        $stmt -> bindParam(':id_item', $id_item);
        $stmt -> bindParam(':id_carrinho', $id_carrinho);
        $stmt -> bindParam(':id_peca', $id_peca);

        try {
            $stmt -> execute();
            header("Location: carrinho.php");
            exit();
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao alterar quantidade no carrinho!') </script>";
        }
    }
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
                        <?php foreach($items as $item):?>
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
                                                
                                                    <div class="qtde-container_carrinho">
                                                        <button type="button" class="qtde-btn-carrinho" onclick="alterarQtde(this, -1)">−</button>

                                                        <input type="number" class="qtde_input_carrinho" 
                                                                              name="quantidade"
                                                                              id="quantidade_produto"
                                                                              value="<?= $item['quantidade'] ?>" 
                                                                              min="1" max="<?= $item['qtde_estoque'] ?>" 
                                                                              readonly 
                                                                              data-valor="<?= $item['valor'] ?>" 
                                                                              data-original="<?= $item['quantidade'] ?>">
                                                                              
                                                        <button type="button" class="qtde-btn-carrinho" onclick="alterarQtde(this, 1)">+</button>

                                                        <div class="btn-salvar-container" style="display: none;">
                                                            <button name="salvar_nova_quantidade" 
                                                                    class="salvar_nova_quantidade"
                                                                    style="width: 57px; background-color: #49c549ff;";
                                                                    onclick="var nova_quantidade = document.getElementById('quantidade_produto');
                                                                             window.location.href='carrinho.php?alterar_qtde=<?=$item['id_peca']?>&id_item=<?=$item['id_item']?>&qtde='+nova_quantidade.value;">
                                                                             Salvar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Preço unitário -->
                                                <span class="preco_unit">R$ <?= number_format($item['valor'], 2, ',', '.') ?></span>
                                            </div>
                                    </div>
                                </td>

                                <td class="valor_total_produto">
                                    <span class="preco_total">R$ <?= number_format($item['valor'] * $item['quantidade'], 2, ',', '.') ?></span>
                                </td>

                                <td class="col_lixo_carrinho">
                                    <button type="button" class="remover_carrinho" onclick="var confirma = confirm('Tem certeza que deseja excluir esse produto do carrinho?'); 
                                                                                            if(confirma) { window.location.href='carrinho.php?exclui_peca=<?=$item['id_item']?>&id_peca=<?=$item['id_peca']?>'}  ">
                                        <ion-icon name="trash-outline" class="lixo_carrinho"></ion-icon>
                                    </button>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </table>

                </div>

            </div>

            <div class="resumo_compra">  </div>

        </div>

    </div>




    <script>
        // quando a página carregar, ajusta subtotais e total
        document.addEventListener('DOMContentLoaded', function () {
            atualizarTodosSubtotais();
            atualizarTotalCompra();
            monitorarMudancaQuantidade();
        });

        function alterarQtde(botao, valor) {
            if (!botao) return;
            const container = botao.parentElement;
            const input = container.querySelector(".qtde_input_carrinho");
            if (!input) return;

            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max) || Infinity;
            let atual = parseInt(input.value) || min;

            atual += valor;
            if (atual < min) atual = min;
            if (atual > max) atual = max;

            input.value = atual;

            // recalcular subtotal da linha
            // normaliza vírgula -> ponto caso o dataset venha com vírgula
            const precoUnit = parseFloat(String(input.dataset.valor).replace(',', '.')) || 0;
            const subtotal = atual * precoUnit;

            const linha = botao.closest("tr");
            const spanTotal = linha.querySelector(".preco_total");
            if (spanTotal) {
                spanTotal.textContent = formatBRL(subtotal);
            }

            atualizarTotalCompra();

            const original = parseInt(input.dataset.original) || 0;
            const containerQtde = input.closest('.qtde-container_carrinho');
            const btnSalvar = containerQtde.querySelector('.btn-salvar-container');

            if (btnSalvar) {
                btnSalvar.style.display = (original !== atual) ? 'block' : 'none';
            }
        }

        function monitorarMudancaQuantidade() {
            document.querySelectorAll('.qtde_input_carrinho').forEach(function(input) {
                input.addEventListener('change', function () {
                    const original = parseInt(input.dataset.original) || 0;
                    const atual = parseInt(input.value) || 0;

                    const container = input.closest('.qtde-container_carrinho');
                    const btnSalvar = container.querySelector('.btn-salvar-container');

                    if (btnSalvar) {
                        btnSalvar.style.display = (original !== atual) ? 'block' : 'none';
                    }
                });
            });
        }

        function formatBRL(number) {
            return 'R$ ' + Number(number).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function atualizarTotalCompra() {
            let total = 0;
            let qtdTotal = 0;

            document.querySelectorAll('.preco_total').forEach(function(span) {
                // texto "R$ 1.234,56" -> converte para número
                let txt = span.textContent || '';
                txt = txt.replace(/\s/g, '').replace('R$', '').replace(/\./g, '').replace(',', '.');
                const val = parseFloat(txt) || 0;
                total += val;
            });

            // soma as quantidades
            document.querySelectorAll('.qtde_input_carrinho').forEach(function(input) {
                qtdTotal += parseInt(input.value) || 0;
            });

            const resumo = document.querySelector('.resumo_compra');
            resumo.innerHTML = `
                <div class="resumo_box" style="padding:15px; border-radius:8px; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <h3>Resumo da compra</h3>
                    <hr>
                    <p>Produtos: <strong>${qtdTotal}</strong></p>
                    <p class="frete_carrinho">Frete: Gratís</p>
                    <p>Total: <strong>${formatBRL(total)}</strong></p>
                    <button onclick="finalizarCompra()" class="comprar_carrinho"> Finalizar Compra </button>
                    <button onclick="continuarCompra()" class="comprar_carrinho"> Continuar Comprando </button>
                </div>
            `;
        }


        function atualizarTodosSubtotais() {
            document.querySelectorAll('.qtde_input_carrinho').forEach(function(input) {
                const qtd = parseInt(input.value) || 0;
                const precoUnit = parseFloat(String(input.dataset.valor).replace(',', '.')) || 0;
                const subtotal = qtd * precoUnit;
                const linha = input.closest('tr');
                const spanTotal = linha.querySelector('.preco_total');
                if (spanTotal) spanTotal.textContent = formatBRL(subtotal);
            });
        }

        function continuarCompra() {
            window.location.href='produtos.php';
        }

        function finalizarCompra() {
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