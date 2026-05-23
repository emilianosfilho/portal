<?php 
session_start();

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set('America/Manaus');
clearstatcache();
require "../../pages/conf/define.php";
require "../../pages/conf/functions.php";
require "../../pages/conf/conectaOracle.php";
require "../../pages/logistica/function.php";

require_once("../../plugins/fpdf/stylesheet.php");
require_once("../../plugins/fpdf/fpdf.php");
require_once("../../plugins/fpdf/code39.php");

$debug = false;
// $debug = true;

if ($debug) varDump2($_GET);

//NUMERO DE RESULTADOS POR PÁGINA
$por_pagina = 16;
if ($debug) varDump2('por_pagina: '.$por_pagina); 

$PAGINA = array();
$num_pagina = -1;


if ($debug) varDump2($_SESSION['produtos']); 

if (is_array($_SESSION['produtos']) && !is_null($_SESSION['produtos'])) {
	$ETIQUETAS = array();
	for ($j=1; $j < $_GET['POSICAOINI']; $j++) { 
		array_push($ETIQUETAS, null);
	}
	foreach ($_SESSION['produtos'] as $keytmp => $valuetmp) {
		$valuetmp['QTETIQUETA'] = ((!isset($valuetmp['QTETIQUETA'])) ? $valuetmp['QTETIQUETA'] = 1:$valuetmp['QTETIQUETA']);

		for ($i=1; $i <= $valuetmp['QTETIQUETA']; $i++) { 
			if ($valuetmp['CODPROD'] <> "") {
				array_push($ETIQUETAS, $valuetmp);
			}
		}
	}
	if ($debug) varDump2("ETIQUETAS"); 
	if ($debug) varDump2($ETIQUETAS); 

	foreach ($ETIQUETAS as $key => $value) {
		// varDump2($key % $por_pagina);
		if (($key % $por_pagina) == 0) {
			$num_pagina++;
		}
		if (!isset($PAGINA[$num_pagina])) {
			$PAGINA[$num_pagina] = array();
		}
		array_push($PAGINA[$num_pagina], $value);
	}
} else {
	exibeMensagem("Nenhuma etiqueta identificada");
	fechaAba();
}

if ($debug) varDump2("PAGINA"); 
if ($debug) varDump2($PAGINA); 

//TIPO DO PDF GERADO
//F-> SALVA NO ENDEREÇO ESPECIFICADO NA VAR END_FINAL
$tipo_pdf = "F";

// varDump2($_SESSION['produtos']);
// varDump2($ETIQUETAS);

$ETIQ_ALTURA = floatval(34.2);
$ETIQ_LARGURA = floatval(99);

//PREPARA PARA GERAR O PDF

$pdf = new PDF_Code39();
$pdf = new PDF_Code39('P','mm','A4');

foreach ($PAGINA as $keyP => $valueP) {
	//EXIBE OS REGISTROS
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetMargins(7,6,1);
	$pdf->SetAutoPageBreak(false,0);

	// varDump2($valueP);

	foreach ($valueP as $ETIQ_ATUAL_PAG => $valueI) {

		if ($ETIQ_ATUAL_PAG < 8) {
			$pos_x = (4.5);
			$pos_y = (13.5+(($ETIQ_ATUAL_PAG)*$ETIQ_ALTURA));
		} else {
			$pos_x = (106);
			$pos_y = (13.5+(($ETIQ_ATUAL_PAG-8)*$ETIQ_ALTURA));
		}

		if (is_null($valueI)) {

			$pdf->SetXY(($pos_x+1), ($pos_y +3));
			$pdf->Cell(93, 21, "", 0, 1);

		
		} else {
			
			$pdf->Image('../../dist/img/logo_header_vertical.png', ($pos_x +3), ($pos_y +3), 10, 27);

			$pdf->Cell(1, 3, "", 0, 1);

			$pdf->SetXY(($pos_x+1), ($pos_y +3));

			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(13, 6, "", 0, 0);
			$pdf->SetFont($font_helvetica, $style_b, $tam_22);
			$pdf->Cell(40, 6, "W".$valueI['CODPROD']."-".$valueI['DV'], 0, 0, 'L');
			$pdf->SetFont($font_helvetica, $style_n, $tam_8);
			$pdf->Cell(12, 6, "LOCAL: ", 0, 0, 'R');
			$pdf->SetFont($font_helvetica, $style_b, $tam_16);
			$pdf->Cell(28, 6, $valueI['LOCACAO'], 0, 1, 'L');

			$pdf->SetXY(($pos_x+2.5), ($pos_y +9));
			$pdf->Cell(51.5, 6, "", 0, 1);

			$pdf->Cell(15, 15, "", 0, 0);
			if ($valueI['CODPROD'] <> "") {
				$pdf->Code39(($pos_x +15),($pos_y +10),$valueI['CODPROD'],1,10);
			}
			
			$pdf->SetFont($font_arial,$style_n,$tam_14);
			$pdf->Cell(1, 15, "", 0, 1);
			$pdf->SetXY(($pos_x+1), ($pos_y +24));
			$pdf->Cell(13, 6, "", 0, 0);
			if ($valueI['DESCRICAO']<> "") {
				$pdf->Cell(80, 6, substr(trim($valueI['DESCRICAO']), 0, 25), 0, 1, 'L');
			}
		}

		// varDump2($valueI);
		
	}

}

//SAIDA DO PDF
// dest  =  Destination where to send the document. It can be one of the following: 
//     I: send the file inline to the browser. The PDF viewer is used if available.
//     D: send to the browser and force a file download with the name given by name.
//     F: save to a local file with the name given by name (may include a path).
//     S: return the document as a string.
$dest = "I";

$name = @date('Ymd_His_')."ETIQUETA.pdf";

// isUTF8
//     Indicates if name is encoded in ISO-8859-1 (false) or UTF-8 (true). Only used for destinations I and D.
//     The default value is false. 
$isUTF8 = true;

if (!$debug) $pdf->Output($dest, $name, $isUTF8);

?>
