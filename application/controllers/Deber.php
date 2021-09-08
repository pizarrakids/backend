<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	exit;
}

class Deber extends REST_Controller {
    
    public function __construct() {
        //llamando a constructor padre
        parent::__construct();
        
        $this->load->database(); //pegandose a la base
        $this->load->model('Deber_model'); //cargando el modelo Deber
        $this->load->model('User_model'); //cargando el modelo User
        $this->load->model('Alumnos_model'); //cargando el modelo User
        
    }
    
    /**
     * SERVICIO QUE ENVIA LOS RECURSOS POR JSON
     * DE LOS RECURSOS SEGUN EL DETALLE
     */
    public function deberes_get(){
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
                  
        
        $deberes = $this->Deber_model->toma_deberes($detalleId);  
        $alumnos = $this->Alumnos_model->por_plan_detalle_id($detalleId);
        
        if(isset($deberes)){
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente.',
                'deberes' => $deberes,
                'alumnos' => $alumnos
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
    
    
    public function deber_post(){
        /*** recibiendo datos post ****/
        if(json_decode(file_get_contents('php://input'), true)){
            $data = json_decode(file_get_contents('php://input'), true);
        }else{
            $data = $this->post();
        }
        
        $token = $data['token'];
        ///// fin de recibiendo datos post
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        if (!$perfil) {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No ha realizado un ingreso de sesión!!!',
                'data' => null
            );

            $this->response($respuesta);
        } else {

            $insertar = $this->Deber_model->insertar($data);

            if ($insertar == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'El deber no se grabó correctamente, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Deber creado correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }
    
    
    /****
     * METODO QUE DEVUELVE LOS DATOS DE UN DEBER SEGUN EL ID
     */
    public function deber_get(){
        $token = $this->uri->segment(3);
        $deberId = $this->uri->segment(4);
        
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
                  
        
        $deberes = $this->Deber_model->toma_deber($deberId);       
        
        if(isset($deberes)){
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente.',
                'deberes' => $deberes
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
    
    
    /*** METODO DE ELIMNACION DE DEBER ****/
    public function delete_get(){
        $token = $this->uri->segment(3);
        $deberId = $this->uri->segment(4);
        
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
                  
        $eliminar = $this->Deber_model->eliminar($deberId);
       
        
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
            
            $this->response($respuesta);
        }
    }
    
    
    /***** CREA MATERIAL DE APOYO ******/
    public function insertamaterial_post(){
         /*** recibiendo datos post ****/
        if(json_decode(file_get_contents('php://input'), true)){
            $data = json_decode(file_get_contents('php://input'), true);
        }else{
            $data = $this->post();
        }
        
        $token = $data['token'];
        ///// fin de recibiendo datos post
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        if (!$perfil) {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No ha realizado un ingreso de sesión!!!',
                'data' => null
            );

            $this->response($respuesta);
        } else {

            $insertar = $this->Deber_model->insertar_material($data);

            if ($insertar == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'El deber no se grabó correctamente, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Deber creado correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }
    
    
    
    
     /***** ACTUALIZA MATERIAL DE APOYO ******/
    public function actualizardeber_post(){
         /*** recibiendo datos post ****/
        if(json_decode(file_get_contents('php://input'), true)){
            $data = json_decode(file_get_contents('php://input'), true);
        }else{
            $data = $this->post();
        }
        
        $token = $data['token'];
        ///// fin de recibiendo datos post
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        if (!$perfil) {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No ha realizado un ingreso de sesión!!!',
                'data' => null
            );

            $this->response($respuesta);
        } else {

            $insertar = $this->Deber_model->actualizar_deber($data);

            if ($insertar == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'El deber no se actualizó correctamente, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Deber actualizado correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }
    
    
    /*** METODO DE descargar DE MATERIAL DEL DEBER ENVIADO POR PROFESOR****/
    public function descargarmaterial_get(){
        $token = $this->uri->segment(3);
        $materialId = $this->uri->segment(4);
        
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
                  
        $download = $this->Deber_model->download($materialId);
       
    }
    
    
    
    /*** METODO DE ELIMNACION DEL MATERIAL DEL DEBER ****/
    public function deletematerial_get(){
        $token = $this->uri->segment(3);
        $materialId = $this->uri->segment(4);
        
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
                  
        $eliminar = $this->Deber_model->eliminar_material($materialId);
       
        
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
            
            $this->response($respuesta);
        }
    }
    
    
    /*** METODO DE QUE PRESENTA TODOS LOS DEBERES POR REGISTRO ID****/
    public function todoslosdeberes_get(){
        $token = $this->uri->segment(3);
        $registroId = $this->uri->segment(4);
                
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
        $deberes = $this->Deber_model->consulta_deberes_x_registro($registroId);
        if(isset($deberes)){
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros cnsultados correctamente.',
                'data' => $deberes
            );
            $this->response($respuesta);
        }else{
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existen deberes!!!',
                'data' => null
            );
            $this->response($respuesta);
        }
    }
    
    /*** METODO DE QUE PRESENTA TODOS LOS DEBERES ****/
//    public function todoslosdeberes_get(){
//        $token = $this->uri->segment(3);
//        
//        
//        $perfil = $this->User_model->revisa_perfil_x_token($token);
//        
//        if($perfil == FALSE){
//            $respuesta = array(
//                'error' => true,
//                'mensaje' => 'Usuario no permitido.',
//                'data' => null
//            );
//            
//            $this->response($respuesta);
//            exit;
//        }
//                  
//        $eliminar = $this->Deber_model->eliminar_material($materialId);
//       
//        
//        if(isset($eliminar)){
//            $respuesta = array(
//                'error' => FALSE,
//                'mensaje' => 'Registro eliminado correctamente.',
//                'data' => null
//            );
//            
//            $this->response($respuesta);
//            
//        }else{
//            $respuesta = array(
//                'error' => TRUE,
//                'mensaje' => 'No permite la eliminación del registro!!!',
//                'data' => null
//            );
//            
//            $this->response($respuesta);
//        }
//    }
    
    
    
     /***** CALIFICACION DE DEBERES ******/
    public function calificardeber_post(){
         /*** recibiendo datos post ****/
        if(json_decode(file_get_contents('php://input'), true)){
            $data = json_decode(file_get_contents('php://input'), true);
        }else{
            $data = $this->post();
        }
        
        $token = $data['token'];
        ///// fin de recibiendo datos post
        
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        
        if (!$perfil) {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No ha realizado un ingreso de sesión!!!',
                'data' => null
            );

            $this->response($respuesta);
        } else {

            $calificar = $this->Deber_model->calificar_deber($data);

            if ($calificar == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'El deber no se actualizó correctamente, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Deber actualizado correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }
    
    
}
