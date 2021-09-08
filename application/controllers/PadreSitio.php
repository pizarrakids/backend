<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

class PadreSitio extends REST_Controller {

    public function __construct() {
        //llamando a constructor padre
        parent::__construct();

        $this->load->database(); //pegandose a la base
        $this->load->model('PadreSitio_model'); //cargando el modelo Plan Semanal
        $this->load->model('User_model'); //cargando el modelo User
    }

    /*     * *
     * ENTREGA DATOS DIAS Y HORAS
     */

    public function kids_get() {
        $token = $this->uri->segment(3);
        $correoPadre = $this->uri->segment(4);

        $perfil = $this->User_model->revisa_perfil_x_token($token);

        if ($perfil == FALSE) {
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );

            $this->response($respuesta);
            exit;
        }

        $hijos = $this->PadreSitio_model->hijos($correoPadre);
        

        if (isset($hijos)) {
            $respuesta = array(
                'error' => false,
                'mensaje' => 'Registros consultados correctamente.',
                'data' => $hijos
            );

            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No tiene asignado hijos!!!',
                'data' => null
            );

            $this->response($respuesta);
        }
         
    }
    
    
    /***
     * SERVICIO PARA ENTREGAR LOS DETALLE DEL DEBER
     */
    public function deberes_get() {
        $token = $this->uri->segment(3);
        $estudianteCorreo = $this->uri->segment(4);
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        if ($perfil == FALSE) {
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );

            $this->response($respuesta);
            exit;
        }
        $deberes = $this->PadreSitio_model->deberes($estudianteCorreo);
        
        if (isset($deberes)) {
            $respuesta = array(
                'error' => false,
                'mensaje' => 'Deberes del estudiante.',
                'deberes' => $deberes
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No tiene asignado deberes!!!',
                'data' => null
            );
            $this->response($respuesta);
        }
         
    }
    
    /***
     * SERVICIO PARA ENTREGAR LOS DETALLE DEL DEBER
     */
    public function deber_get() {
        $token = $this->uri->segment(3);
        $deberId = $this->uri->segment(4);
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        if ($perfil == FALSE) {
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );

            $this->response($respuesta);
            exit;
        }
        $deberes = $this->PadreSitio_model->deber_detalle($deberId);
        
        if (isset($deberes)) {
            $respuesta = array(
                'error' => false,
                'mensaje' => 'Deberes del estudiante.',
                'deberes' => $deberes
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No tiene asignado deberes!!!',
                'data' => null
            );
            $this->response($respuesta);
        }
         
    }
    
    
    /***
     * SERVICIO PARA ENTREGAR ARCHIVOS AL CALIFICACION
     * Parametros formulario para tabla est_deber_calificacion_entrega
     */
    public function entregadeber_post(){
        
        /*         * * recibiendo datos post *** */
        if (json_decode(file_get_contents('php://input'), true)) {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
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

            $this->response($respuesta, REST_Controller::HTTP_UNAUTHORIZED);
        } else {

            $entregar = $this->PadreSitio_model->entegar_deber($data);
            

            if ($entregar == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'El deber no se insertó, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Archivo entregados correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }
    
    
    /***
     * servicio de entrega de listado de archivos entregados por calificacion
     */
    
    public function archivosentregados_get(){
        $token = $this->uri->segment(3);
        $deberCalificionId = $this->uri->segment(4);
        $perfil = $this->User_model->revisa_perfil_x_token($token);
        if ($perfil == FALSE) {
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );

            $this->response($respuesta);
            exit;
        }
        $deberes = $this->PadreSitio_model->get_deberes_entregados($deberCalificionId);
        
        if (isset($deberes)) {
            $respuesta = array(
                'error' => false,
                'mensaje' => 'Deberes del estudiante.',
                'deberes' => $deberes
            );
            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No tiene deberes entregados!!!',
                'data' => null
            );
            $this->response($respuesta);
        }
    }
    
    /*****
     * sertvicio de eliminacion de entrega de archivo de deber
     */
    public function eliminarentrega_get(){
        $token = $this->uri->segment(3);
        $entregaId = $this->uri->segment(4);

        $perfil = $this->User_model->revisa_perfil_x_token($token);

        if ($perfil == FALSE) {
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );

            $this->response($respuesta, REST_Controller::HTTP_UNAUTHORIZED);
            exit;
        }

        $eliminar = $this->PadreSitio_model->eliminar_entrega($entregaId);

        if (isset($eliminar)) {
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registro eliminado correctamente.',
                'data' => null
            );

            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'La entrega no se puede eliminar!!!',
                'data' => null
            );

            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }
   
}
