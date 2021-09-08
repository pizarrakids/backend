<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	exit;
}

class Estudiante extends REST_Controller {
    
    public function __construct() {
        //llamando a constructor padre
        parent::__construct();
        
        $this->load->database(); //pegandose a la base
        $this->load->model('User_model'); //cargando el modelo User
        $this->load->model('Alumnos_model'); //cargando el modelo User
        
    }
    
    /**
     * SERVICIO QUE ENVIA LOS RECURSOS POR JSON
     * DE LOS RECURSOS SEGUN EL DETALLE
     */
    public function datos_get(){
        $token = $this->uri->segment(3);
        $correo = $this->uri->segment(4);
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        if($perfil == FALSE){
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );
            
            $this->response($respuesta);
            exit;
        }
        
        
        $alumno = $this->Alumnos_model->datos_alumno($correo);
        
        if(isset($alumno)){
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente.',
                'alumno' => $alumno
            );
            
            $this->response($respuesta);
            
        }else{
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existe el alumno!!!',
                'data' => null
            );
            
            $this->response($respuesta);
        }
        
    }
    
   
}
