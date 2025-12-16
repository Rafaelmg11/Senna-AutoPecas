<?php
    session_start();

    require_once 'conexao.php';

    $msg = "";

    $id_usuario = $_SESSION['id_usuario'];
    $id_carrinho = $_SESSION['id_carrinho'];

    $valor_total = (float) 0;

    $sql = "SELECT car.id_carrinho, ci.id_item, ci.id_peca, ci.quantidade, p.nome, p.qtde_estoque, p.imagem_capa, p.valor
            FROM carrinho AS car
            INNER JOIN carrinho_item AS ci ON car.id_carrinho = ci.id_carrinho
            INNER JOIN peca AS p ON ci.id_peca = p.id_peca
            WHERE car.id_carrinho = :id_carrinho";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_carrinho',  $id_carrinho, PDO::PARAM_INT);

    try {
        $stmt -> execute();
        $carrinho_finalizado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<script> alert('Erro ao trazer seu carrinho, tente novamente!'); window.location.href='carrinho.php'; </script>";
    }

    // DEFININDO O 'VALOR TOTAL' QUE VAI PARA A TABELA 'compra'
    foreach ($carrinho_finalizado as $peca) {
        $valor_total += $peca['valor'] * $peca['quantidade'];
    }

    // TRAZ A DATA E O HORARIO DE QUE ESTÃO SENDO REALIZADOS
    $date = new DateTime("now", new DateTimeZone("America/Sao_Paulo"));
    $agora = $date->format("Y-m-d H:i:s");

    // INSERINDO NA TABELA DE COMPRA
    $query = "INSERT INTO compra (id_usuario, data_compra, tipo_pagamento, valor_total) VALUES (:id_usuario, :data_compra, :tipo_pagamento, :valor_total)";

    // DEFININDO TIPO DO PAGAMENTO
    $tipo_pagamento = "pix";

    $stmt = $pdo -> prepare($query);
    $stmt -> bindParam(':id_usuario', $id_usuario);
    $stmt -> bindParam(':data_compra', $agora);
    $stmt -> bindParam(':tipo_pagamento', $tipo_pagamento);
    $stmt -> bindParam(':valor_total', $valor_total);
    
    try {
        $stmt -> execute();
        $msg = "Compra efetuada com sucesso! Obrigado pela preferência.";
    } catch (PDOException $e) {
        $msg = "Houve um erro ao efetuar sua compra: " . $e -> getMessage();
    }

    // TRAZENDO DE VOLTA O ID DE COMPRA QUE ACABOU DE SER CADASTRADO
    $stmt = $pdo->prepare("SELECT * FROM compra WHERE id_usuario = :id ORDER BY id_compra DESC LIMIT 1");
    $stmt->bindParam(":id", $_SESSION['id_usuario']);
    $stmt->execute();
    $ultima_compra = $stmt->fetch(PDO::FETCH_ASSOC);

    // ADICIONANDO PRODUTOS AO compra_item
    foreach ($carrinho_finalizado as $peca) {
        $stmt = $pdo -> prepare('INSERT INTO compra_item (id_compra, id_peca, quantidade, valor_unitario) VALUES (:id_compra, :id_peca, :quantidade, :valor)');
        $stmt->bindParam(':id_compra', $ultima_compra['id_compra']);
        $stmt->bindParam(':id_peca', $peca['id_peca']);
        $stmt->bindParam(':quantidade', $peca['quantidade']);
        $stmt->bindParam(':valor', $peca['valor']);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            // echo "<script> alert('Erro ao adicionar o item na compra.') </script>";
            echo $e->getMessage();
        }
    }

    // EXCLUINDO OS ITENS DO CARRINHO
    $stmt = $pdo -> prepare("DELETE FROM carrinho_item WHERE id_carrinho = :id");
    $stmt->bindParam(':id', $_SESSION['id_carrinho']);
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "<script> alert('Erro ao retirar os itens do carrinho!'); window.location.href='carrinho.php'; </script>";
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizando Compra</title>

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/carrinho.css">
</head>
<body>
    <!--MENU NAV BAR-->
    <?php include 'menu_nav.php';?>

    <div class="container_compra_finalizada">

        <div class="container_mensagem">
            <h2><?=$msg?></h2>
        </div>

    </div>

</body>
</html>