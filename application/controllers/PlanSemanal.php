<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	exit;
}

class PlanSemanal extends REST_Controller {
    
    public function __construct() {
        //llamando a constructor padre
        parent::__construct();
        
        $this->load->database(); //pegandose a la base
        $this->load->model('PlanSemanal_model'); //cargando el modelo Plan Semanal
        $this->load->model('User_model'); //cargando el modelo User
        
    }
    
    public function planes_get(){
        
        $token = $this->uri->segment(3);
        $registroId = $this->uri->segment(4);
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
//        print_r($perfil);
//        die();
        
        if($perfil == FALSE){
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );
            
            $this->response($respuesta, REST_Controller::HTTP_UNAUTHORIZED);
            exit;
        }
                  
        
        $planes = $this->PlanSemanal_model->consulta_todos($registroId);
        
        if(isset($planes)){
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente.',
                'data' => $planes
            );
            
            $this->response($respuesta);
            
        }else{
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existen planes todavia!!!',
                'data' => null
            );
            
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
        
    }
    
    
    /**
     * METODO QUE RECIBE POST PARA CREAR EL PLAN SEMANAL DE INICIALES
     * 
     */
    public function crear_post(){
        if(json_decode(file_get_contents('php://input'), true)){
            $data = json_decode(file_get_contents('php://input'), true);
            $token =  $data['token'];
            $registroId =    $data['registroId'];
            
        }else{
            $data = $this->post();
            $token =  $data['token'];
            $registroId =    $data['registroId'];
        }
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        if(!$perfil){
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No ha realizado un ingreso de sesión!!!',
                'data' => null
            );
            
            $this->response($respuesta, REST_Controller::HTTP_UNAUTHORIZED);
        }else{
            $crearPlan = $this->PlanSemanal_model->crear($registroId);
            
            if($crearPlan > 0){
                    $respuesta = array(
                    'error' => FALSE,
                    'mensaje' => 'Plan semanal creado correctamente.',
                    'data' => $crearPlan
                );
                $this->response($respuesta);
            }else{
                $respuesta = array(
                    'error' => TRUE,
                    'mensaje' => 'No se pudo crear el Plan Semanal. Consulte con el Administrador!!!',
                    'data' => $crearPlan
                );
                $this->response($respuesta, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        }
        
        
    }
    
    
}