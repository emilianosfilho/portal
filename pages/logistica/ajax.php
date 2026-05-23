<?php 
$timeout = 10800;//10800 segundos = 180 minutos = 3 horas
ini_set( "session.gc_maxlifetime", $timeout );// Defina o máximo de tempo da sessão
ini_set( "session.cookie_lifetime", $timeout );// Defina a vida útil do cookie da sessão
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

if (!empty($_POST)) {
	$dados = $_POST;
} else {
	$dados = $_GET;
}

if (isset($dados['acao'])) {
	switch ($dados['acao']) {
		case 'buscaNomeCliente':
			echo json_encode(buscaNomeCliente($dados['CODCLI']));
			break;

		case 'buscaNomeFornecedor':
			echo json_encode(buscaNomeFornecedor($dados['CODFORNEC']));
			break;

		case 'filtraTipoequip':
			echo json_encode(filtraTipoequip($dados['IDMONTADORA']));
			break;
		
		default:
			echo json_encode("ERRO ação não definida: ".$dados['acao']);
			break;
	}
}