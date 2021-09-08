<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PlanSemanalDetalle_model extends CI_Model {

    private $registroId;

    /*     * *********************************** INICIA NUEVA proceso de ingreso de detalles **************
     *  (1)
     * METODO QUE PROCESA LOS PLANES SEMANALES REALIZADOS DE ACUERDO A LA SEMANA ENVIADA
     * IONGRESA EN LAS FECHAS
     * ORDENADOS DESCENDENTEMENTE
     * @param type $token
     * @param type $registroId
     */

    public function procesa_planes($planId) {

        $this->db->select("*");
        $this->db->from("doc_plan_semanal_iniciales");
        $this->db->where(array("id" => $planId));
        $query = $this->db->get();
        $resp = $query->row();
        $this->procesar_fechas($resp->fecha_inicio, $resp->fecha_finaliza, $planId);

        $this->db->reset_query(); //resetea la consulta
        
        $this->db->select("*");
        $this->db->from("doc_plan_semanal_iniciales_detalle");
        $this->db->where(array('plan_id' => $planId));
        $this->db->order_by("fecha","ASC");
        $query = $this->db->get();
        $detalles = $query->result();
   
        return $detalles;
    }

    /**
     * (2)
     * METODO QUE BUSCA LOS DIAS A NIVEL DE SERIE
     * Y PASA A METODO DE BUSCA HORAS PARA INYECTAR EL DIA Y LA HORA
     * Previo revisa si existe, si existe no ingresa
     * @param type $registroId
     */
    private function procesar_fechas($fechaInicio, $fechaFin, $planId) {
        $date1 = new DateTime($fechaInicio);
        $date2 = new DateTime($fechaFin);
        $diff = $date1->diff($date2);
        // will output 2 days
        $diferencia = $diff->days;

        $j = 0;
        for ($i = 0; $i <= $diferencia; $i++) {
            $j++;
            $fecha = date("Y-m-d", strtotime($fechaInicio . "+ $i days"));

            $this->procesa_horas($fecha, $j, $planId);
        }
    }

    /**
     * (3)
     * METODO QUE PROCESA LAS HORAS
     * @param type $fecha
     * @param type $numeroDia
     * @param type $planId
     */
    private function procesa_horas($fecha, $numeroDia, $planId) {
        /*         * ******************* TOMA EL ID DEL DIA *************************** */
        $this->db->select("id");
        $this->db->from("gen_dia");
        $this->db->where(array("numero" => $numeroDia));
        $query = $this->db->get();
        $dias = $query->row();
        $diaId = $dias->id;
        /////////////// FIN DE TOMA DE ID DE DIA ///////////////////////

        $this->db->reset_query(); //resetea la consulta

        /*         * ******** CONSULTA LAS HORAS ************ */
        $this->db->select("*");
        $this->db->from("hor_horario_inicial_x_anio_lectivo");
        $this->db->order_by("numero_hora", "ASC");
        $query = $this->db->get();
        $horas = $query->result();

        foreach ($horas as $hora) {
            $horaId = $hora->id;

            $this->inserta_horas($planId, $diaId, $fecha, $horaId);
        }

        //////////// FIN DE CONSULTA DE HORAS ////////////////////
    }

    /**
     * (4)
     * METODO QUE VERIFICA SI EXISTEN LAS HORAS PLANEADAS, CASO CONTRARIO LA INGRESA
     * CON TITULO ... 
     * @param type $planId
     * @param type $diaId
     * @param type $fecha
     * @param type $horaId
     * @return type
     */
    private function inserta_horas($planId, $diaId, $fecha, $horaId) {

        $this->db->select('id');
        $this->db->from("doc_plan_semanal_iniciales_detalle");
        $this->db->where(array(
            "plan_id" => $planId,
            "dia_id" => $diaId,
            "fecha" => $fecha,
            "hora_id" => $horaId
        ));
        $query = $this->db->get();
        $existencia = $query->row();

        if ($existencia) {
            return;
        } else {
            $this->db->reset_query(); //resetea la consulta
            $data = array(
                'plan_id' => $planId,
                'dia_id' => $diaId,
                'fecha' => $fecha,
                'hora_id' => $horaId,
                'tema' => "..."
            );
            $this->db->insert('doc_plan_semanal_iniciales_detalle', $data);
        }
    }

    /////////////////// TERMINA NUEVA SEMANA ///////////////////////////////////
    
    /****************************
     * (5)
     * metodo que retorna los dias
     */
     public function consulta_dias(){
        $this->db->select("*");
        $this->db->from("gen_dia");
        $this->db->order_by("numero");
        $query = $this->db->get();
        $resp = $query->result();
        return $resp;
    }
    
    /****************************
     * (6)
     * metodo que retorna las horas
     */
     public function consulta_horas(){
        $this->db->select("*");
        $this->db->from("hor_horario_inicial_x_anio_lectivo");
        $this->db->order_by("numero_hora");
        $query = $this->db->get();
        $resp = $query->result();
        return $resp;
    }
    
    ////////////// FIN METODO ///////
    
    
    /************************
     * (1)
     * metodos para tomar datos de detalle por id
     */
    
    public function get_detalle_x_id($detalleId){
        $this->db->select("d.id 
		,d.fecha 
		,d.tema 
		,d.es_calificado 
		,p.semana_numero 
		,p.registro_academico_id 
                ,p.fecha_cierre
		,dia.nombre as dia 
		,h.nombre as hora
		,h.hora_inicio as hora_inicia
		,h.hora_finaliza as hora_finaliza");
        $this->db->from("doc_plan_semanal_iniciales_detalle d");
        $this->db->join("doc_plan_semanal_iniciales p", "p.id = d.plan_id ");
        $this->db->join("gen_dia dia", "dia.id = d.dia_id ");
        $this->db->join("hor_horario_inicial_x_anio_lectivo h", "h.id = d.hora_id");
        $this->db->where(array("d.id" => $detalleId));
        $query = $this->db->get();
        $resultado = $query->row();
        
        return $resultado;
        
    }
    ///////////// termina toma de detalle //////////
    
    
    /**********************************************
     * (1)
     * Toma destrezas por detalle
     * @param type $detalleId
     */
    public function toma_destrezas_x_detalle($detalleId){
        $curso = $this->consulta_curso($detalleId);
        
        $this->db->select("d.id as destreza_id
		,d.codigo as destreza_codigo
		,d.nombre as destreza
		,d.imprescindible 
		,e.nombre as eje
		,a.nombre as ambito");
        $this->db->from("cur_curriculo_eje e");
        $this->db->join("cur_curriculo_ambito a", "a.eje_id = e.id");
        $this->db->join("cur_curriculo_destreza d", "d.ambito_id = a.id");
        $this->db->where(array("e.curso_id" => $curso->curso_id));
        $this->db->order_by("e.codigo ASC, a.nombre ASC, d.nombre ASC");
        $query = $this->db->get();
        $respuesta = $query->result();        
        return $respuesta;
        
    }
    
    private function consulta_curso($detalleId){
        $this->db->select("r.curso_id");
        $this->db->from("doc_plan_semanal_iniciales_detalle d");
        $this->db->join("doc_plan_semanal_iniciales p", "p.id = d.plan_id");
        $this->db->join("doc_registro_academico r", "r.id = p.registro_academico_id");
        $this->db->where(array("d.id" => $detalleId));
        $query = $this->db->get();
        $resultado = $query->row();
        return $resultado;
    }
    
    ///////////////// fin de toma destrezas por detalle ///////////////////
    
    
    
    public function actualiza_detalle($detalleId, $campo, $contenido){
        $data = array(
            $campo => $contenido 
        );
        $this->db->set($data);
        $this->db->where(array("id" => $detalleId));
        if($this->db->update("doc_plan_semanal_iniciales_detalle")){
            return true;
        }else{
            return false;
        }
        
    }
    
    
    /*****
     * METODO QUE CONSTRUYE LAS CALIFICACIONES DE LA ACTIVIDAD
     */
    
    public function calificaciones($detalleId){
        $sql = "insert into doc_calificaciones_inicial(estudiante_usuario, detalle_id, es_activa )
select 	u.correo, d.id,true
from 	doc_plan_semanal_iniciales_detalle d
		inner join doc_plan_semanal_iniciales p on p.id = d.plan_id 
		inner join doc_registro_academico r on r.id = p.registro_academico_id 
		inner join doc_registro_x_matriculados e on e.registro_id = r.id 
		inner join est_matricula m on m.id = e.matricula_id 
		inner join acc_usuario u on u.correo = m.usuario_estudiante 
where 	d.id = $detalleId
		and u.correo not in (select estudiante_usuario 
							 from 	doc_calificaciones_inicial
							 where	estudiante_usuario = u.correo 
							 		and detalle_id = d.id 
							);";
        $this->db->query($sql);
       
        $this->db->reset_query(); //resetea la consulta
        $this->db->select("c.id, c.estudiante_usuario, c.detalle_id, c.calificacion, c.es_activa, concat(u.apellidos,' ',u.nombres ) as estudiante ");
        $this->db->from('doc_calificaciones_inicial c');
        $this->db->join('acc_usuario u','u.correo = c.estudiante_usuario');
        $this->db->where(array('c.detalle_id' => $detalleId));
        $this->db->order_by('u.apellidos ASC, u.nombres ASC');
        $query = $this->db->get();
        $respuesta = $query->result();        
        return $respuesta;
        
        
    }
    
    
    public function modifica_nota($dataPost){
        
        $data = array(
            $dataPost['campo'] => $dataPost['valor']
        );
        
        $this->db->set($data);
        $this->db->where(array("id" => $dataPost['calificacion_id']));
        if($this->db->update("doc_calificaciones_inicial")){
            return true;
        }else{
            return false;
        }
    }
}
