<?php 
session_start();
ini_set("xdebug.var_display_max_depth", -1);
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('memory_limit', '24G');
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set('America/Manaus');
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/vendas/function.php';
require_once "../../plugins/code39/code39.php";

$debug = true;
$debug = false;

if($debug) varDump2($_SESSION['DADOS']);

// varDump2($_SESSION['DADOS']); die();

$PDF['CABECALHO'] = buscaPCPEDC($_SESSION['DADOS']);
$PDF['ITENS'] = buscaPCPEDI($_SESSION['DADOS']);
if($debug) varDump2($PDF);

$NUMPEDRCA = $PDF['CABECALHO']['NUMPEDRCA'];
if($debug) varDump2('NUMPEDRCA: '.$NUMPEDRCA);


//TÍTULO DO RELATÓRIO
$titulo = "PEDIDO VENDA";

//ENDEREÇO ONDE SERÁ GERADO O PDF
$end_final = $PDF['CABECALHO']['NUMPED'].'_'.str_replace(" ", "_", $titulo).".pdf";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF é usado, se disponível.
//D-> enviar para o navegador e forçar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

if($debug) varDump2($PDF['ITENS']);

$ETIQ_ALTURA = floatval(33);
$ETIQ_LARGURA = floatval(99);

$pdf=new PDF_Code39('L','mm',array(103,35));


foreach ($PDF['ITENS'] as $keyP => $valueI) {
	//EXIBE OS REGISTROS
	$pdf->AliasNbPages();
	$pdf->AddPage();	
	$pdf->SetMargins(1,1,1);
	$pdf->SetAutoPageBreak(false,0);

	// varDump2($valueI);


	$filename = '../../dist/img/logo_header_vertical.png';
	if($debug) varDump2(file_exists($filename));
	$pdf->Image($filename, 3, 1, 9, 33);


	$pdf->SetXY(($pos_x+1), ($pos_y +3));

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('arial','b','20');
	$pdf->Cell(13, 6, "", 0, 0);
	$pdf->Cell(50, 6, "W".$valueI['CODPROD']."-".$valueI['DV'], 0, 0, 'L');
	$pdf->SetFont('arial','','10');
	$pdf->Cell(15, 5, "LOCAL: ", 0, 0, 'R');
	$pdf->SetFont('arial','b','10');
	$pdf->Cell(20, 5, ($valueI['LOCACAO'])?trim($valueI['LOCACAO']):'', 0, 1, 'L');

	$pdf->Cell(63, 5, "", 0, 0);
	$pdf->SetFont('arial','','10');
	$pdf->Cell(15, 5, "QTD: ", 0, 0, 'R');
	$pdf->SetFont('arial','B','10');
	$pdf->Cell(20, 5, intval($valueI['QTFATURADA']), 0, 1, 'L');


	$pdf->Cell(63, 5, "", 0, 0);
	$pdf->SetFont('arial','','10');
	$pdf->Cell(15, 6, "DATA: ", 0, 0, 'R');
	$pdf->SetFont('arial','B','10');
	$pdf->Cell(20, 6, formataDataOracletoBr($PDF['CABECALHO']['DATA']), 0, 1, 'L');


	$pdf->Cell(63, 5, "", 0, 0);
	$pdf->SetFont('arial','','10');
	$pdf->Cell(15, 5, "QTD PED.: ", 0, 0, 'R');
	$pdf->SetFont('arial','B','10');
	$pdf->Cell(20, 5, trim($valueI['QTFATURADA']), 0, 1, 'L');


	$pdf->Cell(13, 5, "", 0, 0);
	$pdf->SetFont('arial','b','8');
	$pdf->Cell(50, 6, $PDF['CABECALHO']['CODCLI'].'- '.substr($PDF['CABECALHO']['CLIENTE'],0,23), 0, 0, 'L');
	$pdf->SetFont('arial','','10');
	$pdf->Cell(15, 6, "PV: ", 0, 0, 'R');
	$pdf->SetFont('arial','B','10');
	$pdf->Cell(20, 6, $PDF['CABECALHO']['NUMPED'], 0, 1, 'L');


	$pdf->Cell(15, 15, "", 0, 0);
	$pdf->Code39(($pos_x +15),($pos_y +9),$valueI['CODPROD'],1.2,7);

}


//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
redireciona($end_final);
?>