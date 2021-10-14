<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';
header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	exit;
}

class Registrosacademicos extends REST_Controller {
    
    public function __construct() {
        //llamando a constructor padre
        parent::__construct();
        $this->load->database(); //pegandose a la base
        $this->load->model('RegistrosAcademicos_model'); //cargando el modelo Registro
        $this->load->model('User_model'); //cargando el modelo User
        
    }
    
     
    /**
     * Metodo que entrega todos los registros academicos del docente
     * url del servicio: http://192.168.100.150/codigneiter/index.php/Registrosacademicos/registros/ZG9jZWluaTFAbWFpbC5jb20yMDIxMDMwMjE3Mjk0MA==
     */
    public function registros_get(){
       
        $token = $this->uri->segment(3);
        $registros = $this->RegistrosAcademicos_model->registros_academicos($token);
    
        if( isset( $registros )){
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente',
                'data' => $registros
            );
            
            $this->response( $respuesta );
            
        }else{
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existen registros creados para el usuario!!!',
                'data' => null
            );
            
            $this->response( $respuesta, REST_Controller::HTTP_NOT_FOUND );
        }
    }
    
    /***
     * METRO PARA ENTREGAR LA INFORMACION DE UN REGISTRO
     * PARAMETROS: 
     * 3: token
     * 4: rgistro_id
     * 
     * url del servicio: http://192.168.100.150/codigneiter/index.php/Registrosacademicos/registro/ZG9jZWluaTFAbWFpbC5jb20yMDIxMDMwMjE3Mjk0MA==/7
     */
    public function registro_get(){
        $token = $this->uri->segment(3);
        $registroId = $this->uri->segment(4);
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        $datosRegistro = $this->RegistrosAcademicos_model->registro_x_id($registroId);
        if( isset( $datosRegistro )){
            
            $estudiantes = $this->RegistrosAcademicos_model->estudiantes_x_registro($registroId);
            
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Datos consultados correctamente',
                'data' => $datosRegistro,
                'estudiantes' => $estudiantes,
                'perfil' => $perfil
            );
            
            $this->response( $respuesta );
            
        }else{
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existen datos para el registro solicitado!!!',
                'data' => null
            );
            
            $this->response( $respuesta, REST_Controller::HTTP_NOT_FOUND );
        }
    }
    
    
    
    
}