document.addEventListener("DOMContentLoaded", () => {

  // ==== Acordeão ====
  document.querySelectorAll(".accordion-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const accordion = btn.parentElement;

      // fecha os outros
      document.querySelectorAll(".accordion").forEach(acc => {
        if (acc !== accordion) acc.classList.remove("active");
      });

      accordion.classList.toggle("active");
    });
  });

  // ==== Destacar link atual ====
  const currentPage = window.location.pathname.split("/").pop().split("?")[0].split("#")[0];
  document.querySelectorAll(".accordion-container a").forEach(link => {
    const href = link.getAttribute("href").split("?")[0].split("#")[0];
    if(href === currentPage){
      const accordion = link.closest(".accordion");
      accordion.classList.add("active");
      link.classList.add("active-link");
    }
  });

  // ==== Toggle Sidebar ====
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleSidebar');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    document.querySelector('.container').classList.toggle('sidebar-collapsed');
  });

  // ==== Função para abas internas (se usar) ====
  window.mostrarTela = function(telaId){
    document.querySelectorAll('.tela').forEach(t => t.style.display = 'none');
    document.getElementById(telaId).style.display = 'block';
  };

});
