<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PadreSitio_model extends CI_Model {
    /*     * *********************************** INICIA NUEVA proceso de ingreso de detalles **************
     *  (1)
     * METODO QUE CONSULTA LOS HIJOS DEL USUARIO CONECTADO
     * IONGRESA EN LAS FECHAS
     * ORDENADOS DESCENDENTEMENTE
     * @param type $token
     * @param type $registroId
     */
    public function hijos($correoPadre) {
        
        $this->db->select("e.perfil_id, e.correo, e.nombres, e.apellidos, e.estado, e.nickname, e.avatar, e.numero_celular
		,e.fecha_nacimiento, e.cargo, e.titulo, e.genero, e.anio_lectivo_id
                ,c.nombre_instituto as curso
		,c.tema 
		,par.nombre as paralelo");
        $this->db->from("padre_x_estudiante pe");
        $this->db->join("acc_usuario e","e.correo = pe.correo_estudiante");
        $this->db->join("est_matricula m","m.anio_lectivo_id = e.anio_lectivo_id");
        $this->db->join("ins_curso c","c.id = m.curso_id");
        $this->db->join("ins_paralelo par","par.id = m.paralelo_id");
       
        $where = "pe.correo_padre = '$correoPadre' and m.usuario_estudiante = e.correo";
        
        $this->db->where($where);
        
        
        $query = $this->db->get();
        $hijos = $query->result();
        
        if(count($hijos) > 0){
            return $hijos;
        }else{
            return false;
        }
    }

    
    /*     * *********************************** INICIA NUEVA proceso de ingreso de detalles **************
     *  (1)
     * METODO QUE CONSULTA LOS DEBERES QUE TIENE EL HIJO
     * @param type $token
     * @param type $correoAlumno
     */
    public function deberes($estudianteCorreo) {
        
        $this->db->select("c.id 
		,c.calificacion 
		,deb.fecha_inicio 
		,deb.fecha_entrega 
		,deb.tema 
                ,deb.id as deber_id
		,case 
			when calificacion is null then 'NO ENTREGADO'
			else 'ENTEGADO'
		end as estado");
        $this->db->from("est_deber_calificacion c");
        $this->db->join("doc_registro_x_matriculados rm","rm.id = c.registro_matri_id");
        $this->db->join("est_matricula mat","mat.id = rm.matricula_id");
        $this->db->join("acc_usuario est","est.correo = mat.usuario_estudiante");
        $this->db->join("doc_deber deb","deb.id = c.deber_id");
        $this->db->where(array("est.correo" => $estudianteCorreo));
        $this->db->order_by("fecha_entrega desc");
        $query = $this->db->get();
        $deberes = $query->result();
        if(count($deberes) > 0){
            return $deberes;
        }else{
            return false;
        }
        
    }

    
    /*     * ****************CONSULTA DE DETALLE DEBER **************
     *  (1)
     * METODO QUE CONSULTA LOS DEBERES QUE TIENE EL HIJO
     * @param type $token
     * @param type $correoAlumno
     */
    public function deber_detalle($deberId) {
        
        $this->load->model('Deber_model'); //cargando el modelo Deber
        $deber = $this->Deber_model->toma_deber($deberId); //toma los datos del deber
             
        return $deber;
        
    }
    
    
    
     /*     * *
     * INGRESA, SUBE EL ARCHIVO A LA RUTA por ejemplo
     * /var/www/html/codigneiter/system/images/deberesentregados
     * 
     */
    public function entegar_deber($dataPost) {
        
        $calificacionId = $dataPost["deber_calificacion_id"];
        $nombreArchivo = $dataPost["nombreArchivo"];
        $observacion = $dataPost["observacion"];
        $archivo = $dataPost["base64textString"];
        $archivo = base64_decode($archivo);

        $hoy = date("Y-m-d H:i:s");
        $hoyNombre = date("YmdHis");
        $nombreArchivoFile = $hoyNombre.$nombreArchivo;

        $filePath = 'homeworksloaded/' . $nombreArchivoFile;

        if (file_put_contents($filePath, $archivo)) {
            $data = array(
                "deber_calificacion_id" => $calificacionId,
                "fecha_entrega" => $hoy,
                "observacion" => $observacion,
                "archivo" => $nombreArchivoFile
            );

            if ($this->db->insert("est_deber_calificacion_entrega", $data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
         
    }
    
    
    /*****(1)
     * metodo para devolver el listado de los archivos entregados por el estudiante
     */
    
    public function get_deberes_entregados($calificacionId){
        $this->db->select('*');
        $this->db->from('est_deber_calificacion_entrega');
        $this->db->where(array('deber_calificacion_id' => $calificacionId));
        $query = $this->db->get();
        $entregados = $query->result();
        return $entregados;
    }

    /****(1)
     * metdo para eliminar registro de entrega
     */
    
    public function eliminar_entrega($entregaId) {
        if ($this->db->delete('est_deber_calificacion_entrega', array('id' => $entregaId))) {
            return true;
        } else {
            return false;
        }
    }
    
}
