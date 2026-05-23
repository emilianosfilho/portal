<?php 
session_start();
date_default_timezone_set('America/Manaus');
error_reporting(E_ALL ^E_NOTICE ^E_WARNING ); 
ini_set('error_reporting', E_ALL ^E_NOTICE ^E_WARNING );
ini_set("display_errors", 1);
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/logistica/function.php';
//PREPARA PARA GERAR O PDF
define("FPDF_FONTPATH", "../../plugins/fpdf/font/");
require("../../plugins/fpdf/fpdf.php");
require("../../plugins/fpdf/stylesheet.php");

$debug = false;
// $debug = true;

if(isset($_POST) && !empty($_POST)){
	$dados = $_POST;
} else {
	if(isset($_GET) && !empty($_GET)){
		$dados = $_GET;
	} else {
		$dados = false;
	}
}
if($debug) varDump2($dados);


//TÍTULO DO RELATÓRIO
$titulo = "EXTRATO INVENTÁRIO";

//ENDEREÇO ONDE SERÁ GERADO O PDF
$end_final = str_replace(" ", "_", $titulo)."".$dados['IDINVENTARIO'].".pdf";

$inv['cab'] = buscaDadosInventarioCab($dados['IDINVENTARIO']);
$inv['itens'] = buscaDadosInventarioItem($dados['IDINVENTARIO']);
if($debug) varDump2($inv);

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF é usado, se disponível.
//D-> enviar para o navegador e forçar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

//NUMERO DE RESULTADOS POR PÁGINA
$regPorPagina = 35;

//LOGO QUE SERÁ COLOCADO NO RELATÓRIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";
$logo_rodape 	= "../../".DIR_IMG."logo_rodape.jpeg";

$existeRestricao = false;

$regTotal = count($inv['itens']);
$totalPaginas = intval(ceil($regTotal/$regPorPagina));
$vltotal = 0;


$pdf = new FPDF();
$pdf = new FPDF('P','mm','A4');

$pdf->AliasNbPages();

for ($paginaAtual=1; $paginaAtual <= $totalPaginas; $paginaAtual++) { 

	$pdf->AddPage();

	$pdf->Image($logo_header,10,10,36,10);

	//MONTA O TITULO DO RELATORIO
	$pdf->SetFont($font_arial,$style_b,$tam_20);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(190, 15, $titulo, 0, 1, 'R');


	/*#####################################################################################################*/

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->Cell(90, 	6, "IDINVENTARIO"	, 0, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_12);
	$pdf->Cell(100, 6, $inv['cab']['IDINVENTARIO']	, 0, 1, 'R');

	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->Cell(90, 	6, "LOCAÇÃO"	, 0, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_12);
	$pdf->Cell(100, 6, $inv['cab']['LOCACAO']	, 0, 1, 'R');

	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->Cell(90, 	6, "CONFERENTE"	, 0, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_12);
	$pdf->Cell(100, 6, $inv['cab']['CONFERENTE']	, 0, 1, 'R');

	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->Cell(90, 	6, "DATA INÍCIO"	, 0, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_12);
	$pdf->Cell(100, 6, $inv['cab']['DTINICIO']	, 0, 1, 'R');

	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->Cell(90, 	6, "DATA FIM"	, 0, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_12);
	$pdf->Cell(100, 6, $inv['cab']['DTFIM']	, 0, 1, 'R');

	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->Cell(90, 	6, "TEMPO"	, 0, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_12);
	if ($inv['cab']['DTFIM'] == "") {
		$pdf->Cell(100, 6, "INVENTÁRIO AINDA NÃO FINALIZADO"	, 0, 1, 'R');
	} else {
		$pdf->Cell(100, 6, $inv['cab']['TEMPO']	, 0, 1, 'R');

	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->Cell(90, 	6, "QTD PRODUTOS"	, 0, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_12);
	$pdf->Cell(100, 6, $regTotal	, 0, 1, 'R');
	}


	$pdf->Cell(190, 5, "", 0, 1); /* PULA LINHA */

	//MONTA O CABEÇALHO DOS ITENS ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_7);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(15, 	5, "CODPROD"		, 'B', 0, 'L');
	$pdf->Cell(20, 	5, "NUMORIGINAL"	, 'B', 0, 'L');
	$pdf->Cell(75, 	5, "DESCRICAO"		, 'B', 0, 'L');
	$pdf->Cell(30, 	5, "MARCA"			, 'B', 0, 'L');
	$pdf->Cell(10, 	5, "PED"			, 'B', 0, 'R');
	$pdf->Cell(10, 	5, "AVARIA"			, 'B', 0, 'R');
	$pdf->Cell(10, 	5, "EST"			, 'B', 0, 'R');
	$pdf->Cell(10, 	5, "CONF"			, 'B', 0, 'R');
	$pdf->Cell(10, 	5, "SALDO"			, 'B', 1, 'R');

	if ($inv['itens']) {
		foreach ($inv['itens'] as $key => $value) {
			if (($value["QTESTOQUE"] - $value["QTCONFERIDA"]) == 0) {
				$pdf->SetFont($font_courier,$style_n,$tam_8);
				$pdf->SetTextColor($r_black,$g_black,$b_black);
			} else {
				$pdf->SetFont($font_courier,$style_b,$tam_8);
				$pdf->SetTextColor($r_red,$g_red,$b_red);
			}
			$pdf->Cell(15, 	5, $value["CODPROD"]		, 0, 0, 'L');
			$pdf->Cell(20, 	5, $value["NUMORIGINAL"]	, 0, 0, 'L');
			$pdf->Cell(75, 	5, $value["DESCRICAO"]		, 0, 0, 'L');
			$pdf->Cell(30, 	5, $value["MARCA"]			, 0, 0, 'L');
			$pdf->Cell(10, 	5, $value["QTPEDIDO"]		, 0, 0, 'R');
			$pdf->Cell(10, 	5, $value["QTAVARIA"]		, 0, 0, 'R');
			$pdf->Cell(10, 	5, $value["QTESTOQUE"]		, 0, 0, 'R');
			$pdf->Cell(10, 	5, $value["QTCONFERIDA"]	, 0, 0, 'R');
			$pdf->Cell(10, 	5, ($value["QTESTOQUE"] - $value["QTCONFERIDA"])	, 0, 1, 'R', 0);
		}
	}





	$pdf->SetFont($font_arial, $style_i, $tam_9);
	$pdf->SetXY(10, 268);
	$pdf->Cell(190, 4, "Página ".$paginaAtual."/".$totalPaginas, 0, 1, 'R');
}

//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
?>