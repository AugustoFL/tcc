function copiarTexto(idElemento){
    //Obtém o elemento com o texto a ser copiado
    var texto = document.getElementById(idElemento);

    //Cria um elemento input temporario ao documento
    var inputTemp = document.createElement("input");
    inputTemp.value = texto.textContent;

    //Adiciona o input temporario ao documento
    document.body.appendChild(inputTemp);

    //Seleciona o conteudo do input
    inputTemp.select();

    //Copia o conteudo selecionado para a area de transferencia
    document.execCommand("copy");

    //Remove o input temporario do documento
    document.body.removeChild(inputTemp);

    //Exibe uma mensagem de confirmação
    alert("Texto copiado: " + texto.textContent);
}