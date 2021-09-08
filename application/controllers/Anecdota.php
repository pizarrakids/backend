<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

class Anecdota extends REST_Controller {

    public function __construct() {
        //llamando a constructor padre
        parent::__construct();

        $this->load->database(); //pegandose a la base
        $this->load->model('Anecdota_model'); //cargando el modelo Plan Semanal
        $this->load->model('User_model'); //cargando el modelo User
    }

    /*     * *
     * ENTREGA DATOS DIAS Y HORAS
     */

    public function anecdota_get() {
        $token = $this->uri->segment(3);
        $calificacionId = $this->uri->segment(4);

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

        $anecdotas = $this->Anecdota_model->anecdota_x_calificacion($calificacionId);

        if (isset($anecdotas)) {
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente.',
                'data' => $anecdotas
            );

            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existen anécdotas todavia!!!',
                'data' => null
            );

            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
         
    }
    
    public function anecdota_post(){
        
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

            $insertar = $this->Anecdota_model->insertar($data);
            

            if ($insertar == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'La anécdota no se insertó, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Anécdota ingresada correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }
    
    
    public function eliminar_get(){
        $token = $this->uri->segment(3);
        $anecdotaId = $this->uri->segment(4);

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

        $eliminar = $this->Anecdota_model->eliminar($anecdotaId);

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
                'mensaje' => 'La anécdota no se puede eliminar!!!',
                'data' => null
            );

            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
    
    /***
     * SERVICIO QUE DEVUELVE LOS DATOS DE UNA ANECDOTA
     */
    public function anecdotaid_get(){
        $token = $this->uri->segment(3);
        $id = $this->uri->segment(4);

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

        $anecdota = $this->Anecdota_model->anecdota_x_id($id);

        if (isset($anecdota)) {
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registro consultados correctamente.',
                'data' => $anecdota
            );

            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existen anécdotas todavia!!!',
                'data' => null
            );

            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
    
    /***
     * ACTUALIZA LA FOTO DE ANECDOTA
     */
    public function actualizarfoto_post(){
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

            $actualizar = $this->Anecdota_model->actualizarFoto($data);
            
            if ($actualizar == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'La foto no se actualizó, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Foto actualizada correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }
    
    
    /****
     * actuliza la descripcion de la anecdota
     */
    public function actualizardescripcion_post(){
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

            $actualizar = $this->Anecdota_model->actualizarDescripcion($data);
            

            if ($actualizar == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'La anécdota no se actualizó, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Anécdota actualizada correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }
    
    
    /****
     * todas las acecdoras del estudiante
     */
    public function anecdotasRstudiante_post(){
         /*         * * recibiendo datos post *** */
        if (json_decode(file_get_contents('php://input'), true)) {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            $data = $this->post();
        }
        $token = $data['token'];
        $correo = $data['correo'];
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

            $anecdotas = $this->Anecdota_model->todasAnecdotas($data);
            

            if ($anecdotas == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'No existen anécdotas asiganadas a su hijo!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Anécdotas consultadas correctamente!!!',
                    'data' => $anecdotas
                );
            }

            $this->response($respuesta);
        }
    }
   
}
