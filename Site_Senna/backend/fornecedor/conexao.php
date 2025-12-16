<?php
    // VARIAVÉIS QUE SERÃO USADAS PARA CONEXÃO
    $database = "senna_db";
    $host = "localhost";
    $usuario = "root";
    $senha = "";

    try {
        // Objeto PDO que será utilizado nas outras classes
        $pdo = new PDO("mysql:host=$host;dbname=$database", $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Usado para tratamento de erros
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Torna o método 'FETCH_ASSOC' padrão de todo fetch que for feito
        ]);

        return $pdo;
    } catch (Exception $e) {
        die("Erro de conexão com o banco de dados: " . $e->getMessage());
    }
?>