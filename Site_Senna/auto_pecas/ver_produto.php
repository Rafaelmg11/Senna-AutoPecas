<?php

    session_start();
    require_once 'conexao.php';

    // Inicializa variáveis para evitar erros
    $peca = []; 
    $vendas = [];    
    $cep_destino = "";
    $pac = [];
    $sedex = [];

    // Carrega a peça (e seu fornecedor) + total de vendas, se foi passado id_peca
    if (isset($_GET['id_peca']) || isset($_POST['id_peca'])) {

        // Pega o id_peca tanto de GET quanto de POST (POST tem prioridade se ambos existirem)
        $id_peca = $_GET['id_peca'] ?? $_POST['id_peca'];

        // Busca a peça e o nome de fornecedor de acordo com as FK
        $sql = "SELECT p.*, f.nome AS 'nome_fornecedor' 
                FROM peca AS p 
                INNER JOIN fornecedor AS f ON p.id_fornecedor = f.id_fornecedor 
                WHERE p.id_peca = :id_peca";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_peca', $id_peca, PDO::PARAM_INT);
        $stmt->execute();
        $peca = $stmt->fetch(PDO::FETCH_ASSOC);

        //Conta quantas vezes a peça ja foi comprada de acordo com as FK
        $sql = "SELECT COUNT(id_peca) AS vendas FROM compra_item WHERE id_peca = :id_peca";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindValue(':id_peca', $id_peca, PDO::PARAM_INT);
        $stmt->execute();
        $vendas = $stmt ->fetch(PDO::FETCH_ASSOC);
    }


    // Se o formulário foi enviado via POST e existe o campo 'cep', calcula frete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cep'])) {

        // Normaliza o CEP de destino: remove tudo que não é dígito
        $cepDestino = preg_replace('/\D/', '', $_POST['cep']);
        $cepOrigem = '89219510'; // CEP fixo da loja



        // Medidas e peso do produto
        // Formatações para evitar erros

        // Altura mínima 2 cm
        $altura = max(2, intval($peca['altura']));

        // Largura mínima 11 cm
        $largura = max(11, intval($peca['largura']));

        // Comprimento mínimo 16 cm
        $comprimento = max(16, intval($peca['comprimento']));

        // Peso mínimo 0.3 kg
        $peso = max(0.3, floatval($peca['peso']));


        //API do Melhor Envio
        $dados = [
            'from' => ['postal_code' => $cepOrigem],  // Cep do remetende
            'to' => ['postal_code' => $cepDestino],  // Cep do destinatario
            'products' => [[
                'width' => $largura, //Largura da peça em cm
                'height' => $altura, //Altura da peça em cm
                'length' => $comprimento, //Comprimento da peça em cm
                'weight' => $peso //Peso da peça em kg
            ]],
        ];



        // Configuração do cURL para chamar a API

        // Oque é o curl: O curl é uma biblioteca/ferramenta ue serve para fazer requisições
        //HTTP direto do PHP. Permitindo chamar uma API externa. 
        //(Ele é como um navegador(não abrimos ele, porém o php abre "por baixo dos panos"))
        
        $curl = curl_init(); //Cria um handle -> Representa a requisição (abre a aba do navegador)
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://www.melhorenvio.com.br/api/v2/me/shipment/calculate",
            CURLOPT_RETURNTRANSFER => true, //Retorna resposta como string
            CURLOPT_CUSTOMREQUEST => "POST", //Method POST
            CURLOPT_POSTFIELDS => json_encode($dados), //Converte o corpo da requisação($dados) em JSON
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNWNhNzhiMWY0MTQ5ZjJkMDRlZDQwMTY1N2RlZjkyMWY2NjRiM2Q4ZDJmMzFhNDIzYzEzYjdiYzM1NzdlZjQyNTljNGIyYTc1MjI2YWJjZmYiLCJpYXQiOjE3NTY5NjMxMjEuNzU5ODE5LCJuYmYiOjE3NTY5NjMxMjEuNzU5ODIsImV4cCI6MTc4ODQ5OTEyMS43NDc4NTUsInN1YiI6IjllZjA1Zjc1LTQ1MmYtNGUyYy04ZDEwLTdhYmNmMjQ0MmRiOCIsInNjb3BlcyI6WyJzaGlwcGluZy1jYWxjdWxhdGUiXX0.v9BrK85rDSgDt6x8IiPyIVeBNqUWmznryWlQpxuP-ghb3vY5p1xWEGZxfdwaXjsr3UL5deXvHwMLBVtkLfb8RqZEQqgNw8B2eyvn-WZ6Xus8xG9mXp-dE1GaKEqr7mmB5hxfT_uQGgMQtBwVBnuucgFry9LkBb-KCLP4Wtpp7H36UYfju21s61tQHhW0jtDhjxvtLXl21tVILagD94nXDkohIKUPElphASkuBdjw44sAteX0ED6BqWFPA_XV6tltaoWf5dyKnyJ01ETlttdYfuhXOBpoMtzCeSpz_46kgBlIKtZ69yWUz3liG2DNxrljGLDve_o1-ZTFu8iN6aPnkIL1Z0E6AMBPh30x1v4hSOuHOAcq7IOX5DYs3K47JFBJsBZoALvvp_RBIhnEEjPxDESe9SOQSB0NNzEEgL7bvSdbdh-h7r-u6ht7xyhSKhWMZfOsMpXhNVJrmO-n_g3dEhi-nPWwcLXAnzcrtTfOEwYuW915OwII_cmVczH8ZlXgW6J8JaKwwTUl3QkDL9bDV-n0blWYzjwHhJ0M1s4BHbbvzTl5nEQ0OeMxKwKwWTFQJbXFQ3JqJ9gHP_WeUNRDK6QAak8RAGRLRufv0vNqb7XSFGPBAl9-xIb7ubbr-RuXQM4ujyU5WxxtPOiXaDkzPJmEEUYMMPzw64CpBjIe0Q4" 
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        // Executa a requisição e captura a resposta como string JSON
        $response = curl_exec($curl);

        // Se deu erro de cURL (problema de rede/token/etc.), interrompe com mensagem
        if ($response === false) {
            die('Erro cURL: ' . curl_error($curl));
        }

        // Encerra o handle do cURL/Fecha a requisição (fecha a aba do navegador)
        curl_close($curl);

        // Transforma o JSON para array PHP
        $resultado = json_decode($response, true);

        // Zera os arrays de serviços (garantia)
        $pac = [];
        $sedex = [];

        // Se a resultado for um array , percorre os serviços retornados
        if (is_array($resultado)) {
            foreach ($resultado as $servico) {

                // Algumas transportadoras podem vir com 'error'. Verifica as transportadoras com erro
                if (isset($servico['error'])) {
                    continue; // ignora transportadora indisponível
                }

                // Normaliza o nome para maiúsculas e seleciona os serviços desejados
                if (strtoupper($servico['name']) === 'PAC') {
                    $pac = $servico;
                }
                if (strtoupper($servico['name']) === 'SEDEX') {
                    $sedex = $servico;
                }
            }
        }

        // Se nenhum dos dois serviços foi encontrado, mostra alerta e redireciona de volta
        if (empty($pac) && empty($sedex)) {
            echo "<script>alert('PAC e SEDEX não disponíveis para este trecho!'); 
                window.location.href='ver_produto.php?id_peca={$peca['id_peca']}';</script>";
            exit();
        }
    }


    //INCIALIZA A VARIAVEL
    $pecas = [];

    $sql = "SELECT id_peca, nome, descricao, valor, imagem_capa FROM peca LIMIT 8";
    $stmt = $pdo->prepare($sql);
    $stmt -> execute();
    $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Produto</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>
<body>
    <!--MENU NAV BAR-->
    <?php include 'menu_nav.php';?>

    <div class="container_pagina_produto">

        <div class="container_produto">

            <div class="container_imagens">
                <div class="container_imagem">

                    <div class="container_mini_imagens">
                        <div class="mini-box"><img src="data:image/png;base64,<?= base64_encode($peca['imagem1'])?>" alt="Sem Imagem"></div>
                        <div class="mini-box"><img src="data:image/png;base64,<?= base64_encode($peca['imagem2'])?>" alt="Sem Imagem"></div>
                        <div class="mini-box"><img src="data:image/png;base64,<?= base64_encode($peca['imagem3'])?>" alt="Sem Imagem"></div>
                        <div class="mini-box"><img src="data:image/png;base64,<?= base64_encode($peca['imagem4'])?>" alt="Sem Imagem"></div>
                    </div>

                    <div class="container_imagem_capa">
                        <img src="data:image/jpeg;base64,<?= !empty($peca['imagem_capa']) ? base64_encode($peca['imagem_capa']) : '' ?>" alt="Sem Imagem">
                    </div>

                </div>

                <div class="container_informacao-3">
                        <h2>Descrição</h4>
                        <p><?=htmlspecialchars($peca['descricao'])?></p>
                </div>

                <div class="tabela-container">
                    <table border="1" class="listar-tabela">
                        <tr>
                            <td>Categoria:</td>
                            <td><?=htmlspecialchars($peca['categoria'])?></td>
                        </tr>

                        <tr>
                            <td>Marca:</td>
                            <td><?=htmlspecialchars($peca['marca'])?></td>
                        </tr>

                        <tr>
                            <td>Montadora:</td>
                            <td><?=htmlspecialchars($peca['montadora'])?></td>
                        </tr>

                        <tr>
                            <td>Fornecedor:</td>
                            <td><?=htmlspecialchars($peca['nome_fornecedor'])?></td>
                        </tr>

                        <tr>
                            <td>Lote:</td>
                            <td><?=htmlspecialchars($peca['lote'])?></td>
                        </tr>

                        <tr>
                            <td>Peso(Kg):</td>
                            <td><?=htmlspecialchars($peca['peso'])?></td>
                        </tr>

                        <tr>
                            <td>Altura(cm):</td>
                            <td><?=htmlspecialchars($peca['altura'])?></td>
                        </tr>

                        <tr>
                            <td>Largura(cm):</td>
                            <td><?=htmlspecialchars($peca['largura'])?></td>
                        </tr>

                        <tr>
                            <td>Comprimento(cm):</td>
                            <td><?=htmlspecialchars($peca['comprimento'])?></td>
                        </tr>

                    </table>
                </div>

            </div>




            <div class="container_frete_compra">

                <div class="container_informacao-1">
                    <img src="img/icones/fornecedor.png" alt="">
                    <p><?=htmlspecialchars($peca['nome_fornecedor'])?></p>
                    <span class="favorito">
                        <ion-icon name="heart-outline"></ion-icon>
                    </span>
                </div>
                <p id='vendas'><?=htmlspecialchars($vendas['vendas'])?> vendas</p>

                <div class="container_informacao-2">
                    <p id="nome"><?=htmlspecialchars($peca['nome'])?></p>
                    <div class="classificacao">
                        <p>4.91 </p>
                        <ion-icon name="star"></ion-icon>
                        <ion-icon name="star"></ion-icon>
                        <ion-icon name="star"></ion-icon>
                        <ion-icon name="star"></ion-icon>
                        <ion-icon name="star"></ion-icon> 
                        <p> (202)</p>
                    </div>
                    <p id="valor">R$<?=htmlspecialchars($peca['valor'])?></p>
                    <p id="desconto_pix">(10% desconto no pix)</p>

                    <div class="quantidade">
                        <label for="quantidade">Quantidade:</label>
                        <form action="carrinho_back-end" method="POST" id="form_quantidade">
                            
                            <select for="valor" id="quantidade" name="quantidade">
                                <?php 
                                    // gera opções de 1 até o estoque disponível
                                    for ($i = 1; $i <= $peca['qtde_estoque']; $i++): 
                                ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>                

                        </form>
                    </div>
                    <p style="color: gray;" id="qtde_estoque_ver_produto">Estoque: <?= htmlspecialchars($peca['qtde_estoque']) ?></p>
                    
                    <div class="container_compra">
                        <div class="botoes">
                            <button id="button_comprar">Comprar Agora</button>
                            <br>
                            <button id="button_carrinho" onclick="var qtde = document.getElementById('quantidade');
                                                                  window.location.href='carrinho.php?peca_add=<?=$peca['id_peca']?>&qtde='+qtde.value;
                                                                  ">Adicionar ao Carrinho</button>
                        </div>
                    </div>                    

                </div>
                
                <hr>

                <div class="container_frete">
                    <p id="fretegratis">FRETE GRÁTIS ACIMA DE R$179.90</p>

                    <div id="resultado_frete">
                        <h4><img src="img/icones/caminhao_frete.png" alt="" id="icone_frete">Fretes disponíveis:</h4>

                        <!--Verifica se a variavel $pac não está vazia-->
                        <?php if (!empty($pac)): ?>
                            <!--Nome do serviço + nome da empresa transportadora-->
                            <p><strong><?= htmlspecialchars($pac['name']) ?> - <?= htmlspecialchars($pac['company']['name']) ?></strong></p>
                            <p>Valor: R$ <?= number_format($pac['price'], 2, ',', '.') ?></p> <!--Formata o valor | Duas casas decimais(2), separador decimal(,), separador de milhar(.)-->
                            <p>Prazo: <?= htmlspecialchars($pac['delivery_time']) ?> dias</p> <!--Prazo de entrega-->
                            <hr>

                        <?php else: ?> <!--Se não existir $pac-->
                            <p>PAC Não calculado.</p>
                        <?php endif; ?>

                        <!--Verifica se a variavel $sedex não está vazia-->
                        <?php if (!empty($sedex)): ?>
                            <!--Nome do serviço + nome da empresa transportadora-->
                            <p><strong><?= htmlspecialchars($sedex['name']) ?> - <?= htmlspecialchars($sedex['company']['name']) ?></strong></p>
                            <p>Valor: R$ <?= number_format($sedex['price'], 2, ',', '.') ?></p> <!--Formata o valor | Duas casas decimais(2), separador decimal(,), separador de milhar(.)-->
                            <p>Prazo: <?= htmlspecialchars($sedex['delivery_time']) ?> dias</p> <!--Prazo de entrega-->
                        <?php else: ?><!--Se não existir $sedex-->
                            <p>Não calculado.</p>
                        <?php endif; ?>
                    </div>

                    <form action="ver_produto.php?id_peca=<?= $peca['id_peca'] ?>" method="POST">
                        <input type="text" name="cep" id="cep" value="<?= htmlspecialchars($cepDestino ?? '')?>" placeholder="Digite seu CEP" required>
                        <input type="hidden" name="id_peca" value="<?= $peca['id_peca'] ?>">
                        <br>
                        <button type="submit" name="calcular_frete" id="button_frete">Calcular Frete</button>
                    </form>

                    <hr>
                    
                    <div class="mini_informacoes">
                        <p><ion-icon name="shield-checkmark-outline" class="icon_mini"></ion-icon>Compra Garantida, receba o produto que está esperando ou devolvemos o dinheiro.</p>
                        <p><ion-icon name="ribbon" class="depoimento-icone" class="icon_mini"></ion-icon>30 dias de garantia de fábrica.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="container_pagina_produto2">

        <div class="container-produto"> <!--INICIO PRODUTOS EM DESTAQUE-->

            <br>

            <div class="dots">
                <h2 class="titulo">Produtos Relacionados</h2>
                <span class="dot active"></span>
                <span class="dot"></span>
            </div>

            <hr class="hr-divisa-produto">

            <div class="carousel-produtos"> <!--INICIO CAROUSEL DE PRODUTOS-->

                <button class="btn-esquerdo">&#10094;</button> <!-- seta esquerda -->
                
                <div class="linha" id="linha-produtos"> <!--INICIO LINHA DO PRODUTO-->
                <?php if(!empty($pecas)):?>
                    <?php foreach ($pecas as $peca2):?>
                        <div class="card-produto"> <!--INICIO ITEM PRODUTO-->
                            <div class="imagem-produto">
                                <a href="ver_produto.php?id_peca=<?=$peca2['id_peca']?>">
                                <img src="data:image/png;base64,<?= base64_encode($peca2['imagem_capa'])?>" alt="">
                                </a>

                                <span class="favorito">
                                    <ion-icon name="heart-outline"></ion-icon>
                                </span>
                            </div>
                            <br>
                            <h6><?=htmlspecialchars($peca2['nome'])?></h6>
                            <div class="classificacao">
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon>
                                <ion-icon name="star"></ion-icon> 
                            </div>
                            <h4><?=htmlspecialchars($peca2['valor'])?></h4>
                        </div> <!--FIM ITEM PRODUTO-->

                    <?php endforeach;?>
                <?php endif;?>
                </div> <!--FIM LINHA DO PRODUTO-->
                
                <button class="btn-direito">&#10095;</button> 

            </div> <!--FIM CAROUSEL DE PRODUTOS-->
        </div>

    </div>
    
    <script>
        // Seleciona a imagem principal (capa)
        const capa = document.querySelector('.container_imagem_capa img');

        // Seleciona todas as miniaturas
        const miniaturas = document.querySelectorAll('.container_mini_imagens .mini-box img');

        miniaturas.forEach(mini => {
            mini.addEventListener('click', () => {
                // Troca a imagem da capa pela miniatura clicada
                const tempSrc = capa.src;
                capa.src = mini.src;
                mini.src = tempSrc;
            });
        });
    </script>

    

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const selectQuantidade = document.getElementById("quantidade");
            const valorQuantidade = document.getElementById("valor");

            const valorUnitario = parseFloat("<?=($peca['valor'])?>");

            function atualizarValor() {
                const quantidade = parseInt(selectQuantidade.value) || 1;
                const valorTotal = valorUnitario * quantidade;

                valorQuantidade.textContent = "R$" + valorTotal.toFixed(2);
            }

            selectQuantidade.addEventListener("change", atualizarValor);
            atualizarValor(); // Atualiza o valor ao carregar a página

        });

    </script>

    <script src="javascript/favoritar.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="javascript/pro_lado.js"></script>

</body>
</html>