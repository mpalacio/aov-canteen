<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {
	public $uses = array();

	public function login() {
		if($this->request->is('post')) {
			$user = $this->User->findByUsername($this->request->data['username']);
			if(!empty($user)) {
				if($user['User']['password'] == md5($this->request->data['password'])) {
					$this->Auth->login($user['User']);
					$this->redirect('/');
				}
				else
					$this->Session->setFlash('Wrong password.', 'error');
			}
			else
				$this->Session->setFlash('Username not found.', 'error');
		}
		if($this->Auth->user()) {
			$this->Session->setFlash('You are already logged in.', 'error');
			$this->redirect('/');
		}
	}

	public function logout() {
		$this->Auth->logout();
		$this->redirect("/");
	}
}
