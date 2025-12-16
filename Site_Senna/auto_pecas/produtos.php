<?php
    session_start();
    require_once 'conexao.php';

    //Inicializa a Variavel
    $pecas = [];

    //Quantidade de itens por página
    $limite = 16;
    
    //Página atual (padrão/inicio = 1)
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    if ($pagina < 1) $pagina = 1;

    // Offset = a partir de qual produto começa
    $offset = ($pagina - 1) * $limite;

    // Total de produtos
    $totalQuery = $pdo->query("SELECT COUNT(*) as total FROM peca");
    $total = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

    //Total por páginas
    $totalPaginas = ceil($total / $limite); //Arredonda pra cima

    //Puxa os produtos da página atual
    $sql = "SELECT id_peca, nome, valor, imagem_capa FROM peca LIMIT :limite OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindValue (':limite', $limite, PDO::PARAM_INT);
    $stmt -> bindValue (':offset', $offset, PDO::PARAM_INT);
    $stmt -> execute();
    $pecas = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    // LÓGICA DOS FILTROS !! LÓGICA DOS FILTROS !! LÓGICA DOS FILTROS !! LÓGICA DOS FILTROS !! LÓGICA DOS FILTROS
    if (isset($_GET['valor_min']) && isset($_GET['valor_max'])) {
        $valor_min = $_GET['valor_min'];
        $valor_max = $_GET['valor_max'];

        $sql = "SELECT id_peca, nome, valor, imagem_capa FROM peca WHERE valor BETWEEN :valor_min AND :valor_max LIMIT :limite OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindValue (':limite', $limite, PDO::PARAM_INT);
        $stmt -> bindValue (':offset', $offset, PDO::PARAM_INT);
        $stmt -> bindParam(':valor_min', $valor_min);
        $stmt -> bindParam(':valor_max', $valor_max);
        $stmt -> execute();
        $pecas = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    } else {
        $valor_min = 0;
        $valor_max = 99999;
    }

    if (isset($_GET['categoria'])) { // Verifica se o input de ID='categoria' já foi colocado
        if (!empty($_GET['categoria'])) {
            $categoria = $_GET['categoria'];

            $categoria_s = str_replace("'", "", $categoria);

            $stmt = $pdo -> prepare("SELECT* FROM peca WHERE categoria = :categoria");
            $stmt -> bindParam(":categoria", $categoria_s, PDO::PARAM_STR);
            $stmt -> execute();

            $pecas = $stmt -> fetchAll(PDO::FETCH_ASSOC);

            if(!$pecas) {
                echo "<script> alert('Não há produtos nessa categoria!') window.location.href='produtos.php'; </script>";
            }
        }
    }

    if (isset($_GET['id_fornecedor'])) { // Verifica se o input de ID='id_fornecedor' já foi colocado
        if (!empty($_GET['id_fornecedor'])) {
            $fornecedor = $_GET['id_fornecedor'];

            $fornecedor_s = str_replace("'", "", $fornecedor);

            $stmt = $pdo -> prepare("SELECT * FROM peca WHERE id_fornecedor = :fornecedor");
            $stmt -> bindParam(":fornecedor", $fornecedor_s, PDO::PARAM_INT);
            $stmt -> execute();

            $pecas = $stmt -> fetchAll(PDO::FETCH_ASSOC);

            if(!$pecas) {
                echo "<script> alert('Não há produtos nessa categoria!') window.location.href='produtos.php'; </script>";
            }
        }
    }

    if (isset($_GET['marca'])) { // Verifica se o input de ID='marca' já foi colocado
        if (!empty($_GET['marca'])) {
            $marca = $_GET['marca'];

            $marca_s = str_replace("'", "", $marca);

            $stmt = $pdo -> prepare("SELECT * FROM peca WHERE marca = :marca");
            $stmt -> bindParam(":marca", $marca_s, PDO::PARAM_STR);
            $stmt -> execute();

            $pecas = $stmt -> fetchAll(PDO::FETCH_ASSOC);

            if(!$pecas) {
                echo "<script> alert('Não há produtos nessa categoria!') window.location.href='produtos.php'; </script>";
            }
        }
    }

    function buscar_categorias($pdo) {
        $stmt = $pdo -> prepare("SELECT DISTINCT categoria FROM peca LIMIT 6");
        try {
            $stmt -> execute();
            $categorias = $stmt -> fetchAll(PDO::FETCH_ASSOC);
            if(!empty($categorias)) {
                return $categorias;
            }
        } catch (PDOException $e) {
            echo "Categorias não encontradas!";
        }
    }

    function buscar_fornecedores($pdo) {
        $stmt = $pdo -> prepare("SELECT DISTINCT p.id_fornecedor, f.nome AS 'nome_fornecedor' FROM peca AS p JOIN fornecedor AS f ON p.id_fornecedor = f.id_fornecedor LIMIT 6");
        try {
            $stmt -> execute();
            $fornecedor = $stmt -> fetchAll(PDO::FETCH_ASSOC);
            if(!empty($fornecedor)) {
                return $fornecedor;
            } 
        } catch (PDOException $e) {
            echo "Fornecedores não encontrados!<br>";
        }
    }

    function buscar_marcas($pdo) {
        $stmt = $pdo -> prepare("SELECT DISTINCT marca FROM peca LIMIT 6");
        try {
            $stmt -> execute();
            $marcas = $stmt -> fetchAll(PDO::FETCH_ASSOC);
            if(!empty($marcas)) {
                return $marcas;
            }
        } catch (PDOException) {
            echo "Marcas não encontradas!<br>";
        }
    }

function buscar_todas_categorias($pdo) {
    $stmt = $pdo->prepare("SELECT DISTINCT categoria FROM peca ORDER BY categoria ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buscar_todas_marcas($pdo) {
    $stmt = $pdo->prepare("SELECT DISTINCT marca FROM peca ORDER BY marca ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function buscar_todos_fornecedores($pdo) {
    $stmt = $pdo->prepare("SELECT DISTINCT p.id_fornecedor, f.nome AS nome_fornecedor 
                           FROM peca AS p 
                           JOIN fornecedor AS f ON p.id_fornecedor = f.id_fornecedor 
                           ORDER BY f.nome ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // LÓGICA DOS FILTROS !! LÓGICA DOS FILTROS !! LÓGICA DOS FILTROS !! LÓGICA DOS FILTROS !! LÓGICA DOS FILTROS

    if (isset($_GET['procura_produto']) && !empty($_GET['procura_produto'])) {
        $pesquisa = '%' . trim($_GET['procura_produto']) . '%'; // Busca em qualquer parte
    
        $sql = "SELECT id_peca, nome, valor, imagem_capa FROM peca 
                WHERE nome LIKE :nome 
                LIMIT :limite OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":nome", $pesquisa, PDO::PARAM_STR);
        $stmt->bindValue(":limite", $limite, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($_GET['procura_produto'])) {
        echo "<script> history.back(); </script>";
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>
<body>
    <!--MENU NAV BAR-->
    <?php include 'menu_nav.php'; include_once 'container_filtro.php';?>
    <div class="container_pagina_produtos">

        <div class="container_opcoes">
            <div class="opcoes">
                <div class="opcao_titulo">
                    <h2>Categoria</h2>
                    <span class="toggle open">+</span>
                </div>
                <div class="opcao_container show">
                    <?php $categorias = buscar_categorias($pdo); if(!empty($categorias)): foreach($categorias as $categoria):?>
                        <p><a href="produtos.php?categoria='<?=htmlspecialchars($categoria['categoria'])?>'"><?=htmlspecialchars($categoria['categoria'])?></a></p>
                    <?php endforeach; ?>
                    <?php endif; ?>

                   
                </div>
            </div>

            <div class="opcoes">
                <div class="opcao_titulo">
                    <h2>Marca</h2>
                    <span class="toggle">+</span>
                </div>
                <div class="opcao_container show">
                    <?php $marcas = buscar_marcas($pdo); if(!empty($marcas)): foreach($marcas as $marca):?>
                        <p><a href="produtos.php?marca='<?=htmlspecialchars($marca['marca'])?>'"><?=htmlspecialchars($marca['marca'])?></a></p>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    
                </div>
            </div>
            
            <div class="opcoes">
                <div class="opcao_titulo">
                    <h2>Preço</h2>
                    <span class="toggle">+</span>
                </div>
                <div class="opcao_container show">
                    <form action="produtos.php" method="GET">
                        <p>Preço Min.</p>
                        <input type="number" name="valor_min" class="valor_fil" id="valor_min" min=0 max=99999 value="<?=$valor_min?>">
    
                        <p>Preço Máx.</p>
                        <input type="number" name="valor_max" class="valor_fil" id="valor_max" min=0 max=99999 value="<?=$valor_max?>">
                        
                        <button type="submit" class="btn_valor_fil">Filtrar</button>
                    </form>
                </div>
            </div>

            <div class="opcoes">
                <div class="opcao_titulo">
                    <h2>Fornecedor</h2>
                    <span class="toggle">+</span>
                </div>
                <div class="opcao_container show">
                    <?php $fornecedores = buscar_fornecedores($pdo); if(!empty($fornecedores)): foreach($fornecedores as $fornecedor):?>
                        <p><a href="produtos.php?id_fornecedor='<?=htmlspecialchars($fornecedor['id_fornecedor'])?>'"><?=htmlspecialchars($fornecedor['nome_fornecedor'])?></a></p>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    
                </div>
            </div>
        </div>

        <div class="container-multiplos-produtos">

        <?php if(!empty($pecas)):?>
            <?php foreach ($pecas as $peca):?>
                <div class="card-produto"> <!--INICIO ITEM PRODUTO-->
                    <div class="imagem-produto">
                        <a href="ver_produto.php?id_peca=<?= $peca['id_peca'] ?>">
                        <img src="data:image/png;base64,<?= base64_encode($peca['imagem_capa'])?>" alt="">
                        </a>

                        <span class="favorito">
                            <ion-icon name="heart-outline"></ion-icon>
                        </span>
                    </div>
                    <br>
                    <h6><?=htmlspecialchars($peca['nome'])?></h6>
                    <div class="classificacao">
                        <ion-icon name="star"></ion-icon>
                        <ion-icon name="star"></ion-icon>
                        <ion-icon name="star"></ion-icon>
                        <ion-icon name="star"></ion-icon>
                        <ion-icon name="star"></ion-icon> 
                    </div>
                        <h4><?=htmlspecialchars($peca['valor'])?></h4>
                    </div> <!--FIM ITEM PRODUTO-->

            <?php endforeach;?>
        <?php endif;?>

        <div class="paginacao_container">
            <div class="paginacao">
                <!-- MOSTRA O NÚMERO DA PÁGINA ATUAL -->
                <h5 style="margin-bottom: 10px;">Página <?= $pagina ?> de <?= $totalPaginas ?></h5>
                <br>
                <?php
                    //Botão anterior
                    if ($pagina > 1){
                        echo '<a href="?pagina=' . ($pagina - 1) . ' "><span>&#8592;</span></a>';
                    }

                    //Quantos números mostrar de cada vez
                    $maxLinks = 4;

                    //Primeira página fixa
                    echo '<a href="?pagina=1"><span>1</span></a>';

                    if ($pagina > $maxLinks + 2){
                        echo '<span>...</span>';
                    }

                    //Números centrais
                    $inicio = max(2, $pagina - 1);
                    $fim = min($totalPaginas - 1, $pagina + 2);

                    for ($i = $inicio; $i <= $fim; $i++){
                        if ($i == $pagina){
                            echo '<span style="">' . $i . '</span>'; 
                        }else{
                            echo '<a href="?pagina=' . $i . '"><span>' . $i . '</span></a>';
                        }
                    }

                    if ($pagina < $totalPaginas - 3){
                        echo '<span>...</span>';
                    }

                    //Última página fixa
                    if ($totalPaginas > 1){
                        echo '<a href="?pagina=' . $totalPaginas . '"><span>' . $totalPaginas . '</span></a>';
                    }

                    //Botão próxima
                    if ($pagina < $totalPaginas){
                        echo '<a href="?pagina=' . ($pagina + 1) . '"><span>&#8594;</span></a>';
                    }
                ?>
            </div>
        </div>
    </div>
    
    <script>
        function verTodos(tipo) {
            if (tipo == 'fornecedor') {
                
            }

            if (tipo == 'categoria') {
                
            }

            if (tipo == 'marca') {
                
            }
        }
    </script>

<!-- ================================
     MODAL CATEGORIA
================================ -->
<div id="modal-categoria" class="modal">
  <div class="modal-content">
    <span class="close" onclick="fecharModal('modal-categoria')">&times;</span>
    <h2>Todas as Categorias</h2>
    <div class="cliente-info">
      <?php 
      $todasCategorias = buscar_todas_categorias($pdo);
      if (!empty($todasCategorias)):
        foreach ($todasCategorias as $cat): ?>
          <p>
            <strong>Categoria:</strong> 
            <a href="produtos.php?categoria='<?=htmlspecialchars($cat['categoria'])?>'">
              <?=htmlspecialchars($cat['categoria'])?>
            </a>
          </p>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>

<!-- ================================
     MODAL MARCA
================================ -->
<div id="modal-marca" class="modal">
  <div class="modal-content">
    <span class="close" onclick="fecharModal('modal-marca')">&times;</span>
    <h2>Todas as Marcas</h2>
    <div class="cliente-info">
      <?php 
      $todasMarcas = buscar_todas_marcas($pdo);
      if (!empty($todasMarcas)):
        foreach ($todasMarcas as $marca): ?>
          <p>
            <strong>Marca:</strong> 
            <a href="produtos.php?marca='<?=htmlspecialchars($marca['marca'])?>'">
              <?=htmlspecialchars($marca['marca'])?>
            </a>
          </p>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>

<!-- ================================
     MODAL FORNECEDOR
================================ -->
<div id="modal-fornecedor" class="modal">
  <div class="modal-content">
    <span class="close" onclick="fecharModal('modal-fornecedor')">&times;</span>
    <h2>Todos os Fornecedores</h2>
    <div class="cliente-info">
      <?php 
      $todosFornecedores = buscar_todos_fornecedores($pdo);
      if (!empty($todosFornecedores)):
        foreach ($todosFornecedores as $forn): ?>
          <p>
            <strong>Fornecedor:</strong> 
            <a href="produtos.php?id_fornecedor='<?=htmlspecialchars($forn['id_fornecedor'])?>'">
              <?=htmlspecialchars($forn['nome_fornecedor'])?>
            </a>
          </p>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>


<script>
function verTodos(tipo) {
    if (tipo === 'categoria') {
        document.getElementById("modal-categoria").style.display = "block";
    }
    if (tipo === 'marca') {
        document.getElementById("modal-marca").style.display = "block";
    }
    if (tipo === 'fornecedor') {
        document.getElementById("modal-fornecedor").style.display = "block";
    }
}

function fecharModal(id) {
    document.getElementById(id).style.display = "none";
}

// Fecha ao clicar fora
window.onclick = function(event) {
    const modals = document.querySelectorAll(".modal");
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}
</script>




    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="javascript/favoritar.js"></script>
    <script src="javascript/pro_lado.js"></script>
    <script src="javascript/accordion.js"></script>
    
</body>
</html>