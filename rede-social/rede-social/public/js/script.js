document.addEventListener('DOMContentLoaded', function () {
    configurarUploadMidia('btn-enviar-imagem', 'input-imagem', 'nome-imagem', 'Imagem selecionada');
    configurarUploadMidia('btn-enviar-video', 'input-video', 'nome-video', 'Vídeo selecionado');
});

function configurarUploadMidia(idBotao, idInput, idLabel, prefixo) {
    const botao = document.getElementById(idBotao);
    const input = document.getElementById(idInput);
    const label = document.getElementById(idLabel);

    if (botao && input) {
        botao.addEventListener('click', function () {
            input.click();
        });
    }

    if (input && label) {
        input.addEventListener('change', function () {
            label.textContent = this.files[0]
                ? prefixo + ': ' + this.files[0].name
                : '';
        });
    }
}

function curtir(postId){

    fetch('ajax/curtir.php',{
        method:'POST',
        headers:{
            'Content-Type':'application/x-www-form-urlencoded'
        },
        body:'post_id=' + postId
    })
    .then(r => r.text())
    .then(t => {
        document.getElementById('curtidas-' + postId).innerText = t;
    });

}

function seguir(usuarioId){

    fetch('ajax/seguir.php',{
        method:'POST',
        headers:{
            'Content-Type':'application/x-www-form-urlencoded'
        },
        body:'usuario_id=' + usuarioId
    })
    .then(r => r.text())
    .then(status => {

        const botao = document.getElementById('btn-seguir-' + usuarioId);

        if(status === 'seguindo'){

            botao.innerText = 'Seguindo';

            botao.classList.remove('is-primary', 'botao-seguir');
            botao.classList.add('is-light');

        }else{

            botao.innerText = 'Seguir';

            botao.classList.remove('is-light');
            botao.classList.add('is-primary', 'botao-seguir');

        }

    });

}
