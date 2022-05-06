<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		$this->render('index');
	}

	public function inscreverse() {
		$this->view->valueCampos = array(
			'nome' => $_POST['nome'] = '',
			'email' => $_POST['email'] = '',
			'senha' => $_POST['senha'] = ''
		);
		
		$this->render('inscreverse');
	}

	public function registrar() {
		// sucesso
		// print_r($_POST);
		

		$usuario = Container::getModel('Usuario');
		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', $_POST['senha']);

		

		if($usuario->validar()) {
			if (count($usuario->getUsuarioPorEmail()) == 0) {
				$usuario->salvar();
				$this->render('cadastro');
			} else {
				$this->view->usuarioExiste = true;
				$this->render('inscreverse');
			}
		} else {
			$this->view->camposInvalidos = true;
			$this->view->valueCampos = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha']
			);
			$this->render('inscreverse');
		}

		// echo '<pre>';
		// print_r($usuario);
		// echo '</pre>';
		

		// erro
	}

}


?>