<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class RegistrosAcademicos_model extends CI_Model{
    public $usuario;
    public $perfil;
    
    
    
    /**METODO QUE DEVUELVE TODOS LOS REGISTROS DEL DOCENTE O ROOT
     * 
     */
    public function registros_academicos( $token ){
        
        //consulta para adquiri perfil
        $this->db->select('acc_perfil.codigo');
        $this->db->from('acc_usuario');
        $this->db->join('acc_perfil', 'acc_perfil.id = acc_usuario.perfil_id');
        $this->db->where(array('acc_usuario.token' => $token));
        $query = $this->db->get();
        $row = $query->row();
        $perfil = $row->codigo;
        
        ////fin de consulta de perfil ////
        
        if($perfil == 'root'){
            $registros = $this->devuelte_todos();               //si es true trae registro de todos los profesores
        }else{
            $registros = $this->devuelve_solo_docente($token); //si es false trae registros de docente
        }
        
        return $registros;
        
    }
    
    /**
     * METODO QUE DEVUELVE TODOS LOS REGISTROS
     * @return type
     */
    private function devuelte_todos(){
        $this->db->select('r.id, c.nombre_instituto as curso, p.nombre as paralelo, r.estado');
        $this->db->from('doc_registro_academico r');
        $this->db->join('acc_usuario u', 'u.correo = r.usuario_docente');
        $this->db->join('ins_malla_x_materia mm', 'mm.id = r.malla_materia_id');
        $this->db->join('ins_materia m', 'm.id = mm.materia_id');
        $this->db->join('ins_curso c', 'c.id = r.curso_id');
        $this->db->join('ins_paralelo p', 'p.id = r.paralelo_id');
        $this->db->order_by('c.nombre_instituto asc, p.nombre asc');
        
        $query = $this->db->get();
        $resultado = $query->result();
        return $resultado;
    }
    
    /**
     * METODO QUE DEVUELVE REGISTROS DE DOCENTE POR TOKEN
     * @param type $token
     * @return type
     */
    private function devuelve_solo_docente($token){
//        $this->db->select('r.id, c.nombre_instituto as curso, p.nombre as paralelo, r.estado');
//        $this->db->from('doc_registro_academico r');
//        $this->db->join('acc_usuario u', 'u.correo = r.usuario_docente');
//        $this->db->join('ins_malla_x_materia mm', 'mm.id = r.malla_materia_id');
//        $this->db->join('ins_materia m', 'm.id = mm.materia_id');
//        $this->db->join('ins_curso c', 'c.id = r.curso_id');
//        $this->db->join('ins_paralelo p', 'p.id = r.paralelo_id');
//        $this->db->where(array('u.token' => $token));
//        $this->db->order_by('c.nombre_instituto asc, p.nombre asc');
//        
//        $query = $this->db->get();
//        $resultado = $query->result();
        
        $query = "select r.id, c.nombre_instituto as curso, p.nombre as paralelo, r.estado
                                    ,(
                                            select 	count(cal.id) as no_calificados
                    from 	est_deber_calificacion cal
                                    inner join doc_deber deb on deb.id = cal.deber_id 
                                    inner join doc_plan_semanal_iniciales_detalle det on det.id = deb.detalle_id 
                                    inner join doc_plan_semanal_iniciales pla on pla.id = det.plan_id 
                                    inner join doc_registro_academico reg on reg.id = pla.registro_academico_id 
                    where 	reg.id = r.id 
                                    and cal.calificacion is null
                                    )
                    from 	doc_registro_academico r
                                    inner join acc_usuario u on u.correo = r.usuario_docente 
                                    inner join ins_malla_x_materia mm on mm.id = r.malla_materia_id 
                                    inner join ins_materia m on m.id = mm.materia_id 
                                    inner join ins_curso c on c.id = r.curso_id 
                                    inner join ins_paralelo p on p.id = r.paralelo_id 
                    where 	u.token = '$token'
                    order by c.nombre_instituto asc, p.nombre asc;";
        $resultado = $this->db->query($query);
        
        return $resultado->result();
    }
    
    
    /**
     * METODO QUE DEVUELVE LOS DATOS DEL REGISTRO QUE SE SOLICITA POR ID
     * @param type $id
     * @return type
     */
    public function registro_x_id($id){
        $this->db->select("r.usuario_docente, concat(doc.nombres,' ',doc.apellidos) as docente ,"
                . "mm.id, m.nombre as materia, r.seccion_id, s.nombre_instituto, r.curso_id, "
                . "c.nombre_instituto, r.paralelo_id, p.nombre, r.estado, "
                . "r.usuario_autoridad_principal, r.usuario_autoridad_secundaria, r.usuario_secretario, r.usuario_coordinador,"
                . "concat(prin.nombres,' ',prin.apellidos) as principal, "
                . "concat(sec.nombres,' ',sec.apellidos) as secundaria, "
                . "concat(secre.nombres,' ',secre.apellidos) as secretaria, "
                . "concat(coor.nombres,' ',coor.apellidos) as coordinador, "
                . "r.rinde_supletorio");
        $this->db->from('doc_registro_academico r');
        $this->db->join('acc_usuario doc', 'doc.correo = r.usuario_docente');
        $this->db->join('ins_malla_x_materia mm', 'mm.id = r.malla_materia_id');
        $this->db->join('ins_materia m', 'm.id = mm.materia_id');
        $this->db->join('ins_curso c', 'c.id = r.curso_id');
        $this->db->join('ins_paralelo p', 'p.id = r.paralelo_id');
        $this->db->join('ins_seccion s', 's.id = r.seccion_id');
        $this->db->join('acc_usuario prin', 'prin.correo = r.usuario_autoridad_principal');
        $this->db->join('acc_usuario sec', 'sec.correo = r.usuario_autoridad_secundaria');
        $this->db->join('acc_usuario secre', 'secre.correo = r.usuario_secretario');
        $this->db->join('acc_usuario coor', 'coor.correo = r.usuario_coordinador');
         
        $this->db->where(array( 'r.id' => $id ));
         
        $query = $this->db->get();
        $resultado = $query->row();
        
        return $resultado;
    }
    
    
    public function estudiantes_x_registro($id){
        $this->db->select("e.id, s.correo, concat(s.apellidos,' ', s.nombres), "
                . "nota_fin_anio, nota_mejora1, nota_mejora2, nota_final_con_mejora, "
                . "nota_supletorio, nota_remedial, nota_gracia, nota_final, m.estado");
        $this->db->from('doc_registro_x_matriculados e');
        $this->db->join('est_matricula m', 'm.id = e.matricula_id');
        $this->db->join('acc_usuario s', 's.correo = m.usuario_estudiante');
         
        $this->db->where(array( 'e.registro_id' => $id ));
        $this->db->order_by('s.apellidos asc, s.nombres asc');
         
        $query = $this->db->get();
        $resultado = $query->result();
        return $resultado;
    }
    
}