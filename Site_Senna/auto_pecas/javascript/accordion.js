// Seleciona todos os elementos que possuem a classe 'opcao_titulo'
const opcoesTitulos = document.querySelectorAll('.opcao_titulo'); 

//Foreach , Para cada elemento encontrado (cada título), adiciona um evento de clique
opcoesTitulos.forEach(header => { 

    //Executa a função quando houver um click
    header.addEventListener('click', () => {

        // Seleciona o próximo elemento irmão do título, que é o container com os itens (.opcao_container)
        const container = header.nextElementSibling;

        
        const toggle = header.querySelector('.toggle'); //Seleciona o toggle

        // Adiciona ou remove a classe 'show' no container de itens
        // Se 'show' estiver presente, remove (fecha o accordion)
        // Se não estiver, adiciona (abre o accordion)
        container.classList.toggle('show'); 
        
        // Adiciona ou remove a classe 'open' no toggle (o "+")
        // Faz o "+" girar 45° quando aberto e voltar quando fechado
        toggle.classList.toggle('open'); //
        
    });
});
