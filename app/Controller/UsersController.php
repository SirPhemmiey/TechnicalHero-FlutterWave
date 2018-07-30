<?php

App::import('Lib', 'JWTLib');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');


//public key FLWPUBK-787fc45c1d72614a18a9501032f3c206-X
//secret key FLWSECK-8277873fbb2d94e80afbc9d3bef53042-X
class UsersController extends AppController {

    public $uses = array('Customer', 'Provider', 'Services', 'Info', 'Login');
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
        $this->layout = false;
        $id = $this->Auth->user('id');
        //$services = $this->Service->findById($id);
        $services = $this->Service->find('all');
        $this->set('services', $services);
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
            $phone = $this->request->data['Info']['phone'];
            $name = $phone = $this->request->data['Info']['name'];
            $this->request->data['Login']['type'] = 'Customer';
            if (!empty($email) && !empty($password) && !empty($phone)) {
               $conditions = array('email' => $email);
               if ($this->Login->hasAny($conditions)) {
                    $msg = "You have an existing account.";
                    $color = "warning";
               }
               else {
                    $this->Info->create();
                    if ($this->Info->save($this->data)) {
                        $this->Login->create();
                        $this->request->data['Login']['email'] = $email;
                        $this->request->data['Login']['password'] = $password;
                        $this->request->data['Login']['info_id'] = $this->Info->id;
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


    public function home() {
        $secret = '7x0jhxt"9(thpX60..A';
        $this->layout = false;
        if ($this->request->is('get')) {
            $header = $this->request->header('Authorization');
            if (empty($header)) {
                $services = "auth";
            }
            else {
                $explodedHeader = explode(" ", $header);
            $mainToken = $explodedHeader[1];
            $decodeHeader = JWT::decode($mainToken, $secret);
            $username = $decodeHeader->username;
            $findUsername = $this->Login->findByUsername($username);
            if (empty($findUsername)) {
                $services = "auth";
            }
            else {
                //get the providers information
                $services = $this->Service->find('all');
            }
            }
            echo json_encode($services);
        }
    }

    public function search() {
        $this->layout = false;
        $this->request->onlyAllow('post');
        //suck put the json endcoded data
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        $selectedService = $data['selectedService'];
        $userLat = $data['destLat'];
        $userLong = $data['destLong'];
        //$search = $this->Provider->find('all', array('condition', array('lga')));
        // $search = $this->Provider->query("select * from providers as Provider join lgas as Lga on
        // Provider.lga_id = Lga.id join services as Service on Provider.service_id = Service.id 
        // where Lga.id = '$lga' and Service.id = '$selectedService' and Provider.status = 'active'");
        // $search = $this->Provider->query(
        //     "SELECT 
        //      *, 
        //        ( 3959 * acos( cos( radians($userLat) ) * cos( radians( latitude ) ) 
        //        * cos( radians(longitude) - radians($userLong)) + sin(radians($userLat)) 
        //        * sin( radians(latitude)))) AS distance 
        //     FROM providers as Provider JOIN services as Service ON Provider.service_id = Service.id
        //     WHERE 
        //     Service.id = '$selectedService' AND
        //     Provider.status = 'active' AND Provider.available = 'yes'
        //     HAVING distance < 1000 
        //     ORDER BY distance");

            $search = $this->Provider->query("SELECT * from providers as Provider JOIN services as Service ON Provider.service_id = Service.id WHERE Service.id = '$selectedService' AND
            Provider.status = 'active' AND Provider.available = 'yes'");
        if (empty($search) || is_null($search)) {
            $search = 'empty';
        }
        echo json_encode($search);
    }

    public function pro_details() {
        $secret = '7x0jhxt"9(thpX60..A';
        $this->layout = false;
        if ($this->request->is('post')) {
            //suck put the json endcoded data
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $provider_id = $data['provider_id'];
            $header = $this->request->header('Authorization');
            if (empty($header) || is_null($header)) {
                $providers = "invalid";
            }
            else {
                $explodedHeader = explode(" ", $header);
                $mainToken = $explodedHeader[1];
                $decodeHeader = JWT::decode($mainToken, $secret);
                $username = $decodeHeader->username;
                $findUsername = $this->Login->findByUsername($username);
                if (empty($findUsername)) {
                    //return false;
                    $providers = "invalid";
                }
                else {
                    //get the providers information
                    //$providers = $this->Provider->query("select * from providers as Provider where Provider.id = '$provider_id'");
                    $providers = $this->Provider->find('first', array('conditions' => array(
                        'Provider.id' => $provider_id)));
                }
            }
            echo json_encode($providers['Provider']);
        }
    }

    public function get_reviewers() {
        $secret = '7x0jhxt"9(thpX60..A';
        $this->layout = false;
        if ($this->request->is('post')) {
            //suck put the json endcoded data
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $provider_id = $data['provider_id'];
            $header = $this->request->header('Authorization');
            if (empty($header) || is_null($header)) {
                $reviewers = "invalid";
            }
            else {
                $explodedHeader = explode(" ", $header);
                $mainToken = $explodedHeader[1];
                $decodeHeader = JWT::decode($mainToken, $secret);
                $username = $decodeHeader->username;
                $findUsername = $this->Login->findByUsername($username);
                if (empty($findUsername)) {
                    $reviewers = "invalid";
                }
                else {
                    //get the providers information
                    $reviewers = $this->Review_provider->query(
                    "select * from review_providers as Review_provider join customers as Customer on 
                    Review_provider.customer_id = Customer.id where Review_provider.provider_id
                    = '$provider_id'");
                    if (empty($reviewers) || is_null($reviewers)) {
                        $reviewers = "empty";
                    }
                }
            }
            echo json_encode($reviewers);
        }
    }

    public function get_prov_name() {
        $this->layout = false;
        $secret = '7x0jhxt"9(thpX60..A';
        if ($this->request->is('post')) {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $prov_id = $data['provider_id'];
            $header = $this->request->header('Authorization');
            $explodedHeader = explode(" ", $header);
            $mainToken = $explodedHeader[1];
            $decodeHeader = JWT::decode($mainToken, $secret);
            $username = $decodeHeader->username;
            $findUsername = $this->Login->findByUsername($username);
            if (empty($findUsername)) {
                $name = "User not found";
            }
            else {
                //get the users id
                $name = $this->Provider->find('all', array('conditions' => array('id' => $prov_id)));
            }
            echo json_encode($name);
        }
    }

    public function save_ref() {
        $this->layout = false;
        if ($this->request->is('post')) {
            $secret = '7x0jhxt"9(thpX60..A';
            $header = $this->request->header('Authorization');
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            $ref = $data['ref'];
            $amount = $data['amount'];
            $prov_id = $data['provider_id'];
            $selectedService = $data['selectedService'];
            $dateValue = $data['dateValue'];
            $phone = $data['phone'];
            $address = $data['address'];
            $destLat = $data['destLat'];
            $destLong = $data['destLong'];
            if (empty($header) || is_null($header)) {
                $ref = "Not authorized";
            }
            else {
                $explodedHeader = explode(" ", $header);
                $mainToken = $explodedHeader[1];
                $decodeHeader = JWT::decode($mainToken, $secret);
                $username = $decodeHeader->username;
                $customer_info_id = $decodeHeader->cus_info_id;
                $findUsername = $this->Login->findByUsername($username);
                if (empty($findUsername)) {
                  $ref = "Not found";
                }
                else {
                    $paymentDet = array(
                        'ref' => $ref,
                        'datetime' => Date("Y-m-d H:i:s"),
                        'customer_info_id' => $customer_info_id,
                        'amount' => $amount
                    );
                    $schData = array(
                        'customer_info_id' => $customer_info_id,
                        'provider_id' => $prov_id,
                        'service_id' => $selectedService,
                        'datetime' => $dateValue,
                        'phone' => $phone,
                        'address' => $address,
                        'status' => "pending",
                        'customer_confirm' => 'yes',
                        'provider_confirm' => "no"
                    );
                    $schCondition = array(
                        'customer_info_id' => $customer_info_id,
                        'status' => 'pending'
                    );
                    if ($this->Schedule->hasAny($schCondition)) {
                        //the customer has a pending schedule
                        $ref = "pending";
                    }
                    else {
                        if ($this->Payment->save($paymentDet)) {
                            //$ref = "done";
                            if ($this->Schedule->save($schData)) {
                                 $schedule_id = $this->Schedule->id;
                                 $trackingData = array(
                                     'schedule_id' => $schedule_id,
                                     'customer_lat' => $destLat,
                                     'customer_long' => $destLong,
                                     'status' => "pending"
                                 );
                                 $ratingsData = array(
                                     'schedule_id' => $schedule_id
                                 );
                                 if ($this->Tracking->save($trackingData)) {
                                    if ($this->Rating->save($ratingsData)) {
                                        $ref = "done";
                                    }
                                 }
                            }
                        }
                        else {
                            $ref = "err";
                        }
                    }
                }
            }
            echo json_encode($ref);
        }
    }
}
