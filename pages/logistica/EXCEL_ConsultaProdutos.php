<?php 
session_start();
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require "../../pages/conf/define.php";
require "../../pages/conf/functions.php";
require "../../pages/conf/conectaOracle.php";
require "../../pages/compras/function.php";

// $debug = true;

if (!$_SESSION['produtos'] || empty($_SESSION['produtos'])) {
	exibeMensagem("Nenhum registro a ser exibido");
	fechaAba();
} 

if($debug) varDump2($_SESSION['produtos']);
if($debug) die();

$excel = array();

if ($_SESSION['produtos']) {
	$html =  '<table style="border: 1px solid black; border-collapse: collapse;">';
	foreach ($_SESSION['produtos'] as $keyR => $valueR) {
		if ($keyR == 0) {
			$html .= PHP_EOL.'<tr>';
			foreach ($valueR as $keyC => $valueC) {
				$html .= '<th style="border: 1px solid black;">'.$keyC."</th>";        
			}
			$html .= "</tr>";
		}
		$html .= PHP_EOL.'<tr>';
		foreach ($valueR as $keyC => $valueC) {
			$html .= '<td style="border: 1px solid black;">'.$valueC."</td>";        
		}
		$html .= "</tr>";
	}
	$html .= "</table>";

	// Configurações header para forçar o download
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"CONSULTA_PRODUTOS.xls\"" );

	echo $html;

}
