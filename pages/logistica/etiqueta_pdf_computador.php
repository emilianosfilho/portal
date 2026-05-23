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

if(isset($_POST) && !empty($_POST)){
 $dados = $_POST;
} else {
	if(isset($_GET) && !empty($_GET)){
	   $dados = $_GET;
	} else {
	  $dados = array();
	  $dados['op'] = '0';
	}
}

// $debug = true;

if($debug) varDump2($dados);

//TÍTULO DO RELATÓRIO
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
	varDump2("Imagem não encontrada");
}

$filename = '../../dist/img/logo_header_vertical.png';
if (file_exists($filename)) {
	$pdf->Image($filename, 87, 1, 9, 33);
} else {
	varDump2("Imagem não encontrada");
}
 
$pdf->SetFont('arial','','12');
$pdf->Cell(7,  6, "", 0, 0, 'C');
$pdf->Cell(35, 6, "Patrimônio NR", 0, 0, 'C');
$pdf->Cell(5, 6, "", 'LR', 0, 'C');
$pdf->Cell(35, 6, "Patrimônio NR", 0, 1, 'C');

$pdf->SetFont('arial','b','24');
$pdf->Cell(7, 9, "", 0, 0, 'C');
$pdf->Cell(35, 9, $dados['patrimonio'], 0, 0, 'C');
$pdf->Cell(5, 9, "", 'LR', 0, 'C');
$pdf->Cell(35, 9, $dados['patrimonio'], 0, 1, 'C');

$pdf->SetFont('arial','','12');
$pdf->Cell(7, 6, "", 0, 0, 'C');
$pdf->Cell(35, 6, "Etiqueta NR", 0, 0, 'C');
$pdf->Cell(5, 6, "", 'LR', 0, 'C');
$pdf->Cell(35, 6, "Etiqueta NR", 0, 1, 'C');

$pdf->SetFont('arial','b','24');
$pdf->Cell(7, 9, "", 0, 0, 'C');
$pdf->Cell(35, 9, $dados['etiqueta'], 0, 0, 'C');
$pdf->Cell(5, 9, "", 'LR', 0, 'C');
$pdf->Cell(35, 9, $dados['etiqueta'], 0, 1, 'C');

//*********************************************************************************

//ENDEREÇO ONDE SERÁ GERADO O PDF
$end_final = @date('Ymd_His_').str_replace(" ", "_", $titulo).".pdf";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF é usado, se disponível.
//D-> enviar para o navegador e forçar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
redireciona($end_final);
?>