<?php
    session_start();
    require_once 'conexao.php';

    //INCIALIZA A VARIAVEL
    $pecas = [];

    $sql = "SELECT id_peca, nome, descricao, valor, imagem_capa FROM peca LIMIT 16";
    $stmt = $pdo->prepare($sql);
    $stmt -> execute();
    $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja Senna Auto Peças</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>
<body>
    
    <!--MENU NAV BAR-->
    <?php include 'menu_nav.php';?>

    <div class="container_pagina">

        <!--INICIO CARROUSEL-->
        <div id="carouselExampleIndicators" class="carousel slide">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                <img src="img/carro1.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                <img src="img/carro2.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                <img src="img/carro3.jpg" class="d-block w-100" alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <!--FIM CARROUSEL-->

        
        <div class="container-produto"> <!--INICIO PRODUTOS EM DESTAQUE-->

            <br>

            <div class="dots">
                <h2 class="titulo">Produtos em Destaque</h2>
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>

            <hr class="hr-divisa-produto">

            <div class="carousel-produtos"> <!--INICIO CAROUSEL DE PRODUTOS-->

                <button class="btn-esquerdo">&#10094;</button> <!-- seta esquerda -->
                
                <div class="linha" id="linha-produtos"> <!--INICIO LINHA DO PRODUTO-->
                <?php if(!empty($pecas)):?>
                    <?php foreach ($pecas as $peca):?>
                        <div class="card-produto"> <!--INICIO ITEM PRODUTO-->
                            <div class="imagem-produto">
                                <a href="ver_produto.php?id_peca=<?=$peca['id_peca']?>">
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
                </div> <!--FIM LINHA DO PRODUTO-->
                
                <button class="btn-direito">&#10095;</button> 

            </div> <!--FIM CAROUSEL DE PRODUTOS-->
        </div>
    </div>


    

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="javascript/favoritar.js"></script>
    <script src="javascript/pro_lado.js"></script>
</body>
</html>

