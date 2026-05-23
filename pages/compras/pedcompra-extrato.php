<?php 
session_start();
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL ^ E_NOTICE);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set("America/Manaus");
require_once("../../pages/conf/define.php");
require_once("../../pages/conf/functions.php");
require_once("../../pages/conf/conectaOracle.php");

require_once("../../pages/compras/function.php");

require_once("../../plugins/fpdf/stylesheet.php");
require_once("../../plugins/fpdf/fpdf.php");
require_once("../../plugins/fpdf/code39.php");

// $debug = true;

$dados = $_GET;

if ($debug) varDump2($dados);

//NUMERO DE RESULTADOS POR PÁGINA
$PAGINA = array();
$max_por_pagina = intval(30);

$CAB = buscaPedcompraCabecalho($dados);
if ($debug) varDump2($CAB);

$ITENS = buscaPedcompraItens($dados);
// if ($debug) varDump2($ITENS);

$qtdTotalItens = count($ITENS);

if ($debug) varDump2('max_por_pagina: '.$max_por_pagina); 

if ($debug) varDump2("Total de registros ".count($ITENS));

if ($debug) varDump2("Total de paginas: ".ceil(count($ITENS)/$max_por_pagina));

$num_pagina = 1;
$itemAtual = 0;

foreach ($ITENS as $key => $value) {
	++$itemAtual;
	if ($itemAtual > $max_por_pagina) {
		$num_pagina++;
	}
	if (!isset($PAGINA[$num_pagina])) {
		$PAGINA[$num_pagina] = array();
	}
	$PAGINA[$num_pagina][$key] = $value;
}


if ($debug) varDump2($PAGINA);

$alturaLinha = 7;


//ORIENTAÇÃO DA PÁGINA
// P: PORTRAIT  = RETRATO (30 registro por)
// L: LANDSCAPE = PAISAGEM  (20 registros por pagina)
$orientation = 'P';


//PREPARA PARA GERAR O PDF
$pdf = new FPDF();
$pdf = new FPDF($orientation,'mm','A4');

foreach ($PAGINA as $key1 => $paginaAtual) {

	//EXIBE OS REGISTROS
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetMargins(10,6,1);
	$pdf->SetAutoPageBreak(false,0);

	$pdf->Image('../../dist/img/logo_header.png', 160, 10, 40, 10);

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFont($font_arial,$style_b,$tam_18);
	$pdf->Cell(130, 10, "EXTRATO DE PEDIDO DE COMPRA" , 0, 1, 'L');

	$pdf->Cell(190, $alturaLinha, "" , 0, 1, 'L');
	$pdf->Cell(190, $alturaLinha, "" , 0, 1, 'L');

	
	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(30, $alturaLinha, "NUMPED" , 'B', 0, 'C', 1);
	$pdf->Cell(70, $alturaLinha, "FORNECEDOR" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "CODFILIAL" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "DATA" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "TIPO" , 'B', 1, 'C', 1);

	$pdf->SetFont($font_helvetica,$style_n,$tam_8);
	$pdf->SetFillColor($r_white,$g_white,$b_white);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(30, $alturaLinha, $CAB["NUMPED"] , 'B', 0, 'C', 1);
	$pdf->Cell(70, $alturaLinha, $CAB["CODFORNEC"].'- '.$CAB["FORNECEDOR"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $CAB["CODFILIAL"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, formataDataOracletoBr($CAB["DATA"]) , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $CAB["TIPO"] , 'B', 1, 'C', 1);

	$pdf->Cell(190, $alturaLinha, "" , 0, 1);
	
	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(10, $alturaLinha, "ORD" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "NUMORIGINAL" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "CODPROD" , 'B', 0, 'L', 1);
	$pdf->Cell(50, $alturaLinha, "DESCRICAO" , 'B', 0, 'L', 1);
	$pdf->Cell(30, $alturaLinha, "MARCA" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "Qt Pedido" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "Vl Pedido" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "Sub Total" , 'B', 1, 'L', 1);


	$contador = 1;
	foreach ($paginaAtual as $key2 => $item) {
		// varDump2($item); die();

		$SUBTOTAL += ($item['QTPEDIDA'] + $item['PCOMPRA']);

		// EXIBE OS ITENS
		$pdf->SetFont($font_helvetica,$style_n,$tam_8);
		$pdf->SetFillColor($r_white,$g_white,$b_white);
		$pdf->SetTextColor($r_black,$g_black,$b_black);

		$pdf->Cell(10, $alturaLinha, $contador++ , 'B', 0, 'L');
		$pdf->Cell(20, $alturaLinha, $item['NUMORIGINAL'] , 'B', 0, 'L');
		$pdf->Cell(20, $alturaLinha, $item['CODPROD'] , 'B', 0, 'L');
		$pdf->Cell(50, $alturaLinha, $item['DESCRICAO'] , 'B', 0, 'L');
		$pdf->Cell(30, $alturaLinha, $item['MARCA'] , 'B', 0, 'L');
		$pdf->Cell(20, $alturaLinha, moeda($item['QTPEDIDA']) , 'B', 0, 'L');
		$pdf->Cell(20, $alturaLinha, moeda($item['PCOMPRA']) , 'B', 0, 'L');
		$pdf->Cell(20, $alturaLinha, (!is_null($SUBTOTAL))?$SUBTOTAL:'' , 'B', 1, 'R');
	}
	
	$pdf->Cell(275, $alturaLinha, "Página ".$key1." de ".count($PAGINA) , 0, 1, 'R');
}

//TIPO DO PDF GERADO
// I: send the file inline to the browser. The PDF viewer is used if available.
// D: send to the browser and force a file download with the name given by name.
// F: save to a local file with the name given by name (may include a path).
// S: return the document as a string.
$output = "I";

$name = @date('Ymd_His_')."EXTRATO_".$dados['CODPROD'].".pdf";

// isUTF8
//     Indicates if name is encoded in ISO-8859-1 (false) or UTF-8 (true). Only used for destinations I and D.
//     The default value is false. 
$isUTF8 = true;

if (!$debug) $pdf->Output($output, $name, $isUTF8);

?>