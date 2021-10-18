<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Deber_model extends CI_Model {
    /*     * *********************************** toma recursos por detalleId **************
     *  (1)
     * METODO QUE CONSULTA LOS DEBERES DE LA SEMANA
     * @param type $semanaId
     */

    public function toma_deberes($detalleId) {
        
        $this->db->select("*");
        $this->db->from("doc_deber deb");
//        $this->db->join("doc_plan_semanal_iniciales_detalle det","det.id = deb.detalle_id");
        $this->db->where(array("deb.detalle_id" => $detalleId));
        $query = $this->db->get();
        $respuesta = $query->result();
        return $respuesta;
    }

    /*     * *********************************** toma debere por detalleId **************
     *  (1)
     * METODO QUE CONSULTA UN DEBER DE LA SEMANA POR id
     * @param type $semanaId
     */

    public function toma_deber($deberId) {
        $this->db->select("*");
        $this->db->from("doc_deber");
        $this->db->where(array("id" => $deberId));
        $query = $this->db->get();
        $deber = $query->row();
        $material = $this->toma_material_deberes($deberId);


        return array(
            "deber" => $deber,
            "material" => $material
        );
    }

    private function toma_material_deberes($deberId) {
        $this->db->select("*");
        $this->db->from("doc_deber_material");
        $this->db->where(array("deber_id" => $deberId));
        $query = $this->db->get();
        $respuesta = $query->result();

        if (isset($respuesta)) {
            return $respuesta;
        } else {
            return null;
        }
    }

    /*     * *********** inserta recurso *****************
     * (1)
     * METODO QUE REALIZA EL TRAMITE PARA INSERCION DE LOS DATOS
     */

    public function insertar($dataPost) {

        $fechaCreacion = date("Y-m-d");

        $data = array(
            "detalle_id" => $dataPost['detalle_id'],
            "tema" => $dataPost['tema'],
            "descripcion" => $dataPost['descripcion'],
            "fecha_creacion" => $fechaCreacion,
            "fecha_inicio" => $dataPost['fecha_inicio'],
            "fecha_entrega" => $dataPost['fecha_entrega'],
        );

        if ($this->db->insert("doc_deber", $data)) {
            
            $this->inserta_registros_para_calificar($this->db->insert_id());
                        
            return $this->db->insert_id(); //retirna el id nuevo
        } else {
            return false;
        }
    }
    
    private function inserta_registros_para_calificar($deberId){
        $query = "insert into est_deber_calificacion(deber_id, registro_matri_id)
                    select 	deb.id, rm.id
                    from 	doc_plan_semanal_iniciales_detalle det
                                    inner join doc_plan_semanal_iniciales cab on cab.id = det.plan_id 
                                    inner join doc_registro_academico r on r.id = cab.registro_academico_id 
                                    inner join doc_registro_x_matriculados rm on rm.registro_id  = r.id
                                    inner join est_matricula mat on mat.id = rm.matricula_id 
                                    inner join acc_usuario est on est.correo = mat.usuario_estudiante
                                    inner join doc_deber deb on deb.detalle_id = det.id
                    where 	deb.id = $deberId
                                    and rm.id not in (select registro_matri_id from est_deber_calificacion 
                                                                      where deber_id = deb.id 
                                                                                    and registro_matri_id = rm.id);";
    $this->db->query($query);
    
    }
    

    /////////////  fin de inserta deber //////////////



    /*     * *
     * INGRESA Y SUBE EL ARCHIVO A LA RUTA por ejemplo
     * /var/www/html/codigneiter/system/images/anecdotas
     * 
     */
    public function insertar_material($dataPost) {

        $deberId = $dataPost["deber_id"];
        $titulo = $dataPost["titulo"];
        $nombreArchivo = $dataPost["nombreArchivo"];
        $archivo = $dataPost["base64textString"];
        $archivo = base64_decode($archivo);

        $hoy = date("Y-m-d H-i-s");
        $nombreArchivo = $hoy . $nombreArchivo;

        $filePath = 'homeworks/teachers/' . $nombreArchivo;

        if (file_put_contents($filePath, $archivo)) {
            $data = array(
                "deber_id" => $deberId,
                "titulo" => $titulo,
                "archivo" => $nombreArchivo
            );

            if ($this->db->insert("doc_deber_material", $data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*     * *
     * INGRESA Y SUBE EL ARCHIVO A LA RUTA por ejemplo
     * /var/www/html/codigneiter/system/images/anecdotas
     * 
     */

    public function actualizar_deber($dataPost) {

        $id = $dataPost["id"];

        $data = array(
            "tema" => $dataPost["tema"],
            "descripcion" => $dataPost["descripcion"],
            "fecha_inicio" => $dataPost["fecha_inicio"],
            "fecha_entrega" => $dataPost["fecha_entrega"]
        );

        $this->db->where(array('id' => $id));

        if ($this->db->update("doc_deber", $data)) {
            return true;
        } else {
            return false;
        }
    }

    /*     * ************************** elimina recurso
     * (1)
     * metodo que elimna el recurso
     */

    public function eliminar($deberId) {
        if ($this->db->delete('doc_deber_material', array('deber_id' => $deberId))) {

            $this->db->delete('doc_deber', array('id' => $deberId));
            return true;
        } else {
            return false;
        }
    }

    /*     * ************************** elimina recurso
     * (1)
     * metodo que descarga material de apoyo ingresado por el docente
     */

    public function download($materialId) {
        
        $this->load->helper('download');

        $this->db->select("*");
        $this->db->from("doc_deber_material");
        $this->db->where(array("id" => $materialId));
        $query = $this->db->get();
        $respuesta = $query->row();

        $archivo = $respuesta->archivo;
        $filePath = 'homeworks/teachers/' . $archivo;

        force_download($filePath, NULL);

    }
    
    
    /*     * ************************** elimina recurso
     * (1)
     * metodo que elimna el material del deber del profesor
     */

    public function eliminar_material($materialId) {
        if ($this->db->delete('doc_deber_material', array('id' => $materialId))) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
    /*     * *
     * CALIFICACION DEL DEBER
     * 
     * 
     */

    public function calificar_deber($dataPost) {

        $id = $dataPost["id"];

        $data = array(
            "calificacion" => $dataPost["calificacion"],
            "observacion" => $dataPost["observacion"]
        );

        $this->db->where(array('id' => $id));

        if ($this->db->update("est_deber_calificacion", $data)) {
            return true;
        } else {
            return false;
        }
    }
    
    
    /***** (1)
     * MUESTRA TODOS LOS DEBERES QUE HA ENVIADO EL PROFESOR DE REGISTRO ID
     */
    public function consulta_deberes_x_registro($registroId){
        $this->db->select("deb.id, deb.detalle_id, deb.tema, deb.descripcion, deb.fecha_creacion, deb.fecha_inicio, deb.fecha_entrega");
        $this->db->from("doc_plan_semanal_iniciales plan");
        $this->db->join("doc_plan_semanal_iniciales_detalle det", "det.plan_id = plan.id");
        $this->db->join("doc_deber deb", "deb.detalle_id = det.id");
        $this->db->where(array("plan.registro_academico_id" => $registroId));
        $this->db->order_by("deb.fecha_entrega asc");
        $query = $this->db->get();
        $respuesta = $query->result();
        if($respuesta){
            return $respuesta;
        }else{
            return false;
        }
        
    }

}
