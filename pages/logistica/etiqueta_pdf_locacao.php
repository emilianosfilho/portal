<?php 
session_start();
ini_set("xdebug.var_display_max_depth", -1);
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE|E_DEPRECATED);
ini_set('memory_limit', '24G');
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/logistica/function.php';
require_once "../../plugins/code39/code39.php";

// $debug = true;

if(isset($_POST) && !empty($_POST)){
 	$dados = $_POST;
} else {
	$dados = $_GET;
}

if($debug) varDump2($dados);

$locacao = trim(mb_strtoupper($dados['locacao'], 'UTF-8'));
if($debug) varDump2($locacao);

$listaLocacao = buscaLocacao($locacao);


//TÍTULO DO RELATÓRIO
$titulo = "ETIQUETA LOCAÇÃO";

$ETIQ_LARGURA = floatval(100);
$ETIQ_ALTURA = floatval(35);

$pdf = new PDF_Code39('L','mm',array($ETIQ_LARGURA, $ETIQ_ALTURA));
$pdf->SetMargins(5, 3, 5);
$pdf->SetAutoPageBreak(false,0);

if ($listaLocacao) {
	foreach ($listaLocacao as $key => $value) {
		$pdf->AddPage();

		$filename = '../../dist/img/logo_header_vertical.png';
		if (file_exists($filename)) {
			$pdf->Image($filename, 3, 1, 9, 33);
		} else {
			varDump2("Imagem não encontrada");
		}


		$pdf->Code39(17, 5, $value['LOCACAO'], 1.5, 15);

		$pdf->Cell(100,  22, "", 0, 1);
		$pdf->SetFont('arial','b', 18);
		$pdf->Cell(12,  8, "", 0, 0, 'C');
		$pdf->Cell(78, 8, "Local: {$value['LOCACAO']}", 0, 1);

	}
}


//*********************************************************************************

//ENDEREÇO ONDE SERÁ GERADO O PDF
$end_final = @date('Ymd_His_').str_replace(" ", "_", $titulo).".pdf";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF é usado, se disponível.
//D-> enviar para o navegador e forçar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
redireciona($end_final);
?>