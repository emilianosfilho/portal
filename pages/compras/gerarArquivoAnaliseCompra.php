<?php 
// $debug = true;

if($debug) varDump2("gerarArquivoAnaliseCompra");
if($debug) varDump2($dados);

if (isset($_SESSION['EXCEL'])) 
	unset($_SESSION['EXCEL']);

$_SESSION['EXCEL']['DESCRICAO'] = "8022 - ANALISE DE COMPRAS";
$_SESSION['EXCEL']['lista'] = consultaSugestaoCompra($dados);
if($debug) varDump2($_SESSION['EXCEL']);


if(!$debug) abreNova("pages/compras/exportaExcel.php");
