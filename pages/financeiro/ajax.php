<?php 
session_start();
ini_set( "session.gc_maxlifetime", 10800 );// Defina o máximo de tempo da sessão
ini_set( "session.cookie_lifetime", 10800 );// Defina a vida útil do cookie da sessão

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_DEPRECATED);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require "../../pages/conf/define.php";
require "../../pages/conf/functions.php";
require "../../pages/conf/conectaOracle.php";
require "../../pages/financeiro/function.php";

if ($_POST) {
	$dados = $_POST;
} else {
	if ($_GET) {
		$dados = $_GET;
	} else {
		$dados = false;
	}
}

// varDump2($dados);

if (isset($dados['acao']) && $dados['acao']<>"") {

	switch ($dados['acao']) {
		case 'buscaTodosEstados':
			$ret = array('status' => 'ok', 'data' => buscaTodosEstados());
			break;
		case 'buscarMunicipiosUF':
			$ret = array('status' => 'ok', 'data' => buscarMunicipiosUF($dados['UF']));
			break;
		case 'buscaFicha':
			if (isset($dados['CNPJ'])) {
				$ret = array('status' => 'ok', 'data' => buscaFichaCNPJ($dados['CNPJ']));
			} else {
				$ret = array('status' => 'ok', 'data' => buscaFichaCPF($dados['CPF']));
			}
			break;
	}
}

header('Content-Type: application/json');
if (!isset($ret) || $ret === false) {
	$ret = array('status' => 'erro', 'dados' => $dados, 'responseText' => 'variavel ret nao encontrada');
}
echo json_encode($ret);