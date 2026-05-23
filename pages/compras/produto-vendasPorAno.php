<?php 
session_start();
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL ^ E_NOTICE);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set("America/Manaus");
require_once("../../pages/conf/define.php");
require_once("../../pages/conf/functions.php");
require_once("../../pages/conf/conectaOracle.php");
require_once("../../pages/logistica/function.php");

require_once("../../plugins/fpdf/stylesheet.php");
require_once("../../plugins/fpdf/fpdf.php");
require_once("../../plugins/fpdf/code39.php");

// $debug = true;

if ($debug) varDump2($_GET);

//NUMERO DE RESULTADOS POR PÁGINA
$por_pagina = 17;
if ($debug) varDump2('por_pagina: '.$por_pagina); 

$estoque = buscaDetalhesEstoqueProduto($_GET['CODPROD']);
if ($debug) varDump2($estoque); 

$extrato = buscaExtrato($_GET['CODPROD']);
if ($debug) varDump2($extrato);


$PAGINA = array();
if ($extrato) {
	foreach ($extrato as $key => $value) {
		// varDump2($key % $por_pagina);
		if (($key % $por_pagina) == 0) {
			$num_pagina++;
		}
		if (!isset($PAGINA[$num_pagina])) {
			$PAGINA[$num_pagina] = array();
		}
		$value['key'] = $key;
		array_push($PAGINA[$num_pagina], $value);
	}
}
if ($debug) varDump2($PAGINA);

$alturaLinha = 7;


//ORIENTAÇÃO DA PÁGINA
// P: PORTRAIT  = RETRATO (30 registro por)
// L: LANDSCAPE = PAISAGEM  (20 registros por pagina)
$orientation = 'L';


//PREPARA PARA GERAR O PDF
$pdf = new FPDF();
$pdf = new FPDF($orientation,'mm','A4');

if (!$PAGINA) {

	//EXIBE OS REGISTROS
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetMargins(10,6,1);
	$pdf->SetAutoPageBreak(false,0);

	$pdf->Image('../../dist/img/logo_header.png', 225, 8, 60, 15);

	$pdf->SetXY(10, 10);

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFont($font_arial,$style_b,$tam_20);
	$pdf->Cell(275, $alturaLinha, "EXTRATO DE PRODUTO" , 0, 1, 'C');
	$pdf->SetFont($font_arial,$style_n,$tam_12);
	$pdf->Cell(25, $alturaLinha, "Tipo:" , 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->Cell(250, $alturaLinha, "Analítico pelo histórico" , 0, 1, 'L');
	$pdf->SetFont($font_arial,$style_n,$tam_12);
	$pdf->Cell(25, $alturaLinha, "Período:" , 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->Cell(250, $alturaLinha, "Todo o histórico de movimentações" , 0, 1, 'L');
	$pdf->SetFont($font_arial,$style_n,$tam_12);
	$pdf->Cell(25, $alturaLinha, "Produto:" , 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->Cell(90, $alturaLinha, 'W'.$estoque['CODPROD'].'-'.$estoque['DV'].'   '.$estoque['DESCRICAO'] , 0, 0, 'L');
	$pdf->SetFont($font_arial,$style_n,$tam_12);
	$pdf->Cell(20, $alturaLinha, "Marca:" , 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->Cell(50, $alturaLinha, (!is_null($estoque['MARCA']))?$estoque['MARCA']:"" , 0, 0, 'L');
	$pdf->SetFont($font_arial,$style_n,$tam_12);
	$pdf->Cell(25, $alturaLinha, "Numoriginal:" , 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->Cell(25, $alturaLinha, (!is_null($estoque['NUMORIGINAL']))?$estoque['NUMORIGINAL']:"" , 0, 0, 'L');
	$pdf->SetFont($font_arial,$style_n,$tam_12);
	$pdf->Cell(20, $alturaLinha, "Local:" , 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->Cell(20, $alturaLinha, (!is_null($estoque['LOCACAO']))?$estoque['LOCACAO']:"" , 0, 1, 'L');
	$pdf->Cell(275, $alturaLinha, "" , 0, 1, 'L');

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(30, $alturaLinha, "QT DISP" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "QT EST" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "QT RESERV" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "QT AVARIA" , 'B', 0, 'C', 1);
	$pdf->Cell(35, $alturaLinha, "QT BLOQUEADA" , 'B', 0, 'C', 1);
	$pdf->Cell(120, $alturaLinha, "MOT BLOQUEIO" , 'B', 1, 'C', 1);

	$pdf->SetFont($font_helvetica,$style_n,$tam_8);
	$pdf->SetFillColor($r_white,$g_white,$b_white);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(30, $alturaLinha, $estoque["QTDISPONIVEL"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $estoque["QTESTGER"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $estoque["QTRESERV"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $estoque["QTINDENIZ"] , 'B', 0, 'C', 1);
	$pdf->Cell(35, $alturaLinha, $estoque["QTBLOQUEADA"] , 'B', 0, 'C', 1);
	$pdf->Cell(120, $alturaLinha, ((empty($estoque["MOTIVOBLOQ"]))?'-':$estoque["MOTIVOBLOQ"]) , 'B', 1, 'C', 1);

	$pdf->Cell(120, $alturaLinha, "" , 0, 1);
	
	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(30, $alturaLinha, "Data Mov" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "Operação" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "Tr. Entr." , 'B', 0, 'R', 1);
	$pdf->Cell(20, $alturaLinha, "Tr. Venda" , 'B', 0, 'R', 1);
	$pdf->Cell(85, $alturaLinha, "Cliente/Fornec" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "Vendedor" , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, "Nº Doc." , 'B', 0, 'R', 1);
	$pdf->Cell(20, $alturaLinha, "Qt.Entr." , 'B', 0, 'R', 1);
	$pdf->Cell(20, $alturaLinha, "Qt.Saída" , 'B', 0, 'R', 1);
	$pdf->Cell(20, $alturaLinha, "SaldoEst." , 'B', 1, 'R', 1);

	$pdf->SetFont($font_helvetica,$style_n,$tam_8);
	$pdf->SetFillColor($r_white,$g_white,$b_white);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(280, $alturaLinha, "Nenhuma movimentação registrada para o produto" , 'B', 1, 'C', 1);

} else {

	foreach ($PAGINA as $key1 => $paginaAtual) {

		//EXIBE OS REGISTROS
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetMargins(10,6,1);
		$pdf->SetAutoPageBreak(false,0);

		$pdf->Image('../../dist/img/logo_header.png', 225, 8, 60, 15);

		$pdf->SetXY(10, 10);

		//MONTA O CABEÇALHO DO ORCAMENTO
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->SetFont($font_arial,$style_b,$tam_20);
		$pdf->Cell(275, $alturaLinha, "EXTRATO DE PRODUTO" , 0, 1, 'C');
		$pdf->SetFont($font_arial,$style_n,$tam_12);
		$pdf->Cell(25, $alturaLinha, "Tipo:" , 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_b,$tam_12);
		$pdf->Cell(250, $alturaLinha, "Analítico pelo histórico" , 0, 1, 'L');
		$pdf->SetFont($font_arial,$style_n,$tam_12);
		$pdf->Cell(25, $alturaLinha, "Período:" , 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_b,$tam_12);
		$pdf->Cell(250, $alturaLinha, "Todo o histórico de movimentações" , 0, 1, 'L');
		$pdf->SetFont($font_arial,$style_n,$tam_12);
		$pdf->Cell(25, $alturaLinha, "Produto:" , 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_b,$tam_12);
		$pdf->Cell(90, $alturaLinha, 'W'.$extrato[0]['CODPROD'].' - '.$extrato[0]['DV'].'   '.$extrato[0]['DESCRICAO'] , 0, 0, 'L');
		$pdf->SetFont($font_arial,$style_n,$tam_12);
		$pdf->Cell(20, $alturaLinha, "Marca:" , 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_b,$tam_12);
		$pdf->Cell(50, $alturaLinha, (!is_null($extrato[0]['MARCA']))?$extrato[0]['MARCA']:"" , 0, 0, 'L');
		$pdf->SetFont($font_arial,$style_n,$tam_12);
		$pdf->Cell(25, $alturaLinha, "Numoriginal:" , 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_b,$tam_12);
		$pdf->Cell(25, $alturaLinha, (!is_null($extrato[0]['NUMORIGINAL']))?$extrato[0]['NUMORIGINAL']:"" , 0, 0, 'L');
		$pdf->SetFont($font_arial,$style_n,$tam_12);
		$pdf->Cell(20, $alturaLinha, "Local:" , 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_b,$tam_12);
		$pdf->Cell(20, $alturaLinha, (!is_null($extrato[0]['LOCACAO']))?$extrato[0]['LOCACAO']:"" , 0, 1, 'L');
		$pdf->Cell(275, $alturaLinha, "" , 0, 1, 'L');

		
		//MONTA O CABEÇALHO DO ORCAMENTO
		$pdf->SetFont($font_arial,$style_b,$tam_9);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->SetFillColor($r_light,$g_light,$b_light);
		$pdf->Cell(30, $alturaLinha, "QT DISP" , 'B', 0, 'C', 1);
		$pdf->Cell(30, $alturaLinha, "QT EST" , 'B', 0, 'C', 1);
		$pdf->Cell(30, $alturaLinha, "QT RESERV" , 'B', 0, 'C', 1);
		$pdf->Cell(30, $alturaLinha, "QT AVARIA" , 'B', 0, 'C', 1);
		$pdf->Cell(35, $alturaLinha, "QT BLOQUEADA" , 'B', 0, 'C', 1);
		$pdf->Cell(120, $alturaLinha, "MOT BLOQUEIO" , 'B', 1, 'C', 1);

		$pdf->SetFont($font_helvetica,$style_n,$tam_8);
		$pdf->SetFillColor($r_white,$g_white,$b_white);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->Cell(30, $alturaLinha, $estoque["QTDISPONIVEL"] , 'B', 0, 'C', 1);
		$pdf->Cell(30, $alturaLinha, $estoque["QTESTGER"] , 'B', 0, 'C', 1);
		$pdf->Cell(30, $alturaLinha, $estoque["QTRESERV"] , 'B', 0, 'C', 1);
		$pdf->Cell(30, $alturaLinha, $estoque["QTINDENIZ"] , 'B', 0, 'C', 1);
		$pdf->Cell(35, $alturaLinha, $estoque["QTBLOQUEADA"] , 'B', 0, 'C', 1);
		$pdf->Cell(120, $alturaLinha, ((empty($estoque["MOTIVOBLOQ"]))?'-':$estoque["MOTIVOBLOQ"]) , 'B', 1, 'C', 1);

		$pdf->Cell(120, $alturaLinha, "" , 0, 1);
		
		//MONTA O CABEÇALHO DO ORCAMENTO
		$pdf->SetFont($font_arial,$style_b,$tam_9);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->SetFillColor($r_light,$g_light,$b_light);
		$pdf->Cell(30, $alturaLinha, "Data Mov" , 'B', 0, 'L', 1);
		$pdf->Cell(20, $alturaLinha, "Operação" , 'B', 0, 'L', 1);
		$pdf->Cell(20, $alturaLinha, "Tr. Entr." , 'B', 0, 'R', 1);
		$pdf->Cell(20, $alturaLinha, "Tr. Venda" , 'B', 0, 'R', 1);
		$pdf->Cell(85, $alturaLinha, "Cliente/Fornec" , 'B', 0, 'L', 1);
		$pdf->Cell(20, $alturaLinha, "Vendedor" , 'B', 0, 'L', 1);
		$pdf->Cell(20, $alturaLinha, "Nº Doc." , 'B', 0, 'R', 1);
		$pdf->Cell(20, $alturaLinha, "Qt.Entr." , 'B', 0, 'R', 1);
		$pdf->Cell(20, $alturaLinha, "Qt.Saída" , 'B', 0, 'R', 1);
		$pdf->Cell(20, $alturaLinha, "SaldoEst." , 'B', 1, 'R', 1);


		
		foreach ($paginaAtual as $key2 => $item) {
			// varDump2($item); die();

			$QTSALDO += ($item['QTENTRADA'] + $item['QTSAIDA']);

			// EXIBE OS ITENS
			$pdf->SetFont($font_helvetica,$style_n,$tam_8);
			$pdf->SetFillColor($r_white,$g_white,$b_white);
			$pdf->SetTextColor($r_black,$g_black,$b_black);

			$pdf->Cell(30, $alturaLinha, (!is_null($item["DTMOVLOG"]))?formataDataOracletoBr($item["DTMOVLOG"]):'' , 'B', 0, 'L');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["CODOPER"]))?$item["CODOPER"]:'' , 'B', 0, 'L');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["NUMTRANSENT"]))?$item["NUMTRANSENT"]:'' , 'B', 0, 'R');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["NUMTRANSVENDA"]))?$item["NUMTRANSVENDA"]:'' , 'B', 0, 'R');
			$pdf->Cell(85, $alturaLinha, (!is_null($item["CLIENTE_FORNEC"]))?substr($item["CLIENTE_FORNEC"], 0, 60):'' , 'B', 0, 'L');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["VENDEDOR"]))?$item["VENDEDOR"]:'' , 'B', 0, 'L');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["NUMNOTA"]))?$item["NUMNOTA"]:'' , 'B', 0, 'R');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["QTENTRADA"]))?$item["QTENTRADA"]:'' , 'B', 0, 'R');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["QTSAIDA"]))?$item["QTSAIDA"]:'' , 'B', 0, 'R');
			$pdf->Cell(20, $alturaLinha, (!is_null($QTSALDO))?$QTSALDO:'' , 'B', 1, 'R');
		}
		
		$pdf->Cell(275, ($alturaLinha*2), "Página ".$key1." de ".count($PAGINA) , 0, 1, 'R');
	}
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