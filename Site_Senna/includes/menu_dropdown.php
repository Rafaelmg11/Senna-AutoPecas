<?php ?>
<link rel="stylesheet" href="css/css_do_menu.css?v=2">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="sidebar">
    <div class="logo">
        <img src="1.png" alt="Logo da Empresa" onclick="window.location.href='principal.php'" style="cursor:pointer;">
    </div>

    <ul class="menu">
        <li class="menu-item">
            <a href="javascript:void(0)" class="has-submenu">
                <i class="fa-solid fa-plus"></i> Cadastrar <i class="fa-solid fa-caret-down submenu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="cadastrar_funcionario.php"><i class="fa-solid fa-user-plus"></i> Funcionário</a></li>
                <li><a href="cadastrar_fornecedor.php"><i class="fa-solid fa-truck"></i> Fornecedor</a></li>
                <li><a href="cadastrar_cliente.php"><i class="fa-solid fa-users"></i> Cliente</a></li>
                <li><a href="cadastrar_peca.php"><i class="fa-solid fa-cogs"></i> Peça</a></li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0)" class="has-submenu">
                <i class="fa-solid fa-pen-to-square"></i> Alterar <i class="fa-solid fa-caret-down submenu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="alterar_funcionario.php"><i class="fa-solid fa-user-pen"></i> Funcionário</a></li>
                <li><a href="alterar_fornecedor.php"><i class="fa-solid fa-truck-ramp-box"></i> Fornecedor</a></li>
                <li><a href="alterar_cliente.php"><i class="fa-solid fa-user-gear"></i> Cliente</a></li>
                <li><a href="alterar_peca.php"><i class="fa-solid fa-screwdriver-wrench"></i> Peça</a></li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0)" class="has-submenu">
                <i class="fa-solid fa-trash"></i> Excluir <i class="fa-solid fa-caret-down submenu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="excluir_funcionario.php"><i class="fa-solid fa-user-xmark"></i> Funcionário</a></li>
                <li><a href="excluir_fornecedor.php"><i class="fa-solid fa-truck-droplet"></i> Fornecedor</a></li>
                <li><a href="excluir_cliente.php"><i class="fa-solid fa-user-slash"></i> Cliente</a></li>
                <li><a href="excluir_peca.php"><i class="fa-solid fa-ban"></i> Peça</a></li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0)" class="has-submenu">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar <i class="fa-solid fa-caret-down submenu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="buscar_funcionario.php"><i class="fa-solid fa-user"></i> Funcionário</a></li>
                <li><a href="buscar_fornecedor.php"><i class="fa-solid fa-truck-fast"></i> Fornecedor</a></li>
                <li><a href="buscar_cliente.php"><i class="fa-solid fa-users-viewfinder"></i> Cliente</a></li>
                <li><a href="buscar_peca.php"><i class="fa-solid fa-gears"></i> Peça</a></li>
            </ul>
        </li>
    </ul>

    <div class="logout">
        <a href="logout.php" onclick="return confirm('Tem certeza que deseja sair de sua conta?');">
            <i class="fa-solid fa-right-from-bracket"></i> Sair
        </a>
    </div>
</nav>

<script>
// Menu lateral interativo com submenus clicáveis
document.querySelectorAll('.has-submenu').forEach(item => {
    item.addEventListener('click', e => {
        const submenu = item.nextElementSibling;
        const open = submenu.style.display === 'flex';
        document.querySelectorAll('.submenu').forEach(sm => sm.style.display = 'none');
        document.querySelectorAll('.submenu-arrow').forEach(arr => arr.style.transform = 'rotate(0deg)');

        if(!open) {
            submenu.style.display = 'flex';
            submenu.style.flexDirection = 'column';
            item.querySelector('.submenu-arrow').style.transform = 'rotate(180deg)';
        }
    });
});
</script>
