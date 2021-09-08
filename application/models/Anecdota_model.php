<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Anecdota_model extends CI_Model {
    /*     * *********************************** INICIA NUEVA proceso de ingreso de detalles **************
     *  (1)
     * METODO QUE PROCESA LOS PLANES SEMANALES REALIZADOS DE ACUERDO A LA SEMANA ENVIADA
     * IONGRESA EN LAS FECHAS
     * ORDENADOS DESCENDENTEMENTE
     * @param type $token
     * @param type $registroId
     */
    public function anecdota_x_calificacion($calificacionId) {

        $this->db->select("*");
        $this->db->from("doc_calificaciones_inicial");
        $this->db->where(array('id' => $calificacionId));
        $query = $this->db->get();
        $calificacion = $query->row();

        $this->db->reset_query(); /// resetea consulta anterior

        $this->db->select("a.id, a.calificacion_id, a.foto, a.descripcion, c.detalle_id");
        $this->db->from("doc_anecdota a");
        $this->db->join("doc_calificaciones_inicial c","c.id = a.calificacion_id");
        $this->db->where(array("calificacion_id" => $calificacionId));
        $query = $this->db->get();
        $anecdotas = $query->result();


        ////toma de estado de semana si es cerrada o abierta

        $datosSemana = $this->verifica_numero_semana($calificacionId);

        $this->load->model('Utilidades_model'); //cargando el modelo Plan Semanal
        $estadoSemana = $this->Utilidades_model->verifica_estado_semana($datosSemana->semana_numero, $datosSemana->registro_academico_id);

        return array(
            'calificacion' => $calificacion,
            'anecdotas' => $anecdotas,
            'estadoSemana' => $estadoSemana
        );
    }

    public function verifica_numero_semana($calificacionId) {
        $this->db->select("p.semana_numero, p.registro_academico_id");
        $this->db->from("doc_calificaciones_inicial c");
        $this->db->join("doc_plan_semanal_iniciales_detalle d", "d.id = c.detalle_id");
        $this->db->join("doc_plan_semanal_iniciales p", "p.id = d.plan_id");
        $this->db->where(array('c.id' => $calificacionId));

        $query = $this->db->get();
        $res = $query->row();

        return $res;
    }
    
    
    
    /***
     * (1)
     * Entrega informacion de anecdota por id
     */
    public function anecdota_x_id($id) {
        $this->db->select("*");
        $this->db->from("doc_anecdota");
        $this->db->where(array("id" => $id));
        $query = $this->db->get();
        $anecdota = $query->row();
        
        return $anecdota;
        
    }

    /*     * *
     * INGRESA Y SUBE EL ARCHIVO A LA RUTA por ejemplo
     * /var/www/html/codigneiter/system/images/anecdotas
     * 
     */
    public function insertar($dataPost) {
        
        $nombre = $dataPost["nombre"];
        $nombreArchivo = $dataPost["nombreArchivo"];
        $archivo = $dataPost["base64textString"];
        $archivo = base64_decode($archivo);

        $hoy = date("Y-m-d H-i-s");
        $nombreArchivo = $hoy.$nombreArchivo;

        $filePath = 'uploads/' . $nombreArchivo;

        if (file_put_contents($filePath, $archivo)) {
            $data = array(
                "calificacion_id" => $dataPost['calificacion_id'],
                "foto" => $nombreArchivo,
                "descripcion" => $dataPost['nombre'],
            );

            if ($this->db->insert("doc_anecdota", $data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
         
    }
   
    
    
    /*     * *
     * ACTUALIZA FOTO Y SUBE EL ARCHIVO A LA RUTA por ejemplo
     * /var/www/html/codigneiter/system/images/anecdotas
     * 
     */
    public function actualizarFoto($dataPost) {
        
        $nombre = $dataPost["nombre"];
        $nombreArchivo = $dataPost["nombreArchivo"];
        $archivo = $dataPost["base64textString"];
        $archivo = base64_decode($archivo);

        $hoy = date("Y-m-d H-i-s");
        $nombreArchivo = $hoy.$nombreArchivo;

        $filePath = 'uploads/' . $nombreArchivo;

        if (file_put_contents($filePath, $archivo)) {
            $data = array(
                "foto" => $nombreArchivo
            );
            $this->db->where(array('id' => $dataPost['id']));

            if ($this->db->update("doc_anecdota", $data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
         
    }
    
    
    /*     * *
     * ACTUALIZA DESCRIPCION DE LA ANECDOTA
     * 
     * 
     */
    public function actualizarDescripcion($dataPost) {
        

            $data = array(
                "descripcion" => $dataPost['descripcion']
            );
            $this->db->where(array('id' => $dataPost['id']));

            if ($this->db->update("doc_anecdota", $data)) {
                return true;
            } else {
                return false;
            }
            
    }

    public function eliminar($anecdotaId) {
        if ($this->db->delete('doc_anecdota', array('id' => $anecdotaId))) {
            return true;
        } else {
            return false;
        }
    }
    
    public function todasAnecdotas($dataPost){
        $this->db->select("a.id, a.descripcion, a.foto, c.calificacion, c.es_activa");
        $this->db->from("doc_anecdota a");
        $this->db->join("doc_calificaciones_inicial c","c.id = a.calificacion_id");
        $this->db->where(array("c.estudiante_usuario" => $dataPost['correo'], "c.es_activa" => true));
        $this->db->order_by("a.id");
        
        $query = $this->db->get();
        $anecdotas = $query->result();
        
        if(count($anecdotas) > 0 ){
            return $anecdotas;
        }else{
            return false;
        }
    }

}
