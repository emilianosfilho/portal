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

$PDF = buscaAtividadesCabecalho($dados);

if($debug) varDump2($PDF);


$alturaLinha = 6;

//T�TULO DO RELAT�RIO
if ($dados['DATAINI'] == $dados['DATAFIM']) {
	$titulo = "RELAT�RIO DE ATIVIDADES - {$dados['DATAINI']}";
} else {
	$titulo = "RELAT�RIO DE ATIVIDADES - {$dados['DATAINI']} � {$dados['DATAFIM']}";
}


//LOGO QUE SER� COLOCADO NO RELAT�RIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";

/*############################################################################################*/
$pdf = new PDF_Code39('P','mm','A4');

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Image($logo_header,10,10,40,10);

$pdf->SetTextColor(139,0,0);
$pdf->SetFont($font_arial,$style_b,$tam_14);
$pdf->Cell(190,($alturaLinha*2),$titulo, 0, 1, 'R');
	
/*#####################################################################################################*/
$pdf->SetFillColor(200,200,200);/* Branco */
$pdf->SetTextColor(0,0,0); /* Preto */

foreach ($PDF as $keyCab => $valueCab) {
	// 	/*#####################################################################################################*/
	$pdf->SetFont('arial','b',10);
	$pdf->Cell(190, $alturaLinha, ''						, 0, 1); //PULA LINHA
	$pdf->Cell(190, ($alturaLinha*1.5), "CAMADO #".$valueCab['ID_CHAMADO']	, 1, 1, 'L', 1);

	$pdf->SetFont('arial','b',8);
	$pdf->Cell(30, ($alturaLinha*0.7), "ABERTURA" 	, 'TL', 0, 'L');
	$pdf->Cell(30, ($alturaLinha*0.7), "TIPO" 		, 'T', 0, 'L');
	$pdf->Cell(50, ($alturaLinha*0.7), "CATEGORIA" 	, 'T', 0, 'L');
	$pdf->Cell(50, ($alturaLinha*0.7), "SOLICITANTE" 	, 'T', 0, 'L');
	$pdf->Cell(30, ($alturaLinha*0.7), "STATUS" 		, 'TR', 1, 'L');

	$pdf->SetFont('arial','',8);
	$pdf->Cell(30, $alturaLinha, $valueCab['DATA_ABERTURA']				, 'BL', 0);
	$pdf->Cell(30, $alturaLinha, converterUTF8($valueCab['TIPO'])			, 'B', 0);
	$pdf->Cell(50, $alturaLinha, converterUTF8($valueCab['CATEGORIA'])	, 'B', 0);
	$pdf->Cell(50, $alturaLinha, substr(converterUTF8($valueCab['USUARIO']),0,25)		, 'B', 0);
	$pdf->Cell(30, $alturaLinha, converterUTF8($valueCab['STATUS'])		, 'BR', 1);

	$pdf->SetFont('arial','b',8);
	$pdf->Cell(190, $alturaLinha, "TITULO" 	,'TLR', 1, 'L');

	$pdf->SetFont('arial','',8);
	if (strlen($valueCab['TITULO'])>100) {
		$pdf->Cell(190, ($alturaLinha*0.75), substr(converterUTF8($valueCab['TITULO']), 0, 100)		, 'LR', 1);
		$pdf->Cell(190, ($alturaLinha*0.75), substr(converterUTF8($valueCab['TITULO']), 100, 200)		, 'BLR', 1);
	} else {
		$pdf->Cell(190, ($alturaLinha*0.75), substr(converterUTF8($valueCab['TITULO']), 0, 100)		, 'LRB', 1);
	}

	$itens = buscaAtividadesRegistros($dados, $valueCab);
	if($debug) varDump2($itens);

	if ($itens) {
		// 	/*###################################################################################################*/
		$pdf->SetFont('arial','b',8);
		$pdf->Cell(30, $alturaLinha, "DATA" 		, 'LT', 0, 'L');
		$pdf->Cell(30, $alturaLinha, "USUARIO" 		, 'T', 0, 'L');
		$pdf->Cell(130, $alturaLinha, "HISTORICO" 	, 'RT', 1, 'L');

		foreach ($itens as $keyIte => $valueIte) {

			$pdf->SetFont('arial','',8);
			$pdf->Cell(30, $alturaLinha, $valueIte['DATA_ATUALIZACAO']			, "L", 0);
			$pdf->Cell(30, $alturaLinha, converterUTF8($valueIte['USUARIO'])		, 0, 0);
			if (strlen($valueIte['HISTORICO'])>100)	 {
				$pdf->Cell(130, $alturaLinha, substr(converterUTF8($valueIte['HISTORICO']), 0, 100)	, "R", 1);
				$pdf->Cell(60, $alturaLinha, "", "L", 0);
				$pdf->Cell(130, $alturaLinha, substr(converterUTF8($valueIte['HISTORICO']), 100, 200)	, "R", 1);
			} else {
				$pdf->Cell(130, $alturaLinha, converterUTF8($valueIte['HISTORICO'])	, "R", 1);
			}
		}
		$pdf->Cell(190, ($alturaLinha*0.1), ""	, "T", 1);
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

