<?php 
session_start();
ini_set("xdebug.var_display_max_depth", -1);
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE|E_DEPRECATED);
ini_set('memory_limit', '24G');
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/logistica/function.php';
require_once "../../plugins/fpdf/fpdf.php";

// $debug = true;

if(isset($_POST) && !empty($_POST)){
 	$dados = $_POST;
} else {
	$dados = $_GET;
}

if($debug) varDump2($dados);

//T�TULO DO RELAT�RIO
$titulo = "ETIQUETA COMPUTADOR";

$altura_linha = (int) 6;


$ETIQ_ALTURA = floatval(35);
$ETIQ_LARGURA = floatval(100);

$pdf = new FPDF('L','mm',array($ETIQ_LARGURA, $ETIQ_ALTURA));
$pdf->SetMargins(5, 3, 5);
$pdf->AddPage();
$pdf->SetAutoPageBreak(false,0);

$filename = '../../dist/img/logo_header_vertical.png';
if (file_exists($filename)) {
	$pdf->Image($filename, 3, 1, 9, 33);
} else {
	varDump2("Imagem n�o encontrada");
}

$pdf->SetFont('arial','b',$dados['tamanho_fonte']);
$pdf->Cell(7,  20, "", 0, 0, 'C');
$pdf->Cell(83, 20, trim(converterUTF8($dados['nome'])), 0, 1, 'C');

if ($dados['rodape']) {
	$pdf->SetFont('arial','','14');
	$pdf->Cell(7,  10, "", 0, 0, 'C');
	$pdf->Cell(83, 10, trim(converterUTF8($dados['rodape'])), 'T', 1, 'C');
}


//*********************************************************************************

//ENDERE�O ONDE SER� GERADO O PDF
$end_final = @date('Ymd_His_').str_replace(" ", "_", $titulo).".pdf";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
redireciona($end_final);
?>