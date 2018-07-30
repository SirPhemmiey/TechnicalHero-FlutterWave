<?php

App::import('Lib', 'JWTLib');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');


//public key FLWPUBK-787fc45c1d72614a18a9501032f3c206-X
//secret key FLWSECK-8277873fbb2d94e80afbc9d3bef53042-X
class UsersController extends AppController {

    public $uses = array('Customer', 'Provider', 'Services');

    public function beforeFilter() {

    }

    public function index() {
        $this->layout = false;
    }

    public function login() {
        $this->layout = false;
        $secret = '7x0jhxt"9(thpX60..A';
            $this->request->onlyAllow('post');
            $json = file_get_contents("php://input");
            //convert it into assoc array
            $data = json_decode($json, true);
            $username = $data['username'];
            $password = $data['password'];
            if ($username != '' && $password != '') {
                $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                $password = $passwordHasher->hash($password);
                $loginInfo = $this->Login->findByUsernameAndPassword($username, $password);
                $userInfo = $this->Customer->findByEmail($username);
                if (!empty($loginInfo) && !empty($userInfo)) {
                    $tokenArray['id'] = $loginInfo['Login']['id'];
                    $tokenArray['username'] = $loginInfo['Login']['username'];
                    $tokenArray['cus_info_id'] = $userInfo['Customer']['id'];
                    $token = JWT::encode($tokenArray, $secret);
                    $response = array(
                        'success' => true,
                        'username' => $loginInfo['Login']['username'], 
                        'token' => $token,
                        'phone' => $userInfo['Customer']['phone_number'],
                        'pic' => $userInfo['Customer']['pic'],
                        'name' => $userInfo['Customer']['full_name']
                    );
                }
                else {
                    $response = array(
                        'error' => 'Invalid credentials.'
                    );
                }
            }
            else {
                //fields are empty
                $response = array(
                    'error' => 'All fields are required.',
                );
            }
            echo json_encode($response);
    }

    public function signup() {
        $this->layout = false;
        $this->request->onlyAllow('post');
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        $full_name = $data['full_name'];
        $username = $data['email'];
        $phone = $data['phone'];
        $password = $data['password'];
        $device_id = $data['device_id'];
        $type = $data['type'];
        //ensure all fields are not empty before processing
        if ($full_name != '' && $phone != '' && $username != '' && $password != '' && $type != '') {
            $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
            $password = $passwordHasher->hash($password);
            $cusData = array(
                'email' => $username,
                'full_name' => $full_name,
                'phone_number' => $phone,
                'status' => 'active',
                'created' => Date("d-m-Y"),
                'device_id' => $device_id
            );
            $cusCondition = array('email' => $username);
            //check if the username already exist
            if ($this->Customer->hasAny($cusCondition)) {
                $response = array(
                    'success' => false,
                    'error' => 'Username is already in use.'
                );
            }
            else {
                //if it doesnt then save the data
                if ($this->Customer->save($cusData)) {
                    //$this->request->data['Login']['info_id'] = 
                    $loginData = array(
                        'username' => $username,
                        'password' => $password,
                        'info_id' => $this->Customer->id,
                        'type' => $type
                    );
                    $this->Login->save($loginData);
                    $response = array(
                        'success' => true
                    );
                }
                else {
                    //if an error occured during registration
                    $response = array(
                        'success' => false,
                        'error' => 'An error occured during registration.'
                    );
                }
            }
        }
        else {
            //if any of the fields are empty
            $response = array(
                'success' => false,
                'error' => 'All fields are required.',
            );
        }
        echo json_encode($response);
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
