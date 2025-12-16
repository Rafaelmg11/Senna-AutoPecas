const capa = document.getElementById('imagem-capa'); // Seleciona a imagem principal
const miniaturas = document.querySelectorAll('.container_mini_imagens .mini-box img'); // Seleciona todas as miniaturas dentro do container

// Para cada miniatura, adiciona um evento de clique
miniaturas.forEach(mini => {
    mini.addEventListener('click', () => {
        // Troca a imagem da capa pela miniatura clicada
        const tempSrc = capa.src; // Armazena temporariamente o src da capa
        capa.src = mini.src; // Troca o src da capa pelo src da miniatura clicada
        mini.src = tempSrc; // Troca o src da miniatura pelo src que estava na capa

        // Adiciona a classe de animação
        capa.classList.add("capa_animada");

        // Remove a classe após a animação terminar
        setTimeout(() => {
            capa.classList.remove("capa-animada");
        }, 500); // Duração da animação em milissegundos (300ms)
    });
});