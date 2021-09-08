<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model{
    public $perfil_id;
    public $correo;
    public $nombres;
    public $apellidos;
    public $clave;
    public $token;
    public $estado;
    public $nickname;
    public $avatar;
    public $numero_celular;
    public $fecha_nacimiento;
    public $cargo;
    public $titulo;
    public $genero;
    public $anio_lectivo_id;
    
    public function login( $usuario, $clave ){
        $clave = md5($clave);
        $this->db->where( array( 'correo' => $usuario, 'clave' => $clave ) );
        $query = $this->db->get('acc_usuario');
        $row = $query->custom_row_object(0, 'User_model');
        
        if( isset($row) ){
            $this->genera_token($usuario);
            $this->db->where( array( 'correo' => $usuario, 'clave' => $clave ) );
            $query = $this->db->get('acc_usuario');
            $row1 = $query->custom_row_object(0,'User_model');
            return $row1;
        }else{
            return $row;
        }
    }
    
    private function genera_token($usuario){
        $fecha = date("YmdHis");
        $token = base64_encode($usuario.$fecha);
        
        $this->db->where('correo' , $usuario);
        $this->db->update('acc_usuario',array('token' => $token));
        
        return $token;
    }
    
    public function perfil($id){
        $query = $this->db->get_where('acc_perfil', array('id' => $id));
        $row = $query->row();
        return $row;
    }
    
    public function revisa_perfil_x_token($token){
        $this->db->select("p.codigo");
        $this->db->from('acc_usuario u');
        $this->db->join('acc_perfil p', 'p.id = u.perfil_id');
         
        $this->db->where(array( 'u.token' => $token ));
        
        $query = $this->db->get();
        $resultado = $query->row();

        if(count($resultado) > 0){
            $resp = $resultado;
        }else{
            $resp = FALSE;
        }
        return $resp;
    }
    
    
}