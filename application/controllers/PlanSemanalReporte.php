<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/TCPDF/examples/tcpdf_include.php';

header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

class PlanSemanalReporte extends REST_Controller {

    private $pdf;
    private $docente;

    public function __construct() {
        //llamando a constructor padre
        parent::__construct();

        $this->load->database(); //pegandose a la base
        $this->load->model('RegistrosAcademicos_model'); //cargando el modelo Plan Semanal
        $this->load->model('PlanSemanalReporte_model'); //clase de reportes de plan semanal
        $this->load->model('User_model'); //cargando el modelo User
    }

    /*     * *
     * ENTREGA DATOS DIAS Y HORAS
     */

    public function plan_get() {

        $token = $this->uri->segment(3);
        $desde = $this->uri->segment(4);
        $hasta = $this->uri->segment(5);
        $periodo = $this->uri->segment(6);
        $registroId = $this->uri->segment(7);

        $perfil = $this->User_model->revisa_perfil_x_token($token);

        if ($perfil == FALSE) {
            $respuesta = array(
                'error' => true,
                'mensaje' => 'Usuario no permitido.',
                'data' => null
            );

            $this->response($respuesta);
            exit;
        }

        $this->genera_pdf($desde, $hasta, $periodo, $registroId, $token);
    }

    private function genera_pdf($desde, $hasta, $periodo, $registroId, $token) {
        // create new PDF document
        $this->pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('PizarraKids');
        $this->pdf->SetTitle('Planificación-Docente');
        $this->pdf->SetSubject('PDF');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        
        $datosRegistros =  $this->RegistrosAcademicos_model->registro_x_id($registroId);

        $this->docente = $datosRegistros->docente;
        
        $titulo    = $datosRegistros->materia.' '.$datosRegistros->nombre_instituto.' '.$datosRegistros->nombre;
        $subtitulo = $datosRegistros->docente.' del '.$desde.' al '.$hasta. ' / '. $periodo;

// set default header data
//        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006', PDF_HEADER_STRING);
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $titulo , $subtitulo);

// set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $this->pdf->setLanguageArray($l);
        }

// ---------------------------------------------------------
// set font
        $this->pdf->SetFont('dejavusans', '', 10);

// add a page
        $this->pdf->AddPage();


        $html = $this->genera_html_pdf($desde, $hasta, $registroId);
        
        // output the HTML content
        $this->pdf->writeHTML($html, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// reset pointer to the last page
        $this->pdf->lastPage();

// ---------------------------------------------------------
//Close and output PDF document
        $this->pdf->Output('example_006.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
    }

    private function genera_html_pdf($desde, $hasta, $registroId) {
        
        $html = '<style>';
        $html .= '.centrarTexto{text-align: center;}';
        $html .= '.conBorde{border: solid #ccc 0.2px;}';
        $html .= '</style>';

        $html .= '<table border="1" cellpadding="3px">';
        $html .= "<tr>";
        $html .= '<td class="centrarTexto conBorde"><strong>AMBITOS</strong></td>';
        $html .= '<td class="centrarTexto conBorde"><strong>DESTREZAS</strong></td>';
        $html .= '<td class="centrarTexto conBorde"><strong>ACTIVIDADES</strong></td>';
        $html .= '<td class="centrarTexto conBorde"><strong>RECURSOS Y MATERIALES</strong></td>';
        $html .= '<td class="centrarTexto conBorde"><strong>INDICADORES PARA EVALUAR</strong></td>';
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= '<td class="conBorde paddingLados">';
        $ambitos =  $this->PlanSemanalReporte_model->consulta_ambitos($desde, $hasta, $registroId);
        foreach ($ambitos as $ambito){
            $html .= $ambito->codigo.' '.$ambito->nombre.'<br>';
        }
        $html .= '</td>';
        
        $html .= '<td class="conBorde paddingLados">';
        $destrezas =  $this->PlanSemanalReporte_model->consulta_destrezas($desde, $hasta, $registroId);
        foreach ($destrezas as $destreza){
            $html .= $destreza->codigo.' '.$destreza->nombre.'<br>';
        }
        $html .= '</td>';

        $html .= '<td class="conBorde paddingLados">';
        $actividades =  $this->PlanSemanalReporte_model->consulta_actividades($desde, $hasta, $registroId);
        foreach ($actividades as $actividad){
            $html .= $actividad->tema.'<br>';
        }
        $html .= '</td>';

        $html .= '<td class="conBorde paddingLados">';
        $recursos =  $this->PlanSemanalReporte_model->consulta_recursos($desde, $hasta, $registroId);
        foreach ($recursos as $recurso){
            $html .= $recurso->contenido.'<br>';
        }
        $html .= '</td>';

        $html .= '<td class="conBorde paddingLados">';
        $indicadores =  $this->PlanSemanalReporte_model->consulta_indicadores($desde, $hasta, $registroId);
        foreach ($indicadores as $indicador){
            $html .= $indicador->contenido.'<br>';
        }
        $html .= '</td>';

        $html .= "</tr>";
        $html .= "</table>";
        
        $html .= '<h1>Desarrollado por '.$this->docente.'</h1>';
        // output the HTML content
        $this->pdf->writeHTML($html, true, false, true, false, '');
        //$this->pdf->AddPage();

    }

}
