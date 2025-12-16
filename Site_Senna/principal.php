<?php
session_start();

require_once 'includes/conexao.php';

$niveis = [
    1 => "Administrador",
    2 => "Gerente",
    3 => "Funcionário",
    4 => "Almoxarife",
    5 => "Cliente"
];

$nivelUsuario = $niveis[$_SESSION['id_perfil']] ?? "Desconhecido";
$id_usuario = $_SESSION['id_usuario'];

$stmt = $pdo -> prepare('SELECT imagem FROM funcionario WHERE id_usuario = :id');
$stmt->bindParam(':id', $id_usuario);
$stmt->execute();
$imagem = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['imagem_usuario'] = $imagem['imagem'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boas-vindas</title>

    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #e6ebf0);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .welcome-card {
            background: #fff;
            padding: 60px 50px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
            max-width: 800px;
            width: 95%;
            animation: fadeIn 0.8s ease-in-out;
        }

        .welcome-card img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #ddd;
            margin-bottom: 20px;
        }

        .welcome-card h1 {
            font-size: 2.4rem;
            margin-bottom: 10px;
            color: #222;
        }

        .welcome-card p {
            font-size: 1.2rem;
            color: #555;
            margin: 10px 0;
        }

        .welcome-card .role {
            font-weight: bold;
            color: #0077b6;
            font-size: 1.3rem;
            margin-bottom: 25px;
        }

        .btn-start {
            display: inline-block;
            margin-top: 25px;
            padding: 15px 30px;
            background: #0077b6;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn-start:hover {
            background: #023e8a;
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <!-- Foto de Perfil -->
        <?php
$temFoto = !empty($_SESSION['imagem_usuario']);
?>

<?php if ($temFoto): ?>
    <img src="data:image/jpeg;base64,<?= base64_encode($_SESSION['imagem_usuario']) ?>" alt="Foto de Perfil">
<?php endif; ?>

             
        <!-- Nome e nível -->
        <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']); ?>!</h1>
        <p class="role"><?= $nivelUsuario; ?></p>

        <!-- Apresentação -->
        <p><strong>Senna Auto Peças</strong> é especializada em soluções automotivas com qualidade, confiança e compromisso.</p>
        <p>Trabalhamos com os melhores fornecedores para garantir o melhor custo-benefício para nossos clientes.</p>

        <!-- Botão para acessar o sistema -->
        <a href="main.php" class="btn-start">Acessar o Sistema</a>
    </div>
</body>
</html>
