<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PlanSemanal_model extends CI_Model{
    private $registroId;
    
    /**
     * METODO QUE CONSULTA LOS PLANES SEMANALES REALIZADOS
     * ORDENADOS DESCENDENTEMENTE
     * @param type $token
     * @param type $registroId
     */
    public function consulta_todos($registroId){
        
        $this->db->select("*");
        $this->db->from("doc_plan_semanal_iniciales");
        $this->db->where(array("registro_academico_id" => $registroId));
        $this->db->order_by('id DESC');
        $query = $this->db->get();
        $resp = $query->result();
        
        return $resp;
    }
    
    
    /*********************************** INICIA NUEVA  SEMANA **************
     * (1)
     * METODO QUE CREA SEMANA NUEVA
     * @param type $registroId
     */ 
    public function crear($registroId){
        $ultimaSemana = $this->consulta_ultima_semana($registroId);
        if(isset($ultimaSemana)){
            $ultimaSema = $ultimaSemana->semana_numero;
            $fechaFinal = $ultimaSemana->fecha_finaliza;
            
            $date = date("Y-m-d");
            //Incrementando 1 dias
            $fechaInicia = date("Y-m-d",strtotime($fechaFinal."+ 1 days"));
            
            
        }else{
            $ultimaSema = 0;
            $this->db->select("valor_cadena");
            $this->db->from("ins_parametros");
            $this->db->where(array("codigo" => 'iniciaplaninicial'));
            $query = $this->db->get();
            $resp = $query->row();
            $fechaInicia =  $resp->valor_cadena;
        }
        
        $nuevaSemana    = $ultimaSema + 1;
        $fechaFinaliza  = date("Y-m-d",strtotime($fechaInicia."+ 6 days"));
        $fechaCierre    = date("Y-m-d",strtotime($fechaFinaliza."+ 5 days"));
        
        $insertado = $this->crea_semana($registroId, $nuevaSemana, $fechaInicia, $fechaFinaliza, $fechaCierre);
        
        return $insertado;
        
    }
    
    /** (2)
     * RECUPERA EL ULTIMO NUMERO DE SEMANA SEGUN EL REGISTRO ACADEMICO ID
     * @param type $registroId
     * @return type
     */
    private function consulta_ultima_semana($registroId){
        $this->db->select("semana_numero, fecha_finaliza");
        $this->db->from("doc_plan_semanal_iniciales");
        $this->db->where(array( "registro_academico_id" => $registroId));
        $this->db->order_by('semana_numero', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        $resp = $query->row();
        
        return $resp;
    }
    
    
    /**(3)
     * inyecta el registro de la semana 
     * @param type $nuevaSemana
     * @param type $registroId
     */
    private function crea_semana($registroId, $nuevaSemana, $fechaInicio, $fechaFinaliza, $fechaCierre){
        
        $data = array(
            'registro_academico_id' => $registroId, 
            'semana_numero'         => $nuevaSemana, 
            'fecha_inicio'          => "$fechaInicio", 
            'fecha_finaliza'        => "$fechaFinaliza",
            'fecha_cierre'          => "$fechaCierre"
        );
        
        $this->db->insert('doc_plan_semanal_iniciales', $data);
        
        return $this->db->insert_id(); //retirna el id nuevo
        
    }

    /////////////////// TERMINA NUEVA SEMANA ///////////////////////////////////

    
}

