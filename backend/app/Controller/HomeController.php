<?php

//public key FLWPUBK-787fc45c1d72614a18a9501032f3c206-X
//secret key FLWSECK-8277873fbb2d94e80afbc9d3bef53042-X
class HomeController extends AppController {

    public function beforeFilter() {

    }

    public function index() {
        $this->layout = false;
    }

    public function login() {
        $this->layout = false;
        
    }
}