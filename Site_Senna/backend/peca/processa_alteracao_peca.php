<?php
session_start();
require_once '../../includes/conexao.php';

// Verifica se o usuário tem permissão (ajuste os perfis conforme seu sistema)
if (!isset($_SESSION['id_perfil']) || !in_array($_SESSION['id_perfil'], [1,2,3])) {
    echo "<script>alert('Acesso negado!');window.location.href='../../alterar_peca.php'</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_peca       = $_POST['id_peca'] ?? null;
    $categoria     = $_POST['categoria'] ?? null;
    $nome          = $_POST['nome'] ?? null;
    $descricao     = $_POST['descricao'] ?? null;
    $qtde_estoque  = $_POST['qtde_estoque'] ?? null;
    $lote          = $_POST['lote'] ?? null;
    $valor         = $_POST['valor'] ?? null;
    $id_fornecedor = $_POST['id_fornecedor'] ?? null;
    $peso          = $_POST['peso'] ?? null;
    $altura        = $_POST['altura'] ?? null;
    $largura       = $_POST['largura'] ?? null;
    $comprimento   = $_POST['comprimento'] ?? null;

    if (!$id_peca) {
        echo "<script>alert('ID da peça não informado!'); window.history.back();</script>";
        exit();
    }

    // Monta os campos do UPDATE
    $campos = [
        "categoria = :categoria",
        "nome = :nome",
        "descricao = :descricao",
        "qtde_estoque = :qtde_estoque",
        "lote = :lote",
        "valor = :valor",
        "id_fornecedor = :id_fornecedor",
        "peso = :peso",
        "altura = :altura",
        "largura = :largura",
        "comprimento = :comprimento"
    ];

    $params = [
        ':id_peca'      => $id_peca,
        ':categoria'    => $categoria,
        ':nome'         => $nome,
        ':descricao'    => $descricao,
        ':qtde_estoque' => $qtde_estoque,
        ':lote'         => $lote,
        ':valor'        => $valor,
        ':id_fornecedor'=> $id_fornecedor,
        ':peso'         => $peso,
        ':altura'       => $altura,
        ':largura'      => $largura,
        ':comprimento'  => $comprimento
    ];

    // Atualiza imagens se enviadas
    foreach (['imagem_capa', 'imagem1', 'imagem2', 'imagem3', 'imagem4'] as $img) {
        if (!empty($_FILES[$img]['tmp_name'])) {
            $campos[] = "$img = :$img";
            $params[":$img"] = file_get_contents($_FILES[$img]['tmp_name']);
        }
    }

    $sql = "UPDATE peca SET " . implode(", ", $campos) . " WHERE id_peca = :id_peca";

    try {
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if (strpos($key, 'imagem') !== false && $value !== null) {
                $stmt->bindParam($key, $params[$key], PDO::PARAM_LOB);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->execute();

        echo "<script>alert('Peça alterada com sucesso!'); window.location.href='../../alterar_peca.php?id={$id_peca}';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Erro ao alterar peça: ". addslashes($e->getMessage()) ."'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Acesso inválido!'); window.location.href='../../alterar_peca.php';</script>";
}
