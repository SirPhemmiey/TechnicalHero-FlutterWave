<?php

App::import('Lib', 'JWTLib');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');


//public key FLWPUBK-787fc45c1d72614a18a9501032f3c206-X
//secret key FLWSECK-8277873fbb2d94e80afbc9d3bef53042-X
class UsersController extends AppController {

    public $uses = array('Customer', 'Provider', 'Service', 'Customer', 'Login');
    public $components = array('Auth');
    public function beforeFilter() {
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
        //$this->Auth->authError = array('Please login in');
        $this->Auth->loginRedirect = array('controller' => 'users', 'action' => 'index');
        $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
        $this->Auth->authenticate = array(
            'Form' => array(
                'userModel' => 'Login', 'fields' => array(
                    'username' => 'email', 'password' => 'password')));
        $this->Auth->allow('login', 'signup', 'logout');
    }

    public function index() {
        $this->layout = 'index';
        $id = $this->Auth->user('id');
        $customer = $this->Customer->findById($id);
        $services = $this->Service->find('all');
        $this->set(compact('services', 'customer'));
    }
    
    public function login() {
        $this->layout = 'login';
        if ($this->request->is('post')) {
            if (!empty($this->request->data)) {
                if ($this->Auth->login()) {
                    $this->redirect('/users/index');
                }
                else {
                    $msg = "Invalid credentials";
                    $color = "danger";
                    // $this->redirect(array('controller' => 'users', 'action' => 'login', 'msg' => $msg));
                }
            }
            else {
                $msg = "Please fill all fields";
                $color = "warning";
                // $this->redirect(array('controller' => 'users', 'action' => 'login', 'msg' => $msg));
            }
            $this->set(compact('msg', 'color'));
        }
    }

    public function signup() {
        $this->layout = 'login';
        if ($this->request->is('post')) {
            $email = $this->request->data['Login']['email'];
            $password = $this->request->data['Login']['password'];
            $phone = $this->request->data['Customer']['phone'];
            $name = $phone = $this->request->data['Customer']['name'];
            $this->request->data['Login']['type'] = 'Customer';
            if (!empty($email) && !empty($password) && !empty($phone)) {
               $conditions = array('email' => $email);
               if ($this->Login->hasAny($conditions)) {
                    $msg = "You have an existing account.";
                    $color = "warning";
               }
               else {
                    $this->Customer->create();
                    if ($this->Customer->save($this->data)) {
                        $this->Login->create();
                        $this->request->data['Login']['email'] = $email;
                        $this->request->data['Login']['password'] = $password;
                        $this->request->data['Login']['info_id'] = $this->Customer->id;
                        if ($this->Login->save($this->data)) {
                            $msg = "Account successfully created. You can now login";
                            $color = "success";
                        }
                    }
                    else {
                        $msg = "Oops! An error occured while creating your account";
                        $color = "danger";
                    }
               }
            }
            else {
                $msg = "Please fill all fields";
                $color = "warning";
                // $this->redirect(array('controller' => 'users', 'action' => 'signup', 'msg' => $msg));
            }
            $this->set(compact('msg', 'color'));
        }
       
    }
}
