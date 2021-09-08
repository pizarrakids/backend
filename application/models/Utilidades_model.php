<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Utilidades_model extends CI_Model{
    
    
    public function tramita_post( $postNormal, $json ){
     
        print_r($json, $postNormal);
        
    }
    
    
    public function verifica_estado_semana($semanaNumero, $registroId){
        
        $hoy = date("Y-m-d H:i:s");
        
        $this->db->select("fecha_cierre");
        $this->db->from("doc_plan_semanal_iniciales");
        $this->db->where(array(
            'registro_academico_id' => $registroId,
            'semana_numero' => $semanaNumero
        ));
        $query = $this->db->get();
        $fechaCierre = $query->row();
        
        if($hoy < $fechaCierre->fecha_cierre){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function download($pathFile){
               
        $data = file_get_contents($pathFile);
        force_download($pathFile, $data);
    }
    
}