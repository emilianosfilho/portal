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

// varDump2($_SESSION);

//NUMERO DE RESULTADOS POR PÁGINA
$por_pagina = 20;

//ALTURA PADRÃO DA LINHA
$alturaLinha = 7;

$PAGINA = array();
$num_pagina = -1;

if ($LISTAPRODUTOS = buscaProdutosNF($_GET)) {
	// code...

	foreach ($LISTAPRODUTOS as $key => $value) {
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
	// varDump2($PAGINA);

	if ($_GET['NUMNOTA'] <> "") {
		$CAB = buscaCabecalhoNFEntrada($_GET);
	} else {
		$CAB = false;
	}
	// varDump2($CAB);	

	//TIPO DO PDF GERADO
	// I: send the file inline to the browser. The PDF viewer is used if available.
	// D: send to the browser and force a file download with the name given by name.
	// F: save to a local file with the name given by name (may include a path).
	// S: return the document as a string.
	$output = "I";

	//ORIENTAÇÃO DA PÁGINA
	// P: PORTRAIT  = RETRATO (190)
	// L: LANDSCAPE = PAISAGEM (280)
	$orientation = 'L';


	$pdf = new FPDF();
	$pdf = new FPDF($orientation,'mm','A4');

	foreach ($PAGINA as $key1 => $paginaAtual) {

		//EXIBE OS REGISTROS
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetMargins(10,6,1);
		$pdf->SetAutoPageBreak(false,0);


		$pdf->SetXY(10, 10);
		$pdf->Cell(60, 13, "" , 0, 0);
		$pdf->SetFont($font_arial,$style_b,$tam_16);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->Cell(220, 13, mb_convert_encoding("ESPELHO DE NOTA FISCAL", 'Windows-1252', 'UTF-8') , 0, 1);
		
		// PULA LINHA
		$pdf->Cell(280, $alturaLinha, '' , 0, 1);

		$pdf->Image('../../dist/img/logo_header.png', 10, 10, 50, 13);

		//MONTA O CABEÇALHO DO ORCAMENTO
		$pdf->SetFont($font_arial,$style_b,$tam_10);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->SetFillColor($r_light,$g_light,$b_light);
		$pdf->Cell(160, $alturaLinha, mb_convert_encoding("FORNECEDOR", 'Windows-1252', 'UTF-8') , 'B', 0, 'L', 1);
		$pdf->Cell(40, $alturaLinha, mb_convert_encoding("ENTRADA", 'Windows-1252', 'UTF-8') , 'B', 0, 'C', 1);
		$pdf->Cell(40, $alturaLinha, mb_convert_encoding("NUM NOTA", 'Windows-1252', 'UTF-8') , 'B', 0, 'C', 1);
		$pdf->Cell(40, $alturaLinha, mb_convert_encoding("TRANSAÇÃO", 'Windows-1252', 'UTF-8') , 'B', 1, 'C', 1);

		$pdf->SetFont($font_helvetica,$style_n,$tam_9);
		$pdf->SetFillColor($r_white,$g_white,$b_white);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->Cell(160, $alturaLinha, $CAB["CODFORNEC"].'- '.$CAB["FORNECEDOR"] , 'B', 0, 'L');
		$pdf->Cell(40, $alturaLinha, formataDataOracletoBr($CAB["DTMOV"]) , 'B', 0, 'C');
		$pdf->Cell(40, $alturaLinha, $CAB["NUMNOTA"] , 'B', 0, 'C');
		$pdf->Cell(40, $alturaLinha, $CAB["NUMTRANSENT"] , 'B', 1, 'C');

		// PULA LINHA
		$pdf->Cell(280, $alturaLinha, '' , 0, 1);
		
		//MONTA O CABEÇALHO DO ORCAMENTO
		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->SetFillColor($r_light,$g_light,$b_light);
		$pdf->Cell(10, $alturaLinha, mb_convert_encoding("ORD", 'Windows-1252', 'UTF-8') , 'B', 0, 'L', 1);
		$pdf->Cell(35, $alturaLinha, mb_convert_encoding("CODFAB", 'Windows-1252', 'UTF-8') , 'B', 0, 'L', 1);
		$pdf->Cell(25, $alturaLinha, mb_convert_encoding("CODPROD", 'Windows-1252', 'UTF-8') , 'B', 0, 'L', 1);
		$pdf->Cell(30, $alturaLinha, mb_convert_encoding("NUMORIGINAL", 'Windows-1252', 'UTF-8') , 'B', 0, 'L', 1);
		$pdf->Cell(70, $alturaLinha, mb_convert_encoding("DESCRIÇÃO", 'Windows-1252', 'UTF-8') , 'B', 0, 'L', 1);
		$pdf->Cell(35, $alturaLinha, mb_convert_encoding("MARCA", 'Windows-1252', 'UTF-8') , 'B', 0, 'L', 1);
		$pdf->Cell(25, $alturaLinha, mb_convert_encoding("LOCAÇÃO", 'Windows-1252', 'UTF-8') , 'B', 0, 'C', 1);
		$pdf->Cell(25, $alturaLinha, mb_convert_encoding("ENTRADA", 'Windows-1252', 'UTF-8') , 'B', 0, 'C', 1);
		$pdf->Cell(25, $alturaLinha, mb_convert_encoding("SALDO", 'Windows-1252', 'UTF-8') , 'B', 1, 'C', 1);



		foreach ($paginaAtual as $key2 => $item) {
			// varDump2($item); die();
			$item["NUMORIGINAL"] 	= (($item["NUMORIGINAL"]<>"")?$item["NUMORIGINAL"]:' ');
			$item["DESCRICAO"] 		= (($item["DESCRICAO"]<>"")?$item["DESCRICAO"]:' ');
			$item["MARCA"] 				= (($item["MARCA"]<>"")?$item["MARCA"]:' ');
			$item["LOCACAO"] 			= (($item["LOCACAO"]<>"")?$item["LOCACAO"]:' ');

			// EXIBE OS ITENS
			$pdf->SetFont($font_helvetica,$style_n,$tam_9);
			$pdf->SetFillColor($r_white,$g_white,$b_white);
			$pdf->SetTextColor($r_black,$g_black,$b_black);
			$pdf->Cell(10, $alturaLinha, mb_convert_encoding(($key2+1), 'Windows-1252', 'UTF-8') , 'B', 0, 'L');
			$pdf->Cell(35, $alturaLinha, mb_convert_encoding((is_null($item["CODFAB"]))?" ":$item["CODFAB"], 'Windows-1252', 'UTF-8') , 'B', 0, 'L');
			$pdf->Cell(25, $alturaLinha, mb_convert_encoding((is_null($item["CODPROD"]))?" ":$item["CODPROD"], 'Windows-1252', 'UTF-8') , 'B', 0, 'L');
			$pdf->Cell(30, $alturaLinha, mb_convert_encoding($item["NUMORIGINAL"], 'Windows-1252', 'UTF-8') , 'B', 0, 'L');
			$pdf->Cell(70, $alturaLinha, mb_convert_encoding($item["DESCRICAO"], 'Windows-1252', 'UTF-8') , 'B', 0, 'L');
			$pdf->Cell(35, $alturaLinha, mb_convert_encoding($item["MARCA"], 'Windows-1252', 'UTF-8') , 'B', 0, 'L');
			$pdf->Cell(25, $alturaLinha, mb_convert_encoding((is_null($item["LOCACAO"]))?" ":$item["LOCACAO"], 'Windows-1252', 'UTF-8') , 'B', 0, 'C');
			$pdf->Cell(25, $alturaLinha, mb_convert_encoding($item["QTPEDIDA"], 'Windows-1252', 'UTF-8') , 'B', 0, 'C');
			$pdf->Cell(25, $alturaLinha, mb_convert_encoding($item["QTSALDO"], 'Windows-1252', 'UTF-8') , 'B', 1, 'C');
			
		}

	}


	//SAIDA DO PDF
	$pdf->Output($output);

} else {
	exibeMensagem("Nenhum produto encontrado");
	// fechaAba();
}
	
?>
