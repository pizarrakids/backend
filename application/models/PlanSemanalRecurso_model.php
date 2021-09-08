<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PlanSemanalRecurso_model extends CI_Model {

    /*     * *********************************** toma recursos por detalleId **************
     *  (1)
     * METODO QUE CONSULTA LOS RECURSOS POR EL DETALLEID PASADO COMO PARAMETRO
     * @param type $registroId
     */

    public function toma_recurso_x_detalle($detalleId) {
        $this->db->select("*");
        $this->db->from("doc_plan_semanal_iniciales_recursos");
        $this->db->where(array("id_plan_semanal_detalle" => $detalleId));
        $query = $this->db->get();
        $respuesta = $query->result();
        return $respuesta;
    }
    
    
    /************* inserta recurso *****************
     * (1)
     * METODO QUE REALIZA EL TRAMITE PARA INSERCION DE LOS DATOS
     */
    public function inserta_recurso($dataPost){
        
        $data = array(
            'id_plan_semanal_detalle' => $dataPost['detalle_id'], 
            'codigo'                  => $dataPost['codigo'], 
            'tipo'                    => $dataPost['tipo'], 
            'contenido'               => $dataPost['contenido']
        );
        
        if($this->db->insert('doc_plan_semanal_iniciales_recursos', $data)){
            return true;
        }else{
            return false;
        }
        
    }
    
    /////////////  fin de inserta recurso //////////////

    
    /**************************** elimina recurso
     * (1)
     * metodo que elimna el recurso
     */
    public function eliminar_recurso($recursoId){
        if($this->db->delete('doc_plan_semanal_iniciales_recursos', array('id' => $recursoId))){
            return true;
        }else{
            return false;
        }
    }

}
