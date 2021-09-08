<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PlanSemanalReporte_model extends CI_Model{
    private $registroId;
    
    
    public function consulta_ambitos($desde, $hasta, $registroId){
        
        $query = "select  a.codigo
                                        ,a.nombre 
                        from 	doc_plan_semanal_iniciales_recursos r
                                        inner join doc_plan_semanal_iniciales_detalle d on d.id = r.id_plan_semanal_detalle
                                        inner join doc_plan_semanal_iniciales p on p.id = d.plan_id 
                                        inner join cur_curriculo_destreza des on des.codigo = r.codigo 
                                        inner join cur_curriculo_ambito a on a.id = des.ambito_id 
                        where 	p.registro_academico_id = $registroId
                                        and p.fecha_inicio >= '$desde 00:00:00'
                                        and p.fecha_finaliza <= '$hasta 23:59:59'
                                        and r.tipo = 'destreza'
                        group by a.codigo, a.nombre;";
       $res = $this->db->query($query);
       return $res->result();
    }
    
    public function consulta_destrezas($desde, $hasta, $registroId){
        
        $query = "select  des.codigo
                                        ,des.nombre 
                        from 	doc_plan_semanal_iniciales_recursos r
                                        inner join doc_plan_semanal_iniciales_detalle d on d.id = r.id_plan_semanal_detalle
                                        inner join doc_plan_semanal_iniciales p on p.id = d.plan_id 
                                        inner join cur_curriculo_destreza des on des.codigo = r.codigo 
                                        inner join cur_curriculo_ambito a on a.id = des.ambito_id 
                        where 	p.registro_academico_id = $registroId
                                        and p.fecha_inicio >= '$desde 00:00:00'
                                        and p.fecha_finaliza <= '$hasta 23:59:59'
                                        and r.tipo = 'destreza'
                        group by des.codigo, des.nombre;";
       $res = $this->db->query($query);
       return $res->result();
    }
    
    public function consulta_recursos($desde, $hasta, $registroId){
        
        $query = "select  r.contenido
                        from 	doc_plan_semanal_iniciales_recursos r
                                        inner join doc_plan_semanal_iniciales_detalle d on d.id = r.id_plan_semanal_detalle
                                        inner join doc_plan_semanal_iniciales p on p.id = d.plan_id 
                        where 	p.registro_academico_id = $registroId
                                        and p.fecha_inicio >= '$desde 00:00:00'
                                        and p.fecha_finaliza <= '$hasta 23:59:59'
                                        and r.tipo = 'material'
                        group by r.contenido;";
       $res = $this->db->query($query);
       return $res->result();
    }
    
    public function consulta_indicadores($desde, $hasta, $registroId){
        
        $query = "select  r.contenido
                        from 	doc_plan_semanal_iniciales_recursos r
                                        inner join doc_plan_semanal_iniciales_detalle d on d.id = r.id_plan_semanal_detalle
                                        inner join doc_plan_semanal_iniciales p on p.id = d.plan_id 
                        where 	p.registro_academico_id = $registroId
                                        and p.fecha_inicio >= '$desde 00:00:00'
                                        and p.fecha_finaliza <= '$hasta 23:59:59'
                                        and r.tipo = 'indicador'
                        group by r.contenido;";
       $res = $this->db->query($query);
       return $res->result();
    }
    
    public function consulta_actividades($desde, $hasta, $registroId){
        
        $query = "select  d.tema 
                    from 	doc_plan_semanal_iniciales_detalle d
                                    inner join doc_plan_semanal_iniciales p on p.id = d.plan_id 
                    where 	p.registro_academico_id = $registroId
                                    and p.fecha_inicio >= '$desde 00:00:00'
                                    and p.fecha_finaliza <= '$hasta 00:00:00'
                    group by d.tema;";
       $res = $this->db->query($query);
       return $res->result();
    }
    
   

    
}

