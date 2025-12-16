var EntrarPainel = document.getElementById("EntrarPainel");
var CadastroSite = document.getElementById("CadastroSite");
var Indicador = document.getElementById("Indicador");


function Entrar(){
    EntrarPainel.style.transform = "translateX(0)";
    CadastroSite.style.transform = "translateX(100%)";
    Indicador.style.transform = "translateX(0%)";
}

function Cadastro(){
    EntrarPainel.style.transform = "translateX(-100%)";
    CadastroSite.style.transform = "translateX(0)";
    Indicador.style.transform = "translateX(100%)"; // 🔥 usa porcentagem
}
