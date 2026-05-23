<?php 
session_start();
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
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
$por_pagina = 15;
if ($debug) varDump2('por_pagina: '.$por_pagina); 

if (!isset($_GET['CODPROD']) || empty($_GET['CODPROD'])) {
	exibeMensagem("CODPROD não identificado!");
	die();
}

$estoque = buscaDetalhesEstoqueProduto($_GET['CODPROD']);
if ($debug) varDump2($estoque); 

$extrato = buscaExtrato($_GET['CODPROD']);
if ($debug) varDump2($extrato);


$PAGINA = [];
if ($extrato) {
	foreach ($extrato as $key => $value) {
		// varDump2($key % $por_pagina);
		if (($key % $por_pagina) == 0) {
			$num_pagina++;
		}
		if (!isset($PAGINA[$num_pagina])) {
			$PAGINA[$num_pagina] = [];
		}
		$value['key'] = $key;
		array_push($PAGINA[$num_pagina], $value);
	}
}
if (empty($PAGINA)) {
	$PAGINA[0] = []; 
}
if ($debug) varDump2($PAGINA);

$alturaLinha = 6;


//ORIENTAÇÃO DA PÁGINA
// P: PORTRAIT  = RETRATO (30 registro por)
// L: LANDSCAPE = PAISAGEM  (20 registros por pagina)
$orientation = 'L';


//PREPARA PARA GERAR O PDF
$pdf = new FPDF();
$pdf = new FPDF($orientation,'mm','A4');

foreach ($PAGINA as $key1 => $paginaAtual) {

	//EXIBE OS REGISTROS
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetMargins(10,6,1);
	$pdf->SetAutoPageBreak(false,0);

	$pdf->Image('../../dist/img/logo_header.png', 245, 10, 40, 10);

	$pdf->SetXY(10, 10);

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetTextColor($r_danger,$g_danger,$b_danger);
	$pdf->SetFont($font_arial,$style_b,$tam_20);
	$pdf->Cell(275, ($alturaLinha*2), converterUTF8("EXTRATO DE PRODUTO") , 0, 1, 'L');

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(275, $alturaLinha, converterUTF8("Informações do produto") , 'B', 1, 'L');

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(30, $alturaLinha, "Numoriginal" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "Codprod" , 'B', 0, 'C', 1);
	$pdf->Cell(125, $alturaLinha, converterUTF8("Descrição") , 'B', 0, 'C', 1);
	$pdf->Cell(60, $alturaLinha, "Marca" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, converterUTF8("Locação") , 'B', 1, 'C', 1);

	$pdf->SetFont($font_helvetica,$style_n,$tam_12);
	$pdf->SetFillColor($r_white,$g_white,$b_white);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(30, $alturaLinha, $estoque['NUMORIGINAL'] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, 'W'.$_GET['CODPROD'].'-'.$estoque['DV'] , 'B', 0, 'C', 1);
	$pdf->Cell(125, $alturaLinha, $estoque['DESCRICAO'] , 'B', 0, 'C', 1);
	$pdf->Cell(60, $alturaLinha, $estoque['MARCA'] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $estoque['LOCACAO'] , 'B', 1, 'C', 1);
	

	$pdf->Cell(275, ($alturaLinha/2), "" , 0, 1);

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(275, $alturaLinha, converterUTF8("Informações de Estoque") , 'B', 1, 'L');

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(30, $alturaLinha, "Estoque" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "Reservado" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "Bloqueada" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, "Avariado" , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, converterUTF8("Disponível") , 'B', 0, 'C', 1);
	$pdf->Cell(125, $alturaLinha, "Motivo Bloqueio" , 'B', 1, 'C', 1);

	$pdf->SetFont($font_helvetica,$style_n,$tam_8);
	$pdf->SetFillColor($r_white,$g_white,$b_white);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(30, $alturaLinha, $estoque["QTESTGER"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $estoque["QTRESERV"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $estoque["QTBLOQUEADA"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $estoque["QTAVARIA"] , 'B', 0, 'C', 1);
	$pdf->Cell(30, $alturaLinha, $estoque["QTDISPONIVEL"] , 'B', 0, 'C', 1);
	$pdf->Cell(125, $alturaLinha, ((empty($estoque["MOTIVOBLOQ"]))?'-':$estoque["MOTIVOBLOQ"]) , 'B', 1, 'C', 1);

	$pdf->Cell(275, ($alturaLinha/2), "" , 0, 1);



	$pendentes = buscaPedidosPendentesCODPROD($_GET['CODPROD']);
	if ($pendentes) {

		//MONTA O CABEÇALHO DO ORCAMENTO
		$pdf->SetFont($font_arial,$style_b,$tam_12);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->Cell(275, $alturaLinha, converterUTF8("Informações do Estoque Reservado") , 'B', 1, 'L');

		
		$pdf->SetFont($font_arial,$style_b,$tam_9);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->SetFillColor($r_light,$g_light,$b_light);
		$pdf->Cell(30, $alturaLinha, "Data" , 'B', 0, 'L', 1);
		$pdf->Cell(85, $alturaLinha, "Cliente" , 'B', 0, 'L', 1);
		$pdf->Cell(20, $alturaLinha, "Pedido" , 'B', 0, 'C', 1);
		$pdf->Cell(30, $alturaLinha, "Posição" , 'B', 0, 'C', 1);
		$pdf->Cell(30, $alturaLinha, "Vendedor" , 'B', 0, 'L', 1);
		$pdf->Cell(20, $alturaLinha, "Qtde." , 'B', 0, 'C', 1);
		$pdf->Cell(20, $alturaLinha, "P. Venda" , 'B', 1, 'C', 1);

		$pdf->SetFont($font_helvetica,$style_n,$tam_8);
		$pdf->SetFillColor($r_white,$g_white,$b_white);
		$pdf->SetTextColor($r_black,$g_black,$b_black);

		foreach ($pendentes as $key => $valuePend) {
			$pdf->Cell(30, $alturaLinha, formataDataOracletoBr($valuePend["DATA"]) , 'B', 0, 'L', 1);
			$pdf->Cell(85, $alturaLinha, $valuePend["CLIENTE"] , 'B', 0, 'L', 1);
			$pdf->Cell(20, $alturaLinha, $valuePend["NUMPED"] , 'B', 0, 'C', 1);
			$pdf->Cell(30, $alturaLinha, $valuePend["POSICAO"] , 'B', 0, 'C', 1);
			$pdf->Cell(30, $alturaLinha, $valuePend["VENDEDOR"] , 'B', 0, 'L', 1);
			$pdf->Cell(20, $alturaLinha, intval($valuePend["QT"]) , 'B', 0, 'C', 1);
			$pdf->Cell(20, $alturaLinha, "R$ ".moeda($valuePend["PVENDA"]) , 'B', 1, 'C', 1);
		}

		$pdf->Cell(275, ($alturaLinha/2), "" , 0, 1);
	}
		

	
	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_12);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(275, $alturaLinha, converterUTF8("Informações de movimentação") , 'B', 1, 'L');


	$pdf->SetFont($font_arial,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(30, $alturaLinha, converterUTF8("Data Mov") , 'B', 0, 'L', 1);
	$pdf->Cell(30, $alturaLinha, converterUTF8("Operação") , 'B', 0, 'L', 1);
	$pdf->Cell(14, $alturaLinha, converterUTF8("Tr. Entr.") , 'B', 0, 'L', 1);
	$pdf->Cell(16, $alturaLinha, converterUTF8("Tr. Venda") , 'B', 0, 'L', 1);
	$pdf->Cell(85, $alturaLinha, converterUTF8("Cliente/Fornec") , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, converterUTF8("Vendedor") , 'B', 0, 'L', 1);
	$pdf->Cell(20, $alturaLinha, converterUTF8("Nº Doc.") , 'B', 0, 'R', 1);
	$pdf->Cell(20, $alturaLinha, converterUTF8("Qt.Entr.") , 'B', 0, 'R', 1);
	$pdf->Cell(20, $alturaLinha, converterUTF8("Qt.Saída") , 'B', 0, 'R', 1);
	$pdf->Cell(20, $alturaLinha, converterUTF8("SaldoEst.") , 'B', 1, 'R', 1);


	$pdf->SetFont($font_helvetica,$style_n,$tam_8);
	$pdf->SetFillColor($r_white,$g_white,$b_white);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	if (count($paginaAtual) > 0) {
		foreach ($paginaAtual as $key2 => $item) {
			// varDump2($item); die();

			$QTSALDO += ($item['QTENTRADA'] + $item['QTSAIDA']);

			// EXIBE OS ITENS
			$pdf->Cell(30, $alturaLinha, (!is_null($item["DTMOVLOG"]))?formataDataOracletoBr($item["DTMOVLOG"]):'' , 'B', 0, 'L');
			$pdf->Cell(30, $alturaLinha, (!is_null($item["CODOPER"]))?$item["CODOPER"]:'' , 'B', 0, 'L');
			$pdf->Cell(14, $alturaLinha, (!is_null($item["NUMTRANSENT"]))?$item["NUMTRANSENT"]:'' , 'B', 0, 'L');
			$pdf->Cell(16, $alturaLinha, (!is_null($item["NUMTRANSVENDA"]))?$item["NUMTRANSVENDA"]:'' , 'B', 0, 'L');
			$pdf->Cell(85, $alturaLinha, substr(((!is_null($item["CLIENTE_FORNEC"]))?substr($item["CLIENTE_FORNEC"], 0, 60):''), 0, 45) , 'B', 0, 'L');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["VENDEDOR"]))?$item["VENDEDOR"]:'' , 'B', 0, 'L');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["NUMNOTA"]))?$item["NUMNOTA"]:'' , 'B', 0, 'R');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["QTENTRADA"]))?$item["QTENTRADA"]:'' , 'B', 0, 'R');
			$pdf->Cell(20, $alturaLinha, (!is_null($item["QTSAIDA"]))?$item["QTSAIDA"]:'' , 'B', 0, 'R');
			$pdf->Cell(20, $alturaLinha, (!is_null($QTSALDO))?$QTSALDO:'' , 'B', 1, 'R');
		}
	} else {
			$pdf->Cell(275, $alturaLinha, converterUTF8("Nenhuma movimentação registrada para este produto") , 'B', 1, 'C');
	}
	
	$pdf->Cell(275, ($alturaLinha*2), converterUTF8("Página ".$key1." de ".count($PAGINA)) , 0, 1, 'R');
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