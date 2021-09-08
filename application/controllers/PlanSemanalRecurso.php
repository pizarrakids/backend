<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	exit;
}

class PlanSemanalRecurso extends REST_Controller {
    
    public function __construct() {
        //llamando a constructor padre
        parent::__construct();
        
        $this->load->database(); //pegandose a la base
        $this->load->model('PlanSemanalDetalle_model'); //cargando el modelo Plan Semanal Detalla
        $this->load->model('PlanSemanalRecurso_model'); //cargando el modelo Plan Semanal Recursos
        $this->load->model('User_model'); //cargando el modelo User
        
    }
    
    /**
     * SERVICIO QUE ENVIA LOS RECURSOS POR JSON
     * DE LOS RECURSOS SEGUN EL DETALLE
     */
    public function recurso_get(){
        $token = $this->uri->segment(3);
        $detalleId = $this->uri->segment(4);
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        if($perfil == FALSE){
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );
            
            $this->response($respuesta, REST_Controller::HTTP_UNAUTHORIZED);
            exit;
        }
                  
        
        $detalles = $this->PlanSemanalDetalle_model->get_detalle_x_id($detalleId);
        $destrezas = $this->PlanSemanalDetalle_model->toma_destrezas_x_detalle($detalleId);
        $recursos = $this->PlanSemanalRecurso_model->toma_recurso_x_detalle($detalleId);
       
        
        if(isset($detalles)){
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente.',
                'dataDetalle' => $detalles,
                'dataDestrezas' => $destrezas,
                'dataRecursos' => $recursos
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
    
    
    public function recurso_post(){
        /*** recibiendo datos post ****/
        if(json_decode(file_get_contents('php://input'), true)){
            $data = json_decode(file_get_contents('php://input'), true);
        }else{
            $data = $this->post();
        }
        
        
        $token = $data['token'];
        ///// fin de recibiendo datos post
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        $inserta = $this->PlanSemanalRecurso_model->inserta_recurso($data);
        
        if($inserta == false){
            $respuesta = array(
                'error'     => true,
                'mensaje'   => 'Registro no se pudo agregar. Intente más tarde!!!',
                'data' => null
            );
        }else{
            $respuesta = array(
                'error'     => false,
                'mensaje'   => 'Registro agregado con éxito!!!',
                'data' => null
            );
        }
        
        $this->response($respuesta);
    }
    
    public function delete_get(){
        $token = $this->uri->segment(3);
        $recursoId = $this->uri->segment(4);
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        if($perfil == FALSE){
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );
            
            $this->response($respuesta, REST_Controller::HTTP_UNAUTHORIZED);
            exit;
        }
                  
        $eliminar = $this->PlanSemanalRecurso_model->eliminar_recurso($recursoId);
       
        
        if(isset($eliminar)){
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registro eliminado correctamente.',
                'data' => null
            );
            
            $this->response($respuesta);
            
        }else{
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No permite la eliminación del registro!!!',
                'data' => null
            );
            
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
    
    
    
}