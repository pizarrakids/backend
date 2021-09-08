<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

class PlanSemanalDetalle extends REST_Controller {

    public function __construct() {
        //llamando a constructor padre
        parent::__construct();

        $this->load->database(); //pegandose a la base
        $this->load->model('PlanSemanalDetalle_model'); //cargando el modelo Plan Semanal
        $this->load->model('User_model'); //cargando el modelo User
    }

    /*     * *
     * ENTREGA DATOS DIAS Y HORAS
     */

    public function plandetalle_get() {
        $token = $this->uri->segment(3);
        $planId = $this->uri->segment(4);

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


        $detalles = $this->PlanSemanalDetalle_model->procesa_planes($planId);
        $dias = $this->PlanSemanalDetalle_model->consulta_dias();
        $horas = $this->PlanSemanalDetalle_model->consulta_horas();

        if (isset($detalles)) {
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente.',
                'data' => $detalles,
                'dias' => $dias,
                'horas' => $horas
            );

            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existen planes todavia!!!',
                'data' => null
            );

            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /*     * *
     * ACTUALIZA EL TEMA Y ES CALIFICADO DEL DETALLA
     */

    public function detalle_post() {
        if (json_decode(file_get_contents('php://input'), true)) {
            $data = json_decode(file_get_contents('php://input'), true);
            $token = $data['token'];
            $detalleId = $data['detalle_id'];
            $campo = $data['campo'];
            $contenido = $data['contenido'];
        } else {
            $data = $this->post();
            $token = $data['token'];
            $detalleId = $data['detalle_id'];
            $campo = $data['campo'];
            $contenido = $data['contenido'];
        }

        $perfil = $this->User_model->revisa_perfil_x_token($token);

        if (!$perfil) {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No ha realizado un ingreso de sesión!!!',
                'data' => null
            );

            $this->response($respuesta, REST_Controller::HTTP_UNAUTHORIZED);
        } else {
            $actualizaPlan = $this->PlanSemanalDetalle_model->actualiza_detalle($detalleId, $campo, $contenido);

            if ($actualizaPlan) {
                $respuesta = array(
                    'error' => FALSE,
                    'mensaje' => "$campo actualizado correctamente."
                );
                $this->response($respuesta);
            } else {
                $respuesta = array(
                    'error' => TRUE,
                    'mensaje' => 'No se pudo actualizar el campo. Consulte con el Administrador!!!'
                );
                $this->response($respuesta, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /*     * **
     * retorna datos de calificaciones al ingresar
     */

    public function calificaractividad_get() {
        $token = $this->uri->segment(3);
        $detalleId = $this->uri->segment(4);

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

        $calificaciones = $this->PlanSemanalDetalle_model->calificaciones($detalleId);

        if (isset($calificaciones)) {
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registros consultados correctamente.',
                'data' => $calificaciones
            );

            $this->response($respuesta);
        } else {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'No existen planes todavia!!!',
                'data' => null
            );

            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /*     * ****
     * CAMBIA LA NOTA
     */

    public function calificaractividad_post() {
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

            $modificaNota = $this->PlanSemanalDetalle_model->modifica_nota($data);

            if ($modificaNota == false) {
                $respuesta = array(
                    'error' => true,
                    'mensaje' => 'La nota no se actualizó, comuníquese con el Administrador!!!',
                    'data' => null
                );
            } else {
                $respuesta = array(
                    'error' => false,
                    'mensaje' => 'Nota actualizada correctamente!!!',
                    'data' => null
                );
            }

            $this->response($respuesta);
        }
    }

}
