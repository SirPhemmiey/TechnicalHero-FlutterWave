<?php
App::uses('AppModel', 'Model');
class Login extends AppModel {
    public $belongsTo = 'Customer';
    public function beforeSave($options = array()){
        if(!empty($this->data[$this->alias]['password'])){
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }
}