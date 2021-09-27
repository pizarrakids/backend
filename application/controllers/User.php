<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	exit;
}

class User extends REST_Controller {
    
    public function __construct() {
        //llamando a constructor padre
        parent::__construct();
        
        $this->load->database(); //pegandose a la base
        $this->load->model('User_model'); //cargando el modelo User        
    }
    
    /**
     * METODO PARA REALIZAR LOGIN DE USUARIO
     * url del servicio: http://192.168.100.150/codigneiter/index.php/User/login
     */ 
    public function login_post(){

        libxml_disable_entity_loader(true);
        
        if(json_decode(file_get_contents('php://input'), true)){
            $data = json_decode(file_get_contents('php://input'), true);
            $usuario =  $data['correo'];
            $clave =    $data['clave'];
            
        }else{
            $data = $this->post();
            $usuario =  $data['correo'];
            $clave =    $data['clave'];
        }
                
        //validar usuario
        if(!isset($usuario) || !isset($clave)){
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'Correo y clave son campos requeridos!!!'
            );
            
            $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
        }
        
        $login = $this->User_model->login( $usuario, $clave );
        
        if( isset( $login )){
            
            $perfil = $this->User_model->perfil($login->perfil_id);
            
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Login correcto',
                'data' => $login,
                'perfil' => $perfil
            );
            
            $this->response( $respuesta );
            
        }else{
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existe el usuario',
                'data' => null
            );
            
            $this->response( $respuesta );
        }
        
        
    }
    
}