<?php 
session_start();
ini_set("xdebug.var_display_max_depth", -1);
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('memory_limit', '24G');
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/chamados/function.php';
require_once "../../plugins/code39/code39.php";

// $debug = true;


$dados = (empty($_POST)?$_GET:$_POST);
if($debug) varDump2($dados);

$datas = geraDatasIntervalo($dados['DATAINI'], $dados['DATAFIM']);
if($debug) varDump2($datas);

$alturaLinha = 5;

//T�TULO DO RELAT�RIO
$titulo = "RELAT�RIO DE ATIVIDADES POR DATA-HORA";

//LOGO QUE SER� COLOCADO NO RELAT�RIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";

/*############################################################################################*/
$pdf = new PDF_Code39('L','mm','A4');

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Image($logo_header,245,10,40,10);

$pdf->SetTextColor(139,0,0);
$pdf->SetFont($font_arial,$style_b,$tam_20);
$pdf->Cell(275,($alturaLinha*2),$titulo, 0, 1, 'L');
	
/*#####################################################################################################*/
$pdf->SetFillColor(200,200,200);/* Branco */
$pdf->SetTextColor(0,0,0); /* Preto */

foreach ($datas as $keyDatas => $data) {
	$itens = buscaAtividadesRegistrosPorData($data);
	if($debug) varDump2($itens);

	if ($itens) {

		$pdf->SetFont('arial','b',$tam_14);
		$pdf->Cell(275, ($alturaLinha*2), "DATA: ".$data	, 0, 1, 'L');

		$pdf->SetFont('arial','b',$tam_8);
		$pdf->Cell(15, $alturaLinha, "HORA" 	, 'LT', 0, 'L', 1);
		$pdf->Cell(10, $alturaLinha, "ID" 		, 'T', 0, 'L', 1);
		$pdf->Cell(30, $alturaLinha, "SOLICITANTE" 	, 'T', 0, 'L', 1);
		$pdf->Cell(90, $alturaLinha, "CHAMADO" 	, 'T', 0, 'L', 1);
		$pdf->Cell(135, $alturaLinha, "HISTORICO" 	, 'RT', 1, 'L', 1);

		$pdf->SetFont('arial','',$tam_7);
		foreach ($itens as $key => $historico) {
			$pdf->Cell(15, $alturaLinha, end(explode(" ", $historico['DATA_ATUALIZACAO']))	, "TL", 0);
			$pdf->Cell(10, $alturaLinha, $historico['ID_CHAMADO'] , "T", 0);
			$pdf->Cell(30, $alturaLinha, substr(converterUTF8($historico['SOLICITANTE']), 0, 15) , "T", 0);
			$pdf->Cell(90, $alturaLinha, substr(converterUTF8($historico['TITULO']), 0, 50) , "T", 0);
			$pdf->Cell(135, $alturaLinha, substr(converterUTF8($historico['HISTORICO']), 0, 90)	, "TR", 1);
			
			if (strlen($historico['TITULO']) > 50 || strlen($historico['HISTORICO']) > 90) {
				$pdf->Cell(25, $alturaLinha, ""	, "L", 0);
				$pdf->Cell(30, $alturaLinha, '' , 0, 0);
				$pdf->Cell(90, $alturaLinha, substr(converterUTF8($historico['TITULO']), 50, 100) , 0, 0);
				$pdf->Cell(135, $alturaLinha, substr(converterUTF8($historico['HISTORICO']), 90, 180)	, "R", 1);

				if (strlen($historico['TITULO']) > 100 || strlen($historico['HISTORICO']) > 180) {
					$pdf->Cell(25, $alturaLinha, ""	, "L", 0);
					$pdf->Cell(30, $alturaLinha, '' , 0, 0);
					$pdf->Cell(90, $alturaLinha, substr(converterUTF8($historico['TITULO']), 100, 150) , 0, 0);
					$pdf->Cell(135, $alturaLinha, substr(converterUTF8($historico['HISTORICO']), 180, 270)	, "R", 1);
				}
			}
		}
		$pdf->Cell(280, $alturaLinha, '', "T", 1); //PULA LINHA
	}
}

if(!$debug){
	### TIPO DO PDF GERADO ###
	//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
	//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
	//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
	//S-> retorna o documento como uma string.
	$tipo_pdf = "I";

	//SAIDA DO PDF
	$pdf->Output($tipo_pdf, $end_final, true);
	varDump2("Arquivo criado com sucesso ".$end_final);
	fechaAba();
}

