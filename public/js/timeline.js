$(document).ready(() => {
    $('.toggledOn').click(() => {
        $('.timeline').toggle('slow')
        $('.direito').toggle('slow')
    })
})

function esconder_menu() {
        const largura_tela = window.innerWidth;

        if (largura_tela >= 991) {
            $('.timeline').show('slow')
            $('.direito').show('slow')  
        } else if(largura_tela <= 991) {
            $('.direito').hide() 
            $('.timeline').show('slow')  
        }
    }



// function esconder_menu() {
//     const largura_tela = window.innerWidth;
//     if (largura_tela <= 991) {
//         $('.navigation').addClass('show');
//         // $('#container-central').removeClass('container');
//         $('.direito').hide();
//         $('.esquerdo.text').hide()
//         $('.toggle-search').show('slow');
        
//         if ($('.toggle-search').hasClass('active')) {
//             $('.direito').show();
//         } else {
//             $('.direito').hide();
//         }
        
//     } else {
//         $('.navigation').removeClass('show');
//         // $('#container-central').addClass('container');
//         $('.direito').show();
//         $('.toggle-search').hide()
//         $('.centro').show('slow');
//         $('.toggle-search').removeClass('active');        
//     }
// }
$(document).ready(function () {
    $('.form-busca').focusin(() => {
        $('.busca').addClass('active');
    });
    $('.form-busca').focusout(() => {
        $('.busca').removeClass('active');
    });
    // Pesquisa
    $("#busca").keyup(function () {
        let busca = $('#busca').val();
        let length_busca = $('#busca').val().length;
        if (length_busca == 0) {
            $('.listando-busca').html('');
        }
        $.ajax({
            url: `/quem_seguir?pesquisarPor=${busca}`,
            dataType: 'json',
            success: function (result) {
                $('#busca').focusout(() => {
                    $('.noticias').show('fast');
                })
                if (length_busca >= 1) {
                    $('.listando-busca').removeClass('d-none');
                    $('.noticias').hide();
                    $('.listando-busca').html('');
                    result.forEach(function (value) {
                        console.log(value['foto_perfil'])
                        value['foto_perfil'] == 'img/perfil.png'
                        if (value['seguindo_sn'] != 1) {
                            $('.listando-busca').prepend(`<div class="resultados"><div class="profile"><div class="imgBx"><img src="uploads/${value['foto_perfil'] ? value['foto_perfil'] : 'perfil.png'}" alt=""></div></div><div><div class="nome">${value['nome']}</div><div class="botoes"><a href="/acao?acao=seguir&id_usuario=${value['id']}">Seguir</a><!-- <a href="#">Deixar de seguir</a> --></div></div></div>`);
                        } else if (value['seguindo_sn'] != 0) {
                            $('.listando-busca').prepend(`<div class="resultados"><div class="profile"><div class="imgBx"><img src="uploads/${value['foto_perfil'] ? value['foto_perfil'] : 'perfil.png'}" alt=""></div></div><div><div class="nome">${value['nome']}</div><div class="botoes"><a href="/acao?acao=deixar_seguir&id_usuario=${value['id']}">Deixar de seguir</a><!-- <a href="#">Deixar de seguir</a> --></div></div></div>`);
                        }
                    });
                } else {
                    $('.resultados').addClass('d-none');
                    console.log('chegou aqui')
                }
            }
        });
    });
    // Seçao pre-carregamento foto do perfil
    $('#upload-foto').change(() => {
        $('#foto_profile_atualizar').html('<img src="img/perfil_foco.png">')
    })
    // Seçao de editar o perfil
    $('#editar-perfil-menu').click(() => {
        $('.container-editar-perfil').toggleClass('active')
    });
    $('#editar-perfil').click(() => {
        $('.container-editar-perfil').toggleClass('active')
    });
    $('#close-edit-profle').click(() => {
        $('.container-editar-perfil').removeClass('active')
    });
    $('#botao-edit-perfil').click(() => {
        $('#form-edit-perfil').slideToggle('fast', () => {
            $('#botao-edit-perfil').toggleClass('active');
        })
    })
    $('#icon-upload-foto').click(() => {
        $('#salva-foto-perfil').show('slow')
    })

    // Seçao botao pesquisar

    $('.toggle-search').click(() => {        
        $('.centro').fadeToggle(0001);
        $('.direito').slideToggle(0001 , () => {
            $('.toggle-search').toggleClass('active');
        });
    })
});