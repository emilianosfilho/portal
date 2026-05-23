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
					
		case 'consultarPeca':
			$codpeca = mb_strtoupper(trim($dados['codpeca']), 'UTF-8');
			$sql = "SELECT * FROM (
				SELECT TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
				       TRUNC(P.CODPROD) AS CODPROD,
				       TRUNC(P.DV) AS DV,
				       TRIM(P.DESCRICAO) AS DESCRICAO,
				       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
				           P.MARCA) AS MARCA,
				       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
				       NULL AS VIDE,
				       GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
				                                               1,
				                                               'VP',
				                                               TRUNC(SYSDATE)),
				                0) AS SALDO,
				       PR.PVENDA AS PVENDA_ORI,
				       (CEIL(PR.PVENDA * 100) / 100) AS PVENDA
				  FROM PCPRODUT P, PCTABPR PR
				 WHERE PR.CODPROD = P.CODPROD
				   AND P.DTEXCLUSAO IS NULL
				   AND PR.NUMREGIAO = '1'
				   AND ((P.NUMORIGINAL LIKE '".$codpeca."%' OR 'W' || P.CODPROD LIKE '".$codpeca."%'))
				UNION
				SELECT TRIM(V.CODPECA) AS NUMORIGINAL,
				       NULL AS CODPROD,
				       NULL AS DV,
				       TRIM(V.DESCRICAO) AS DESCRICAO,
				       TRIM(V.APLICMARCA) AS MARCA,
				       NULL AS LOCACAO,
				       V.VIDE AS VIDE,
				       0 AS SALDO,
				       NULL AS PVENDA_ORI,
				       NULL AS PVENDA
				  FROM ORCVIDE V
				 WHERE V.DTEXCLUSAO IS NULL
				   AND ((V.CODPECA LIKE '".$codpeca."%' AND V.VIDE IS NOT NULL) OR
				       (V.VIDE LIKE '".$codpeca."%' AND V.CODPECA IS NOT NULL))
				UNION
				SELECT TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
				       TRUNC(P.CODPROD) AS CODPROD,
				       TRUNC(P.DV) AS DV,
				       TRIM(P.DESCRICAO) AS DESCRICAO,
				       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
				           P.MARCA) AS MARCA,
				       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
				       NULL AS VIDE,
				       GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
				                                               1,
				                                               'VP',
				                                               TRUNC(SYSDATE)),
				                0) AS SALDO,
				       PR.PVENDA AS PVENDA_ORI,
				       (CEIL(PR.PVENDA * 100) / 100) AS PVENDA
				  FROM PCPRODUT P, PCTABPR PR
				 WHERE PR.CODPROD = P.CODPROD
				   AND P.DTEXCLUSAO IS NULL
				   AND PR.NUMREGIAO = '1'
				   AND (P.NUMORIGINAL IN
				       (SELECT TRIM(V2.CODPECA)
				           FROM ORCVIDE V2
				          WHERE V2.DTEXCLUSAO IS NULL
				            AND (V2.VIDE LIKE '".$codpeca."%' AND V2.CODPECA IS NOT NULL))) ) BASE
				ORDER BY NVL(BASE.SALDO,0) DESC";
			$data = selectOracle($sql);
			if (empty($data)) {
				$ret = array('status' => 400, 'sql' => $sql, 'data' => false, 'responseText' => 'Nenhuma peça encontrada para a pesquisa: '.$codpeca);
			} else {
				$ret = array('responseText' => 'SUCESSO ao executar consultarPeca', 'data' => $data);
			}
			break;
	}

	if (!isset($ret) || $ret === false) {
		$ret = array('status' => 400, 'dados' => $dados, 'responseText' => 'variavel ret nao encontrada');
	}
	
	header('Content-Type: application/json');
	echo json_encode($ret);
}