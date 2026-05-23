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

//NUMERO DE RESULTADOS POR PÁGINA
$por_pagina = 8;
$PAGINA = array();
$num_pagina = -1;
foreach ($PDF['ITENS'] as $key => $value) {
	// varDump2($key % $por_pagina);
	if (($key % $por_pagina)==0) {
		$num_pagina++;
	}
	if (!isset($PAGINA[$num_pagina])) {
		$PAGINA[$num_pagina] = array();
	}
	array_push($PAGINA[$num_pagina], $value);
}

// varDump2($PAGINA); die();

$ETIQ_ALTURA = floatval(34.5);
$ETIQ_LARGURA = floatval(99);

$pdf = new PDF_Code39();
$pdf = new PDF_Code39('P','mm','A4');


foreach ($PAGINA as $keyP => $valueP) {
	//EXIBE OS REGISTROS
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetMargins(7,7,1);
	$pdf->SetAutoPageBreak(false,0);

	// varDump2($valueP);

	foreach ($valueP as $ETIQ_ATUAL_PAG => $valueI) {
		// varDump2($valueI); die();

		$pos_x = (4.5);
		$pos_y = (13.5+(($ETIQ_ATUAL_PAG)*$ETIQ_ALTURA));

		if (is_null($valueI)) {
			$filename = '../../dist/img/borda_invisivel_etiqueta.png';
			if($debug) varDump2(file_exists($filename));
			$pdf->Image($filename, $pos_x, $pos_y, $ETIQ_LARGURA, $ETIQ_ALTURA);
		} else {

			// $pdf->Image('../../dist/img/borda_etiqueta.png', $pos_x, $pos_y, $ETIQ_LARGURA, $ETIQ_ALTURA);
			$filename = '../../dist/img/logo_header_vertical.png';
			if($debug) varDump2(file_exists($filename));
			$pdf->Image($filename, ($pos_x +3), ($pos_y +3), 8, 27);

			$pdf->Cell(1, 3, "", 0, 1);

			$pdf->SetXY(($pos_x+1), ($pos_y +3));

			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('arial','b','20');
			$pdf->Cell(13, 6, "", 0, 0);
			$pdf->Cell(50.5, 6, "W".$valueI['CODPROD']."-".$valueI['DV'], 0, 0, 'L');
			
			$pdf->SetFont('arial','','10');
			$pdf->Cell(15, 5, "LOCAL: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','10');
			$pdf->Cell(25, 5, ($valueI['LOCACAO'])?trim($valueI['LOCACAO']):'', 0, 0, 'L');

			$pdf->SetFont('arial','','10');
			$pdf->Cell(10, 5, "", 0, 0, 'R');
			$pdf->SetFont('arial','B','12');
			$pdf->Cell(80, 5, trim($valueI['PRODUTO']), 0, 1, 'L');
			
			$pdf->Cell(62, 5, "", 0, 0, 'R');
			$pdf->SetFont('arial','','10');
			$pdf->Cell(15, 5, "QTD: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','10');
			$pdf->Cell(25, 5, intval($valueI['QTFATURADA']), 0, 0, 'L');

			$pdf->SetFont('arial','','10');
			$pdf->Cell(15, 5, "", 0, 0, 'R');
			$pdf->Cell(25, 5, "NUM ORIGINAL: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','14');
			$pdf->Cell(15, 5, trim($valueI['NUMORIGINAL']), 0, 1, 'L');

			$pdf->Cell(62, 5, "", 0, 0, 'R');
			$pdf->SetFont('arial','','10');
			$pdf->Cell(15, 6, "DATA: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','10');
			$pdf->Cell(25, 6, formataDataOracletoBr($PDF['CABECALHO']['DATA']), 0, 0, 'L');

			$pdf->SetFont('arial','','10');
			$pdf->Cell(20, 5, "", 0, 0, 'R');
			$pdf->Cell(25, 5, "LOCAL: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','10');
			$pdf->Cell(15, 5, ($valueI['LOCACAO'])?trim($valueI['LOCACAO']):'', 0, 1, 'L');

			$pdf->Cell(12, 3, "", 0, 1);
			$pdf->Cell(12, 6, "", 0, 0, 'L');
			$pdf->SetFont('arial','b','8');
			$pdf->Cell(50, 6, substr($valueI['PRODUTO'],0,30), 0, 0, 'L');
			$pdf->SetFont('arial','','10');
			$pdf->Cell(15, 5, "ORC.: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','10');
			$pdf->Cell(25, 5, trim($PDF['CABECALHO']['NUMPEDCLI']), 0, 0, 'L');

			$pdf->SetFont('arial','','10');
			$pdf->Cell(15, 5, "", 0, 0, 'R');
			$pdf->Cell(25, 5, "QTD PED.: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','10');
			$pdf->Cell(15, 5, trim($valueI['QTFATURADA']), 0, 1, 'L');


			$pdf->Cell(12, 6, "", 0, 0, 'L');
			$pdf->SetFont('arial','b','8');
			$pdf->Cell(50, 6, $PDF['CABECALHO']['CODCLI'].'- '.substr($PDF['CABECALHO']['CLIENTE'],0,25), 0, 0, 'L');
			$pdf->SetFont('arial','','10');
			$pdf->Cell(15, 6, "PV: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','10');
			$pdf->Cell(25, 6, $PDF['CABECALHO']['NUMPED'], 0, 0, 'L');

			$pdf->SetFont('arial','','10');
			$pdf->Cell(15, 5, "", 0, 0, 'R');
			$pdf->Cell(25, 5, "QTD EST.: ", 0, 0, 'R');
			$pdf->SetFont('arial','B','10');
			$pdf->Cell(15, 5, trim($valueI['SALDO']), 0, 1, 'L');


			$pdf->Cell(15, 15, "", 0, 0);
			$pdf->Code39(($pos_x +15),($pos_y +9),$valueI['CODPROD'],1.2,7);

		}


		// varDump2($valueI);
		
	}

}


//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
redireciona($end_final);
?>