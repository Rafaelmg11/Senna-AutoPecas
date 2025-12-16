document.addEventListener("DOMContentLoaded", () => { //Aguarda o carregamento do DOM (garante que os elementos estão disponíveis)
  const favoritos = document.querySelectorAll(".favorito ion-icon"); // Seleciona todos os ícones de favorito

  favoritos.forEach(icon => {// Percorre cada ícone
    icon.addEventListener("click", () => { // Adiciona um evento de clique a cada item percorrido
      // troca ícone
      if (icon.getAttribute("name") === "heart-outline") { //Lê o atributo "name" do ícone e verifica se é "heart-outline" (coração vazio ou preenchido)
        icon.setAttribute("name", "heart"); //Se for "heart-outline", troca para "heart" (coração preenchido)
        icon.style.color = "red"; // Muda a cor do ícone para vermelho
      } else {
        icon.setAttribute("name", "heart-outline"); //Se for "heart", troca para "heart-outline" (coração vazio)
        icon.style.color = "rgb(80, 80, 80)"; // Muda a cor do ícone para cinza
      }

      // adiciona classe de animação
      icon.classList.add("clicked");

      // remove depois que acabar a animação
      setTimeout(() => { 
        icon.classList.remove("clicked"); // Remove a classe "clicked" após a animação para que possa ser reaplicada em futuros cliques
      }, 300); // Duração da animação (300ms) 
    });
  });
});