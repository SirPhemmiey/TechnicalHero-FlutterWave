<?php

App::import('Lib', 'JWTLib');

//public key FLWPUBK-787fc45c1d72614a18a9501032f3c206-X
//secret key FLWSECK-8277873fbb2d94e80afbc9d3bef53042-X
class UsersController extends AppController {

    public $uses = array('Customer', 'Provider', 'Service', 'Customer', 'Login', 'Transaction', 'Schedule');
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
        $this->Auth->allow('login', 'signup', 'pay', 'logout', 'redirect_me');
    }

    public function index() {
        $this->layout = 'index';
        $id = $this->Auth->user('id');
        $customer = $this->Customer->findById($id);
        $services = $this->Service->find('all', array('group' => 'Service.id'));
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
                }
            }
            else {
                $msg = "Please fill all fields";
                $color = "warning";
            }
            $this->set(compact('msg', 'color'));
        }
    }

    public function logout() {
            $this->redirect($this->Auth->logout());
    }

    public function signup() {
        $this->layout = 'login';
        if ($this->request->is('post')) {
            $email = $this->request->data['Login']['email'];
            $password = $this->request->data['Login']['password'];
            $phone = $this->request->data['Customer']['phone'];
            $address = $this->request->data['Customer']['address'];
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
                        $this->request->data['Login']['customer_id'] = $this->Customer->id;
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

    public function service_providers() {
        $this->layout = 'index';
        $id = $this->Auth->user('id');
        $customer = $this->Customer->findById($id);
        $service_id = $_GET['id'];
        $providers = $this->Service->Provider->find('all', array('conditions' => array('Service.id' => $service_id)));
       $randomNumber =  $this->generate();
        $this->set(compact('customer', 'providers'));
    }

    function generate() {
        $randomNumber = md5(uniqid());
        return $randomNumber;
    }
    public function pay() {
        $this->layout = false;
        $id = $this->Auth->user('id');
        // $findEmail = $this->Login->findById($id, array('group' => 'Login.id'));
        $info = $this->Customer->Login->find('first', array('conditions' => array('Login.id' => $id)));
        $email = $info['Login']['email'];
        if ($this->request->is('post')) {
        $service_id = $_POST['service_id'];
        $provider_id = $_POST['provider_id'];
        $datetime = date("Y-m-d H:i:s");
        $amount = $_POST['amount'];
        $currency = "NGN";
        $randomRef =  $this->generate();
        $publicKey = "FLWPUBK-787fc45c1d72614a18a9501032f3c206-X";
        // $redirectUrl = "http://localhost/TechnicalHero-FlutterWave/users/redirect";
        $redirectUrl = "http://www.playspread.com/flutter/users/redirect_me?amount=$amount&currency=$currency&service_id=$service_id&provider_id=$provider_id&date=$datetime";
        $ch = curl_init();
        $data = array(
            'amount' => $amount,
            'customer_email' => $email,
            'currency' => $currency,
            'txref' => $randomRef,
            'PBFPubKey' => $publicKey,
            'redirect_url' => $redirectUrl,
        );
        curl_setopt_array($ch, array(
            CURLOPT_URL => "https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/hosted/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'content-type: application/json',
                'cache-control: no-cache'
            ]
        ));
        $response = curl_exec($ch);
        $err = curl_error($ch);

        if ($err) {
            die('Error occured: '. $err);
        }

        $transaction = json_decode($response);

        if (!$transaction->data && !$transaction->data->link) {
            print_r("API RETURNED ERROR: ". $transaction->message);
        }

        header("Location: ". $transaction->data->link);
        }
    }

    public function redirect_me() {
        $this->layout = 'index';
        $id = $this->Auth->user('id');
        $customer = $this->Customer->findById($id);
        if (isset($_GET['txref'])) {
            $ref = $_GET['txref'];
            $amount = $_GET['amount']; //Correct Amount from Server
            $currency = $_GET['currency']; //Correct Currency from Server
            $provider_id = $_GET['provider_id'];
            $service_id = $_GET['service_id'];
            $datetime = $_GET['date'];
    
            $query = array(
                "SECKEY" => "FLWSECK-8277873fbb2d94e80afbc9d3bef53042-X",
                "txref" => $ref,
                "include_payment_entity" => "1"
            );
    
            $data_string = json_encode($query);
                    
            $ch = curl_init('https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/xrequery');                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
            $response = curl_exec($ch);
    
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
    
            curl_close($ch);
    
            $resp = json_decode($response, true);
    
            $paymentStatus = $resp['data']['status'];
            $chargeResponsecode = $resp['data']['chargecode'];
            $chargeAmount = $resp['data']['amount'];
            $chargeCurrency = $resp['data']['currency'];
    
            if (($chargeResponsecode == "00" || $chargeResponsecode == "0")&& ($chargeAmount == $amount)  && ($chargeCurrency == $currency)) {
                $info = $this->Customer->Login->find('first', array('conditions' => array('Login.id' => $id)));
                $cus_info_id = $info['Customer']['id'];
                $phone = $info['Customer']['phone'];
                $address = $info['Customer']['address'];
                $this->request->data['Transaction']['customer_id'] = $this->Auth->user('id');
                $this->request->data['Transaction']['amount'] = $chargeAmount;
                $this->request->data['Transaction']['status'] = $paymentStatus;
                $this->request->data['Transaction']['currency'] = $chargeCurrency;
                $this->request->data['Transaction']['txref'] = $ref;
                $this->request->data['Transaction']['datetime'] = date('Y-m-d H:i:s');
                $this->Transaction->create();
                $this->Schedule->create();
                if ($this->Transaction->save($this->data)) {
                  //echo "Success";
                  //echo $raa;
                  $this->request->data['Schedule']['customer_info_id'] = $cus_info_id;
                  $this->request->data['Schedule']['provider_id'] = $provider_id;
                  $this->request->data['Schedule']['service_id'] = $service_id;
                  $this->request->data['Schedule']['datetime'] = $datetime;
                  $this->request->data['Schedule']['phone'] = $phone;
                  $this->request->data['Schedule']['status'] = 'pending';
                  $this->request->data['Schedule']['address'] = $address;
                  if ($this->Schedule->save($this->data)) {
                      $msg = "success";
                      $provider = $this->Provider->find('first', array('conditions' => array('Provider.id' => $provider_id)), array('group' => 'Provider.id'));
                  }
                  else {

                  }
                }
            } else {
                //Dont Give Value and return to Failure page
                $msg = "fail";
            }
        }
            else {
          die('No reference supplied');
        }
        $this->set(compact('chargeAmount', 'paymentStatus', 'customer', 'msg', 'provider'));
    }
}
