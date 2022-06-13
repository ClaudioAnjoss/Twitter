<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {
    public function timeline() {

        $this->validarAuth();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id_usuario' , $_SESSION['id']);

        $limite = 10;
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $deslocamento = ($pagina -1) * $limite;

        $this->view->pagina_ativa = $pagina;

        // $tweets = $tweet->getAll();
        $tweets = $tweet->getPorPagina($limite, $deslocamento);
        $total_paginas = $tweet->totalTweets();
        $total_paginas = $total_paginas['total'] / $limite;
        $this->view->total_paginas = ceil($total_paginas);

        // Informaçoes do usuario
        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->infoUsuario = $usuario->infoUsuario();
        $this->view->totalTweets = $usuario->totalTweets();
        $this->view->totalSeguindo = $usuario->totalSeguindo();
        $this->view->totalSeguidores = $usuario->totalSeguidores();
        $this->view->fotoPerfil = $usuario->getFotoPerfil();

        $this->view->tweets = $tweets;

        $this->render('timeline');
        
    }

    public function tweet() {

        $this->validarAuth();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('tweet' , $_POST['tweet']);
        $tweet->__set('id_usuario' , $_SESSION['id']);

        $tweet->salvar();
        
        header('Location: /timeline');
        
    }

    public function validarAuth() {
        session_start();

        if((!isset($_SESSION['id']) || $_SESSION['id'] == '') && (!isset($_SESSION['nome']) || $_SESSION['nome'] == '')) {
            header('Location: /entrar?auth=false'); 
        }
    }

    public function quemSeguir() {

        $this->validarAuth();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = array();

        if ($pesquisarPor != '') {
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome' , $pesquisarPor);
            $usuario->__set('id' , $_SESSION['id']);

            $usuarios = $usuario->getAll();

            echo json_encode($usuarios);
        }

        // Informaçoes do usuario
        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->infoUsuario = $usuario->infoUsuario();
        $this->view->totalTweets = $usuario->totalTweets();
        $this->view->totalSeguindo = $usuario->totalSeguindo();
        $this->view->totalSeguidores = $usuario->totalSeguidores();

        $this->view->usuarioPesquisado = $usuarios;
    }

    public function acao() {
        $this->validarAuth();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id' , $_SESSION['id']);

        if($acao == 'seguir') {
            $usuario->seguirUsuario($id_usuario);
            header('Location: /timeline');
        } else if($acao == 'deixar_seguir') {
            $usuario->deixarseguirUsuario($id_usuario);
            header('Location: /timeline');
        }
        
    }

    public function excluir_tweets() {
        print_r($_GET);

        $this->validarAuth();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id', $_GET['id_do_tweet']);

        $tweet_excluido = $tweet->excluir();

        if($tweet_excluido) {
            header('Location: /timeline?excluir=success');
        } else {
            header('Location: /timeline?excluir=fail');
        }
    }

    public function foto_perfil() {

        $this->validarAuth();

        echo json_encode('Oi');

        if(isset($_POST['acao'])) {
            $arquivo = $_FILES['foto_perfil'];

            $arquivoNovo = explode('.',$arquivo['name']);

            if($arquivoNovo[sizeof($arquivoNovo)-1] == 'jpg' || $arquivoNovo[sizeof($arquivoNovo)-1] == 'png' || $arquivoNovo[sizeof($arquivoNovo)-1] == 'jpeg') {

                $novo_nome = md5(time()) . '.' . $arquivoNovo[sizeof($arquivoNovo)-1]; 
                move_uploaded_file($arquivo['tmp_name'],'uploads/'.$novo_nome);

                $this->view->fotoTemporaria = 'uploads/'.$novo_nome;

                $usuario = Container::getModel('Usuario');
                $usuario->__set('id' , $_SESSION['id']);
                $usuario->__set('foto_perfil' , $novo_nome);

                $foto_perfil = $usuario->setFoto_perfil();

                if($foto_perfil) {
                    header('Location: /timeline?image_profile=success');
                } else {
                    header('Location: /timeline?image_profile=erro');
                }
                
            } else {
                die(header('Location: /timeline?image_profile=invalido'));
            }
        }
    }

    public function editar_perfil() {
        echo '<pre>';
        print_r($_POST);
        echo '<pre>';

        $this->validarAuth();

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id' , $_SESSION['id']);
        $usuario->__set('nome' , $_POST['nome']);
        $usuario->__set('email' , $_POST['email']);

        $retorno = $usuario->editar_perfil();

        if($retorno) {
            header('Location: /timeline?edit_profile=success');
        } else {
            header('Location: /timeline?edit_profile=erro');
        }
    }
}

?>