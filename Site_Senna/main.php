<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_perfil'])) {
    header("Location: principal.php");
    exit;
}

$niveis = [
    1 => "Administrador",
    2 => "Gerente",
    3 => "Funcionário",
    4 => "Almoxarife",
    5 => "Cliente"
];
?>

<link rel="stylesheet" href="css/main_css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main_css.css">
    <title>Main</title>

</head>
<body>
    <?php include_once 'includes/sidebar.php'; ?>
    <script src="js/javascript.js"></script>
</body>
</html>