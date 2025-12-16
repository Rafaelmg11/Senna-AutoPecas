const linha = document.getElementById("linha-produtos"); // Pega o container dos cards
const anterior = document.querySelector(".btn-esquerdo"); // Botão anterior
const proximo = document.querySelector(".btn-direito"); // Botão próximo
const dots = document.querySelectorAll(".dot"); // Pontinhos indicadores

let cardWidth = document.querySelector(".card-produto").offsetWidth + 25; // largura + gap // 25 é o gap entre os cards
let cardsPorVez = 4; // Quantos cards aparecem por vez

// Quantas "páginas" existem
let totalCards = document.querySelectorAll(".card-produto").length; // Total de cards existentes no carrossel
let totalPaginas = Math.ceil(totalCards / cardsPorVez); // Total de páginas (arredondado para cima)

let paginaAtual = 0; // Página inicial


function atualizarDots() { 
  dots.forEach((dot, i) => { // Percorre todos os dots, dot representa o elemento atual e i o índice do dot no array
    dot.classList.toggle("active", i === paginaAtual); // Acessa a lista de classes, adiciona a classe "active" se o índice do dot for igual à página atual, caso contrário, remove a classe "active"
  });
}

// Botão próximo
proximo.addEventListener("click", () => { // Adiciona um ouvinte de evento de clique ao botão "próximo"
  if (paginaAtual < totalPaginas - 1) { // Verifica se a página atual é menor que o total de páginas menos 1 (para evitar ultrapassar o limite)
    paginaAtual++; // Incrementa a paginaAtual -> vai para a próxima página
    linha.scrollTo({ left: cardWidth * cardsPorVez * paginaAtual, behavior: "smooth" }); // Desloca o container "linha" para a esquerda, calculando a posição com base na largura do card, número de cards por vez e a página atual. O comportamento é suave (smooth)
    atualizarDots(); // Atualiza os dots para refletir a página atual
  }
});

// Botão anterior
anterior.addEventListener("click", () => {
  if (paginaAtual > 0) {
    paginaAtual--; // Decrementa a paginaAtual -> volta para a página anterior
    linha.scrollTo({ left: cardWidth * cardsPorVez * paginaAtual, behavior: "smooth" });
    atualizarDots();
  }
});

// Clique direto no dot
dots.forEach((dot, i) => { //Para cada dot, adiciona um listener de clique. i é o índice do dot, que corresponde à página que ele representa.
  dot.addEventListener("click", () => {
    paginaAtual = i; // Atualiza a página atual para o índice do dot clicado
    linha.scrollTo({ left: cardWidth * cardsPorVez * paginaAtual, behavior: "smooth" });
    atualizarDots();
  });
});

// Garante que começa no 1º
atualizarDots();
