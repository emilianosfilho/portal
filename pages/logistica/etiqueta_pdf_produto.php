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
require_once "../../plugins/code39/code39.php";

if(!empty($_GET)){
	$dados = $_GET;
} else {
	$dados = array();
	$dados['op'] = '0';
}

// $debug = true;

if($debug) varDump2($dados);

if (!isset($dados['CODPROD']) || empty($dados['CODPROD'])) {
	exibeMensagem("ERRO. CODPROD inválido.");
	fechaAba();
}

$produto = buscaDadosProduto($dados['CODPROD']);
// varDump2($produto);

//T�TULO DO RELAT�RIO
$titulo = "ETIQUETA PRODUTO";

//ENDERE�O ONDE SER� GERADO O PDF
$end_final = @date('Ymd_His_').str_replace(" ", "_", $titulo).".pdf";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";


$ETIQ_ALTURA = floatval(35);
$ETIQ_LARGURA = floatval(100);

$pdf = new PDF_Code39();
$pdf = new PDF_Code39('L','mm',array($ETIQ_LARGURA, $ETIQ_ALTURA));

//EXIBE OS REGISTROS
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(1,1,1);
$pdf->SetAutoPageBreak(false,0);

$filename = '../../dist/img/logo_header_vertical.png';
$pdf->Image($filename, 3, 2, 10, 30);

$pdf->Cell(1, 3, "", 0, 1);

$pdf->Code39(15, 10, $produto['CODPROD'], 1.3, 10);

$pdf->SetXY(1, 3);

$pdf->SetTextColor(0,0,0);
$pdf->Cell(13, 6, "", 0, 0);
$pdf->SetFont('arial','b','24');
$pdf->Cell(57, 6, "W".$produto['CODPROD']."-".$produto['DV'], 0, 0, 'L');
$pdf->SetFont('arial','','10');
$pdf->Cell(25, 6, "LOCAL: ", 0, 1, 'C');

$pdf->SetFont('arial','B','18');
$pdf->Cell(70, 10, "", 0, 0);
$pdf->Cell(25, 10, ($produto['LOCACAO'])?trim($produto['LOCACAO']):'', 0, 1, 'C');

$pdf->Cell(95, 7, "", 0, 1);

$pdf->SetFont('arial','','12');
$pdf->Cell(13, 5, "", 0, 0, 'R');
$pdf->SetFont('arial','B','12');
$pdf->Cell(82, 5, trim($produto['DESCRICAO']), 0, 1, 'L');


//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
redireciona($end_final);
?>