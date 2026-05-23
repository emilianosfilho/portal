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

function buscaNomeCliente($codcli){
	$sql = "SELECT CLIENTE FROM PCCLIENT WHERE CODCLI = ".$codcli;
	$ret = selectOracle($sql);
	if ($ret === false) {
		$ret[0]['CLIENTE'] = 'CLIENTE NÃO ENCONTRADO';
	}
	return $ret[0]['CLIENTE'];
}

function buscaNomeVendedor($codusur){
	$sql = "SELECT NOME AS VENDEDOR FROM PCUSUARI WHERE DTEXCLUSAO IS NULL AND CODUSUR = ".$codusur;
	$ret = selectOracle($sql);
	if ($ret === false) {
		$ret[0]['VENDEDOR'] = 'VENDEDOR NÃO ENCONTRADO';
	}
	return $ret[0]['VENDEDOR'];
}

if (isset($_GET['codcli']) && $_GET['codcli'] <> "") {
	echo buscaNomeCliente($_GET['codcli']);
}

if (isset($_GET['codusur']) && $_GET['codusur'] <> "") {
	echo buscaNomeVendedor($_GET['codusur']);
}

?>