<style>
.hover_local:hover{
  color: #6aff00 !important;
  transform: scale(1.1);
}
</style>



<!-- Sidebar -->
<div class="sidebar" id="sidebar">

  <!-- Perfil do Usuário -->
  <div class="sidebar-user">

  <img 
  src="<?= !empty($_SESSION['imagem_usuario']) ? 'data:image/jpeg;base64,' . base64_encode($_SESSION['imagem_usuario']) : 'caminho/para/imagem_padrao.jpg' ?>" 
  alt="Foto Usuário" 
  class="user-img">
    <span class="user-name"><?= htmlspecialchars($_SESSION['usuario']); ?></span>
  </div>

  <hr>

  <!-- Accordion Menu -->
  <?php
  $menus = [
      'Cliente' => [
          'perfis' => [1,2,3],
          'links' => [
              ['href'=>'cadastrar_cliente.php','label'=>'Cadastrar Cliente','icon'=>'add-circle-outline'],
              ['href'=>'buscar_cliente.php','label'=>'Buscar Cliente','icon'=>'search-outline'],
              ['href'=>'alterar_cliente.php','label'=>'Atualizar Cliente','icon'=>'color-wand-outline'],
              ['href'=>'excluir_cliente.php','label'=>'Excluir Cliente','icon'=>'trash-outline','perfil'=>1],
          ]
      ],
      'Peças' => [
          'perfis' => [1,2,3,4],
          'links' => [
              ['href'=>'buscar_peca.php','label'=>'Buscar Peça','icon'=>'search-outline'],
              ['href'=>'cadastrar_peca.php','label'=>'Cadastrar Peça','icon'=>'add-circle-outline','exclude'=>3],
              ['href'=>'alterar_peca.php','label'=>'Atualizar Peça','icon'=>'color-wand-outline','exclude'=>3],
              ['href'=>'excluir_peca.php','label'=>'Excluir Peça','icon'=>'trash-outline','perfil'=>1],
          ]
      ],
      'Fornecedor' => [
          'perfis' => [1,2,4],
          'links' => [
              ['href'=>'cadastrar_fornecedor.php','label'=>'Cadastrar Fornecedor','icon'=>'add-circle-outline'],
              ['href'=>'buscar_fornecedor.php','label'=>'Buscar Fornecedor','icon'=>'search-outline'],
              ['href'=>'alterar_fornecedor.php','label'=>'Atualizar Fornecedor','icon'=>'color-wand-outline'],
              ['href'=>'excluir_fornecedor.php','label'=>'Excluir Fornecedor','icon'=>'trash-outline','perfil'=>1],
          ]
      ],
      'Funcionário' => [
          'perfis' => [1,2],
          'links' => [
              ['href'=>'cadastrar_funcionario.php','label'=>'Cadastrar Funcionário','icon'=>'add-circle-outline'],
              ['href'=>'buscar_funcionario.php','label'=>'Buscar Funcionário','icon'=>'search-outline'],
              ['href'=>'alterar_funcionario.php','label'=>'Atualizar Funcionário','icon'=>'color-wand-outline'],
              ['href'=>'excluir_funcionario.php','label'=>'Excluir Funcionário','icon'=>'trash-outline','perfil'=>1],
          ]
      ]
  ];

  foreach($menus as $titulo => $menu):
      if(!in_array($_SESSION['id_perfil'], $menu['perfis'])) continue;
  ?>
    <div class="accordion">
      <button class="accordion-btn">
        <?= $titulo ?>
        <ion-icon class="accordion-icon" name="caret-down-outline"></ion-icon>
      </button>
      <div class="accordion-container">
        <?php foreach($menu['links'] as $link):
            if(isset($link['perfil']) && $_SESSION['id_perfil'] != $link['perfil']) continue;
            if(isset($link['exclude']) && $_SESSION['id_perfil'] == $link['exclude']) continue;
        ?>
          <a href="<?= $link['href'] ?>">
            <ion-icon name="<?= $link['icon'] ?>" class="icon"></ion-icon>
            <?= $link['label'] ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <button class="toggle-sidebar" id="toggleSidebar">☰</button>

  <hr>
  <a href="visualizar_estoque.php" style="color: #FFF; margin: auto; text-decoration: none; transition: transform 1.1s ease;" class="hover_local"><ion-icon name="layers-outline" class="icon"></ion-icon>Visualizar Estoque</a>

  <button class="logout-btn" onclick="if(confirm('Você tem certeza que deseja sair?')) window.location.href='index.php'">
    <ion-icon name="log-out-outline" class="icon"></ion-icon>Logout
  </button>

</div>
