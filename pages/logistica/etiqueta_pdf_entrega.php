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

if (!$dados['NUMNOTA'] || empty($dados['NUMNOTA'])) {
	exibeMensagem("ERRO. NUMNOTA inv�lido");
	fechaAba();
} 

$dadosNota = buscaDadosEtiquetaEntrega($dados['NUMNOTA']);
// varDump2($dadosNota);

//T�TULO DO RELAT�RIO
$titulo = "ENTREGA";

//ENDERE�O ONDE SER� GERADO O PDF
$end_final = str_replace(" ", "_", mb_strtolower($titulo)).".pdf";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

//NUMERO DE RESULTADOS POR P�GINA
$regPorPagina = 30;
$alturaLinhaItem = 6;
$erro = 0;

//LOGO QUE SER� COLOCADO NO RELAT�RIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";

$totalPaginas = (int) $dados['VOLUMES'];

$contPag=0;
$contReg=0;

$contadorGeral = 0;

$pdf = new PDF_Code39('P','mm','A4');

$pdf->SetMargins(10, 10, 10);
$pdf->AliasNbPages();


for ($pagina=1; $pagina <= $totalPaginas; $pagina++) { 
	
	$pdf->AddPage();

	$pdf->SetFont($font_arial,$style_b,24);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(190, 10, "REMETENTE" , 1, 1);

	$pdf->SetFont($font_arial,$style_b,18);
	$pdf->Cell(190, 10, sanitizeOracleString($dadosNota['FIL_RAZAO'], 0, true) , 'LR', 1);
	$pdf->SetFont($font_arial,$style_n,18);
	$pdf->Cell(190, 10, sanitizeOracleString($dadosNota['FIL_ENDERECO'], 0, true).", NR ".sanitizeOracleString($dadosNota['FIL_NUMERO'], 0, true) , 'LR', 1);
	$pdf->Cell(190, 10, sanitizeOracleString($dadosNota['FIL_BAIRRO'], 0, true)." - ".sanitizeOracleString($dadosNota['FIL_CIDADE'], 0, true)." / ".sanitizeOracleString($dadosNota['FIL_UF'], 0, true) , 'LR', 1);
	$pdf->Cell(190, 10, "CEP: ".formataCEP($dadosNota['FIL_CEP']) , 'LR', 1);
	$pdf->Cell(190, 5, "" , 'LBR', 1);

	$pdf->Image('../../dist/img/logo_header.png', 145, 48, 50, 13);

	$pdf->Cell(190, 5, "" , 0, 1);

	$pdf->SetFont($font_arial,$style_b,24);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(190, 10, "DESTINAT�RIO" , 1, 1);

	$pdf->SetFont($font_arial,$style_b,18);
	$pdf->Cell(190, 10, utf8_encode(sanitizeOracleString($dadosNota['CLI_CLIENTE'], 0, true)) , 'LR', 1);
	$pdf->SetFont($font_arial,$style_n,18);
	$pdf->Cell(190, 10, utf8_encode(sanitizeOracleString($dadosNota['CLI_ENDERECO'], 0, true).", NR ".sanitizeOracleString($dadosNota['CLI_NUMERO'], 0, true)) , 'LR', 1);
	$pdf->Cell(190, 10, utf8_encode(sanitizeOracleString($dadosNota['CLI_BAIRRO'], 0, true)." - ".sanitizeOracleString($dadosNota['CLI_CIDADE'], 0, true)." / ".sanitizeOracleString($dadosNota['CLI_UF'], 0, true)) , 'LR', 1);
	$pdf->Cell(190, 15, utf8_encode("CEP: ".formataCEP($dadosNota['CLI_CEP'])."		".sanitizeOracleString($dadosNota['CLI_PONTOREF'], 0, true)) , 'LR', 1);
	$pdf->Code39(130, 110, formataCEP($dadosNota['CLI_CEP']), 1, 12);

	$pdf->SetFont($font_arial,$style_n,18);
	$pdf->Cell(190, 10, "OBS.: ".converterUTF8(mb_strtoupper($dados['OBS'], 'UTF-8')) , 'LR', 1);

	$pdf->SetFont($font_arial,$style_b,24);
	$pdf->Cell(130, 10, "NF-e: ".$dadosNota['NUMNOTA'] , 'LB', 0);
	$pdf->Cell(60, 10, "VOLUME: ".$pagina." / ".$totalPaginas , 'RB', 1, 'R');


}

//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();