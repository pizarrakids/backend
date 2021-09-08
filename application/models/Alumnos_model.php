<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Alumnos_model extends CI_Model {
    /*     * *********************************** toma recursos por detalleId **************
     *  (1)
     * METODO QUE CONSULTA LOS DEBERES DE LA SEMANA
     * @param type $semanaId
     */

    public function por_plan_detalle_id($detalleId) {
        
        $this->db->select("rm.id,concat(u.apellidos, ' ', u.nombres) as estudiante");
        $this->db->from("doc_plan_semanal_iniciales_detalle det");
        $this->db->join("doc_plan_semanal_iniciales p","p.id = det.plan_id");
        $this->db->join("doc_registro_academico r","r.id = p.registro_academico_id");
        $this->db->join("doc_registro_x_matriculados rm","rm.registro_id = r.id");
        $this->db->join("est_matricula mat","mat.id = rm.matricula_id");
        $this->db->join("acc_usuario u","u.correo = mat.usuario_estudiante");
        $this->db->where(array("det.id" => $detalleId));
        $this->db->order_by('u.apellidos asc, u.nombres asc');
        
        $query = $this->db->get();
        $respuesta = $query->result();
        return $respuesta;
    }

    
    /*** (1)
     * METODO PARA ENTREGAR LOS DATOS DEL ALUMNO
     */
    
    public function datos_alumno($correo){
        $this->db->select("concat(nombres, ' ',apellidos) as nombre, nickname, avatar");
        $this->db->from('acc_usuario');
        $this->db->where(array('correo' => $correo));
        $query = $this->db->get();
        $respuesta = $query->row();
        if($respuesta){
            return $respuesta;
        }else{
            return false;
        }
    }
   
}
