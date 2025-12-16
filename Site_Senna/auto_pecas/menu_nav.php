<?php
    require_once 'conexao.php';

    if (isset($_GET['produto']) && !empty($_GET['produto'])) {
        $pesquisa = $_GET['produto'];

        $sql = "SELECT id_peca, nome, valor, imagem_capa FROM peca WHERE nome = :nome LIMIT :limite OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindValue (":nome", "$pesquisa%");
        $stmt -> bindValue (":limite", $limite, PDO::PARAM_INT);
        $stmt -> bindValue (":offset", $offset, PDO::PARAM_INT);
        $stmt -> execute();
        $pecas = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja Senna AutoPeças</title>
    <link rel="stylesheet" href="css/styles.css">

</head>
<body>
        
        <!--INICIO NAVEGAÇÃO-->
        <div class="navbar">
            <div class="navbar-content">

                <div class="nav-left">
                    <a href=""><img src="img/icones/user_com_circulo.svg" alt="User" class="icone_user"></a>
                    <img src="img/logo_branca_navbar.png" alt="logo" width="150px">
                </div>


                <form class="campo_pesquisa" action="produtos.php" method="GET">
                    <input type="text" name="procura_produto" id="procura_produto" placeholder="Buscar produto...">

                    <button type="submit">
                        <ion-icon name="search-outline" class="icon"></ion-icon>
                    </button>
                </form>

                <div class="nav-right">
                    <img src="img/icones/favoritos.png" alt="Favoritos">
                    <a href="historico_compras.php"><img src="img/icones/sacola_compras.png" alt="Sacola"></a>
                    <a href="carrinho.php"><img src="img/icones/carrinho_vazio.svg" alt="Carrinho"></a>
                    <a href="../index.php"><button class="btn-logout">Logout  &#8594;</button></a>
                </div>

            </div>
        </div>
        <!--FIM NAVEGAÇÃO-->




        <!--INICIO NAVEGAÇÃO-->
        <div class="navbar2">
            <div class="navbar-content">
                <!--INICIO MENU DE NAVEGAÇÃO-->
                <nav>
                    <ul id="MenuItens2">
                        <li><a href="loja.php" title="">Inicio</a></li>
                        <li><a href="produtos.php" title="">Produtos</a></li>
                        <li><a href="" title="">Categorias</a></li>
                        <li><a href="" title="">Marcas</a></li>
                    </ul>   
                </nav>
            </div>
        </div>
        <!--FIM NAVEGAÇÃO-->



</body>
</html>