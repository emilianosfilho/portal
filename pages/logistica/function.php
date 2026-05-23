<?php  
function buscaVendedores(){
	$sql =  "SELECT CODUSUR, NOME 
			 FROM PCUSUARI 
			WHERE CODUSUR IS NOT NULL
			  AND DTEXCLUSAO IS NULL
			  AND DTTERMINO IS NULL
			ORDER BY NOME asc";
	$ret = selectOracle($sql);
	return $ret;
}

function validaLocacao($LOCACAO){
	$sql = "SELECT COUNT(*) QT FROM PCPRODUT WHERE INFORMACOESTECNICAS like '".$LOCACAO."' AND DTEXCLUSAO IS NULL";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	if ($ret[0]['QT']>0) {
		return true;
	} else {
		return false;
	}

}

function salvarAlteracaoLocacao($dados){
	// varDump2($dados); 
	$sql = "UPDATE PCPRODUT SET INFORMACOESTECNICAS = '".mb_strtoupper(trim($dados['LOCACAO_NEW']), 'UTF-8')."' WHERE CODPROD = ".$dados['CODPROD'];
	// varDump2($sql);
	if (executarOracle($sql)){
		$sql2 = "INSERT INTO OMGLOGALTERALOCACAO (DTALTERACAO, IDUSUARIO, CODPROD, LOCACAO_OLD, LOCACAO_NEW) VALUES (
				SYSDATE,
				".$_SESSION['login']['IDUSUARIO'].",
				".$dados['CODPROD'].",
				'".mb_strtoupper(trim($dados['LOCACAO_OLD']), 'UTF-8')."',
				'".mb_strtoupper(trim($dados['LOCACAO_NEW']), 'UTF-8')."'
				)";
		// varDump2($sql2);
		if (executarOracle($sql2))	{
			if (isset($dados['IDCHECKIN']) && !empty($dados['IDCHECKIN']) && isset($dados['IDCHECKINI']) && !empty($dados['IDCHECKINI'])) {
				$sql3 = "UPDATE OMGCHECKINI SET LOCACAO = '".mb_strtoupper(trim($dados['LOCACAO_NEW']), 'UTF-8')."' WHERE IDCHECKIN = ".$dados['IDCHECKIN']." AND IDCHECKINI = ".$dados['IDCHECKINI'];
				// varDump2($sql3);
				return executarOracle($sql3);
			}
		} else {
			return false;	
		}
	} else {
		return false;	
	}
}

function salvarAlteracaoLocacaoCheckin($dados){

	$sql = "UPDATE PCPRODUT SET INFORMACOESTECNICAS = '".mb_strtoupper(trim($dados['LOCACAO_NEW']), 'UTF-8')."' WHERE CODPROD = ".$dados['CODPROD'];
	// varDump2($sql);
	executarOracle($sql);

	$sql1 = "UPDATE OMGCHECKINI SET LOCACAO = '".mb_strtoupper(trim($dados['LOCACAO_NEW']), 'UTF-8')."' WHERE CODPROD = ".$dados['CODPROD'];
	// varDump2($sql);
	executarOracle($sql1);

	$sql2 = "INSERT INTO OMGLOGALTERALOCACAO (DTALTERACAO, IDUSUARIO, CODPROD, LOCACAO_OLD, LOCACAO_NEW) VALUES (
					SYSDATE,
					".$_SESSION['login']['IDUSUARIO'].",
					".$dados['CODPROD'].",
					'".mb_strtoupper(trim($dados['LOCACAO_OLD']), 'UTF-8')."',
					'".mb_strtoupper(trim($dados['LOCACAO_NEW']), 'UTF-8')."'
					)";
	// varDump2($sql2);
	executarOracle($sql2);

}

function buscaHistoricoLocacao($CODPROD){
	$sql = "SELECT L.*, TO_CHAR(l.dtalteracao, 'DD/MM/YYYY HH24:MI:SS') AS DTALTERACAO, U.NOME AS USUARIO FROM OMGLOGALTERALOCACAO L, ORCUSUARIO U
					WHERE L.IDUSUARIO = U.IDUSUARIO
						AND L.CODPROD = ".$CODPROD."
					ORDER BY l.DTALTERACAO ASC";
	return selectOracle($sql);
}

function inventario_pesquisar($dados){
	$sql = "SELECT C.IDINVENTARIO, 
					C.DTINICIO AS DATA, 
					C.DTFIM, 
					C.IDUSUARIOCONFERENTE, 
					U.NOME AS CONFERENTE,
					C.LOCACAO,
					DECODE(C.DTFIM, NULL, 'NAOFINALIZADO', 'FINALIZADO') AS STATUSINVENTARIO
		 FROM OMGINVENTARIOC C,  ORCUSUARIO U
		WHERE C.IDUSUARIOCONFERENTE = U.IDUSUARIO (+) 
			AND TRUNC(C.DTINICIO) BETWEEN ".formataDataBRtoOracle($dados['DATAINI'])." AND ".formataDataBRtoOracle($dados['DATAFIM'])."
			AND C.LOCACAO IS NOT NULL ";

	if ($dados['IDINVENTARIO'] <> "") {
		$sql .= PHP_EOL." AND C.IDINVENTARIO = ".intval($dados['IDINVENTARIO']);
	}
	if ($dados['LOCACAO'] <> "") {
		$sql .= PHP_EOL." AND C.LOCACAO = '".strtoupper(trim($dados['LOCACAO']))."'";
	}
	if ($dados['STATUSINVENTARIO'] == "NAOFINALIZADO") {
		$sql .= PHP_EOL." AND C.DTFIM IS NULL";
	} else {
		if ($dados['STATUSINVENTARIO'] == "FINALIZADO") {
			$sql .= PHP_EOL." AND C.DTFIM IS NOT NULL";
		}
	}

	varDump2($dados);
	varDump2($sql);
	// die();
	$_SESSION['INVENTARIO'] = selectOracle($sql);

}


function buscaConferentesInventario(){
	$sql = "SELECT C.IDUSUARIOCONFERENTE, U.NOME AS CONFERENTE
					FROM OMGINVENTARIOC C, ORCUSUARIO U
				 WHERE C.IDUSUARIOCONFERENTE = U.IDUSUARIO
					 AND C.IDUSUARIOCONFERENTE NOT IN (1)
				 GROUP BY C.IDUSUARIOCONFERENTE, U.NOME
				 ORDER BY U.NOME ASC";
	return selectOracle($sql);
}

function buscaDadosInventario($dados){
	$sql = "SELECT C.IDINVENTARIO, 
					C.DTINICIO AS DATA, 
					C.DTFIM, 
					C.IDUSUARIOCONFERENTE, 
					U.NOME AS CONFERENTE,
					C.LOCACAO,
					DECODE(C.DTFIM, NULL, 'NAOFINALIZADO', 'FINALIZADO') AS STATUSINVENTARIO
		 FROM OMGINVENTARIOC C,  ORCUSUARIO U
		WHERE C.IDUSUARIOCONFERENTE = U.IDUSUARIO (+) 
			AND C.LOCACAO IS NOT NULL 
			AND C.IDINVENTARIO = ".$dados['IDINVENTARIO'];
	// varDump2($dados);
	// varDump2($sql);
	$ret = selectOracle($sql);
	$ret = reset($ret);
	// varDump2($ret);

	$ret['ITEM'] = buscaItemInventario($dados);
	// varDump2($sql);
	return $ret;
}

function buscaItemInventario($dados){
	$sql = "SELECT I.IDINVENTARIO,
			 TRIM(UPPER(P.INFORMACOESTECNICAS)) AS LOCACAO,
			 TRUNC(P.CODPROD) AS CODPROD,
			 TRUNC(P.DV) AS DV,
			 TRIM(UPPER(P.NUMORIGINAL)) AS NUMORIGINAL,
			 TRIM(UPPER(P.DESCRICAO)) AS DESCRICAO,
			 TRIM(UPPER(M.MARCA)) AS MARCA,
			 I.QTESTOQUE,
			 I.QTPEDIDO,
			 I.QTAVARIA,
			 I.QTCONFERIDA,
			 I.DTULTCONFERENCIA,
			 I.IDCONFERENTE,
			 SUBSTR(U.NOME,0,20) AS CONFERENTE
	FROM OMGINVENTARIOI I
	LEFT JOIN PCPRODUT P
		ON I.CODPROD = P.CODPROD
	LEFT JOIN PCMARCA M
		ON P.CODMARCA = M.CODMARCA
	LEFT JOIN PCEST E
		ON P.CODPROD = E.CODPROD
	LEFT JOIN ORCUSUARIO U
		ON U.IDUSUARIO = I.IDCONFERENTE
 WHERE E.CODFILIAL = 1
	 AND I.IDINVENTARIO = '".$dados['IDINVENTARIO']."'
	 ORDER BY P.NUMORIGINAL ASC";
	 // varDump2($sql);
	 return selectOracle($sql);
}

function conferirItemINVENTARIO($dados){
	if ($dados['CODPROD']=='') {
		insereModal('danger', 'o campo CODPROD é obrigatório');
		return false;
	} else {
		$erro = true;
		foreach ($_SESSION['INVENTARIO']['ITEM'] as $key => $value) {
			if (($value['IDINVENTARIO'] == $dados['IDINVENTARIO']) && ($value['CODPROD'] == $dados['CODPROD'])) {
				$erro = false;
				$dados['QTCONFERIDA'] += $_SESSION['INVENTARIO']['ITEM'][$key]['QTCONFERIDA'];
				atualizaQtconferidaInventario($dados);
			}
		}
		if ($erro) {
			insereModal('danger', 'O CODPROD '.$dados['CODPROD'].' informado não pertence a locação '.$dados['LOCACAO'].' ');
		}
	}
}

function atualizaQtconferidaInventario($dados){
	$sql = "UPDATE OMGINVENTARIOI 
				 SET DTULTCONFERENCIA = SYSDATE, 
				 	 IDCONFERENTE = ".$_SESSION['login']['IDUSUARIO'].", 
				 	 QTCONFERIDA = ".$dados['QTCONFERIDA']."
			 WHERE IDINVENTARIO = ".$dados['IDINVENTARIO']."
				 AND CODPROD = ".$dados['CODPROD'];
	// varDump2($sql);
	if(executarOracle($sql)){
		$_SESSION['INVENTARIO']['ITEM'] = buscaItemInventario($dados);
	}
}

function finalizarInventario($inventario){
	$sql = "UPDATE OMGINVENTARIOC 
			SET DTFIM = SYSDATE 
			WHERE IDINVENTARIO = ".$inventario['IDINVENTARIO'];
	if (executarOracle($sql)) {
		exibeMensagem("Inventário finalizado com sucesso!");
		redireciona('index.php?op=134&aba-inventario&acao=limparLista');
	} else {
		insereModal("danger", "Não foi possível finalizar o Inventário!");
	}
}

function abrirNovoInventario($dados){
	// varDump2($dados);
	if ($dados['LOCACAO']=='') {
		insereModal('danger', 'O campo LOCAÇÃO é obrigatório');
	} else {
		$dados['LOCACAO'] = mb_strtoupper(trim($dados['LOCACAO']), 'UTF-8');
		$produtos = buscaProdutosPorLocacao($dados['LOCACAO']);
		if (!$produtos) {
			insereModal('warning', 'A locação '.$dados['LOCACAO'].' não é válida ou não possui nenhum produto válido');
		} else {
			// varDump2($produtos);
			$sql = "INSERT INTO OMGINVENTARIOC (
				IDINVENTARIO,
				LOCACAO,
				IDUSUARIOCONFERENTE
				) VALUES (
				/*IDINVENTARIO*/(SELECT NVL(MAX(IDINVENTARIO),0)+1 FROM OMGINVENTARIOC),
				/*LOCACAO*/'".$dados['LOCACAO']."',
				/*IDUSUARIOCONFERENTE*/'".$_SESSION['login']['IDUSUARIO']."'
				)";
			// varDump2($sql);
			
			if(executarOracle($sql)){

				$sql = "SELECT NVL(MAX(IDINVENTARIO),0) AS IDINVENTARIO
							FROM OMGINVENTARIOC 
						 WHERE LOCACAO = '".$dados['LOCACAO']."'";
        $ret = selectOracle($sql);
        $ret = reset($ret);
				$IDINVENTARIO = $ret['IDINVENTARIO'];

				$sql = "INSERT INTO OMGINVENTARIOI (IDINVENTARIO, CODPROD, NUMORIGINAL, DESCRICAO, MARCA, QTESTOQUE, QTPEDIDO, QTAVARIA)
				 ( ";

				foreach ($produtos as $key => $value) {
					$sql .= PHP_EOL." SELECT 
						".$IDINVENTARIO.", 
						'".$value['CODPROD']."', 
						'".$value['NUMORIGINAL']."', 
						'".$value['DESCRICAO']."', 
						'".$value['MARCA']."', 
						'".$value['QTESTOQUE']."', 
						'".$value['QTPEDIDA']."', 
						'".$value['QTAVARIA']."'
						FROM dual UNION ALL ";
				}
				$sql = substr($sql, 0, -10);
				$sql .= PHP_EOL." )";
				// varDump2($sql);
				// die();
				executarOracle($sql);

				redireciona('index.php?op=134&acao=abrirInventario&IDINVENTARIO='.$IDINVENTARIO);
			}
		}
	}
}

function buscaProdutosPorLocacao($LOCACAO){
	$sql = "SELECT P.INFORMACOESTECNICAS AS LOCACAO,
					 TRUNC(P.CODPROD) AS CODPROD,
					 TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
					 TRIM(P.DESCRICAO) AS DESCRICAO,
					 TRIM(M.MARCA) AS MARCA,
					 NVL(E.QTESTGER,0) AS QTESTOQUE,
					 NVL(E.QTPEDIDA,0) AS QTPEDIDA,
					 NVL(E.QTINDENIZ,0) AS QTAVARIA
				FROM PCPRODUT P, PCMARCA M, PCEST E
			 WHERE NVL(P.CODMARCA, 1) = M.CODMARCA
				 AND P.CODPROD = E.CODPROD
				 AND E.CODFILIAL = 1
				 AND P.DTEXCLUSAO IS NULL
				 AND P.INFORMACOESTECNICAS like '".mb_strtoupper(trim($LOCACAO), 'UTF-8')."%'
			 GROUP BY P.INFORMACOESTECNICAS,
						P.CODPROD,
						P.NUMORIGINAL,
						P.DESCRICAO,
						M.MARCA,
						E.QTESTGER,
						E.QTPEDIDA,
						E.QTINDENIZ
			ORDER BY E.QTESTGER DESC";
	// varDump2($sql);
	return selectOracle($sql);
}


function buscaProdutosLogistica($dados){
	$ok = false;
	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
			 TRUNC(P.DV) AS DV,
			 TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
			 TRIM(P.DESCRICAO) AS DESCRICAO,
			 TRIM(M.MARCA) AS MARCA,
			 P.INFORMACOESTECNICAS AS LOCACAO,
			 NVL(E.QTESTGER,0) AS QTESTOQUE
	FROM PCPRODUT P
	LEFT JOIN PCMARCA M
		ON P.CODMARCA = M.CODMARCA
	LEFT JOIN PCEST E
		ON P.CODPROD = E.CODPROD
 WHERE E.CODFILIAL = 1";
	if (isset($dados['CAMPO'])) {
		if ($dados['CAMPO']=="NUMORIGINAL") {
			$ok = true;
			$sql .= PHP_EOL." AND P.NUMORIGINAL LIKE '".mb_strtoupper($dados['VALOR'],'UTF-8')."%'";
		}
		if ($dados['CAMPO']=="CODPROD" && is_numeric($dados['VALOR'])) {
			$ok = true;
			$sql .= PHP_EOL." AND P.CODPROD = '".$dados['VALOR']."'";
		}
		if ($dados['CAMPO']=="LOCACAO") {
			$ok = true;
			$sql .= PHP_EOL." AND P.INFORMACOESTECNICAS LIKE '".mb_strtoupper($dados['VALOR'],'UTF-8')."%'";
		}
		if ($dados['CAMPO']=="DESCRICAO") {
			$ok = true;
			$sql .= PHP_EOL." AND P.DESCRICAO LIKE '".mb_strtoupper($dados['VALOR'],'UTF-8')."%'";
		}
		if ($dados['CAMPO']=="NUMNOTA" && is_numeric($dados['VALOR'])) {
			$ok = true;
			$sql .= PHP_EOL." AND P.CODPROD IN (SELECT M.CODPROD
																						FROM PCMOV M
																					 WHERE M.CODOPER IN ('E', 'ED')
																						 AND M.DTCANCEL IS NULL
																						 AND M.NUMNOTA = '".$dados['VALOR']."')";
		}
		if ($dados['CAMPO']=="VIDE") {
			$ok = true;
			$sql .= PHP_EOL." AND P.CODPROD IN (SELECT P.CODPROD
																						FROM PCPRODUT P
																					 WHERE P.DTEXCLUSAO IS NULL
																						 AND (P.NUMORIGINAL IN (SELECT V1.VIDE 
																																		 FROM ORCVIDE V1 
																																		WHERE V1.CODPECA = '".$dados['VALOR']."')
																									OR
																									P.NUMORIGINAL IN (SELECT V2.CODPECA 
																																		 FROM ORCVIDE V2 
																																		WHERE V2.VIDE = '".$dados['VALOR']."')
																																	))";
		}
	}
	$sql .= PHP_EOL." GROUP BY P.CODPROD, P.DV, P.NUMORIGINAL, P.DESCRICAO, M.MARCA, P.INFORMACOESTECNICAS, E.QTESTGER";
	$sql .= PHP_EOL." ORDER BY P.DESCRICAO, P.NUMORIGINAL, P.CODPROD ASC";
	if ($ok) {
		$ret = selectOracle($sql);
		if ($dados['CAMPO']=="VIDE") {
			$sql2 = "SELECT  NULL          AS CODPROD,
											 NULL          AS DV,
											 V1.VIDE       AS NUMORIGINAL,
											 V1.DESCRICAO,
											 V1.APLICMARCA,
											 NULL          AS LOCACAO,
											 0             AS QTESTOQUE
									FROM ORCVIDE V1
								 WHERE V1.CODPECA = '".$dados['VALOR']."'
									 AND V1.VIDE IS NOT NULL
							UNION
							SELECT   NULL          AS CODPROD,
											 NULL          AS DV,
											 V1.CODPECA    AS NUMORIGINAL,
											 V1.DESCRICAO,
											 V1.APLICMARCA,
											 NULL          AS LOCACAO,
											 0             AS QTESTOQUE
									FROM ORCVIDE V1
								 WHERE V1.VIDE = '".$dados['VALOR']."'
									 AND V1.CODPECA IS NOT NULL";
			$ret2 = selectOracle($sql2);
			if ($ret && $ret2) {
				$result = array_merge($ret, $ret2);
				return $result;
			}
		} else {
			return $ret;
		}

	} else {
		return false;
	}
}

function abreNovoInventario($LOCACAO){
	$sql = "INSERT INTO OMGINVENTARIOC (IDINVENTARIO, DTINICIO, HORAINICIO, IDUSUARIOCONFERENTE, LOCACAO) 
		VALUES ((SELECT NVL(MAX(IDINVENTARIO),0)+1 FROM OMGINVENTARIOC), SYSDATE, (SELECT TO_CHAR(SYSDATE, 'HH24:MI:SS') FROM DUAL), ".$_SESSION['login']['IDUSUARIO'].", '".$LOCACAO."' )";
	
	if (executarOracle($sql)) {

		if($inventario = pesquisaInventarioAbertoLocacao($LOCACAO)){

			if ($ret2 = buscaProdutosPorLocacao($LOCACAO)) {
				// varDump2($ret2); die();
				$sql3 = "INSERT ALL ".PHP_EOL;
				foreach ($ret2 as $key2 => $value2) {
					$sql3 .= "INTO OMGINVENTARIOI ( IDINVENTARIO, CODPROD, NUMORIGINAL, DESCRICAO, MARCA, QTESTOQUE, QTPEDIDO, QTAVARIA, QTCONFERIDA ) VALUES ( ".$inventario[0]['IDINVENTARIO'].", '".$value2['CODPROD']."', '".$value2['NUMORIGINAL']."', '".$value2['DESCRICAO']."', '".$value2['MARCA']."', '".$value2['QTESTOQUE']."', '".$value2['QTPEDIDO']."', '".$value2['QTAVARIA']."', '".$value2['QTCONFERIDA']."' )".PHP_EOL;
				}
				$sql3 .= "SELECT 1 FROM dual";
				// varDump2($sql3);
				executarOracle($sql3);
			}
			return buscaOMGINVENTARIOC($inventario[0]['IDINVENTARIO']);

		} else {
			return false;
		}
	} else {
		insereModal('danger', 'Erro ao salvar um novo inventário para a locação '.$LOCACAO);
	}
	

}

function buscaOMGINVENTARIOC($IDINVENTARIO){
	$sql = "SELECT C.IDINVENTARIO,
			 C.DTINICIO,
			 C.DTFIM,
			 C.IDUSUARIOCONFERENTE,
			 C.LOCACAO,
			 C.DTEXCLUSAO,
			 C.USUARIOEXCLUSAO,
			 C.HORAINICIO,
			 DECODE(C.DTFIM, NULL, 'PENDENTE', 'FINALIZADO') AS STATUS,
			 U.NOME AS CONFERENTE
	FROM OMGINVENTARIOC C, ORCUSUARIO U
 WHERE C.IDUSUARIOCONFERENTE = U.IDUSUARIO
	 AND C.IDINVENTARIO = ".$IDINVENTARIO;
	// varDump2($sql);
	$ret = selectOracle($sql);
	return reset($ret);
}


// function buscaItensInventario($IDINVENTARIO){
// 	if ($IDINVENTARIO <> "") {
// 		$sql = "SELECT I.IDINVENTARIO,
// 									 I.CODPROD,
// 									 I.NUMORIGINAL,
// 									 I.DESCRICAO,
// 									 NVL(I.MARCA, ' ') AS MARCA,
// 									 I.QTESTOQUE,
// 									 I.QTPEDIDO,
// 									 I.QTAVARIA,
// 									 I.QTCONFERIDA,
// 									 I.DTULTCONFERENCIA,
// 									 CASE
// 										 WHEN I.QTESTOQUE = I.QTCONFERIDA THEN 'OK'
// 										 WHEN I.QTESTOQUE < I.QTCONFERIDA THEN 'EXCESSO'
// 										 ELSE 'FALTA'
// 										 END AS STATUS
// 							FROM OMGINVENTARIOI I
// 						 WHERE I.IDINVENTARIO = ".$IDINVENTARIO."
// 						 ORDER BY I.DTULTCONFERENCIA DESC, I.QTESTOQUE DESC";
// 		return selectOracle($sql);
// 	} else {
// 		return false;
// 	}
// }


function excluirInventario($inventario){
	$sql = "DELETE OMGINVENTARIOC 
			WHERE IDINVENTARIO = ".$inventario['IDINVENTARIO'];
	if (executarOracle($sql)) {
		insereModal("success", "Inventário excluído com sucesso!");
	} else {
		insereModal("danger", "Não foi possível excluir o Inventário!");
	}
}


function buscaLocacao($locacao){
	$sql = "SELECT DISTINCT TRIM(INFORMACOESTECNICAS) AS LOCACAO 
					  FROM PCPRODUT 
					 WHERE INFORMACOESTECNICAS like '{$locacao}%'
					 ORDER BY TRIM(INFORMACOESTECNICAS) ASC";
	return selectOracle($sql);
}


function buscaLocacaoCorreta($CODPROD){
	$sql = "SELECT INFORMACOESTECNICAS FROM PCPRODUT WHERE CODPROD = ".$CODPROD;
	$ret = selectOracle($sql);
	return $ret[0]['INFORMACOESTECNICAS'];
}


function pesquisaInventarioAbertoLocacao($LOCACAO){
	$sql = "SELECT C.*, U.NOME AS CONFERENTE
						FROM OMGINVENTARIOC C, ORCUSUARIO U
					 WHERE C.IDUSUARIOCONFERENTE = U.IDUSUARIO
						 AND C.IDINVENTARIO IS NOT NULL
						 AND C.LOCACAO = '".$LOCACAO."'
					 ORDER BY C.DTFIM DESC";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	return $ret;
}

function buscaConferentes(){
	$sql = "SELECT DISTINCT C.IDUSUARIOCONFERENTE, U.NOME AS CONFERENTE
						FROM OMGINVENTARIOC C, ORCUSUARIO U
					 WHERE C.IDUSUARIOCONFERENTE = U.IDUSUARIO
					 ORDER BY U.NOME";
	// varDump2($sql);
	return selectOracle($sql);
}

function buscaUsuariosLogistica(){
	$sql = "SELECT U.IDUSUARIO, U.NOME FROM ORCUSUARIO U, ORCPERMISSAOUSUARIO PU
					WHERE U.IDUSUARIO = PU.IDUSUARIO
					AND PU.IDPERMISSAO = 3
					ORDER BY U.NOME ASC";
	// varDump2($sql);
	return selectOracle($sql);
}

function insereProdutoInventario($produto, $IDINVENTARIO){
	$sql = "INSERT INTO OMGINVENTARIOI (
						IDINVENTARIO,
						DTULTCONFERENCIA,
						CODPROD,
						NUMORIGINAL,
						DESCRICAO,
						MARCA,
						QTESTOQUE,
						QTPEDIDO,
						QTAVARIA,
						QTCONFERIDA
					) VALUES (
						".$IDINVENTARIO.",
						SYSDATE,
						'".$produto['CODPROD']."',
						'".$produto['NUMORIGINAL']."',
						'".$produto['DESCRICAO']."',
						'".$produto['MARCA']."',
						'".$produto['QTESTOQUE']."',
						'".$produto['QTPEDIDO']."',
						'".$produto['QTAVARIA']."',
						'0'
					)";
	// varDump2($produto);
	// varDump2($sql);
	return executarOracle($sql);
}

function removeProdutoInventario($produto, $IDINVENTARIO){
	$sql = "DELETE FROM OMGINVENTARIOI WHERE IDINVENTARIO = ".$IDINVENTARIO." AND CODPROD = ".$produto['CODPROD'];
	// varDump2($produto);
	// varDump2($sql);
	return executarOracle($sql);
}

function buscaExtrato($CODPROD){
	$sql = "SELECT *  FROM VIEW_OMEGA_EXTRATOPRODUTO B
			 WHERE B.CODPROD = ".$CODPROD."
			 ORDER BY B.SEQMOV, B.DTMOVLOG ASC";
	// varDump2($sql);
	if ( $ret = selectOracle($sql) ){
		return $ret;
	} else {
		return [];
	}
}


function buscaPedidosPendentesCODPROD(int $CODPROD) {
	$sql = "SELECT C.DATA,
			       C.NUMPED,
			       DECODE(C.POSICAO,
			              'F',
			              'FATURADO',
			              'P',
			              'PENDENTE',
			              'M',
			              'MONTADO',
			              'L',
			              'LIBERADO',
			              'B',
			              'BLOQUEADO',
			              'R',
			              'REJEITADO',
			              C.POSICAO) POSICAO,
			       C.CODCLI,
			       TRIM(CL.CLIENTE) AS CLIENTE,
			       C.CODUSUR,
			       REGEXP_REPLACE(REPLACE(REPLACE(U.NOME, ', ', ','), ' ,', ','),
			                      '[[:blank:]][^,]+',
			                      '') AS VENDEDOR,
			       I.QT,
			       I.PVENDA
			  FROM PCPEDC C, PCPEDI I, PCPRODUT P, PCCLIENT CL, PCUSUARI U
			 WHERE C.NUMPED = I.NUMPED
			   AND I.CODPROD = P.CODPROD
			   AND C.CODCLI = CL.CODCLI
			   AND C.CODUSUR = U.CODUSUR
			   AND C.DTCANCEL IS NULL
			   AND C.POSICAO NOT IN ('F')
			   AND I.CODPROD = {$CODPROD}
			 ORDER BY C.DATA DESC";
	return selectOracle($sql);
}

function checkout_pesquisar($dados){
	$sql = "SELECT   l.idorcamento,
					         l.numped,
					         l.datapedido,
					         l.codusur,
					         l.vendedor,
					         l.codcli,
					         l.cliente,
					         l.idcheckout,
					         l.idusurconferente,
					         l.conferente,
					         l.dtinicio,
					         l.dtfim,
					         l.dtemtransito,
					         l.statuscheckout,
					         l.posicao,
					         l.clientebalcao,
					         l.separarpedido,
					         l.previsao,
					         l.dtprevisao,
					         l.espera
					  FROM   view_portal_pedidos_logistica l
					  WHERE 1=1";

	switch ($dados['FATURAMENTO']) {
		case 'NAOFATURADOS':
			$sql .= PHP_EOL." AND l.posicao <> 'FATURADO'";
			break;
		case 'FATURADOS':
			$sql .= PHP_EOL." AND l.posicao = 'FATURADO'";
			break;
	}

	switch ($dados['STATUSCHECKOUT']) {
		case 'FATURADOS':
			$sql .= PHP_EOL." AND l.posicao = 'FATURADO'";
			break;
		case 'NAOFINALIZADO':
			$sql .= PHP_EOL." AND l.dtemtransito IS NULL AND l.dtfim IS NULL";
			break;
		case 'EM TRANSITO':
			$sql .= PHP_EOL." AND l.dtemtransito IS NOT NULL AND l.dtfim IS NULL";
			break;
	}
		
	if ($dados['DATAINI'] <> "") {
		$sql .= PHP_EOL." AND TRUNC(l.datapedido) >= ".formataDataBRtoOracle($dados['DATAINI'])."";
	}
	if ($dados['DATAFIM'] <> "") {
		$sql .= PHP_EOL." AND TRUNC(l.datapedido) <= ".formataDataBRtoOracle($dados['DATAFIM'])."";
	}
	$sql .= PHP_EOL."  ORDER BY l.datapedido, l.numped ASC";
	// varDump2($dados);
	// varDump2($sql);
	$ret = selectOracle($sql);
	// varDump2($ret);
	// Die();
	return $ret;
}

function decodePosicao($posicao){
	if ($posicao == "") {
		return $posicao;
	} else {
		switch ($posicao) {
			case 'FATURADO':
				return '<span class="badge rounded-pill text-md bg-success">FATURADO</span>';
				break;
			case 'LIBERADO':
				return '<span class="badge rounded-pill text-md bg-primary">LIBERADO</span>';
				break;
			case 'MONTADO':
				return '<span class="badge rounded-pill text-md bg-primary">MONTADO</span>';
				break;
			case 'BLOQUEADO':
				return '<span class="badge rounded-pill text-md bg-danger">BLOQUEADO</span>';
				break;
			case 'CANCELADO':
				return '<span class="badge rounded-pill text-md bg-danger">CANCELADO</span>';
				break;
			default:
				return '<span class="badge rounded-pill text-md bg-secondary">'.$posicao.'</span>';
				break;
		}
	}
}

function decodeStatusLogistica($STATUSCHECKOUT){
	switch ($STATUSCHECKOUT) {
		case 'FINALIZADO':
			return '<span class="badge rounded-pill text-md bg-success">FINALIZADO</span>';
			break;
		case 'TRANSITO':
			return '<span class="badge rounded-pill text-md bg-warning">EM TRANSITO</span>';
			break;
		case 'SEPARANDO':
			return '<span class="badge rounded-pill text-md bg-primary">SEPARANDO</span>';
			break;
		case 'PENDENTE':
			return '<span class="badge rounded-pill text-md bg-secondary">PENDENTE</span>';
			break;
		default:
			return '<span class="badge rounded-pill text-md bg-secondary">'.$STATUSCHECKOUT.'</span>';
			break;
	}
}

function decodeClienteBalcao($CLIENTEBALCAO){
	switch ($CLIENTEBALCAO) {
		case 'SIM':
			return '<span class="badge rounded-pill text-md bg-success">SIM</span>';
			break;
		default:
			return '<span class="badge rounded-pill text-md bg-secondary">NÃO</span>';
			break;
	}
}

function decodeSepararPedido($separarPedido){
	switch ($separarPedido) {
		case 'SIM':
			return '<span class="badge rounded-pill text-md bg-success">SIM</span>';
			break;
		default:
			return '<span class="badge rounded-pill text-md bg-secondary">NÃO</span>';
			break;
	}
}

function buscaItensSeparacao($NUMPED){
	$sql = "SELECT P.CODPROD,
			 P.DV,
			 P.NUMORIGINAL,
			 P.DESCRICAO,
			 M.MARCA,
			 P.INFORMACOESTECNICAS AS LOCACAO,
			 NVL(I.QT,0) AS QTPEDIDA,
			 NVL(I.QTSEPARADA,0) AS QTSEPARADA
	FROM PCPEDI I, PCPRODUT P, PCMARCA M
 WHERE I.CODPROD = P.CODPROD
	 AND P.CODMARCA = M.CODMARCA
	 AND I.NUMPED = ".$NUMPED;
	return selectOracle($sql);
}

function buscaOMGCHECKOUTC($IDCHECKOUT){
	$sql = "SELECT C.*, CLI.cliente
			FROM OMGCHECKOUTC C , PCCLIENT CLI
			WHERE C.codcli = CLI.codcli
			  AND C.IDCHECKOUT = {$IDCHECKOUT}";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return reset($ret);
}

function buscaOMGCHECKOUTI($IDCHECKOUT){
	$sql = "SELECT * FROM OMGCHECKOUTI I WHERE I.IDCHECKOUT = {$IDCHECKOUT}";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret;
}




function checkout_iniciar($dados){
	$sql = "INSERT INTO OMGCHECKOUTC (IDCHECKOUT, NUMPED, NUMPEDCLI, NUMNOTA, DTPEDIDO, CODUSUR, CODCLI, IDUSURCONFERENTE) 
	  SELECT DISTINCT (SELECT NVL(MAX(C1.IDCHECKOUT),0)+1 FROM OMGCHECKOUTC C1) AS IDCHECKOUT,
			 C.NUMPED,
			 C.NUMPEDCLI,
			 C.NUMNOTA,
			 C.DATA AS DTPEDIDO,
			 C.CODUSUR,
			 C.CODCLI,
			 '".$_SESSION['login']['IDUSUARIO']."' AS IDUSURCONFERENTE
		FROM PCPEDC C, PCCLIENT CL
	 WHERE C.CODCLI = CL.CODCLI
		 AND C.DTCANCEL IS NULL
		 AND C.NUMPED = '".$dados['NUMPED']."'";
	// varDump2($sql); 
	// die();
	if(executarOracle($sql)){
		$sql = "SELECT IDCHECKOUT FROM OMGCHECKOUTC WHERE NUMPED = '".$dados['NUMPED']."'";
		if ($ret = selectOracle($sql) ){
			$IDCHECKOUT = $ret[0]['IDCHECKOUT'];
			// varDump2("IDCHECKOUT: {$IDCHECKOUT}");

			$sql = "INSERT INTO OMGCHECKOUTI (IDCHECKOUTI, IDCHECKOUT, NUMPED, CODPROD, DV, NUMORIGINAL, DESCRICAO, MARCA,LOCACAO, QTPEDIDA)
					  SELECT (SELECT NVL(MAX(IDCHECKOUTI), 0) + 1 FROM OMGCHECKOUTI) AS IDCHECKOUTI,
					         {$IDCHECKOUT} AS IDCHECKOUT,
					         I.NUMPED,
					         P.CODPROD,
					         P.DV,
					         TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
					         TRIM(P.DESCRICAO) AS DESCRICAO,
					         TRIM(M.MARCA) AS MARCA,
					         TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
					         SUM(NVL(I.QT, 0)) AS QTPEDIDA
					    FROM PCPEDI I, PCPRODUT P, PCMARCA M
					   WHERE I.CODPROD = P.CODPROD
					     AND P.CODMARCA = M.CODMARCA(+)
					     AND I.NUMPED = '".$dados['NUMPED']."'
					     AND P.CODPROD NOT IN (SELECT OI.CODPROD
					                             FROM OMGCHECKOUTI OI
					                            WHERE OI.NUMPED = '".$dados['NUMPED']."'
					                              AND OI.IDCHECKOUT = ".$IDCHECKOUT.")
					   GROUP BY I.NUMPED,
					            P.CODPROD,
					            P.DV,
					            TRIM(P.NUMORIGINAL),
					            TRIM(P.DESCRICAO),
					            TRIM(M.MARCA),
					            TRIM(P.INFORMACOESTECNICAS)
					   ORDER BY TRIM(P.INFORMACOESTECNICAS) ASC";
			executarOracle($sql);
			// varDump2($sql);
			// die();

			return $IDCHECKOUT;
		}
	}
}

function buscaItensPCPEDI($NUMPED){
		$sql = "SELECT I.NUMPED,
					 P.CODPROD,
					 P.DV,
					 P.NUMORIGINAL,
					 P.DESCRICAO,
					 M.MARCA,
					 P.INFORMACOESTECNICAS AS LOCACAO,
					 SUM(I.QT) AS QTPEDIDA
			FROM PCPEDI I, PCPRODUT P, PCMARCA M
		 WHERE I.CODPROD = P.CODPROD
			 AND P.CODMARCA = M.CODMARCA
			 AND I.NUMPED = ".$NUMPED."
		 GROUP BY I.NUMPED,
				P.CODPROD,
				P.DV,
				P.NUMORIGINAL,
				P.DESCRICAO,
				M.MARCA,
				P.INFORMACOESTECNICAS";
		$ret = selectOracle($sql);
		// varDump2($dados);
		// varDump2($sql);
		// varDump2($ret);
		// die();
		return $ret;
}


/*function pesquisaProduto($dados){
	// varDump2($dados);

	if (!empty($dados['CODPECA'])) {
		$CODPECA = mb_strtoupper(trim($dados['CODPECA']), 'UTF-8');

		$sql = "WITH
			EST AS (
			    SELECT CODPROD, MAX(QTRESERV) AS QTRESERVADA
			    FROM PCEST
			    WHERE CODFILIAL = 1
			    GROUP BY CODPROD
			),
			VENDA AS (
			    SELECT CODPROD, MAX(PVENDA) AS PVENDA
			    FROM PCTABPR
			    WHERE NUMREGIAO = 1
			    GROUP BY CODPROD
			),
			BASE AS (
			    SELECT
			        P.CODPROD,
			        P.DV,
			        P.NUMORIGINAL,
			        P.DESCRICAO,
			        P.INFORMACOESTECNICAS,
			        M.MARCA
			    FROM PCPRODUT P
			    LEFT JOIN PCMARCA M ON M.CODMARCA = P.CODMARCA
			    WHERE P.DTEXCLUSAO IS NULL
			),
			VIDES AS (
			    SELECT
			        P.CODPROD,
			        MAX(V.VIDE) AS VIDE
			    FROM BASE P
			    JOIN ORCVIDE V
			      ON (P.NUMORIGINAL = V.VIDE OR P.NUMORIGINAL = V.CODPECA)
			    WHERE V.DTEXCLUSAO IS NULL
			      AND (V.CODPECA LIKE '{$CODPECA}' OR V.VIDE LIKE '{$CODPECA}')
			    GROUP BY P.CODPROD
			)
			SELECT
			    B.CODPROD,
			    B.DV,
			    TRIM(B.NUMORIGINAL)         AS NUMORIGINAL,
			    TRIM(B.DESCRICAO)           AS DESCRICAO,
			    TRIM(B.MARCA)               AS MARCA,
			    TRIM(B.INFORMACOESTECNICAS) AS LOCACAO,
			    CASE
			        WHEN TRIM(V.VIDE) <> TRIM(B.NUMORIGINAL)
			        THEN TRIM(V.VIDE)
			        ELSE NULL
			        END                     AS VIDE,
			    ROUND(VE.PVENDA, 2)         AS PVENDA,
			    NVL(E.QTRESERVADA, 0)       AS QTRESERVADA,
			    GREATEST(
			        PKG_ESTOQUE.ESTOQUE_DISPONIVEL(B.CODPROD, 1, 'VP', TRUNC(SYSDATE)),
			        0
			    )                           AS QTSALDO,
			    1                           AS QTETIQUETA
			FROM BASE B
			LEFT JOIN EST   E  ON E.CODPROD  = B.CODPROD
			LEFT JOIN VENDA VE ON VE.CODPROD = B.CODPROD
			LEFT JOIN VIDES V  ON V.CODPROD  = B.CODPROD
			WHERE
			      B.NUMORIGINAL LIKE '{$CODPECA}%'
			   OR 'W' || B.CODPROD = '{$CODPECA}'
			   OR V.VIDE IS NOT NULL";

		return selectOracle($sql);
	}	

	if (!empty($dados['DESCRICAO'])) {
		$dados['DESCRICAO'] = mb_strtoupper(trim($dados['DESCRICAO']), 'UTF-8');
		$dados['DESCRICAO'] = str_replace(" ", "%", $dados['DESCRICAO']);
		$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
				       TRUNC(P.DV) AS DV,
				       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
				       TRIM(P.DESCRICAO) AS DESCRICAO,
				       TRIM(NVL(M.MARCA,P.MARCA)) AS MARCA,
				       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
				       NULL AS VIDE,
				       (SELECT NVL(MAX(QTRESERV), 0)
				          FROM PCEST
				         WHERE PCEST.CODPROD = P.CODPROD
				           AND CODFILIAL = 1) AS QTRESERVADA,
				       (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
				                                                       1,
				                                                       'VP',
				                                                       TRUNC(SYSDATE)),
				                        0)
				          from dual) AS QTSALDO,
				       1 AS QTETIQUETA
				  FROM PCPRODUT P, PCMARCA M
				 WHERE P.CODMARCA = M.CODMARCA(+)
				   AND P.DTEXCLUSAO IS NULL
				   AND (P.DESCRICAO LIKE '%{$dados['DESCRICAO']}%' OR TRIM(NVL(M.MARCA,P.MARCA)) LIKE '%{$dados['DESCRICAO']}%')";
		return selectOracle($sql);
	}

	if (!empty($dados['LOCACAO'])) {
		$dados['LOCACAO'] = mb_strtoupper(trim($dados['LOCACAO']), 'UTF-8');
		$dados['LOCACAO'] = str_replace(" ", "%", $dados['LOCACAO']);
		$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
				       TRUNC(P.DV) AS DV,
				       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
				       TRIM(P.DESCRICAO) AS DESCRICAO,
				       TRIM(M.MARCA) AS MARCA,
				       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
				       NULL AS VIDE,
				       (SELECT NVL(MAX(QTRESERV), 0)
				          FROM PCEST
				         WHERE PCEST.CODPROD = P.CODPROD
				           AND CODFILIAL = 1) AS QTRESERVADA,
				       (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
				                                                       1,
				                                                       'VP',
				                                                       TRUNC(SYSDATE)),
				                        0)
				          from dual) AS QTSALDO,
				       1 AS QTETIQUETA
				  FROM PCPRODUT P, PCMARCA M
				 WHERE P.CODMARCA = M.CODMARCA(+)
				   AND P.DTEXCLUSAO IS NULL
				   AND TRIM(P.INFORMACOESTECNICAS) LIKE '{$dados['LOCACAO']}%'";
		return selectOracle($sql);
	}


	if (!empty($dados['NUMNOTA'])) {
		$dados['NUMNOTA'] = intval($dados['NUMNOTA']);
		$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
			       TRUNC(P.DV) AS DV,
			       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
			       TRIM(P.DESCRICAO) AS DESCRICAO,
			       TRIM(MA.MARCA) AS MARCA,
			       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
			       NULL AS VIDE,
			       (SELECT NVL(MAX(QTRESERV), 0)
			          FROM PCEST
			         WHERE PCEST.CODPROD = P.CODPROD
			           AND PCEST.CODFILIAL = 1) AS QTRESERVADA,
			       (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
			                                                       1,
			                                                       'VP',
			                                                       TRUNC(SYSDATE)),
			                        0)
			          from dual) AS QTSALDO,
			       M.QT AS QTETIQUETA
			  FROM PCPRODUT P, PCMARCA MA, PCMOV M
			 WHERE P.CODMARCA = MA.CODMARCA
			   AND P.DTEXCLUSAO IS NULL
			   AND P.CODPROD = M.CODPROD   
			   AND M.CODOPER LIKE 'E%'
			   AND M.DTCANCEL IS NULL
			   AND M.NUMNOTA = {$dados['NUMNOTA']}";

		return selectOracle($sql);
	}


	if (!empty($dados['CODFAB'])) {
		$dados['CODFAB'] = mb_strtoupper(trim($dados['CODFAB']), 'UTF-8');
		$dados['CODFAB'] = str_replace(" ", "%", $dados['CODFAB']);
		$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
				       TRUNC(P.DV) AS DV,
				       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
				       TRIM(P.DESCRICAO) AS DESCRICAO,
				       TRIM(MA.MARCA) AS MARCA,
				       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
				       NULL AS VIDE,
				       (SELECT NVL(MAX(QTRESERV), 0)
				          FROM PCEST
				         WHERE PCEST.CODPROD = P.CODPROD
				           AND PCEST.CODFILIAL = 1) AS QTRESERVADA,
				       (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
				                                                       1,
				                                                       'VP',
				                                                       TRUNC(SYSDATE)),
				                        0)
				          from dual) AS QTSALDO,
				       1 AS QTETIQUETA
				  FROM PCPRODUT P, PCMARCA MA
				 WHERE P.CODMARCA = MA.CODMARCA
				   AND P.DTEXCLUSAO IS NULL
				   AND P.CODFAB = '{$dados['CODFAB']}'
				UNION
				SELECT TRUNC(P.CODPROD) AS CODPROD,
				       TRUNC(P.DV) AS DV,
				       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
				       TRIM(P.DESCRICAO) AS DESCRICAO,
				       TRIM(MA.MARCA) AS MARCA,
				       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
				       NULL AS VIDE,
				       (SELECT NVL(MAX(QTRESERV), 0)
				          FROM PCEST
				         WHERE PCEST.CODPROD = P.CODPROD
				           AND PCEST.CODFILIAL = 1) AS QTRESERVADA,
				       (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
				                                                       1,
				                                                       'VP',
				                                                       TRUNC(SYSDATE)),
				                        0)
				          from dual) AS QTSALDO,
				       1 AS QTETIQUETA
				  FROM PCPRODUT P, PCMARCA MA
				 WHERE P.CODMARCA = MA.CODMARCA
				   AND P.DTEXCLUSAO IS NULL
				   AND P.CODPROD IN
				       (SELECT CF.CODPROD FROM PCCODFABRICA CF WHERE CF.CODFAB LIKE '{$dados['CODFAB']}')";
		// varDump2($sql);

		return selectOracle($sql);
	}

	insereModal("danger", "Nenum filtro informado para pesquisa!");
	return false;
}*/


function pesquisaProduto($dados) {
    $filtroSql = "";
    $bindValue = "";

    // 1. Validação e Preparação do Filtro (Mantendo o padrão solicitado)
    if (!empty($dados['CODPECA'])) {
        $bindValue = mb_strtoupper(trim($dados['CODPECA']), 'UTF-8');
        $filtroSql = "AND (B.NUMORIGINAL LIKE '{$bindValue}%' OR 'W' || B.CODPROD = '{$bindValue}' OR V.VIDE IS NOT NULL)";
    } 
    elseif (!empty($dados['DESCRICAO'])) {
        $bindValue = mb_strtoupper(trim($dados['DESCRICAO']), 'UTF-8');
        $bindValue = str_replace(" ", "%", $bindValue);
        $filtroSql = "AND (B.DESCRICAO LIKE '%{$bindValue}%' OR B.MARCA LIKE '%{$bindValue}%')";
    } 
    elseif (!empty($dados['LOCACAO'])) {
        $bindValue = mb_strtoupper(trim($dados['LOCACAO']), 'UTF-8');
        $filtroSql = "AND B.INFORMACOESTECNICAS LIKE '{$bindValue}%'";
    } 
    elseif (!empty($dados['CODFAB'])) {
        $bindValue = mb_strtoupper(trim($dados['CODFAB']), 'UTF-8');
        $filtroSql = "AND (B.CODFAB = '{$bindValue}' OR B.CODPROD IN (SELECT CF.CODPROD FROM PCCODFABRICA CF WHERE CF.CODFAB LIKE '{$bindValue}'))";
    } 
    elseif (!empty($dados['NUMNOTA'])) {
        $numnota = intval($dados['NUMNOTA']);
        // Filtro especial para Nota Fiscal via JOIN na query principal ou subquery
        $filtroSql = "AND EXISTS (SELECT 1 FROM PCMOV M WHERE M.CODPROD = B.CODPROD AND M.NUMNOTA = {$numnota} AND M.DTCANCEL IS NULL AND M.CODOPER LIKE 'E%')";
    } 
    else {
        insereModal("danger", "Nenhum filtro informado para pesquisa!");
        return false;
    }

    // 2. Query Unificada com Performance Oracle (CTEs)
    $sql = "WITH
        EST AS (
            SELECT CODPROD, MAX(QTRESERV) AS QTRESERVADA
            FROM PCEST
            WHERE CODFILIAL = 1
            GROUP BY CODPROD
        ),
        VENDA AS (
            SELECT CODPROD, MAX(PVENDA) AS PVENDA
            FROM PCTABPR
            WHERE NUMREGIAO = 1
            GROUP BY CODPROD
        ),
        BASE AS (
            SELECT
                P.CODPROD, P.DV, P.NUMORIGINAL, P.DESCRICAO,
                P.INFORMACOESTECNICAS, P.CODFAB, M.MARCA
            FROM PCPRODUT P
            LEFT JOIN PCMARCA M ON M.CODMARCA = P.CODMARCA
            WHERE P.DTEXCLUSAO IS NULL
        ),
        VIDES AS (
            SELECT P.CODPROD, MAX(V.VIDE) AS VIDE
            FROM BASE P
            JOIN ORCVIDE V ON (P.NUMORIGINAL = V.VIDE OR P.NUMORIGINAL = V.CODPECA)
            WHERE V.DTEXCLUSAO IS NULL
              AND (V.CODPECA LIKE '{$bindValue}' OR V.VIDE LIKE '{$bindValue}')
            GROUP BY P.CODPROD
        )
        SELECT
            B.CODPROD,
            B.DV,
            TRIM(B.NUMORIGINAL)         AS NUMORIGINAL,
            TRIM(B.DESCRICAO)           AS DESCRICAO,
            TRIM(B.MARCA)               AS MARCA,
            TRIM(B.INFORMACOESTECNICAS) AS LOCACAO,
            CASE 
                WHEN V.VIDE IS NOT NULL AND TRIM(V.VIDE) <> TRIM(B.NUMORIGINAL) 
                THEN TRIM(V.VIDE) 
                ELSE NULL 
            END                         AS VIDE,
            ROUND(NVL(VE.PVENDA, 0), 2)  AS PVENDA,
            NVL(E.QTRESERVADA, 0)       AS QTRESERVADA,
            GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(B.CODPROD, 1, 'VP', TRUNC(SYSDATE)), 0) AS QTSALDO,
            1                           AS QTETIQUETA
        FROM BASE B
        LEFT JOIN EST   E  ON E.CODPROD  = B.CODPROD
        LEFT JOIN VENDA VE ON VE.CODPROD = B.CODPROD
        LEFT JOIN VIDES V  ON V.CODPROD  = B.CODPROD
        WHERE 1=1 
        {$filtroSql}
        ORDER BY B.DESCRICAO";

    return selectOracle($sql);
}


function pesquisaProdutoNF($dados){
	// varDump2($dados);

	$sql = "SELECT base.numnota,
		           base.numtransent,
		           base.codprod,
		           base.dv,
		           base.numoriginal,
		           base.descricao,
		           base.marca,
		           base.locacao,
		           SUM (base.qtpedida) AS qtpedida,
		           MAX (base.qtsaldo) AS qtsaldo
		    FROM   (SELECT   TRUNC (pcmov.numnota) AS numnota,
		                     TRUNC (pcmov.numtransent) AS numtransent,
		                     TRUNC (pcprodut.codprod) AS codprod,
		                     TRUNC (pcprodut.dv) AS dv,
		                     TRIM (pcprodut.numoriginal) AS numoriginal,
		                     TRIM (pcprodut.descricao) AS descricao,
		                     TRIM (pcmarca.marca) AS marca,
		                     TRIM (pcprodut.informacoestecnicas) AS locacao,
		                     NVL (pcmov.qt, 0) AS qtpedida,
		                     (SELECT   GREATEST (
		                                   pkg_estoque.estoque_disponivel (
		                                       pcprodut.codprod,
		                                       1,
		                                       'VP',
		                                       TRUNC (SYSDATE)),
		                                   0)
		                        FROM   DUAL)
		                         AS qtsaldo
		              FROM   pcmov, pcprodut, pcmarca
		             WHERE       pcmov.codprod = pcprodut.codprod
		                     AND NVL (pcprodut.codmarca, 1) = pcmarca.codmarca
		                     AND pcprodut.dtexclusao IS NULL
		                     AND pcmov.dtcancel IS NULL) base
		   WHERE   base.numnota = {$dados['NUMNOTA']} AND base.numtransent = {$dados['NUMTRANSENT']}
		GROUP BY   base.numnota,
		           base.numtransent,
		           base.codprod,
		           base.dv,
		           base.numoriginal,
		           base.descricao,
		           base.marca,
		           base.locacao
		ORDER BY   base.locacao ASC";
	$ret = selectOracle($sql);
	// varDump2($dados);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret;
}

function marcaExiste($MARCA){
	$sql = "SELECT MARCA FROM PCMARCA WHERE MARCA LIKE '".$MARCA."'";
	$ret = selectOracle($sql);
	if ($ret && count($ret)>0) {
		return true;
	} else {
		return false;
	}
}

function busca_produtos_vide($CODPECA){
	$sql = "SELECT P.CODPROD, P.NUMORIGINAL, P.MARCA
						FROM PCPRODUT P
					 WHERE P.DTEXCLUSAO IS NULL
						 AND P.NUMORIGINAL IN (SELECT V1.VIDE
																		 FROM ORCVIDE V1
																		WHERE V1.VIDE IS NOT NULL
																			AND V1.VIDE NOT IN ('OPCAO', 'INFO')
																			AND V1.CODPECA LIKE '".$CODPECA."%')
					UNION 
					SELECT P.CODPROD, P.NUMORIGINAL, P.MARCA
						FROM PCPRODUT P
					 WHERE P.DTEXCLUSAO IS NULL
						 AND P.NUMORIGINAL IN (SELECT V1.CODPECA
																		 FROM ORCVIDE V1
																		WHERE V1.CODPECA IS NOT NULL
																			AND V1.VIDE NOT IN ('OPCAO', 'INFO')
																			AND V1.VIDE LIKE '".$CODPECA."%')";
	$lista = false;
	if( $ret = selectOracle($sql) ){
		$lista = "";
		foreach ($ret as $key => $value) {
			$lista .= $value['CODPROD'].", ";
		}
		$lista = substr($lista, 0, -2);
	}
	return $lista;

}

function pesquisaNota($dados){
	$sql = "SELECT DISTINCT M.DTMOV, M.NUMNOTA, M.NUMTRANSENT, M.CODFORNEC, F.FORNECEDOR
					FROM PCMOV M, PCFORNEC F
					WHERE M.CODFORNEC = F.CODFORNEC";
	if ($dados['NUMNOTA'] <> "") {
		$sql .= "AND (M.NUMNOTA = ".$dados['NUMNOTA']." OR M.NUMTRANSENT = ".$dados['NUMNOTA']." OR M.CODFORNEC = ".$dados['NUMNOTA'].")";
	} else {
		$sql .= " AND trunc(DTMOV) >= to_date('".$dados['DATAINI']."', 'DD/MM/YYYY')";
		$sql .= " AND trunc(DTMOV) <= to_date('".$dados['DATAFIM']."', 'DD/MM/YYYY')";
	}
	$sql .= " ORDER BY M.DTMOV DESC";
	$ret = selectOracle($sql);
	// varDump2($dados);
	varDump2($sql);
	// varDump2($ret);
	return $ret;
}

function pesquisaNotaEntrada($dados){
	$sql = "SELECT DISTINCT M.DTMOV,
								M.NUMNOTA,
								M.NUMTRANSENT,
								M.NUMNOTADEV,
								M.CODFILIAL,
								DECODE(M.CODOPER,
											 'EA',
											 'Ent Ajuste de mercadoria',
											 'EB',
											 'Ent Bonificação',
											 'EC',
											 'Ent Consignação',
											 'ED',
											 'Ent Devolução Cliente',
											 'EF',
											 'Ent comodato',
											 'EG',
											 'Ent Beneficiamento',
											 'EI',
											 'Ent Inventário',
											 'EL',
											 'Ent Perca de mercadoria',
											 'EM',
											 'Ent Material de Consumo',
											 'EN',
											 'Ent Devol Consignada',
											 'EO',
											 'Ent Devol Comodato',
											 'EP',
											 'Ent Estorno produção',
											 'ER',
											 'Ent Simples remessa',
											 'ES',
											 'Ent Sobra de merc',
											 'ET',
											 'Ent Transferência merc',
											 'EV',
											 'Ent Devol remessa beneficiamento',
											 'E1',
											 'Ent avaria',
											 'Ex',
											 'Ent avulsa',
											 'Entrada Mercadoria') AS OPERACAO,
								DECODE(M.CODOPER, 'ED', M.CODCLI, M.CODFORNEC) AS CODFORNEC,
								DECODE(M.CODOPER, 'ED', C.CLIENTE, F.FORNECEDOR) AS FORNECEDOR,
								NVL((SELECT MAX(DECODE(CI.DTFIM, NULL, 'INCOMPLETO', 'FINALIZADO'))
									 FROM OMGCHECKINC CI
									WHERE CI.NUMNOTA = M.NUMNOTA
										AND CI.NUMTRANSENT = M.NUMTRANSENT
									), 'PENDENTE') AS STATUS_CHECKIN,
								(SELECT MAX(U.NOME)
									 FROM OMGCHECKINC CI, ORCUSUARIO U
									WHERE CI.IDUSURCONFERENTE = U.IDUSUARIO
										AND CI.NUMNOTA = M.NUMNOTA
										AND CI.NUMTRANSENT = M.NUMTRANSENT) AS CONFERENTE
	FROM PCMOV M, PCFORNEC F, PCCLIENT C
 WHERE (M.CODFORNEC = F.CODFORNEC (+) AND F.REVENDA = 'S')
	 AND M.CODCLI = C.CODCLI(+)
	 AND M.CODOPER LIKE 'E%'
	 AND M.DTCANCEL IS NULL";

	if ($dados['NUMNOTA'] <> "") {
		$sql .= PHP_EOL . " AND (M.NUMNOTA LIKE '".$dados['NUMNOTA']."' OR M.NUMTRANSENT LIKE '".$dados['NUMNOTA']."' )";
	} else {
		$sql .= PHP_EOL . " AND TRUNC(M.DTMOV) >= TO_DATE('".$dados['DATAINI']."', 'DD/MM/YYYY') ";
		$sql .= PHP_EOL . " AND TRUNC(M.DTMOV) <= TO_DATE('".$dados['DATAFIM']."', 'DD/MM/YYYY') ";
	} 

	if ($dados['CODFORNEC'] <> "") {
		$sql .= PHP_EOL . " AND DECODE(M.CODOPER, 'ED', C.CODCLI, F.CODFORNEC) = '".$dados['CODFORNEC']."' ";
	}

	if ($dados['FILTRO'] == "NUMNOTADEV") {
		$sql .= PHP_EOL . " AND M.CODOPER = 'ED'";
	} else {
		$sql .= PHP_EOL . " AND M.CODOPER <> 'ED'";
	}

	$sql .= PHP_EOL . " ORDER BY M.NUMTRANSENT";
	$ret = selectOracle($sql);
	// varDump2($dados);
	// varDump2($sql);
	// varDump2($ret);
	if ($ret) {
		if ($dados['NUMNOTA'] == "") {
			foreach ($ret as $key => $value) {
				if ($dados['STATUSCHECKIN']=='FINALIZADO' && $value['STATUS_CHECKIN'] <> 'FINALIZADO') {
					unset($ret[$key]);
				}
				if ($dados['STATUSCHECKIN']=='NAOFINALIZADO' && $value['STATUS_CHECKIN'] == 'FINALIZADO') {
					unset($ret[$key]);
				}
			}
		}
		return $ret;
	} else {
		return false;
	}
}

function buscaCabecalhoNFEntrada($dados){
	$sql = "SELECT DISTINCT F.CODFORNEC, F.FORNECEDOR, M.NUMNOTA, M.NUMTRANSENT, M.DTMOV
	FROM PCMOV M, PCFORNEC F
 WHERE M.CODFORNEC = F.CODFORNEC
	 AND M.CODOPER like 'E%'
	 AND M.DTCANCEL IS NULL
	 AND M.NUMNOTA = ".$dados['NUMNOTA']."
	 AND M.NUMTRANSENT = ".$dados['NUMTRANSENT'];
	 // varDump2($sql);
	 $ret = selectOracle($sql);
   return reset($ret);
}

function buscaProdutosNF($dados){
	$sql = "SELECT M.NUMPED,
			 M.NUMNOTA,
			 M.NUMTRANSENT,
			 M.CODFORNEC,
       		 NVL((SELECT MAX(CF.CODFAB) FROM PCCODFABRICA CF WHERE CF.CODPROD = P.CODPROD AND CF.CODFORNEC = M.CODFORNEC), P.CODFAB) AS CODFAB,
			 'W' || TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS CODPROD,
			 P.NUMORIGINAL,
			 P.DESCRICAO,
			 MA.MARCA,
			 P.INFORMACOESTECNICAS AS LOCACAO,
			 SUM(M.QT) AS QTPEDIDA,
			 (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
																																 1,
																																 'VP',
																																 TRUNC(SYSDATE)),
																	0)
										from dual) AS QTSALDO
	FROM PCMOV M, PCPRODUT P, PCMARCA MA
 WHERE M.CODPROD = P.CODPROD
	 AND P.CODMARCA = MA.CODMARCA (+)
	 AND M.CODOPER LIKE 'E%'
	 AND M.DTCANCEL IS NULL
	 AND M.NUMNOTA = '".$dados['NUMNOTA']."'
	 AND M.NUMTRANSENT = '".$dados['NUMTRANSENT']."'
 GROUP BY M.NUMPED,
		M.NUMNOTA,
		M.NUMTRANSENT,
		M.CODFORNEC,
		P.CODPROD,
		P.DV,
		P.CODFAB,
		P.NUMORIGINAL,
		P.DESCRICAO,
		MA.MARCA,
		P.INFORMACOESTECNICAS
 ORDER BY P.INFORMACOESTECNICAS ASC";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret;
}

function buscaDadosCheckinID (int $idCheckin = 0): array|false {
	
	if($idCheckin <= 0) {
		insereModal("danger", "ERRo ao executar buscaDadosCheckinID. IDCHECKIN inválido.");
		return false;
	}

	$sql = "SELECT * FROM OMGCHECKINC WHERE IDCHECKIN = $idCheckin";
  $ret = selectOracle($sql);
  $cab = reset($ret);

	$sql = "SELECT  i.idcheckin,
					i.idcheckini,
					p.codprod,
					p.dv,
					p.numoriginal,
					p.descricao,
					p.marca,
					p.informacoestecnicas AS locacao,
					p.codfab,
					i.qtpedida,
					i.qtconferida,
					GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(p.codprod, 1, 'VP', TRUNC(SYSDATE)), 0) AS qtsaldo,	
					i.observacao,
					i.dataconferencia,
					i.numseqped
				FROM       omgcheckini i
					JOIN
						pcprodut p
					ON i.codprod = p.codprod
			WHERE   i.idcheckin = $idCheckin
			ORDER BY   p.informacoestecnicas";
	$itens = selectOracle($sql);

	return array("CAB" => $cab, "ITENS" => $itens);
}

function validaCheckin($dados){
	$sql = "SELECT * FROM OMGCHECKINC 
					 WHERE NUMNOTA = ".$dados['NUMNOTA']." 
						 AND NUMTRANSENT = ".$dados['NUMTRANSENT']."";
	if($ret = selectOracle($sql)){
		if (count($ret) == 0) {
			return false;
		} else {
			return true;
		}
	}
}

function checkin_defineExcluir($dados){
	if (isset($dados['IDCHECKIN']) && !empty($dados['IDCHECKIN'])) {
		$sql = "DELETE FROM OMGCHECKINI WHERE IDCHECKIN = {$dados['IDCHECKIN']}";
		if (executarOracle($sql)) {
			$sql = "DELETE FROM OMGCHECKINC WHERE IDCHECKIN = {$dados['IDCHECKIN']}";
			return executarOracle($sql);
		}
	} else {
		return false;
	}
}

function checkin_defineTransito($dados){
	$sql = "UPDATE OMGCHECKINC SET DTTRANSITO = SYSDATE, DTFIM = null WHERE IDCHECKIN = {$dados['IDCHECKIN']}";
	// varDump2($sql);
	if ($dados['IDCHECKIN'] && !empty($dados['IDCHECKIN'])) {
		return executarOracle($sql);
	} else {
		return false;
	}
}

function checkin_defineFinalizar($dados){
	$sql = "UPDATE OMGCHECKINC SET DTTRANSITO = NULL, DTFIM = SYSDATE WHERE IDCHECKIN = {$dados['IDCHECKIN']}";
	// varDump2($sql); die();
	if ($dados['IDCHECKIN'] && !empty($dados['IDCHECKIN'])) {
		return executarOracle($sql);
	} else {
		return false;
	}
}



function checkin_defineGerar($dados){
	$sql = "UPDATE OMGCHECKINC SET DTINICIO = sysdate, DTTRANSITO = NULL, DTFIM = NULL WHERE IDCHECKIN = {$dados['IDCHECKIN']}";
	// varDump2($sql); die();
	if ($dados['IDCHECKIN'] && !empty($dados['IDCHECKIN'])) {
		return executarOracle($sql);
	} else {
		return false;
	}
}

function buscaDadosProduto($codprod){
	$sql = "SELECT  TRIM (p.codprod) AS codprod,
					TRIM (p.dv) AS dv,
					TRIM (p.descricao) AS descricao,
					TRIM (p.numoriginal) AS numoriginal,
					TRIM (m.marca) AS marca,
					TRIM (p.informacoestecnicas) AS locacao,
					p.codmarca,
					p.codfab,
					p.nbm,
					p.codncmex,
					d.codepto,
					TRIM (d.descricao) AS departamento,
					s.codsec,
					TRIM(s.descricao) AS secao
			FROM           pcprodut p
					INNER JOIN pcdepto d ON p.codepto = d.codepto
					INNER JOIN pcsecao s ON p.codsec = s.codsec
					INNER JOIN pcmarca m ON p.codmarca = m.codmarca
			WHERE   codprod = $codprod";
  $ret = selectOracle($sql);
  return reset($ret);
}

function produto_atualizaDados(array $produto):bool {
	
	$matricula = (int) $_SESSION['login']['MATRICULA'] ?? 0;
	
	$descricao = sanitizeOracleString($produto['DESCRICAO']);
	$numoriginal = sanitizeOracleString($produto['NUMORIGINAL']);
	$locacao = sanitizeOracleString($produto['LOCACAO']);
	$codfab = sanitizeOracleString($produto['CODFAB']);
	$nbm = sanitizeOracleString($produto['NBM']);
	$codncmex = sanitizeOracleString($produto['CODNCMEX']);

	$codprod = (int) $produto['CODPROD'] ?? 0;
	$codmarca = (int) $produto['CODMARCA'] ?? 0;
	$codepto = (int) $produto['CODEPTO'] ?? 0;
	$codsec = (int) $produto['CODSEC'] ?? 0;
	
	if ($codprod <= 0) {
		exibeMensagem("ERRo ao executar produto_atualizaDados. CODPROD inválido");
		return false;
	}

	$sql = "UPDATE PCPRODUT 
			   SET DESCRICAO = '$descricao',
			       NUMORIGINAL = '$numoriginal',
			       INFORMACOESTECNICAS = '$locacao',
			       CODFAB = '$codfab',
			       NBM = '$nbm',
			       CODNCMEX = '$codncmex',
			       CODMARCA = $codmarca,
			       CODEPTO = $codepto,
			       CODSEC = $codsec,
				   CODUSUULTALTCOM = $matricula
			 WHERE CODPROD = $codprod";
	if (executarOracle($sql)){
		insereModal("success", "Produto atualizado com sucesso!");
		return true;
	} else {
		return false;
	}
}

###########################################################################################################

/**
 * Função Orquestradora: Garante a existência dos registros.
 * Tenta localizar ou criar OMGCHECKINC e seus respectivos itens na OMGCHECKINI.
 * @return array Retorna sempre um array com chaves 'cabecalho' e 'itens'.
 */
function validaEProcessaCheckinEntrada(array $dados): array {
    $retorno = [
        'cabecalho' => [],
        'itens'     => []
    ];

    // 1. Tenta buscar o cabeçalho existente
    $checkinCabecalho = verificaCheckinExiste($dados);

    // 2. Se não existe, tenta gerar
    if (!$checkinCabecalho) {
        $checkinCabecalho = geraNovoOMGCHECKINC($dados);
    }

    // Se após a tentativa de gerar ainda não existir (ex: nota não encontrada na PCMOV)
    if (!$checkinCabecalho) {
        if (function_exists('insereModal')) {
            insereModal('warning', 'Atenção: Não foi possível localizar ou gerar o cabeçalho. Verifique se a Nota/Transação existe na PCMOV.');
        }
        return $retorno; // Retorna array vazio, mas não FALSE
    }

    $retorno['cabecalho'] = $checkinCabecalho;
    $idCheckin = (int)$checkinCabecalho['IDCHECKIN'];
    
    // Sincroniza o ID para as próximas funções
    $dados['IDCHECKIN'] = $idCheckin;

    // 3. Verifica se existem itens
    $checkinItens = buscaOMGCHECKINI($dados);

    // 4. Se não existem itens, tenta gerar em bloco
    if (!$checkinItens || count($checkinItens) === 0) {
        $checkinItens = geraNovoOMGCHECKINI($dados);
    }

    $retorno['itens'] = $checkinItens ?: [];

    return $retorno;
}

function verificaCheckinExiste(array $dados): array|false {
    $numNota     = isset($dados['NUMNOTA']) ? (int)$dados['NUMNOTA'] : 0;
    $numTransEnt = isset($dados['NUMTRANSENT']) ? (int)$dados['NUMTRANSENT'] : 0;

    if ($numNota <= 0) return false;

    $sql = "SELECT * FROM OMGCHECKINC 
            WHERE NUMNOTA = $numNota 
              AND NUMTRANSENT = $numTransEnt
            ORDER BY IDCHECKIN DESC"; // Pega o mais recente se houver duplicidade
            
    $res = selectOracle($sql);
    return (!empty($res)) ? $res[0] : false;
}

function geraNovoOMGCHECKINC(array $dados): array|false {
    $numNota     = (int)($dados['NUMNOTA'] ?? 0);
    $numTransEnt = (int)($dados['NUMTRANSENT'] ?? 0);
    $idUsuario   = (int)($_SESSION['login']['IDUSUARIO'] ?? 0);

    // Query otimizada para buscar dados da PCMOV (Entrada) e inserir na OMGCHECKINC
    $sql = "INSERT INTO OMGCHECKINC (IDCHECKIN, NUMNOTA, NUMTRANSENT, DTMOV, CODFORNEC, FORNECEDOR, IDUSURCONFERENTE)
            SELECT (SELECT NVL(MAX(IDCHECKIN), 0) + 1 FROM OMGCHECKINC),
                   M.NUMNOTA, M.NUMTRANSENT, M.DTMOV, M.CODFORNEC, F.FORNECEDOR, $idUsuario
            FROM PCMOV M
            INNER JOIN PCFORNEC F ON M.CODFORNEC = F.CODFORNEC
            WHERE M.NUMNOTA = $numNota 
              AND M.NUMTRANSENT = $numTransEnt
              AND M.DTCANCEL IS NULL
              AND ROWNUM = 1";

    try {
        executarOracle($sql);
        return verificaCheckinExiste($dados);
    } catch (Exception $e) {
        return false;
    }
}

function geraNovoOMGCHECKINI(array $dados): array|false {
    $idCheckin   = (int)($dados['IDCHECKIN'] ?? 0);
    $numNota     = (int)($dados['NUMNOTA'] ?? 0);
    $numTransEnt = (int)($dados['NUMTRANSENT'] ?? 0);
    $idUsuario   = (int)($_SESSION['login']['IDUSUARIO'] ?? 0);

    if ($idCheckin <= 0) return false;

    // Bulk Insert buscando da PCMOV/PCPRODUT
    $sql = "INSERT INTO OMGCHECKINI (
                IDCHECKIN, IDCHECKINI, NUMNOTA, NUMTRANSENT, IDUSURCONFERENTE, 
                NUMSEQPED, CODPROD, DV, NUMORIGINAL, DESCRICAO, MARCA, LOCACAO, QTPEDIDA
            )
            SELECT 
                $idCheckin,
                (SELECT NVL(MAX(IDCHECKINI), 0) FROM OMGCHECKINI) + ROWNUM,
                M.NUMNOTA, M.NUMTRANSENT, $idUsuario, NVL(M.NUMSEQPED, 0),
                M.CODPROD, P.DV, P.NUMORIGINAL, SUBSTR(P.DESCRICAO, 1, 100),
                P.MARCA, P.OBS2, M.QT
            FROM PCMOV M
            INNER JOIN PCPRODUT P ON M.CODPROD = P.CODPROD
            WHERE M.NUMNOTA = $numNota
              AND M.NUMTRANSENT = $numTransEnt
              AND M.DTCANCEL IS NULL";

    try {
        executarOracle($sql);
        return buscaOMGCHECKINI($dados);
    } catch (Exception $e) {
        return false;
    }
}

function buscaOMGCHECKINI(array $dados): array {
    $idCheckin = (int)($dados['IDCHECKIN'] ?? 0);
    if ($idCheckin <= 0) return [];

    $sql = "SELECT I.*, P.DESCRICAO, P.CODFAB 
            FROM OMGCHECKINI I
            INNER JOIN PCPRODUT P ON I.CODPROD = P.CODPROD
            WHERE I.IDCHECKIN = $idCheckin";

    $res = selectOracle($sql);
    return is_array($res) ? $res : [];
}


function pesquisaRelatorio($dados){
	$sql = "SELECT CO.IDCHECKOUT,
								 CO.DTINICIO,
								 DECODE(CO.DTFIM, NULL, 'PENDENTE', 'FINALIZADO') AS POSICAO,
								 CO.IDUSURCONFERENTE,
								 U.NOME AS CONFERENTE,
								 CO.CODUSUR,
								 RCA.NOME AS VENDEDOR,
								 CO.CODCLI,
								 CLI.CLIENTE,
								 CO.NUMPED,
								 CO.NUMPEDCLI       
						FROM OMGCHECKOUTC CO, ORCUSUARIO U, PCUSUARI RCA, PCCLIENT CLI
					 WHERE CO.IDUSURCONFERENTE = U.IDUSUARIO
						 AND CO.CODUSUR = RCA.CODUSUR
						 AND CO.CODCLI = CLI.CODCLI
						 AND trunc(CO.DTINICIO) >= to_date('".$dados['DATAINI']."', 'DD/MM/YYYY')
						 AND trunc(CO.DTINICIO) <= to_date('".$dados['DATAFIM']."', 'DD/MM/YYYY')";
	if ($dados['POSICAO'] <> 'ALL') {
		if ($dados['POSICAO'] == 'FINALIZADOS') {
			$sql .= PHP_EOL." AND CO.DTFIM IS NOT NULL";
		} else {
			$sql .= PHP_EOL." AND CO.DTFIM IS NULL";
		}
	}
	if ($dados['CODUSUR'] <> 'ALL') {
		$sql .= PHP_EOL." ORDER BY CO.IDCHECKOUT ASC";
	}
	$sql .= PHP_EOL." ORDER BY CO.IDCHECKOUT ASC";
	
	// varDump2($dados);
	// varDump2($sql);
	return selectOracle($sql);
}

function checkin_pesquisar($dados){
	// varDump2($dados);

	if ($dados['STATUSCHECKIN'] == 'DEVOL') {
		$sql = "SELECT  pcnfent.numnota,
                   pcnfent.numtransent,
                   pcnfent.dtent,
                   pcnfent.dtemissao,
                   pcclient.codcli,
                   pcclient.cliente,
                   pcnfent.codfunclanc,
                   pcempr.nome nomefuncionario,
                   pcnfent.codfilial,
                   pcfilial.razaosocial AS filial,
                   pcmov.codoper,
                   DECODE (pcmov.codoper,
                           'E', 'ENT. MERCADORIA',
                           'ER', 'ENT. SIMPLES REMESSA',
                           'ED', 'ENT. DEVOLUçãO CLIENTE',
                           'EI', 'ENT. INVENTáRIO',
                           'EM', 'ENT. MATERIAL CONSUMO',
                           'E1', 'ENT. AVARIA',
                           'EX', 'ENT. DEVOLUçãO AVULSA',
                           pcmov.codoper)
                       AS operacao,
                   ROUND (
                       SUM( (DECODE (pcmov.status,
                                     'A', NVL (pcmov.qtcont, 0),
                                     NVL (pcmov.qt, 0)))
                           * (DECODE (NVL (pcmov.punit, 0),
                                      0, NVL (pcmov.punitcont, 0),
                                      pcmov.punit))),
                       2)
                       vltotal,
                   CASE
                       WHEN omgcheckinc.dtinicio IS NULL THEN 'PENDENTE'
                       WHEN omgcheckinc.dtfim IS NOT NULL THEN 'FINALIZADO'
                       WHEN omgcheckinc.dttransito IS NOT NULL THEN 'EM TRANSITO'
                       ELSE 'SEPARANDO'
                   END
                       AS status_checkin,
                   orcusuario.nome as conferente,
                   omgcheckinc.IDCHECKIN
            FROM   pcmov,
                   pcprodut,
                   pcfornec,
                   pcclient,
                   pcnfent,
                   pcfilial,
                   pcempr,
                   pcconsum,
                   pcprodfilial,
                   pcpedido,
                   omgcheckinc,
                   orcusuario
           WHERE       (pcmov.codprod = pcprodut.codprod)
                   AND pcmov.codfornec =pcfornec.codfornec
                   AND pcempr.matricula(+) = pcnfent.codfunclanc
                   AND pcmov.numtransent = pcnfent.numtransent
                   AND pcnfent.codfilial = pcfilial.codigo
                   AND pcmov.numnota = omgcheckinc.numnota(+)
                   AND omgcheckinc.IDUSURCONFERENTE = orcusuario.idusuario (+)
                   AND pcnfent.tipodescarga NOT IN ('7', '8', 'N', 'F')
                   AND pcmov.codoper LIKE 'ED%'
                   AND pcmov.dtcancel IS NULL
		               AND pcfornec.revenda = 'S'
		               AND pcfornec.tipofornec <> 'O'                   
                   AND (pcmov.qt > 0 OR (pcmov.qtcont > 0 AND pcnfent.tipodescarga = '4'))
                   AND pcmov.codprod = pcprodfilial.codprod
                   AND pcmov.codfilial = pcprodfilial.codfilial
                   AND pcmov.numped = pcpedido.numped(+)
                   AND pcmov.codcli = pcclient.codcli(+) ";
					if ($dados['NUMNOTA'] <> '') {
						$sql .= PHP_EOL." AND (pcnfent.numnota = '".$dados['NUMNOTA']."' OR pcnfent.numtransent = '".$dados['NUMNOTA']."')";
					} else {
						$sql .= PHP_EOL." AND trunc(pcnfent.dtent) >= to_date('".$dados['DATAINI']."', 'DD/MM/YYYY')";
						$sql .= PHP_EOL." AND trunc(pcnfent.dtent) <= to_date('".$dados['DATAFIM']."', 'DD/MM/YYYY')";
					}
					$sql .= PHP_EOL." GROUP BY   pcnfent.numnota,
						               pcnfent.numtransent,
						               pcnfent.dtent,
						               pcnfent.dtemissao,
						               pcclient.codcli,
						               pcclient.cliente,
						               pcnfent.codfunclanc,
						               pcempr.nome,
						               pcnfent.codfilial,
						               pcfilial.razaosocial,
						               pcmov.codoper,
						               omgcheckinc.dtinicio,
						               omgcheckinc.dtfim,
						               omgcheckinc.dttransito,
						               orcusuario.nome,
						               omgcheckinc.IDCHECKIN";
	} else {

		$sql = "SELECT  pcnfent.numnota,
		               pcnfent.numtransent,
		               pcnfent.dtent,
		               pcnfent.dtemissao,
		               pcnfent.codfornec,
		               pcfornec.fornecedor,
		               pcnfent.codfunclanc,
		               pcempr.nome nomefuncionario,
		               pcnfent.codfilial,
		               pcfilial.razaosocial AS filial,
		               pcmov.codoper,
		               DECODE (pcmov.codoper,
		                       'E', 'ENT. MERCADORIA',
		                       'ER', 'ENT. SIMPLES REMESSA',
		                       'ED', 'ENT. DEVOLUçãO CLIENTE',
		                       'EI', 'ENT. INVENTáRIO',
		                       'EM', 'ENT. MATERIAL CONSUMO',
		                       'E1', 'ENT. AVARIA',
		                       'EX', 'ENT. DEVOLUçãO AVULSA',
		                       pcmov.codoper)
		                   AS operacao,
		               ROUND (
		                   SUM( (DECODE (pcmov.status,
		                                 'A', NVL (pcmov.qtcont, 0),
		                                 NVL (pcmov.qt, 0)))
		                       * (DECODE (NVL (pcmov.punit, 0),
		                                  0, NVL (pcmov.punitcont, 0),
		                                  pcmov.punit))),
		                   2)
		                   vltotal,
		               CASE
		                   WHEN omgcheckinc.dtinicio IS NULL THEN 'PENDENTE'
		                   WHEN omgcheckinc.dtfim IS NOT NULL THEN 'FINALIZADO'
		                   WHEN omgcheckinc.dttransito IS NOT NULL THEN 'EM TRANSITO'
		                   ELSE 'SEPARANDO'
		               END
		                   AS status_checkin,
		               orcusuario.nome as conferente,
		               omgcheckinc.IDCHECKIN
		        FROM   pcmov,
		               pcprodut,
		               pcfornec,
		               pcnfent,
		               pcfilial,
		               pcempr,
		               pcconsum,
		               pcprodfilial,
		               pcpedido,
		               omgcheckinc,
		               orcusuario
		       WHERE       (pcmov.codprod = pcprodut.codprod)
		               AND pcempr.matricula(+) = pcnfent.codfunclanc
		               AND pcmov.numtransent = pcnfent.numtransent
		               AND pcnfent.codfilial = pcfilial.codigo
		               AND pcmov.numnota = omgcheckinc.numnota(+)
		               AND pcmov.numtransent = omgcheckinc.numtransent(+)
		               AND omgcheckinc.IDUSURCONFERENTE = orcusuario.idusuario (+)
		               AND pcnfent.tipodescarga NOT IN ('6', '7', '8', 'N', 'F')
		               AND pcmov.codoper LIKE 'E%'
		               AND pcmov.codoper <> 'EA'
		               AND pcmov.dtcancel IS NULL
		               AND pcfornec.revenda = 'S'
		               AND pcfornec.tipofornec <> 'O'
		               AND (pcmov.qt > 0
		                    OR (pcmov.qtcont > 0 AND pcnfent.tipodescarga = '4'))
		               AND pcmov.codprod = pcprodfilial.codprod
		               AND pcmov.codfilial = pcprodfilial.codfilial
		               AND pcmov.numped = pcpedido.numped(+)
		               AND pcnfent.codfornec = pcfornec.codfornec";

		if ($dados['NUMNOTA'] <> '') {
			$sql .= PHP_EOL." AND (pcnfent.numnota = '".$dados['NUMNOTA']."')";
		} else {
			$sql .= PHP_EOL." AND trunc(pcnfent.dtent) >= to_date('".$dados['DATAINI']."', 'DD/MM/YYYY')";
			$sql .= PHP_EOL." AND trunc(pcnfent.dtent) <= to_date('".$dados['DATAFIM']."', 'DD/MM/YYYY')";

			if (!empty($dados['CODFORNEC'])) {
				$sql .= PHP_EOL." AND pcnfent.codfornec = '".$dados['CODFORNEC']."'";
			}

		}


		$sql .= PHP_EOL."GROUP BY   pcnfent.numnota,
	               pcnfent.numtransent,
	               pcnfent.dtent,
	               pcnfent.dtemissao,
	               pcnfent.codfornec,
	               pcfornec.fornecedor,
	               pcnfent.codfunclanc,
	               pcempr.nome,
	               pcnfent.codfilial,
	               pcfilial.razaosocial,
	               pcmov.codoper,
	               omgcheckinc.dtinicio,
	               omgcheckinc.dtfim,
	               omgcheckinc.dttransito,
	               orcusuario.nome,
	               omgcheckinc.IDCHECKIN";

	}

	// varDump2($sql);
	$ret = selectOracle($sql);
	// varDump2($dados);
	// varDump2($ret);
	if ($ret || !empty($ret)) {
		foreach ($ret as $key => $value) {
			if ($dados['STATUSCHECKIN'] == 'NAOFINALIZADO' && $value["STATUS_CHECKIN"] == "FINALIZADO") {
				unset($ret[$key]);
			}
			if ($dados['STATUSCHECKIN'] == 'FINALIZADO' && $value["STATUS_CHECKIN"] <> "FINALIZADO") {
				unset($ret[$key]);
			}
			if ($dados['STATUSCHECKIN'] == "EM TRANSITO" && $value["STATUS_CHECKIN"] <> "EM TRANSITO") {
				unset($ret[$key]);
			}
		}
		return $ret;
	} else {
		return false;
	}

}


function confirmaConferirItemCHECKIN($dados){
	// varDump2($dados);
	$IDCHECKIN    = intval($dados['IDCHECKIN']);
	$QTCONFERIDA  = intval($dados['QTCONFERIDA']);
	$CODPROD      = intval($dados['CODPROD']);

	if (validaCodprodCHECKIN($IDCHECKIN, $CODPROD)) {
		$sql = "UPDATE OMGCHECKINI I
				   SET I.QTCONFERIDA = (I.QTCONFERIDA+{$QTCONFERIDA})
			 	 WHERE I.IDCHECKIN = {$IDCHECKIN}
				   AND I.CODPROD = {$CODPROD}";
		// varDump2($sql); die();
		executarOracle($sql);
	} else {
		insereModal('danger', 'Produto '.$CODPROD.' não está contida nesta nota fiscal');
	}
}

function validaCodprodCHECKIN($IDCHECKIN, $CODPROD){
	$sql = "SELECT I.CODPROD 
			  FROM OMGCHECKINI I
			 WHERE I.IDCHECKIN = {$IDCHECKIN}
			   AND I.CODPROD = {$CODPROD}";
	// varDump2($sql);
	$ret = selectOracle($sql);
	if ($ret && count($ret)>0) {
		return true;
	} else {
		return false;
	}
}

function insereModalEditarCheckin($dados){
	// varDump2($dados);
	echo '<div class="modal fade" id="modalEditarCheckin" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info">
				<h4 class="modal-title" id="labelmodalAlteraSenha">
					<i class="fas fa-tasks"></i> Editar Quantidade Conferida
				</h4>
			</div>

			<form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="op" value="135">
				<input type="hidden" name="IDCHECKIN" value="'.$dados['IDCHECKIN'].'">
				<input type="hidden" name="IDCHECKINI" value="'.$dados['IDCHECKINI'].'">
				<input type="hidden" name="CODPROD" value="'.$dados['CODPROD'].'">

				<div class="modal-body">
					<div class="row mb-2">
						<label for="inputPassword" class="col-sm-3 col-form-label">QT CONFERIDA</label>
						<div class="col-sm-9">
							<input type="text" name="QTCONFERIDA" class="form-control" value="'.$dados['QTCONFERIDA'].'" required>
						</div>
					</div>
					<div class="row">
						<label for="inputPassword" class="col-sm-3 col-form-label">OBSERVAÇÃO</label>
						<div class="col-sm-9">
							<input type="text" name="OBSERVACAO" class="form-control" value="'.$dados['OBSERVACAO'].'" required placeholder="Motivo do ajuste">
						</div>
					</div>
				</div>

				<div class="modal-footer d-flex justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fa fa-ban"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-success" name="acao" value="salvarEditarCheckin">
						<i class="fa fa-forward"></i> Avançar
					</button>
				</div>

			</form>

		</div>
	</div>
</div>';
}

function insereModalEditarCancelados($dados){
	// varDump2($dados);
	echo '<div class="modal fade" id="modalEditarCheckin" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info">
				<h4 class="modal-title" id="labelmodalAlteraSenha">
					<i class="fas fa-tasks"></i> Editar Quantidade Conferida
				</h4>
			</div>

			<form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="op" value="'.$dados['op'].'">
				<input type="hidden" name="NUMPED" value="'.$dados['NUMPED'].'">
				<input type="hidden" name="CODPROD" value="'.$dados['CODPROD'].'">

				<div class="modal-body">
					<div class="row mb-2">
						<label for="inputPassword" class="col-sm-3 col-form-label">QT CONFERIDA</label>
						<div class="col-sm-9">
							<input type="text" name="QTCONFERIDA" class="form-control" value="'.$dados['QTCONFERIDA'].'" required>
						</div>
					</div>
					<div class="row">
						<label for="inputPassword" class="col-sm-3 col-form-label">OBSERVAÇÃO</label>
						<div class="col-sm-9">
							<input type="text" name="OBSERVACAO" class="form-control" value="'.$dados['OBSERVACAO'].'" required placeholder="Motivo do ajuste">
						</div>
					</div>
				</div>

				<div class="modal-footer d-flex justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fa fa-ban"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-success" name="acao" value="salvarEditarCancelados">
						<i class="fa fa-forward"></i> Avançar
					</button>
				</div>

			</form>

		</div>
	</div>
</div>';
}

function buscaProdutoCHECKIN($CODPROD, $IDCHECKIN){
	if ($_SESSION['CHECKIN']['ITEM']) {
		foreach ($_SESSION['CHECKIN']['ITEM'] as $key => $value) {
			if (($value['CODPROD'] == $CODPROD) && ($value['IDCHECKIN'] == $IDCHECKIN)) {
				return $value;
				break;
			}
		}
		return false;
	} else {
		return false;
	}
}

function buscaMarcas():array { 
	$sql = "SELECT   codmarca, marca
			FROM   pcmarca
		   WHERE   ativo = 'S'
		     AND   marca is not null
		ORDER BY   marca";
    $ret = selectOracle($sql);
    return ($ret && count($ret) > 0) ? $ret : false;
}

function buscaDepartamentos():array { 
	$sql = "SELECT   d.codepto, d.descricao AS DEPARTAMENTO
				FROM   pcdepto d
			WHERE   NVL (d.ativo, 'S') = 'S'
			ORDER BY   d.descricao";
    $ret = selectOracle($sql);
    return ($ret && count($ret) > 0) ? $ret : false;
}

function buscaSecoes():array { 
	$sql = "  SELECT   min(s.codsec) as codsec, s.descricao AS SECAO
				FROM   pcsecao s
			WHERE   s.dtexclusao is null
			GROUP BY s.descricao
			ORDER BY   s.descricao";
    $ret = selectOracle($sql);
    return ($ret && count($ret) > 0) ? $ret : false;
}


function validaProdutoLocacao($dados){
	$produto = buscaDadosProduto($dados['CODPROD']);
	// varDump2($dados);
	// varDump2($produto);
	if (trim($dados['CODPROD']) == trim($produto['CODPROD']) && 
		trim($dados['LOCACAO']) == trim($produto['LOCACAO']) ) {
		return true;
	} else {
		return false;
	}

}

function salvarEditarCheckin($dados){
	$sql = "UPDATE OMGCHECKINI
			 SET QTCONFERIDA = ".$dados['QTCONFERIDA']."
				,OBSERVACAO = '".mb_strtoupper($dados['OBSERVACAO'],'UTF-8')."'
		   WHERE IDCHECKIN = ".$dados['IDCHECKIN']."
			 AND IDCHECKINI = ".$dados['IDCHECKINI']."";
	// varDump2($sql);
	executarOracle($sql);
}

function salvarEditarCancelados($dados){
	$sql = "UPDATE OMGCANCELADOS
			 SET QTCONFERIDA = ".$dados['QTCONFERIDA'].", OBSERVACAO = '".mb_strtoupper($dados['OBSERVACAO'],'UTF-8')."'
			WHERE NUMPED = ".$dados['NUMPED']." AND CODPROD = ".$dados['CODPROD']."";
	// varDump2($dados);
	// varDump2($sql);
	// executarOracle($sql);
	foreach ($_SESSION['CANCELADOS'] as $key => $value){
		if (($value['NUMPED']==$dados['NUMPED']) && ($value['CODPROD']==$dados['CODPROD'])) {
			$_SESSION['CANCELADOS'][$key]['QTCONFERIDA'] = $dados['QTCONFERIDA'];
		}
	}

}

function finalizarCHECKIN($dados){
	$erro=0;
	if ($_SESSION['CHECKIN']['ITEM']) {
		foreach ($_SESSION['CHECKIN']['ITEM'] as $key => $value) {
			if (($value['QTCONFERIDA'] <> $value['QTPEDIDA']) && ($value['OBSERVACAO'] == "")) {
				$erro++;
			}
		}
	} else {
		$erro++;
	}

	if ($erro==0) {
		$sql = "UPDATE OMGCHECKINC SET DTFIM = SYSDATE
						 WHERE IDCHECKIN = ".$dados['IDCHECKIN'];
		// varDump2($dados);
		// varDump2($sql);
		// die();
		if(executarOracle($sql)){
			exibeMensagem('Conferência finalizada com sucesso!');
		}
	} else {
		insereModal('danger', '<h4>Não foi possível finalziar o Check-in</h4><p>Foram encontradas divergências sem observação registrada.</p>');
	}
}

function checkout_resetar($dados){
	$sql = "DELETE FROM OMGCHECKOUTI WHERE IDCHECKOUT = '{$dados['IDCHECKOUT']}'";
	if (!executarOracle($sql)) {
		insereModal("danger", "ERRO ao excluir os itens do Check-out {$dados['IDCHECKOUT']}");
	} else {
		$sql = "DELETE FROM OMGCHECKOUTC WHERE IDCHECKOUT = '{$dados['IDCHECKOUT']}'";
		if (!executarOracle($sql)) {
			insereModal("danger", "ERRO ao excluir o cabeçalho do Check-out {$dados['IDCHECKOUT']}");
		} else {
			insereModal("success", "dados do Check-out {$dados['IDCHECKOUT']} resetados com sucesso!");
		}
	}
}


function conferirItemCHECKOUT($dados){
	// varDump2($dados);
	$IDCHECKOUT = $dados['IDCHECKOUT'];
	if (strripos($dados['CODPROD'], '*')) {
		$tmpQtConferida 	= explode('*', $dados['CODPROD']);
		$QTCONFERIDA 	    = reset($tmpQtConferida);
		$CODPROD 	        = end($tmpQtConferida);
	} else {
		$QTCONFERIDA 	= 1;
		$CODPROD 		= $dados['CODPROD'];
	}

	if (validaCodprodCHECKOUT($CODPROD, $IDCHECKOUT)) {
		$sql = "UPDATE OMGCHECKOUTI I
				   SET I.QTCONFERIDA = (I.QTCONFERIDA+{$QTCONFERIDA})
			     WHERE I.IDCHECKOUT = {$dados['IDCHECKOUT']}
				   AND I.CODPROD = {$CODPROD}";
		// varDump2($sql);
		executarOracle($sql);
	} else {
		insereModal('danger', 'Produto '.$CODPROD.' não está contida nesta nota fiscal');
	}
}

function validaCodprodCHECKOUT($CODPROD, $IDCHECKOUT){
	$sql = "SELECT CODPROD 
			  FROM OMGCHECKOUTI 
			 WHERE CODPROD = {$CODPROD}
			   AND IDCHECKOUT = {$IDCHECKOUT}";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	if ($ret && count($ret)>0) {
		return true;
	} else {
		return false;
	}
}



function insereModalEditarCheckout($dados){
	// varDump2($dados);
	echo '<div class="modal fade" id="modalEditarCheckout" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info">
				<h4 class="modal-title" id="labelmodalAlteraSenha">
					<i class="fas fa-tasks"></i> Editar Quantidade Conferida
				</h4>
			</div>

			<form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="op" value="137">

				<div class="modal-body">
					<div class="row mb-2">
						<label for="inputPassword" class="col-sm-3 col-form-label">IDCHECKOUT</label>
						<div class="col-sm-9">
							<input type="text" name="IDCHECKOUT" class="form-control" value="'.$dados['IDCHECKOUT'].'" readonly>
						</div>
					</div>
					<div class="row mb-2">
						<label for="inputPassword" class="col-sm-3 col-form-label">IDCHECKOUTI</label>
						<div class="col-sm-9">
							<input type="text" name="IDCHECKOUTI" class="form-control" value="'.$dados['IDCHECKOUTI'].'" readonly>
						</div>
					</div>
					<div class="row mb-2">
						<label for="inputPassword" class="col-sm-3 col-form-label">CODPROD</label>
						<div class="col-sm-9">
							<input type="text" name="CODPROD" class="form-control" value="'.$dados['CODPROD'].'" readonly>
						</div>
					</div>
					<div class="row mb-2">
						<label for="inputPassword" class="col-sm-3 col-form-label">QT CONFERIDA</label>
						<div class="col-sm-9">
							<input type="text" name="QTCONFERIDA" class="form-control" value="'.$dados['QTCONFERIDA'].'" required>
						</div>
					</div>
					<div class="row">
						<label for="inputPassword" class="col-sm-3 col-form-label">OBSERVAÇÃO</label>
						<div class="col-sm-9">
							<input type="text" name="OBSERVACAO" class="form-control" value="" required placeholder="Motivo do ajuste">
						</div>
					</div>
				</div>

				<div class="modal-footer d-flex justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fa fa-ban"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-success" name="acao" value="salvarEditarCheckout">
						<i class="fa fa-forward"></i> Avançar
					</button>
				</div>

			</form>

		</div>
	</div>
</div>';
}



function salvarEditarCheckout($dados){
	$sql = "UPDATE OMGCHECKOUTI
			   SET DTALTERACAO = SYSDATE,
				   QTCONFERIDA = ".$dados['QTCONFERIDA'].",
				   OBSERVACAO = '".mb_strtoupper($dados['OBSERVACAO'],'UTF-8')."'
			 WHERE IDCHECKOUT = ".$dados['IDCHECKOUT']."
			   AND IDCHECKOUTI = ".$dados['IDCHECKOUTI']."
			   AND CODPROD = ".$dados['CODPROD'];
	// varDump2($dados);
	// varDump2($sql); 
	// die();
	return executarOracle($sql);
}



function finalizarCHECKOUT($dados){
	$sql = "UPDATE OMGCHECKOUTC
						 SET DTFIM = SYSDATE
					 WHERE IDCHECKOUT = ".$dados['IDCHECKOUT']."
						 AND NUMPED = '".$dados['NUMPED']."'";
	// varDump2($sql); die();
	if(executarOracle($sql)){
		exibeMensagem('Conferência finalizada com sucesso!');
	}
}

function locacao_pesquisa($dados){
	if (isset($_SESSION['LOCACAO'])) unset($_SESSION['LOCACAO']);
	$_SESSION['LOCACAO'] = array();

	$sql = "SELECT TRIM(INFORMACOESTECNICAS) AS LOCACAO
			FROM PCPRODUT 
		 WHERE INFORMACOESTECNICAS IS NOT NULL
			 AND INFORMACOESTECNICAS LIKE '".mb_strtoupper(trim($dados['LOCACAO']),'UTF-8')."%'
		 GROUP BY TRIM(INFORMACOESTECNICAS)
		 ORDER BY TRIM(INFORMACOESTECNICAS) ASC";
	// varDump2($sql);

	if($ret = selectOracle($sql)){
		// varDump2($ret);
		foreach ($ret as $key => $value) {
			array_push($_SESSION['LOCACAO'], $value);
		}
	} else {
		array_push($_SESSION['LOCACAO'], array('LOCACAO' => mb_strtoupper(trim($dados['LOCACAO']),'UTF-8')));
	}
}

function salvarQTETIQUETAProduto($dados){
	$_SESSION['produtos'][$dados['key']]['QTETIQUETA'] = $dados['QTETIQUETA'];
	redireciona("index.php?op=131");
}

function buscaFornecedores(){
	$sql = "SELECT CODFORNEC,
						 TRIM(FORNECEDOR) || ' [' || TRIM(CIDADE) || ' / ' || ESTADO || ']' AS FORNECEDOR
				FROM PCFORNEC
			 WHERE REVENDA = 'S'
				 AND DTEXCLUSAO IS NULL
			 ORDER BY FORNECEDOR ASC";
	return selectOracle($sql);
}

function confirmarTransito($dados){
	$sql = "UPDATE OMGCHECKOUTC 
						 SET EMTRANSITO = 'S', DTEMTRANSITO = SYSDATE
			 WHERE IDCHECKOUT = ".$dados['IDCHECKOUT'].' AND NUMPED = '.$dados['NUMPED'];
	// varDump2($sql);
	if(executarOracle($sql) === false){
		insereModal('danger', 'ERRO ao alterar o Status do pedido!');
	} else {
		insereModal('success', 'Status do pedido alterado com sucesso!');
	}
}

function buscaClientes(){
	$sql = " SELECT CODCLI, NVL(CLIENTE, FANTASIA) AS CLIENTE
				 FROM PCCLIENT
				WHERE DTEXCLUSAO IS NULL 
					AND CLIENTE NOT LIKE '%INATIV%'
					AND CLIENTE NOT LIKE '%DESATIV%'
					AND TRIM(CLIENTE) <> '.'
					AND CLIENTE IS NOT NULL
				ORDER BY CLIENTE";
	if ($ret = selectOracle($sql)) {
		return $ret;
	} else {
		return false;
	}
}

function buscaNomeCliente($CODCLI){
	$sql = " SELECT NVL(CLIENTE, FANTASIA) AS CLIENTE
				 FROM PCCLIENT
				WHERE DTEXCLUSAO IS NULL 
					AND CLIENTE NOT LIKE '%INATIV%'
					AND CLIENTE NOT LIKE '%DESATIV%'
					AND TRIM(CLIENTE) <> '.'
					AND CLIENTE IS NOT NULL
					AND CODCLI = ".$CODCLI;
	if ($ret = selectOracle($sql)) {
		$CLIENTE = reset($ret);
		return $CLIENTE['CLIENTE'];
	} else {
		return 'COD CLIENTE INVALIDO';
	}
}


function buscaNomeFornecedor($CODFORNEC){
	$sql = " SELECT FORNECEDOR FROM PCFORNEC
						WHERE DTEXCLUSAO IS NULL 
						  AND CODFORNEC = ".$CODFORNEC;
	if($CODFORNEC == ""){
		return '';
	} else {
		if ($ret = selectOracle($sql)) {
			$FORNECEDOR = reset($ret);
			return $FORNECEDOR['FORNECEDOR'];
		} else {
			return 'COD FORNECEDOR INVALIDO';
		}
	}
}

function buscaPedVendaPendentes(){
	$sql = "SELECT   l.idorcamento,
					         l.numped,
					         l.datapedido,
					         l.codusur,
					         l.vendedor,
					         l.codcli,
					         l.cliente,
					         l.idcheckout,
					         l.idusurconferente,
					         l.conferente,
					         l.dtinicio,
					         l.dtfim,
					         l.dtemtransito,
					         l.statuscheckout,
					         l.posicao,
					         l.clientebalcao,
					         l.separarpedido,
					         l.previsao,
					         l.dtprevisao,
					         l.espera,
					         l.decorrido
					  FROM   view_portal_pedidos_logistica l
					 WHERE       l.posicao <> 'FATURADO'
					         AND l.statuscheckout <> 'FINALIZADO'
					         AND l.separarpedido = 'SIM'
					         AND l.dtemtransito is null";
	// varDump2($sql);
	return selectOracle($sql);
}

function buscaPedVendaPendentesLoja(){
	$sql = "SELECT  
    cc.idcheckout,
    c.numped,
    SUBSTR(cl.cliente, 1, 25)                           AS cliente,
    SUBSTR(us.nome, 1, INSTR(us.nome, ' ') - 1)         AS vendedor,

    CASE WHEN c.obsentrega1 LIKE '%CLIENTE ESPERANDO NO BALCAO%' THEN 'SIM' ELSE 'NAO' END AS clientebalcao,
    CASE WHEN c.obsentrega1 LIKE '%SEPARAR ESTE PEDIDO%' THEN 'SIM' ELSE 'NAO' END       AS separarpedido,

    CASE WHEN cc.dtinicio IS NULL THEN 'PENDENTE' ELSE 'SEPARANDO' END AS statuscheckout,

    cc.previsao,
    TO_CHAR(cc.dtprevisao, 'DD/MM/YYYY HH24:MI:SS') AS dtprevisao,

    CASE
        WHEN diff_min < 0 THEN '00:00'
        ELSE diff_min || ':' || LPAD(diff_sec, 2, '0')
    END AS espera

FROM pcpedc c
LEFT JOIN pcclient cl    ON cl.codcli  = c.codcli
LEFT JOIN pcusuari us    ON us.codusur = c.codusur
LEFT JOIN omgcheckoutc cc ON cc.numped = c.numped
LEFT JOIN orcusuario ou   ON ou.idusuario = cc.idusurconferente

-- cálculo feito uma única vez
CROSS APPLY (
    SELECT  
        TRUNC((cc.dtprevisao - SYSDATE) * 1440) AS diff_min,
        TRUNC(
            MOD((cc.dtprevisao - SYSDATE) * 86400, 60)
        ) AS diff_sec
    FROM dual
) t

WHERE c.dtcancel IS NULL
  AND c.dtfat    IS NULL
  AND c.obsentrega2 IS NULL
  AND cc.dtemtransito IS NULL
  AND cc.dtfim IS NULL

ORDER BY c.data, c.hora, c.minuto;
";
	// varDump2($sql);
	return selectOracle($sql);
}


function buscaNotaEntradaPendentes(){
	$sql = "SELECT DISTINCT M.NUMNOTA,
								DECODE(M.CODOPER, 'ED', C.CLIENTE, F.FORNECEDOR) AS FORNECEDOR,
								DECODE(M.CODOPER,
											 'EA',
											 'Ent Ajuste de mercadoria',
											 'EB',
											 'Ent Bonificação',
											 'EC',
											 'Ent Consignação',
											 'ED',
											 'Ent Devolução Cliente',
											 'EF',
											 'Ent comodato',
											 'EG',
											 'Ent Beneficiamento',
											 'EI',
											 'Ent Inventário',
											 'EL',
											 'Ent Perca de mercadoria',
											 'EM',
											 'Ent Material de Consumo',
											 'EN',
											 'Ent Devol Consignada',
											 'EO',
											 'Ent Devol Comodato',
											 'EP',
											 'Ent Estorno produção',
											 'ER',
											 'Ent Simples remessa',
											 'ES',
											 'Ent Sobra de merc',
											 'ET',
											 'Ent Transferência merc',
											 'EV',
											 'Ent Devol remessa beneficiamento',
											 'E1',
											 'Ent avaria',
											 'Ex',
											 'Ent avulsa',
											 'Entrada Mercadoria') AS OPERACAO,
								NVL((SELECT MAX(DECODE(CI.DTFIM,
																			NULL,
																			'INCOMPLETO',
																			'FINALIZADO'))
											FROM OMGCHECKINC CI
										 WHERE CI.NUMNOTA = M.NUMNOTA
											 AND CI.NUMTRANSENT = M.NUMTRANSENT),
										'PENDENTE') AS STATUSCHECKIN,
								(SELECT MAX(U.NOME)
									 FROM OMGCHECKINC CI, ORCUSUARIO U
									WHERE CI.IDUSURCONFERENTE = U.IDUSUARIO
										AND CI.NUMNOTA = M.NUMNOTA
										AND CI.NUMTRANSENT = M.NUMTRANSENT) AS CONFERENTE,
								M.DTMOV,
								
								(  CASE WHEN trunc( (sysdate - M.DTMOV) )>0 THEN trunc( (sysdate - M.DTMOV) ) ||'D ' END  ||  LPAD(trunc( mod( (sysdate - to_date(M.DTMOV||' '||M.HORALANC||':'||M.MINUTOLANC, 'dd-mon-yyyy hh24:mi' ))*24, 24 ) ) , 2 , '0')  ||'h :'|| LPAD(trunc( mod( (sysdate - to_date(M.DTMOV||' '||M.HORALANC||':'||M.MINUTOLANC, 'dd-mon-yyyy hh24:mi' ))*24*60, 60 ) ) , 2 , '0') || 'm'   )   AS ESPERA

		FROM PCMOV M, PCFORNEC F, PCCLIENT C
	 WHERE (M.CODFORNEC = F.CODFORNEC(+) AND F.REVENDA = 'S')
		 AND M.CODCLI = C.CODCLI(+)
		 AND M.CODOPER LIKE 'E%'
		 AND M.DTCANCEL IS NULL
		 AND M.DTMOV >= TRUNC(SYSDATE - 7)
	 ORDER BY M.DTMOV ASC";

	// varDump2($sql);
	$ret = selectOracle($sql);
	$pedidos = array();
	foreach ($ret as $key => $value) {
		if ($value['STATUSCHECKIN'] <> 'FINALIZADO') {
      $fornec = explode(' ', $value['FORNECEDOR']);
      $confer = explode(' ', $value['CONFERENTE']);
			$value['FORNECEDOR'] = (($value['FORNECEDOR']<>'')?reset($fornec):'');
			$value['CONFERENTE'] = (($value['CONFERENTE']<>'')?reset($confer):'');
			array_push($pedidos, $value);
		}
	}
	return $pedidos;
}

function buscaItensCancelados($dados){

	$sql = "INSERT INTO OMGCANCELADOS
	(IDCANCELADOS,
	 NUMPED,
	 CODPROD,
	 DV,
	 DESCRICAO,
	 MARCA,
	 LOCACAO,
	 DATACANC,
	 CODUSUR,
	 RCA,
	 CODCLI,
	 CLIENTE,
	 CANCELADO_POR,
	 MOTIVO,
	 QT)
	WITH p2 AS
	 (SELECT N.NUMPED || '-' || N.CODPROD AS IDCANCELADOS,
			 N.NUMPED,
			 N.CODPROD,
			 P.DV,
			 P.DESCRICAO,
			 M.MARCA,
			 P.INFORMACOESTECNICAS AS LOCACAO,
			 N.DATACANC,
			 U.CODUSUR,
			 U.NOME AS RCA,
			 C.CODCLI,
			 C.CLIENTE,
			 E.NOME AS CANCELADO_POR,
			 N.MOTIVO,
			 N.QT
		FROM PCNFCANITEM N,
			 PCUSUARI    U,
			 PCCLIENT    C,
			 PCEMPR      E,
			 PCPRODUT    P,
			 PCMARCA     M
	 WHERE N.CODUSUR = U.CODUSUR
		 AND N.CODCLI = C.CODCLI
		 AND N.CODFUNCCANC = E.MATRICULA
		 AND N.CODPROD = P.CODPROD
		 AND NVL(P.CODMARCA, 1) = M.CODMARCA
		 AND N.DATACANC BETWEEN to_date('".$dados['DATAINI']."', 'DD/MM/YYYY') 
						AND to_date('".$dados['DATAFIM']."', 'DD/MM/YYYY')
		 AND N.NUMPED || '-' || P.CODPROD NOT IN
			 (SELECT IDCANCELADOS FROM OMGCANCELADOS)
	 ORDER BY P.INFORMACOESTECNICAS)
	SELECT * FROM p2";
	
	if (executarOracle($sql)) {
		$sql = "SELECT * FROM OMGCANCELADOS 
				WHERE DATACANC BETWEEN to_date('".$dados['DATAINI']."', 'DD/MM/YYYY') 
									 AND to_date('".$dados['DATAFIM']."', 'DD/MM/YYYY')";
		return selectOracle($sql);
	} else {
		return false;
	}
}

function conferirItemCancelados($dados){
	if (strpos($dados['CODPROD'], '*' )) {
		$tmpCodprod = explode('*', $dados['CODPROD']);
		$dados['QTCONFERIDA'] = (int) reset($tmpCodprod);
		$dados['CODPROD']     = (int) end($tmpCodprod);
	} else {
		$dados['QTCONFERIDA'] = (int) 1;
		$dados['CODPROD']     = (int) $dados['CODPROD'];
	}
	// varDump2($dados);
	
	$item = false;
	foreach ($_SESSION['CANCELADOS'] as $key => $value) {
		if ($value['CODPROD'] == $dados['CODPROD']) {
			$item = $value;
			$item['key'] = $key;
		}
	}
	if ($item) {
		$newQTCONFERIDA = ($item['QTCONFERIDA']+$dados['QTCONFERIDA']);
		$sql = "UPDATE OMGCANCELADOS SET QTCONFERIDA = ".$newQTCONFERIDA."
				WHERE CODPROD = ".$item['CODPROD']." AND NUMPED = ".$item['NUMPED'];
		executarOracle($sql);
		$_SESSION['CANCELADOS'][$item['key']]['QTCONFERIDA'] = $newQTCONFERIDA;
	} else {
		insereModal('danger', 'Produto não localizado');
	}
}

function buscaDetalhesEstoqueProduto($CODPROD) {
	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
			       TRUNC(P.DV) AS DV,
			       TRIM(P.DESCRICAO) AS DESCRICAO,
			       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
			       TRIM(M.MARCA) AS MARCA,
			       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
			       NVL(E.QTESTGER, 0) AS QTESTGER,
			       (NVL(E.QTRESERV, 0) + NVL(E.QTPENDENTE, 0)) AS QTRESERV,
			       NVL(E.QTBLOQUEADA, 0) AS QTBLOQUEADA,
			       NVL(E.QTINDENIZ, 0) AS QTAVARIA,
			       (NVL(E.QTESTGER, 0) - NVL(E.QTRESERV, 0) - NVL(E.QTPENDENTE, 0) - NVL(E.QTBLOQUEADA, 0) - NVL(E.QTINDENIZ, 0)) AS QTDISPONIVEL,
			       CASE
			         WHEN E.QTBLOQUEADA > 0 THEN
			          D.MOTIVO
			       END AS MOTIVOBLOQ
			  FROM PCPRODUT P, PCMARCA M, PCEST E, PCTABDEV D
			 WHERE P.CODMARCA = M.CODMARCA
			   AND P.CODPROD = E.CODPROD
			   AND E.CODDEVOL = D.CODDEVOL(+)
			   AND E.CODFILIAL = 1
			   AND E.CODPROD = {$CODPROD}";
// varDump2($sql);

	if (empty($CODPROD)) {
		throw new InvalidArgumentException("O CODPROD inválido.");
	} else {
		$ret = selectOracle($sql);
		if (!empty($ret) && !is_null($ret)) {
			return reset($ret);
		} else {
			gerarPCEST();
			return buscaDetalhesEstoqueProduto($CODPROD);
		}
	}
}

function gerarPCEST(){
	$sql = "INSERT INTO pcest (codprod, codfilial)
    SELECT   p.codprod, f.codigo AS codfilial
      FROM   pcprodut p, pcfilial f
     WHERE   f.codigo NOT IN ('99')
             AND NOT EXISTS
                    (SELECT   1
                       FROM   pcest e
                      WHERE   e.codfilial = f.codigo
                              AND e.codprod = p.codprod)";
  return executarOracle($sql);
}

function buscaPCPEDC($dados){
	// varDump2($dados);
	$sql = "SELECT NVL(C.CODFILIAL, 1) AS CODFILIAL,
		   F.RAZAOSOCIAL AS FILIAL,
		   C.NUMPED,
		   C.NUMPEDRCA,
		   C.NUMPEDCLI,
		   C.DATA,
		   DECODE(C.POSICAO,
				  'F',
				  'FATURADO',
				  'M',
				  'MONTADO',
				  'B',
				  'BLOQUEADO',
				  'P',
				  'PENDENTE',
				  C.POSICAO) AS POSICAO,
		   C.DTFAT,
		   C.NUMREGIAO,
		   C.CONDVENDA AS TV,
		   C.NUMCAR,
		   C.FRETEDESPACHO,
		   C.FRETEREDESPACHO,
		   C.VLFRETE,
		   C.CODTRANSP||'-'||C.TRANSPORTADORA AS TRANSPORTADORA,
		   CASE WHEN C.CODEMITENTE = 8888 THEN '8888 Força de Vendas' ELSE U.NOME||'-'||U.NOME END AS EMITENTE,
		   C.OBS1||' - '||C.OBS2 AS OBSERVACAO,
 			 C.OBSENTREGA1,
 			 C.OBSENTREGA2,
		   DECODE(NVL(C.TIPOEMBALAGEM, 'M'), 'M', 'CAIXA', 'UNIDADE') AS EMBALAGEM,
		   U.CODUSUR,
		   U.NOME AS RCA,
		   CO.CODCOB,
		   CO.COBRANCA,
		   PL.CODPLPAG,
		   PL.DESCRICAO AS PLPAG,
		   PL.NUMDIAS AS PRAZOMEDIO,
		   NVL(CL.PRAZOADICIONAL, 0) AS PRAZOADICIONAL,
		   NVL(PL.PRAZO1, 0) || ' / ' || NVL(PL.PRAZO2, 0) || ' / ' ||
		   NVL(PL.PRAZO3, 0) || ' / ' || NVL(PL.PRAZO4, 0) || ' / ' ||
		   NVL(PL.PRAZO5, 0) || ' / ' || NVL(PL.PRAZO6, 0) || ' / ' ||
		   NVL(PL.PRAZO7, 0) || ' / ' || NVL(PL.PRAZO8, 0) || ' / ' ||
		   NVL(PL.PRAZO9, 0) || ' / ' || NVL(PL.PRAZO10, 0) || ' / ' ||
		   NVL(PL.PRAZO11, 0) || ' / ' || NVL(PL.PRAZO12, 0) AS PRAZOPAGAMENTO,
		   CL.CODCLI,
		   TRIM(CL.CLIENTE) AS CLIENTE,
		   TRIM(CL.ENDERENT) AS ENDERECO,
		   TRIM(CL.NUMEROENT) AS NUMERO,
		   TRIM(CL.BAIRROENT) AS BAIRRO,
		   TRIM(CL.PONTOREFER) AS PONTOPREF,
		   CL.TELENT,
		   CL.CEPENT,
		   CL.CGCENT AS CNPJ,
		   TRIM(CL.IEENT) AS IESTADUAL,
		   TRIM(CI.NOMECIDADE) AS CIDADE,
		   CI.UF,
		   A.CODATIV,
		   TRIM(A.RAMO) AS RAMO,
		   UC.NOME AS SEPARADOR
	  FROM PCPEDC   C,
		   PCFILIAL F,
		   PCCLIENT CL,
		   PCCIDADE CI,
		   PCATIVI  A,
		   PCCOB    CO,
		   PCPLPAG  PL,
		   PCUSUARI U,
		   OMGCHECKOUTC CC,
		   ORCUSUARIO UC

	 WHERE C.CODFILIAL = F.CODIGO
	   AND C.CODCLI = CL.CODCLI
	   AND CL.CODCIDADE = CI.CODCIDADE
	   AND CL.CODATV1 = A.CODATIV
	   AND C.CODCOB = CO.CODCOB
	   AND C.CODUSUR = U.CODUSUR
	   AND C.CODPLPAG = PL.CODPLPAG
	   AND C.NUMPED = CC.NUMPED (+)
	   AND CC.IDUSURCONFERENTE = UC.IDUSUARIO (+)";
	if (isset($dados['NUMPEDRCA']) && !empty($dados['NUMPEDRCA'])) {
	   $sql .= PHP_EOL."	AND C.NUMPEDRCA = '{$dados['NUMPEDRCA']}'";
	}
	if (isset($dados['NUMPED']) && !empty($dados['NUMPED'])) {
	   $sql .= PHP_EOL."	AND C.NUMPED = '{$dados['NUMPED']}'";
	}
	// varDump2($sql); 
	  
	if ($ret = selectOracle($sql)) {
		// varDump2($ret); 
		// die();
		return reset($ret);
	} else {
		return false;
	}
}

function buscaPCPEDCNumped($NUMPED){
	$sql = "SELECT NVL(C.CODFILIAL, 1) AS CODFILIAL,
		   F.RAZAOSOCIAL AS FILIAL,
		   C.NUMPED,
		   C.NUMPEDRCA,
		   C.NUMPEDCLI,
		   C.DATA,
		   DECODE(C.POSICAO,
				  'F',
				  'FATURADO',
				  'M',
				  'MONTADO',
				  'B',
				  'BLOQUEADO',
				  'P',
				  'PENDENTE',
				  C.POSICAO) AS POSICAO,
		   C.DTFAT,
		   C.NUMREGIAO,
		   C.CONDVENDA AS TV,
		   C.NUMCAR,
		   C.FRETEDESPACHO,
		   C.FRETEREDESPACHO,
		   C.VLFRETE,
		   C.CODTRANSP||'-'||C.TRANSPORTADORA AS TRANSPORTADORA,
		   CASE WHEN C.CODEMITENTE = 8888 THEN '8888 Força de Vendas' ELSE U.NOME||'-'||U.NOME END AS EMITENTE,
		   C.OBS1||' - '||C.OBS2 AS OBSERVACAO,
		   NVL(C.OBSENTREGA1, 'PEDIDO EM PRATELEIRA') AS OBSENTREGA1,
		   NVL(C.OBSENTREGA2, 'SEPARACAO NORMAL') AS OBSENTREGA2,
		   DECODE(NVL(C.TIPOEMBALAGEM, 'M'), 'M', 'CAIXA', 'UNIDADE') AS EMBALAGEM,
		   U.CODUSUR,
		   U.NOME AS RCA,
		   CO.CODCOB,
		   CO.COBRANCA,
		   PL.CODPLPAG,
		   PL.DESCRICAO AS PLPAG,
		   PL.NUMDIAS AS PRAZOMEDIO,
		   NVL(CL.PRAZOADICIONAL, 0) AS PRAZOADICIONAL,
		   NVL(PL.PRAZO1, 0) || ' / ' || NVL(PL.PRAZO2, 0) || ' / ' ||
		   NVL(PL.PRAZO3, 0) || ' / ' || NVL(PL.PRAZO4, 0) || ' / ' ||
		   NVL(PL.PRAZO5, 0) || ' / ' || NVL(PL.PRAZO6, 0) || ' / ' ||
		   NVL(PL.PRAZO7, 0) || ' / ' || NVL(PL.PRAZO8, 0) || ' / ' ||
		   NVL(PL.PRAZO9, 0) || ' / ' || NVL(PL.PRAZO10, 0) || ' / ' ||
		   NVL(PL.PRAZO11, 0) || ' / ' || NVL(PL.PRAZO12, 0) AS PRAZOPAGAMENTO,
		   CL.CODCLI,
		   TRIM(CL.CLIENTE) AS CLIENTE,
		   TRIM(CL.ENDERENT) AS ENDERECO,
		   TRIM(CL.NUMEROENT) AS NUMERO,
		   TRIM(CL.BAIRROENT) AS BAIRRO,
		   TRIM(CL.PONTOREFER) AS PONTOPREF,
		   CL.TELENT,
		   CL.CEPENT,
		   CL.CGCENT AS CNPJ,
		   TRIM(CL.IEENT) AS IESTADUAL,
		   TRIM(CI.NOMECIDADE) AS CIDADE,
		   CI.UF,
		   A.CODATIV,
		   TRIM(A.RAMO) AS RAMO,
		   UC.NOME AS SEPARADOR,
		   NVL(ORC.EXIBIRCODPECAETIQUETA,'N') AS EXIBIRCODPECAETIQUETA

	  FROM PCPEDC   C,
		   PCFILIAL F,
		   PCCLIENT CL,
		   PCCIDADE CI,
		   PCATIVI  A,
		   PCCOB    CO,
		   PCPLPAG  PL,
		   PCUSUARI U,
		   OMGCHECKOUTC CC,
		   ORCUSUARIO UC,
		   ORCORCAMENTOC ORC
	 WHERE C.CODFILIAL = F.CODIGO
	   AND C.CODCLI = CL.CODCLI
	   AND CL.CODCIDADE = CI.CODCIDADE
	   AND CL.CODATV1 = A.CODATIV
	   AND C.CODCOB = CO.CODCOB
	   AND C.CODUSUR = U.CODUSUR
	   AND C.CODPLPAG = PL.CODPLPAG
	   AND C.NUMPED = CC.NUMPED (+)
	   AND C.NUMPEDCLI = ORC.IDORCAMENTO (+)
	   AND CC.IDUSURCONFERENTE = UC.IDUSUARIO (+)
	   AND C.NUMPED = '".$NUMPED."'";
	  
	if ($ret = selectOracle($sql)) {
		// varDump2($sql); 
		// varDump2($ret); 
		// die();
		return reset($ret);
	} else {
		return false;
	}
}

function buscaPCPEDCFV($dados){
	$sql = "SELECT C.IMPORTADO,
	   NVL(C.CODFILIAL, 1) AS CODFILIAL,
	   F.RAZAOSOCIAL AS FILIAL,
	   C.NUMPED,
	   C.NUMPEDRCA,
	   C.NUMPEDCLI,
	   C.DTABERTURAPEDPALM AS DATA,
	   C.POSICAO_ATUAL,
	   DECODE(C.POSICAO_ATUAL, 
			 'R', 'REJEITADO',
			 'P', 'PENDENTE',
			 'M', 'MONTADO',
			 'F', 'FATURADO',
			 'B', 'BLOQUEADO',
			 'L', 'LIBERADO',
			 C.POSICAO_ATUAL) AS POSICAO,
	   C.OBSERVACAO_PC,
	   C.CONDVENDA AS TV,
	   C.FRETEDESPACHO,
	   C.FRETEREDESPACHO,
	   C.VLFRETE,
	   CASE WHEN C.CODEMITENTE = 8888 THEN '8888 Força de Vendas' ELSE U.NOME||'-'||U.NOME END AS EMITENTE,
	   C.OBS1||' - '||C.OBS2 AS OBSERVACAO,
	   DECODE(NVL(C.OBSENTREGA1, 'NAO'), 'NAO', 'PEDIDO EM PRATELEIRA', 'CLIENTE BALCAO') AS OBSENTREGA1,
	   NVL(C.OBSENTREGA2, 'SEPARACAO NORMAL') AS OBSENTREGA2,
	   U.CODUSUR,
	   U.NOME AS RCA,
	   CO.CODCOB,
	   CO.COBRANCA,
	   PL.CODPLPAG,
	   PL.DESCRICAO AS PLPAG,
	   PL.NUMDIAS AS PRAZOMEDIO,
	   NVL(CL.PRAZOADICIONAL, 0) AS PRAZOADICIONAL,
	   NVL(PL.PRAZO1, 0) || ' / ' || NVL(PL.PRAZO2, 0) || ' / ' ||
	   NVL(PL.PRAZO3, 0) || ' / ' || NVL(PL.PRAZO4, 0) || ' / ' ||
	   NVL(PL.PRAZO5, 0) || ' / ' || NVL(PL.PRAZO6, 0) || ' / ' ||
	   NVL(PL.PRAZO7, 0) || ' / ' || NVL(PL.PRAZO8, 0) || ' / ' ||
	   NVL(PL.PRAZO9, 0) || ' / ' || NVL(PL.PRAZO10, 0) || ' / ' ||
	   NVL(PL.PRAZO11, 0) || ' / ' || NVL(PL.PRAZO12, 0) AS PRAZOPAGAMENTO,
	   CL.CODCLI,
	   TRIM(CL.CLIENTE) AS CLIENTE,
	   TRIM(CL.ENDERENT) AS ENDERECO,
	   TRIM(CL.NUMEROENT) AS NUMERO,
	   TRIM(CL.BAIRROENT) AS BAIRRO,
	   TRIM(CL.PONTOREFER) AS PONTOPREF,
	   CL.TELENT,
	   CL.CEPENT,
	   CL.CGCENT AS CNPJ,
	   TRIM(CL.IEENT) AS IESTADUAL,
	   TRIM(CI.NOMECIDADE) AS CIDADE,
	   CI.UF,
	   A.CODATIV,
	   TRIM(A.RAMO) AS RAMO
  FROM PCPEDCFV   C,
     PCFILIAL F,
	   PCCLIENT CL,
	   PCCIDADE CI,
	   PCATIVI  A,
	   PCCOB    CO,
	   PCPLPAG  PL,
	   PCUSUARI U
 WHERE C.CODFILIAL = F.CODIGO
   AND C.CODCLI = CL.CODCLI
   AND CL.CODCIDADE = CI.CODCIDADE
   AND CL.CODATV1 = A.CODATIV
   AND C.CODCOB = CO.CODCOB
   AND C.CODUSUR = U.CODUSUR
   AND C.CODPLPAG = PL.CODPLPAG";
   if ($dados['NUMPED']<>'') {
   	 $sql .= PHP_EOL."  AND C.NUMPED = ".$dados['NUMPED'];
   }
   if ($dados['NUMPEDCLI']<>'') {
   	 $sql .= PHP_EOL."  AND C.NUMPEDCLI = ".$dados['NUMPEDCLI'];
   }
   if ($dados['NUMPEDRCA']<>'') {
   	 $sql .= PHP_EOL."  AND C.NUMPEDRCA = ".$dados['NUMPEDRCA'];
   }
  
  if ($ret = selectOracle($sql)) {
 		// varDump2($sql); 
   	// varDump2($ret); 
   	// die();
		return reset($ret);
  } else {
		return false;
  }
}


function buscaPCPEDIConferenciaNUMPED($NUMPED){
	$sql = "SELECT I.NUMPED,
			       C.NUMPEDRCA,
			       I.NUMPEDCLI,
			       (SELECT MAX(OI.CODPECA)
			          FROM ORCORCAMENTOC OC, ORCORCAMENTOI OI
			         WHERE OC.IDORCAMENTO = OI.IDORCAMENTO
			           AND OC.IDORCAMENTO = C.NUMPEDCLI
			           AND OI.CODPROD = P.CODPROD
			           AND OI.STATUS = 'A') AS CODPECA,
			       'W' || TRUNC(P.CODPROD) || '-' || TRIM(P.DV) AS CODPROD,
			       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
			       TRIM(P.DESCRICAO) AS PRODUTO,
			       TRIM(M.MARCA) AS MARCA,
			       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
			       P.UNIDADE,
			       MAX(I.PVENDA) AS PVENDA,
			       SUM(I.QT) AS QT
			  FROM PCPEDC C, PCPEDI I, PCPRODUT P, PCMARCA M
			 WHERE C.NUMPED = I.NUMPED
			   AND C.NUMPED = I.NUMPED
			   AND I.CODPROD = P.CODPROD
			   AND P.CODMARCA = M.CODMARCA
			   AND C.NUMPED = {$NUMPED}
			 GROUP BY I.NUMPED,
			          C.NUMPEDRCA,
			          I.NUMPEDCLI,
			          C.NUMPEDCLI,
			          P.CODPROD,
			          'W' || TRUNC(P.CODPROD) || '-' || TRIM(P.DV),
			          TRIM(P.NUMORIGINAL),
			          TRIM(P.DESCRICAO),
			          TRIM(M.MARCA),
			          TRIM(P.INFORMACOESTECNICAS),
			          P.UNIDADE
			 ORDER BY TRIM(P.INFORMACOESTECNICAS) ASC";
	// varDump2($sql);
	return selectOracle($sql);
}


function buscaPCPEDIConferenciaNUMPEDRCA($NUMPEDRCA){
	$sql = "SELECT I.NUMPED,
			       C.NUMPEDRCA,
			       I.NUMPEDCLI,
			       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
			       TRUNC(P.CODPROD) AS CODPROD,
			       TRIM(P.DV) AS DV,
			       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
			       TRIM(P.DESCRICAO) AS PRODUTO,
			       TRIM(M.MARCA) AS MARCA,
			       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
			       P.UNIDADE,
			       ROUND(NVL(I.PVENDA, 0), 2) AS PVENDA,
			       SUM(NVL(I.QT, 0)) AS QTFATURADA
			  FROM PCPEDC C, PCPEDI I, PCPRODUT P, PCMARCA M
			 WHERE C.NUMPED = I.NUMPED
			   AND C.NUMPED = I.NUMPED
			   AND I.CODPROD = P.CODPROD
			   AND P.CODMARCA = M.CODMARCA
			   AND C.NUMPEDRCA = {$NUMPEDRCA}
			 GROUP BY I.NUMPED,
			          C.NUMPEDRCA,
			          I.NUMPEDCLI,
			          TRIM(P.INFORMACOESTECNICAS),
			          TRUNC(P.CODPROD),
			          TRIM(P.DV),
			          TRIM(P.NUMORIGINAL),
			          TRIM(P.DESCRICAO),
			          TRIM(M.MARCA),
			          TRIM(P.NUMORIGINAL),
			          P.UNIDADE,
			          ROUND(NVL(I.PVENDA, 0), 2)
			 ORDER BY TRIM(P.INFORMACOESTECNICAS) ASC";
	// varDump2($sql);
	return selectOracle($sql);
}

function confirmarPrevisao(array $dados){
	$sql = "UPDATE OMGCHECKOUTC 
			SET PREVISAO = ".$dados['PREVISAO'].", 
				DTPREVISAO = (SYSDATE + interval '".$dados['PREVISAO']."' minute)
			WHERE IDCHECKOUT = ".$dados['IDCHECKOUT'];
	// varDump2($dados);
	// varDump2($sql);
	if (executarOracle($sql)){
    $_SESSION['CHECKOUT']['CAB'] = buscaOMGCHECKOUTC($dados['IDCHECKOUT']);
  }
}

function add5Minutos($NUMPED){
	$sql = "UPDATE OMGCHECKOUTC 
			   set dtprevisao = dtprevisao + interval '5' minute 
			 where numped = ".$NUMPED;
	if (executarOracle($sql)){
		insereModal('successs', 'SUCESSO ao adicionar 5 minutos ao tempo de separação');
	} else {
		insereModal('danger', 'ERRO ao adicionar 5 minutos ao tempo de separação');
	}
}

//############################################################################################

function listarCategorias(){
	$sql = "SELECT * FROM OMGCATEGORIA WHERE DTEXCLUSAO IS NULL";
	return selectOracle($sql);
}

function buscaCategoria($CATEGORIA){
	$sql = "SELECT * FROM OMGCATEGORIA WHERE CATEGORIA = '{$CATEGORIA}'";
	if ( $ret = selectOracle($sql) ){
		return reset($ret);
	} else {
		return false;
	}
}

function inserirCategoria($dados){
	$dados['CATEGORIA'] = mb_strtoupper(trim($dados['CATEGORIA']), 'UTF-8');
	if ( $categoria = buscaCategoria($dados['CATEGORIA']) ){
		insereModal("danger", "A CATEGORIA {$dados['CATEGORIA']} JÁ POSSUI CADASTRO!");
		return $categoria;
	} else {
		$sql = "INSERT INTO OMGCATEGORIA (CATEGORIA, USUARIOID) VALUES (
						'{$dados['CATEGORIA']}',
						".$_SESSION['login']['IDUSUARIO'].")";
		if ( $ret = executarOracle($sql) ){
			insereModal("success", "SUCESSO ao cadastrar a categoria: {$dados['CATEGORIA']}.");
			return buscaCategoria($dados['CATEGORIA']);
		} else {
			insereModal("danger", "ERRO ao cadastrar a categoria: {$dados['CATEGORIA']}.");
			return false;
		}
	}
}

//############################################################################################

function listarSubcategorias($CATEGORIAID){
	$sql = "SELECT * FROM OMGSUBCATEGORIA 
					 WHERE DTEXCLUSAO IS NULL 
					   AND CATEGORIAID = {$CATEGORIAID}";
	return selectOracle($sql);
}

function buscaSubcategoria($CATEGORIAID, $SUBCATEGORIA){
	$sql = "SELECT * FROM OMGSUBCATEGORIA 
					 WHERE DTEXCLUSAO IS NULL 
					   AND CATEGORIAID = {$CATEGORIAID} 
					   AND SUBCATEGORIA = '{$SUBCATEGORIA}'";
	if ( $ret = selectOracle($sql) ){
		return reset($ret);
	} else {
		return false;
	}
}

function inserirSubcategoria($dados){
	$SUBCATEGORIA_NOME = mb_strtoupper(trim($dados['SUBCATEGORIA']), 'UTF-8');

	if ( $subcategoria = buscaSubcategoria($dados['CATEGORIAID'], $SUBCATEGORIA_NOME) ){
		insereModal("danger", "A SUB CATEGORIA {$SUBCATEGORIA_NOME} JÁ POSSUI CADASTRO!");
		return $subcategoria;
	} else {
		$sql = "INSERT INTO OMGSUBCATEGORIA (CATEGORIAID, SUBCATEGORIA, USUARIOID) VALUES (
						'{$dados['CATEGORIAID']}',
						'{$SUBCATEGORIA_NOME}',
						".$_SESSION['login']['IDUSUARIO'].")";
		if ( $ret = executarOracle($sql) ){
			insereModal("success", "SUCESSO ao cadastrar a Sub Categoria: {$SUBCATEGORIA_NOME}.");
			return buscaSubcategoria($dados['CATEGORIAID'], $SUBCATEGORIA_NOME);
		} else {
			insereModal("danger", "ERRO ao cadastrar a Sub Categoria: {$SUBCATEGORIA_NOME}.");
			return false;
		}
	}
}

//############################################################################################

function listarGrupos($SUBCATEGORIAID){
	$sql = "SELECT * FROM OMGGRUPO 
					 WHERE DTEXCLUSAO IS NULL 
					   AND SUBCATEGORIAID = {$SUBCATEGORIAID}";
	return selectOracle($sql);
}

function buscaGrupo($SUBCATEGORIAID, $GRUPO){
	$GRUPO = mb_strtoupper(trim($GRUPO), 'UTF-8');
	$sql = "SELECT * FROM OMGGRUPO 
					 WHERE DTEXCLUSAO IS NULL 
					   AND SUBCATEGORIAID = {$SUBCATEGORIAID} 
					   AND GRUPO = '{$GRUPO}'";
	if ( $ret = selectOracle($sql) ){
		return reset($ret);
	} else {
		return false;
	}
}

function inserirGrupo($dados){
	$GRUPO_NOME = mb_strtoupper(trim($dados['GRUPO']), 'UTF-8');

	if ( $grupo = buscaGrupo($dados['SUBCATEGORIAID'], $GRUPO_NOME) ){
		insereModal("danger", "O GRUPO {$GRUPO_NOME} JÁ POSSUI CADASTRO!");
		return $grupo;
	} else {
		$sql = "INSERT INTO OMGGRUPO (SUBCATEGORIAID, GRUPO, USUARIOID) VALUES (
						'{$dados['SUBCATEGORIAID']}',
						'{$GRUPO_NOME}',
						".$_SESSION['login']['IDUSUARIO'].")";
		if ( $ret = executarOracle($sql) ){
			insereModal("success", "SUCESSO ao cadastrar o Grupo: {$GRUPO_NOME}.");
			return buscaGrupo($dados['SUBCATEGORIAID'], $GRUPO_NOME);
		} else {
			insereModal("danger", "ERRO ao cadastrar o Grupo: {$GRUPO_NOME}.");
			return false;
		}
	}
}

####################################################################



function listarMontadoras(){
	$sql = "select * from vmp_montadora where dtexclusao is null order by montadora asc";
	return selectOracle($sql);
}

function buscaMontadoraID($IDMONTADORA){
	$sql = "select * from vmp_montadora where IDMONTADORA = {$IDMONTADORA}";
	$ret = selectOracle($sql);
	return ($ret) ? reset($ret): false;
}

function buscaMontadoraNome($montadora){
	$sql = "select IDMONTADORA, MONTADORA 
	     			from vmp_montadora 
	         where dtexclusao is null 
	           and montadora = '{$montadora}'
	         order by idmontadora asc";
	$ret = selectOracle($sql);
	return ($ret) ? reset($ret): false;
}

function equip_montadoraInsert($dados){
	if (!isset($dados['MONTADORA']) || empty($dados['MONTADORA'])) {
		insereModal("danger", "Descrição da montadora é inválida ou vazia.");
		return;
	}
	$montadora = sanitizeOracleString($dados['MONTADORA'], 0, true);

	if ($montadora_old = buscaMontadoraNome($montadora)) {
		insereModal("warning", "A Montadora {$montadora} já possui cadastro.");
		return;
	} else {
		$sql = "INSERT INTO vmp_montadora (idusuariocad, montadora) VALUES (
			{$_SESSION['login']['IDUSUARIO']}, 
			'{$montadora}'
			)";
		if (executarOracle($sql)){
			insereLogEquipamento('vmp_montadora', 'insert', json_encode(array('MONTADORA'=>$montadora)));
			insereModal("success", "Montadora {$montadora} cadastrada com sucesso.");
			return;
		} else {
			insereModal("danger", "Erro ao cadastrar a Montadora {$montadora}.");
			return;
		}
	}
}

function montadoraUpdate($dados){
	if (isset($dados['MONTADORA']) && ! empty($dados['MONTADORA'])) {
		$montadora = mb_strtoupper(trim($dados['MONTADORA']), 'UTF-8');
		$existe = buscaMontadoraNome($montadora);
		if ($existe && count($existe)>0) {
			insereModal("warning", "A Montadora {$montadora} já possui cadastro.");
			return false;
		} else {
			$sql = "UPDATE vmp_montadora 
								 SET DTALTERACAO = sysdate,
								 		 IDUSUARIOALT = {$_SESSION['login']['IDUSUARIO']},
								 		 montadora = '{$montadora}'
							 WHERE IDMONTADORA = {$dados['IDMONTADORA']}";
			if ($dados['IDMONTADORA']) {
				if (executarOracle($sql)){
					insereLogEquipamento('vmp_montadora', 'update', json_encode(array('MONTADORA'=>$montadora)));
					insereModal("success", "Montadora {$montadora} atualizar com sucesso.");
					return true;
				} else {
					insereModal("danger", "Erro ao atualizar a Montadora {$montadora}.");
					return false;
				}
			} else {
				insereModal("danger", "Erro ao atualizar a Montadora {$montadora}. IDMONTADORA não informado!");
				return false;
			}
		}
	} else {
		insereModal("danger", "Descrição da montadora é inválida ou vazia.");
		return false;
	}
}

function montadoraDelete($dados){
	$montadora = mb_strtoupper(trim($dados['MONTADORA']), 'UTF-8');
	$sql = "DELETE FROM vmp_montadora WHERE IDMONTADORA = {$dados['IDMONTADORA']}";
	if ($dados['IDMONTADORA']) {
		if (executarOracle($sql)){
			insereLogEquipamento('vmp_montadora', 'delete', json_encode(array('MONTADORA'=>$montadora)));
			insereModal("success", "Montadora {$montadora} excluida com sucesso.");
			return true;
		} else {
			insereModal("danger", "Erro ao excluir a Montadora {$montadora}.");
			return false;
		}
	} else {
		insereModal("danger", "Erro ao excluir a Montadora {$montadora}. IDMONTADORA não informado!");
		return false;
	}
}


####################################################################################

function listarTipoequip(){
	$sql = "select * from vmp_tipoequip where dtexclusao is null order by tipoequipamento asc";
	return selectOracle($sql);
}

function buscaTipoequipID($IDTIPOEQUIP){
	$sql = "select * from vmp_tipoequip where IDTIPOEQUIP = {$IDTIPOEQUIP}";
	$ret = selectOracle($sql);
	return ($ret) ? reset($ret): false;
}

function buscaTipoequipNome($TIPOEQUIPAMENTO){
	$sql = "select * from vmp_tipoequip 
					 where dtexclusao is null 
	           and TIPOEQUIPAMENTO = '{$TIPOEQUIPAMENTO}'";
	return selectOracle($sql);
}

function equip_tipoequipInsert($dados){

	if (!isset($dados['TIPOEQUIPAMENTO']) || empty($dados['TIPOEQUIPAMENTO'])) {
		insereModal("danger", "Descrição do Tipo Equipamento é inválida.");
		return;
	}
	$tipoEquipamento = sanitizeOracleString($dados['TIPOEQUIPAMENTO'], 0, true);

	$existe = buscaTipoequipNome($tipoEquipamento);
	if ($existe) {
		insereModal("warning", "O Tipo Equipamento: {$tipoEquipamento} já possui cadastro.");
		return;
	}

	$sql = "INSERT INTO vmp_tipoequip (idusuariocad, tipoEquipamento) VALUES (
		{$_SESSION['login']['IDUSUARIO']}, 
		'{$tipoEquipamento}'
		)";
	if (executarOracle($sql)){
		insereLogEquipamento('vmp_tipoequip', 'insert', json_encode(array('TIPOEQUIPAMENTO'=>$tipoEquipamento)));
		insereModal("success", "SUCESSO ao cadastrar o Tipo Equipamento: {$tipoEquipamento}.");
		return true;
	} else {
		insereModal("danger", "Erro ao cadastrar o Tipo Equipamento: {$tipoEquipamento}.");
		return false;
	}
}

function equip_tipoequipUpdate($dados){
	if (isset($dados['TIPOEQUIPAMENTO']) && ! empty($dados['TIPOEQUIPAMENTO'])) {
		$TIPOEQUIPAMENTO = sanitizeOracleString($dados['TIPOEQUIPAMENTO']);
		$existe = buscaTipoequipNome($TIPOEQUIPAMENTO);
		if ($existe && count($existe)>0) {
			insereModal("warning", "O TIPO EQUIPAMENTO {$TIPOEQUIPAMENTO} já possui cadastro.");
			return false;
		} else {
			$sql = "UPDATE vmp_tipoequip 
								 SET DTALTERACAO = sysdate,
								 		 IDUSUARIOALT = {$_SESSION['login']['IDUSUARIO']},
								 		 TIPOEQUIPAMENTO = '{$TIPOEQUIPAMENTO}' 
							 WHERE IDTIPOEQUIP = {$dados['IDTIPOEQUIP']}";
			if ($dados['IDTIPOEQUIP']) {
				if (executarOracle($sql)){
					insereLogEquipamento('vmp_tipoequip', 'update', json_encode(array('TIPOEQUIPAMENTO'=>$TIPOEQUIPAMENTO)));
					insereModal("success", "TIPO EQUIPAMENTO {$TIPOEQUIPAMENTO} atualizado com sucesso.");
					return true;
				} else {
					insereModal("danger", "Erro ao atualizar o TIPO EQUIPAMENTO {$TIPOEQUIPAMENTO}.");
					return false;
				}
			} else {
				insereModal("danger", "Erro ao atualizar o TIPO EQUIPAMENTO {$TIPOEQUIPAMENTO}. IDTIPOEQUIP não informado!");
				return false;
			}
		}
	} else {
		insereModal("danger", "Descrição do TIPO EQUIPAMENTO é inválida ou vazia.");
		return false;
	}
}

function equip_tipoequipDelete($dados){
	$TIPOEQUIPAMENTO = mb_strtoupper(trim($dados['TIPOEQUIPAMENTO']), 'UTF-8');
	$sql = "DELETE FROM vmp_tipoequip WHERE IDTIPOEQUIP = {$dados['IDTIPOEQUIP']}";
	if ($dados['IDTIPOEQUIP']) {
		if (executarOracle($sql)){
			insereLogEquipamento('vmp_tipoequip', 'delete', json_encode(array('TIPOEQUIPAMENTO'=>$TIPOEQUIPAMENTO)));
			insereModal("success", "TIPO EQUIPAMENTO {$TIPOEQUIPAMENTO} excluido com sucesso.");
			return true;
		} else {
			insereModal("danger", "Erro ao excluir o TIPO EQUIPAMENTO {$TIPOEQUIPAMENTO}.");
			return false;
		}
	} else {
		insereModal("danger", "Erro ao excluir o TIPO EQUIPAMENTO {$TIPOEQUIPAMENTO}. IDTIPOEQUIP não informado!");
		return false;
	}
}


####################################################################################################################

function listarEquipamentos(){
	$sql = "SELECT  m.idmontadora, 
									m.montadora, 
									t.idtipoequip, 
									t.tipoequipamento, 
									e.idequipamento, 
									e.equipamento
    FROM   vmp_equipamento e, vmp_montadora m, vmp_tipoequip t
   WHERE   e.idmontadora = m.idmontadora
     AND   e.idtipoequip = t.idtipoequip
     AND   e.dtexclusao IS NULL";
	return selectOracle($sql);
}

function pesquisaEquipamentos($dados){
	$sql = "SELECT  m.idmontadora, 
									m.montadora, 
									t.idtipoequip, 
									t.tipoequipamento, 
									e.idequipamento, 
									e.equipamento
    FROM   vmp_equipamento e, vmp_montadora m, vmp_tipoequip t
   WHERE   e.idmontadora = m.idmontadora
     AND   e.idtipoequip = t.idtipoequip
     AND   e.dtexclusao IS NULL";

  if ($dados['IDMONTADORA'] <> 'ALL') {
  	$sql .= PHP_EOL." AND e.idmontadora = {$dados['IDMONTADORA']}";
  }
  if ($dados['IDTIPOEQUIP'] <> 'ALL') {
  	$sql .= PHP_EOL." AND e.idtipoequip = {$dados['IDTIPOEQUIP']}";
  }
  return selectOracle($sql);
}

function buscaEquipamentoID($IDEQUIPAMENTO){
	if (!$IDEQUIPAMENTO || $IDEQUIPAMENTO == 0) {
		return false;
	}

	$sql = "SELECT 
				    e.idequipamento,
				    e.equipamento,
				    e.idtipoequip,
				    t.tipoequipamento,
				    e.idmontadora,
				    m.montadora
				FROM vmp_equipamento e
				LEFT JOIN vmp_tipoequip t ON e.idtipoequip = t.idtipoequip
				LEFT JOIN vmp_montadora m ON e.idmontadora = m.idmontadora
				WHERE e.dtexclusao IS NULL
     			AND e.idequipamento = {$IDEQUIPAMENTO}";

	$ret = selectOracle($sql);
	return ($ret) ? reset($ret): false;

}

function buscaTipoequip($tipoEquipamento){
	$sql = "select * from vmp_tipoequip where dtexclusao is null and tipoEquipamento = '{$tipoEquipamento}'";
	return selectOracle($sql);
}

function equipamentosListar(){
	$sql = "SELECT 
				    e.idequipamento,
				    e.equipamento,
				    e.idtipoequip,
				    t.tipoequipamento,
				    e.idmontadora,
				    m.montadora
				FROM vmp_equipamento e
				LEFT JOIN vmp_tipoequip t ON e.idtipoequip = t.idtipoequip
				LEFT JOIN vmp_montadora m ON e.idmontadora = m.idmontadora
				WHERE e.dtexclusao IS NULL
				ORDER BY e.equipamento ASC";
	return selectOracle($sql);
}

/**
 * Valida se um equipamento com a mesma tríade (Montadora, Tipo, Nome) já existe e está ativo.
 * Otimização: Seleciona apenas o ID para performance.
 */
function equip_validaexiste(array $dados): array|false {
    // Sanitização rigorosa antes da query
    $idMontadora = (int) ($dados['IDMONTADORA'] ?? 0);
    $idTipo      = (int) ($dados['IDTIPOEQUIP'] ?? 0);
    $equipamento = sanitizeOracleString($dados['EQUIPAMENTO'] ?? '');

    $sql = "SELECT IDEQUIPAMENTO 
            FROM vmp_equipamento 
            WHERE dtexclusao IS NULL 
              AND IDMONTADORA = {$idMontadora}
              AND IDTIPOEQUIP = {$idTipo}
              AND EQUIPAMENTO = '{$equipamento}'";
              
    return selectOracle($sql);
}

/**
 * Realiza o MERGE do equipamento com validação de duplicidade e log de auditoria.
 */
function equip_merge(array $dados): bool {
    // Validação de entrada
    $nomeEquip = sanitizeOracleString(trim($dados['EQUIPAMENTO_NEW'] ?? ''));
    
    if (empty($nomeEquip)) {
        exibeModalEspecifico("danger"); // Ajustado para sua função de modal padrão
        // Se usar uma função personalizada de mensagem, mantenha:
        // insereModal("danger", "O nome do equipamento é obrigatório.");
        return false;
    }

    $idUsuario = (int) ($_SESSION['login']['IDUSUARIO'] ?? 0);
    $idEquip   = (int) ($dados['IDEQUIPAMENTO'] ?? 0);
    $idMont    = (int) ($dados['IDMONTADORA_NEW'] ?? 0);
    $idTipo    = (int) ($dados['IDTIPOEQUIP_NEW'] ?? 0);

    // 1. Verificação de Duplicidade
    $dados_validacao = [
        'IDMONTADORA' => $idMont,
        'IDTIPOEQUIP' => $idTipo,
        'EQUIPAMENTO' => $nomeEquip
    ];
    
    $registro_existente = equip_validaexiste($dados_validacao);
    $dados_antigos = $registro_existente ? $registro_existente[0] : null;

    // Se o registro existe e pertence a OUTRO ID, bloqueia (Evita duplicar nome na mesma montadora/tipo)
    if ($dados_antigos && (int)$dados_antigos['IDEQUIPAMENTO'] !== $idEquip) {
        // insereModal("danger", "Impossível processar: O equipamento '{$nomeEquip}' já está cadastrado.");
        return false;
    }

    // 2. Execução do MERGE (Sintaxe OCI8 otimizada para Oracle)
    $sql = "MERGE INTO vmp_equipamento t
            USING (SELECT {$idEquip} AS ID FROM DUAL) s
            ON (t.IDEQUIPAMENTO = s.ID)
            WHEN MATCHED THEN
                UPDATE SET 
                    t.IDMONTADORA = {$idMont},
                    t.IDTIPOEQUIP = {$idTipo},
                    t.EQUIPAMENTO = '{$nomeEquip}',
                    t.IDUSUARIOALT = {$idUsuario},
                    t.DTALTERACAO = SYSDATE
            WHEN NOT MATCHED THEN
                INSERT (IDUSUARIOCAD, IDMONTADORA, IDTIPOEQUIP, EQUIPAMENTO, DTCADASTRO)
                VALUES ({$idUsuario}, {$idMont}, {$idTipo}, '{$nomeEquip}', SYSDATE)";

    try {
        if (executarOracle($sql)) {
            // 3. Registro de Log
            $tipoAcao = ($idEquip > 0) ? 'UPDATE' : 'INSERT';
            
            $logConteudo = [
                'OPERACAO'  => $tipoAcao,
                'DATA_HORA' => date('Y-m-d H:i:s'),
                'ATUAL' => [
                    'ID'        => $idEquip,
                    'NOME'      => $nomeEquip,
                    'MONTADORA' => $idMont,
                    'TIPO'      => $idTipo
                ],
                'ANTERIOR' => $dados_antigos
            ];

            insereLogEquipamento('vmp_equipamento', $tipoAcao, $logConteudo);
            return true;
        }
    } catch (Exception $e) {
        // Log de erro de sistema se necessário
        return false;
    }

    return false;
}

/**
 * Insere log de auditoria em formato JSON para a tabela de logs.
 */
function insereLogEquipamento(string $tabela, string $tipo, string|array $dados): bool {
    $idUsuario = (int) ($_SESSION['login']['IDUSUARIO'] ?? 0);
    $tabela    = sanitizeOracleString($tabela);
    $tipo      = strtoupper(sanitizeOracleString($tipo));
    
    // Tratamento de JSON
    $jsonDados = is_array($dados) ? json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) : $dados;
    $jsonDados = sanitizeOracleString($jsonDados);
    
    $sql = "INSERT INTO vmp_logequipamento (IDUSUARIO, TABELA, TIPO, DADOS, DATA) 
            VALUES ($idUsuario, '$tabela', '$tipo', '$jsonDados', SYSDATE)";
            
    return executarOracle($sql);
}

/**
 * Realiza a exclusão (lógica ou física) do equipamento.
 * Recomendado: Soft Delete para manter integridade com processos do Winthor.
 */
function equip_delete(int $idEquipamento = 0): bool {
    // 1. Tratamento e Validação do ID
    $idUsuario = (int) ($_SESSION['login']['IDUSUARIO'] ?? 0);

    if ($idEquipamento <= 0) {
        insereModal("danger", "Erro ao excluir: ID do equipamento inválido ou não informado.");
        return false;
    }

    // 2. Definição da Query (Exclusão Lógica para preservar rastreabilidade)
    // Se realmente precisar remover do banco, use: "DELETE FROM vmp_equipamento WHERE IDEQUIPAMENTO = {$idEquipamento}"
    $sql = "UPDATE vmp_equipamento 
            SET DTEXCLUSAO = SYSDATE, 
                IDUSUARIOALT = {$idUsuario} 
            WHERE IDEQUIPAMENTO = {$idEquipamento}";

    // 3. Execução
    try {
        if (executarOracle($sql)) {
            // 4. Log de Auditoria Consistente
            $logConteudo = [
                'OPERACAO' => 'DELETE_LOGICO',
                'ID'       => $idEquipamento,
                'DATA'     => date('Y-m-d H:i:s'),
                'DADOS'    => ['IDEQUIPAMENTO' => $idEquipamento]
            ];

            insereLogEquipamento('vmp_equipamento', 'DELETE', $logConteudo);

            insereModal("success", "Equipamento {$idEquipamento} excluído com sucesso.");
            return true;
        } else {
            insereModal("danger", "Erro interno: O banco de dados recusou a exclusão do ID {$idEquipamento}.");
            return false;
        }
    } catch (Exception $e) {
        insereModal("danger", "Erro crítico ao excluir equipamento: " . $e->getMessage());
        return false;
    }
}


function buscaEquipamentoNome($EQUIPAMENTO){
	$sql = "select * from vmp_equipamento where dtexclusao is null and equipamento = '{$EQUIPAMENTO}'";
	return selectOracle($sql);
}


##########################################################################################################################

function buscaExtratoAnaliticoProduto($CODPROD){
	$sql = "SELECT DTMOV
     , HORA
     , CODOPER
     , NOMECODOPER
     , HISTORICO
     , HISTORICO2
     , NUMCAR
     , NUMOP
     , NUMTRANSENT
     , NUMTRANSVENDA
     , CODUSUR
     , CODCLI
     , CLIENTE
     , CGC
     , FANTASIA
     , NUMNOTA
     , NUMSEQ
     , NUMSEQPED
     , PUNIT
     , QTENTRADA
     , QTSAIDA
     , NUMLOTE
     , ROWNUM
FROM (
SELECT TRUNC(PCMOV.DTMOV) DTMOV
     , TO_CHAR(DTMOVLOG, 'HH24:MI') HORA
     , PCMOV.CODOPER
     , PCMOV.NUMOP                                                                                                                                                 
     , PCMOV.NUMSEQ                                                                                                                                                
     , PCMOV.NUMSEQPED                                                                                                                                             
     , ROWNUM                                                                                                                                                      
, DECODE(PCMOV.CODOPER , 'E', DECODE(GREATEST(PCMOV.QT,0),0,'Entrada Cancelada','Entrada Merc.')                                                         
                             ,'EB',DECODE(GREATEST(PCMOV.QT,0),0,'Bonif. Cancelada','Entrada Bonific.')                                                  
                             ,'ET',DECODE(GREATEST(PCMOV.QT,0),0,'Entrada Transf. Cancelada','Entrada Transf.')                                          
                             ,'EA',DECODE(GREATEST(PCMOV.QT,0),0,'Entrada Ajuste Cancelada','Ajuste Estoque')                                            
                             ,'E1',DECODE(GREATEST(PCMOV.QT,0),0,'Entrada Avaria Cancelada','Entrada Avaria')                                            
                             ,'EI',DECODE(GREATEST(PCMOV.QT,0),0,'Ajuste Invent. Cancelado','Ajuste Invent.')                                            
                             ,'ED',DECODE(GREATEST(PCMOV.QT,0),0,'Devolucao Cancelada','Dev. Cliente')                                                   
                             ,'EN',DECODE(GREATEST(PCMOV.QT,0),0,'Dev. Venda Consig. Cancelada','Dev. Venda Consignada')                                 
                             ,'ER',DECODE(GREATEST(PCMOV.QT,0),0,'Simples Remessa Cancelada','Simples Remessa')                                          
                             ,'ES',DECODE(GREATEST(PCMOV.QT,0),0,'Sobra Mercadoria Cancelamento','Sobra de Mercadoria')                                  
                             ,'EM',DECODE(GREATEST(PCMOV.QT,0),0,'Ent. Mat. Consumo Cancelada','Entrada Materiais de Consumo')                           
                             ,'EC',DECODE(GREATEST(PCMOV.QT,0),0,'Ent. Consig. Cancelada','Entrada Consignacao')                                         
                             ,'EO',DECODE(GREATEST(PCMOV.QT,0),0,'Dev. Comodato Cancelada','Devolucao de Comodato')                                      
                             ,'EX',DECODE(GREATEST(PCMOV.QT,0),0,'Dev. Avulsa Cancelada','Devolucao Avulsa')                                             
                             ,'EG',DECODE(GREATEST(PCMOV.QT,0),0,'Entrada Benefic. Cancelada','Entrada de Beneficiamento')                               
                             ,'S', DECODE(GREATEST(PCMOV.QT,0),0,'NF Cancelada','Saida')                                                                 
                             ,'SD',DECODE(GREATEST(PCMOV.QT,0),0,'Dev. Cancelada','Dev. Fornecedor')                                                     
                             ,'SB',DECODE(GREATEST(PCMOV.QT,0),0,'Saida Bonific. Cancelada','Saida Bonific.')                                            
                             ,'ST',DECODE(GREATEST(PCMOV.QT,0),0,'Saida Transf. Cancelada','Saida Transf.')                                              
                             ,'S1',DECODE(GREATEST(PCMOV.QT,0),0,'Avaria Reaprov. Cancelada','Avaria por Reaproveitamento')                              
                             ,'SS',DECODE(GREATEST(PCMOV.QT,0),0,'Saída Sobra Cancelada','Saída de Sobra')                                               
                             ,'SA',DECODE(GREATEST(PCMOV.QT,0),0,'Saída Ajuste Cancelada','Ajuste Estoque')                                              
                             ,'SI',DECODE(GREATEST(PCMOV.QT,0),0,'Ajuste Invent. Cancelado','Ajuste Invent.')                                            
                             ,'SR',DECODE(GREATEST(PCMOV.QT,0),0,'Simples Remessa Cancelada','Simples Remessa')                                          
                             ,'SC',DECODE(GREATEST(PCMOV.QT,0),0,'Saída Consig. Cancelada','Saída Consignacao')                                          
                             ,'SO',DECODE(GREATEST(PCMOV.QT,0),0,'Remessa Comodato Cancelada','Remessa de Comodato')                                     
                             ,'SF',DECODE(GREATEST(PCMOV.QT,0),0,'Dev. Comodato Fornec. Cancelada',
'Devolucao de Comodato a Fornecedor')                 
                             ,'RA',DECODE(GREATEST(PCMOV.QT,0),0,'Req. Avulsa Cancelada','Requisicao Avulsa')                                            
                             ,'EP',DECODE(GREATEST(PCMOV.QT,0),0,'Cancelamento Producao','Entrada Producao')                                             
                             ,'SP',DECODE(GREATEST(PCMOV.QT,0),0,'Cancelamento Producao','Requisicao Mat.Prima')                                         
                             ,'SV',DECODE(GREATEST(PCMOV.QT,0),0,'Saída Avaria Cancelada','Saída por Avaria')                                            
                             ,'SM',DECODE(GREATEST(PCMOV.QT,0),0,'Saída Mat. Consumo Cancelada','Saída Materiais de Consumo')                            
                             ,'SL',DECODE(GREATEST(PCMOV.QT,0),0,'Saída Perda Cancelada','Saída de Perda')                                               
                             ,'EL',DECODE(GREATEST(PCMOV.QT,0),0,'Ent. Perda Cancelada','Entrada de Perda')                                              
                             ,'EF',DECODE(GREATEST(PCMOV.QT,0),0,'Entrada Comodato Cancelada','Entrada de Comodato')                                     
                             ,'SN',DECODE(GREATEST(PCMOV.QT,0),0,'Saída Benefic. Cancelada','Saída de Beneficiamento')                                   
                             ,'EV',DECODE(GREATEST(PCMOV.QT,0),0,'Ent. Dev. Rem. Benefic. Cancelada',
'Entrada Devolucao de Remessa para Beneficiamento') 
                             ,'EG',DECODE(GREATEST(PCMOV.QT,0),0,'Ent. Benefic. Cancelada','Entrada de Beneficiamento')                                  
                             ,'Desconhecido') NOMECODOPER                                                                                                    
     ,(CASE                                                                                                                                                        
        WHEN SUBSTR(PCMOV.CODOPER, 1, 1) = 'S' THEN                                                                                                              
         (SELECT /*+ index(PCLANC PCLANC_IDX1)*/                                                                                                                   
           PCLANC.HISTORICO 
            FROM PCLANC 
           WHERE DTLANC = TRUNC(PCMOV.DTMOV) 
             AND DTESTORNOBAIXA IS NULL 
             AND ROWNUM = 1 
             AND NUMTRANSVENDA = PCMOV.NUMTRANSVENDA 
             AND (PCMOV.CODOPER) <> 'SP' 
             AND PCLANC.historico IS NOT NULL 
             AND ROWNUM = 1) 
        WHEN SUBSTR(PCMOV.CODOPER, 1, 1) = 'E' THEN 
         (SELECT /*+ index(PCLANC PCLANC_IDX1)*/ 
           PCLANC.HISTORICO  
            FROM PCLANC 
           WHERE DTLANC = TRUNC(PCMOV.DTMOV) 
             AND DTESTORNOBAIXA IS NULL 
             AND ROWNUM = 1 
             AND NUMTRANSENT = PCMOV.NUMTRANSENT 
             AND (PCMOV.CODOPER) NOT IN ('EP', 'EX') 
             AND PCLANC.historico IS NOT NULL 
             AND ROWNUM = 1) 
        ELSE 
         '' 
      END) HISTORICO 
     ,(CASE 
        WHEN SUBSTR(PCMOV.CODOPER, 1, 1) = 'S' THEN 
         (SELECT /*+ index(PCLANC PCLANC_IDX1)*/ 
           PCLANC.HISTORICO2 
            FROM PCLANC 
           WHERE DTLANC = TRUNC(PCMOV.DTMOV) 
             AND DTESTORNOBAIXA IS NULL 
             AND ROWNUM = 1 
             AND NUMTRANSVENDA = PCMOV.NUMTRANSVENDA 
             AND (PCMOV.CODOPER) <> 'SP' 
             AND PCLANC.historico2 IS NOT NULL 
             AND ROWNUM = 1) 
        WHEN SUBSTR(PCMOV.CODOPER, 1, 1) = 'E' THEN 
         (SELECT /*+ index(PCLANC PCLANC_IDX1)*/ 
           PCLANC.HISTORICO2 
            FROM PCLANC 
           WHERE DTLANC = TRUNC(PCMOV.DTMOV) 
             AND DTESTORNOBAIXA IS NULL 
             AND ROWNUM = 1 
             AND NUMTRANSENT = PCMOV.NUMTRANSENT 
             AND (PCMOV.CODOPER) NOT IN ('EP', 'EX') 
             AND PCLANC.historico2 IS NOT NULL 
             AND ROWNUM = 1) 
        ELSE 
         '' 
      END) HISTORICO2 
     , NVL(PCMOV.NUMCAR,0) NUMCAR
     , NVL(PCMOV.NUMTRANSENT,0) NUMTRANSENT
     , NVL(PCMOV.NUMTRANSVENDA,0) NUMTRANSVENDA
     , PCMOV.CODUSUR
     , CASE WHEN (PCMOV.CODOPER IN ('EP','SP','EA', 'SA', 'S1', 'SL', 'EI', 'SI', 'EL', 'ES','SV')) THEN
            PCMOV.CODFUNCLANC
       ELSE 
            CASE WHEN (PCMOV.CODOPER IN ('E', 'EB', 'ET', 'EG', 'ER', 'EC')) THEN
                 PCMOV.CODFORNEC
            ELSE
                 PCMOV.CODCLI
            END
       END  CODCLI 
     , CASE WHEN (PCMOV.CODOPER IN ('EP','SP','EA', 'SA', 'S1', 'SL', 'EI', 'SI', 'EL', 'ES','SV')) THEN
            PCEMPR.NOME
       ELSE
            CASE WHEN (PCMOV.CODOPER IN ('E', 'EB', 'ET', 'EG', 'ER', 'EC')) THEN
                CASE
                    WHEN (CODOPER = 'ET') AND
                         (SELECT COUNT(*)
                            FROM PCNFENT
                           WHERE NUMTRANSENT = PCMOV.NUMTRANSENT
                             AND TIPODESCARGA IN ('6', '8')) > 0 THEN
                     (SELECT TRIM(CLIENTE)
                        FROM PCCLIENT C
                       WHERE CODCLI = PCMOV.CODFORNEC)
                    ELSE                                                                                                                                           
                     TRIM(PCFORNEC.FORNECEDOR) END                                                                                                                       
            ELSE                                                                                                                                                   
                 TRIM(PCCLIENT.CLIENTE)                                                                                                                                  
            END                                                                                                                                                    
       END CLIENTE                                                                                                                                                 
     , CASE WHEN (PCMOV.CODOPER IN ('EP','SP','EA', 'SA', 'EI', 'SI', 'S1', 'SL', 'EL', 'ES', 'SV')) THEN                                    
            PCEMPR.CPF                                                                                                                                             
       ELSE                                                                                                                                                        
            CASE WHEN (PCMOV.CODOPER IN ('E', 'EB', 'ET', 'EG', 'ER', 'SD')) THEN                                                                      
                CASE                                                                                                                                               
                    WHEN (CODOPER = 'ET') AND                                                                                                                    
                         (SELECT COUNT(*)                                                                                                                          
                            FROM PCNFENT                                                                                                                           
                           WHERE NUMTRANSENT = PCMOV.NUMTRANSENT                                                                                                   
                             AND TIPODESCARGA IN ('6', '8')) > 0 THEN                                                                                          
                     (SELECT CGCENT                                                                                                                                
                        FROM PCCLIENT C                                                                                                                            
                       WHERE CODCLI = PCMOV.CODFORNEC)                                                                                                             
                    ELSE                                                                                                                                           
                     PCFORNEC.CGC END                                                                                                                              
            ELSE                                                                                                                                                   
                 PCCLIENT.CGCENT                                                                                                                                   
            END                                                                                                                                                    
       END CGC                                                                                                                                                     
     , CASE WHEN (PCMOV.CODOPER IN ('EP','SP','EA', 'SA', 'EI', 'SI', 'S1', 'SL', 'EL', 'ES', 'SV')) THEN                                    
            PCEMPR.NOME                                                                                                                                            
       ELSE                                                                                                                                                        
            CASE WHEN (PCMOV.CODOPER IN ('E', 'EB', 'ET', 'EG', 'ER', 'SD')) THEN                                                                      
                CASE                                                                                                                                               
                    WHEN (CODOPER = 'ET') AND                                                                                                                    
                         (SELECT COUNT(*)                                                                                                                          
                            FROM PCNFENT                                                                                                                           
                           WHERE NUMTRANSENT = PCMOV.NUMTRANSENT                                                                                                   
                             AND TIPODESCARGA IN ('6', '8')) > 0 THEN                                                                                          
                     (SELECT TRIM(FANTASIA)                                                                                                                              
                        FROM PCCLIENT C                                                                                                                            
                       WHERE CODCLI = PCMOV.CODFORNEC)                                                                                                             
                    ELSE                                                                                                                                           
                     TRIM(PCFORNEC.FANTASIA) END                                                                                                                         
            ELSE                                                                                                                                                   
                 TRIM(PCCLIENT.FANTASIA)                                                                                                                                 
            END                                                                                                                                                    
       END FANTASIA                                                                                                                                                
     , PCMOV.NUMNOTA                                                                                                                                               
     , NVL(PCMOV.PUNIT,0) PUNIT                                                                                                                                    
     , CASE WHEN (SUBSTR(PCMOV.CODOPER,1,1) = 'E') OR (SUBSTR(PCMOV.CODOPER,1,1) = 'D') THEN                                                                   
            CASE WHEN (DECODE(NVL(PCMOVCOMPLE.QTRETORNOTV13, 0), 0, NVL(PCMOV.QT, 0), PCMOVCOMPLE.QTRETORNOTV13) > 0) THEN                                                                                                            
                 DECODE(NVL(PCMOVCOMPLE.QTRETORNOTV13, 0), 0, NVL(PCMOV.QT, 0), PCMOVCOMPLE.QTRETORNOTV13)                                                                                                                              
            END
       ELSE
            CASE WHEN (DECODE(NVL(PCMOVCOMPLE.QTRETORNOTV13, 0), 0, NVL(PCMOV.QT, 0), PCMOVCOMPLE.QTRETORNOTV13) < 0) THEN
                 NVL((DECODE(NVL(PCMOVCOMPLE.QTRETORNOTV13, 0), 0, NVL(PCMOV.QT, 0), PCMOVCOMPLE.QTRETORNOTV13)*(-1)),0)
            END
       END QTENTRADA
     , CASE WHEN (SUBSTR(PCMOV.CODOPER,1,1) = 'S') OR (SUBSTR(PCMOV.CODOPER,1,1) = 'R') THEN
            CASE WHEN (PCMOV.QT > 0) THEN
                  NVL(PCMOV.QT*(-1),0)
            END
       ELSE
            CASE WHEN (PCMOV.QT < 0) THEN
                 NVL(PCMOV.QT,0)
            END
       END QTSAIDA
     , PCMOV.NUMLOTE
  FROM PCMOV
     , PCEMPR
     , PCCLIENT
     , PCFORNEC
     , PCPRODUT
     , PCMOVCOMPLE
 WHERE PCMOV.CODPROD = {$CODPROD}
   AND PCMOV.CODPROD = PCPRODUT.CODPROD
   AND PCMOV.NUMTRANSITEM = PCMOVCOMPLE.NUMTRANSITEM(+)
   AND NVL(PCMOVCOMPLE.MOVEST, 'S') = 'S' 
   AND PCMOV.CODFORNEC = PCFORNEC.CODFORNEC(+)
   AND PCMOV.CODFUNCLANC = PCEMPR.MATRICULA(+)
   --AND PCMOV.CODUSUR = PCEMPR.MATRICULA(+)
   AND PCMOV.CODCLI = PCCLIENT.CODCLI(+)                                                                                                                           
   AND NVL(PCMOV.CODFILIALNF, PCMOV.CODFILIAL) = '1'                                                                                                        
   AND PCMOV.STATUS IN ('B','AB')                                                                                                                              
   AND ( NOT EXISTS                                                                                                                                                
   (SELECT NUMPED FROM PCPEDC WHERE NUMPED = DECODE(PCMOV.CODOPER, 'EP', -1, 'SP',-1, PCMOV.NUMPED) AND CONDVENDA = 7) OR 
(SUBSTR(PCMOV.CODOPER,1,1) = 'E')) 
   AND NOT EXISTS (SELECT DISTINCT (PCNFSAID.NUMTRANSVENDA)                                                                                                        
           FROM PCNFSAID, PCPRODUT                                                                                                                                 
          WHERE PCNFSAID.NUMTRANSVENDA = PCMOV.NUMTRANSVENDA                                                                                                       
            AND PCNFSAID.CODFILIAL = PCMOV.CODFILIAL                                                                                                               
            AND PCMOV.CODOPER = 'S'                                                                                                                              
            AND PCNFSAID.CONDVENDA IN (4, 7, 14)                                                                                                                   
            AND PCMOV.CODPROD = PCPRODUT.CODPROD                                                                                                                   
            AND PCPRODUT.TIPOMERC = 'CB')                                                                                                                        
   AND NOT (PCMOV.CODOPER IN ('EA','SA') AND                                                                                                                   
            PCMOVCOMPLE.NUMINVENT IS NOT NULL)                                                                                                                     
    AND NOT (FERRAMENTAS.F_BUSCARPARAMETRO_ALFA('DEVOLVESIMPLESREMTV13TOTAL',PCMOV.CODFILIAL,'N') = 'S' 
 AND PCMOV.ROTINACAD LIKE '%1332%' AND NVL(PCMOVCOMPLE.QTRETORNOTV13,0) = 0)                                                                                                                                           
   AND NOT EXISTS (SELECT NUMNOTA FROM PCNFSAID WHERE NUMTRANSVENDA = PCMOV.NUMTRANSVENDA AND SITUACAONFE IN (110,205,301,302,
303))         
     )
  ORDER BY DTMOV ASC";
	return selectOracle($sql);
}

function vide_pesquisar($CODPECA){
  $sql = "SELECT * FROM TABLE(FN_CONSULTAVIDE('{$CODPECA}')) ORDER BY NOMEOPCAO DESC, VIDE ASC";
  // varDump2($sql);
  if ($ret = selectOracle($sql) ){
    foreach ($ret as $key => $value) {
      if (!isset($_SESSION['VIDE'])) 
        $_SESSION['VIDE']=[];
      
      if (!isset($_SESSION['VIDE'][$value['IDVIDE']])) 
        $_SESSION['VIDE'][$value['IDVIDE']] = $value;
      
    }
  }
}

function listaVidesAtivos(){
  $sql = "SELECT * FROM ORCVIDE WHERE DTEXCLUSAO IS NULL";
  if ($ret = selectOracle($sql)) {
    $map = [];
    foreach ($ret as $key => $value) {
      $chaveVide = $value["CODPECA"].'|'.$value["VIDE"].'|'.$value["APLICMARCA"];
      if (!isset($map[$chaveVide])) {
        $map[$chaveVide] = $value;
      }
    }
    return $map;
  } else {
    return false;
  }
}

function buscaLogVide($IDVIDE){
  $sql = "SELECT * FROM ORCLOGVIDE WHERE IDVIDE = {$IDVIDE} ORDER BY IDLOGVIDE ASC";
  return selectOracle($sql);
}

function vide_merge(array $dados, bool $is_recursive = false): int|array|false {
    
    // 1. Lógica de Recursividade (Tratamento de múltiplos VIDES)
    if (!$is_recursive) {
        $vide_str = trim($dados['VIDE'] ?? '');
        // Split por espaços ou vírgulas
        $vides = preg_split('/[\s,]+/', $vide_str, -1, PREG_SPLIT_NO_EMPTY);

        if (count($vides) > 1) {
            $resultados = [];
            foreach ($vides as $v) {
                $dados_temp = $dados;
                $dados_temp['VIDE'] = sanitizeOracleString($v, 0, true); 
                $res = vide_merge($dados_temp, true);
                if ($res) $resultados[] = $res;
            }
            return $resultados; 
        }
        $dados['VIDE'] = !empty($vides) ? sanitizeOracleString($vides[0], 0, true) : '';
    }

    $dados_old = vide_validaExiste($dados);

    // 2. Higienização e Preparação (PHP 8)
    // Usando parâmetros: 0 (sem limite) e true (maiúsculas)
    $codPeca    = sanitizeOracleString($dados['CODPECA'] ?? '', 0, true);
    $vide       = sanitizeOracleString($dados['VIDE'] ?? '', 0, true);
    $aplicMarca = sanitizeOracleString($dados['APLICMARCA'] ?? '', 0, true);
    $descricao  = sanitizeOracleString($dados['DESCRICAO'] ?? '', 0, true);
    $marca      = sanitizeOracleString($dados['MARCA'] ?? '', 0, true);
    $importado  = sanitizeOracleString($dados['IMPORTADO'] ?? 'N', 0, true);
    $preco      = sanitizeOracleString($dados['PRECO'] ?? '0', 0, true);
    $data       = sanitizeOracleString($dados['DATA'] ?? '', 0, true);
    $idUsuario  = (int) ($_SESSION['login']['IDUSUARIO'] ?? 0);

    $nomeOpcao  = empty($vide) ? 'NPR' : (!empty($dados['NOMEOPCAO']) ? sanitizeOracleString($dados['NOMEOPCAO'], 0, true) : 'VIDE');

    // 3. OTIMIZAÇÃO DE PERFORMANCE: Comparação exata para uso de ÍNDICES
    // Removido o TRIM() do SQL para permitir que o Oracle use o Index Range Scan
    $matchVide  = empty($vide)  ? "d.VIDE IS NULL"       : "d.VIDE = '$vide'";
    $matchAplic = empty($aplicMarca) ? "d.APLICMARCA IS NULL" : "d.APLICMARCA = '$aplicMarca'";
    $matchNome  = "d.NOMEOPCAO = '$nomeOpcao'";

    // 4. Montagem do MERGE
    $sql = "MERGE INTO ORCVIDE d
            USING DUAL s
            ON (
                d.CODPECA = '$codPeca' AND 
                $matchVide AND 
                $matchAplic AND 
                $matchNome AND 
                d.DTEXCLUSAO IS NULL
            )
            WHEN MATCHED THEN
                UPDATE SET 
                    d.DESCRICAO = '$descricao',
                    d.MARCA = '$marca',
                    d.IMPORTADO = '$importado',
                    d.PRECO = '$preco',
                    d.DATA = '$data',
                    d.DTULTALTERACAO = SYSDATE,
                    d.USUARIOULTALTERACAO = $idUsuario
            WHEN NOT MATCHED THEN
                INSERT (IDVIDE, DTCADASTRO, USUARIOCADASTRO, CODPECA, VIDE, APLICMARCA, DESCRICAO, MARCA, NOMEOPCAO, IMPORTADO, PRECO, DATA)
                VALUES (
                    (SELECT NVL(MAX(IDVIDE),0)+1 FROM ORCVIDE),
                    SYSDATE,
                    $idUsuario,
                    '$codPeca',
                    " . (empty($vide) ? "NULL" : "'$vide'") . ",
                    " . (empty($aplicMarca) ? "NULL" : "'$aplicMarca'") . ",
                    '$descricao',
                    '$marca',
                    '$nomeOpcao',
                    '$importado',
                    '$preco',
                    '$data'
                )";

    // 5. Execução e Retorno do ID
		if (executarOracle($sql)) {
        
        // Utilizamos a função solicitada para buscar os dados recém inseridos/atualizados
        $dados_new = vide_validaExiste($dados);

        if ($dados_new) {
            // Atualiza a sessão global com os novos dados da peça
            $_SESSION['VIDE'][$dados_new['IDVIDE']] = $dados_new;

            // Lógica de Auditoria: Se existia um estado anterior, define como EDITAR, senão INCLUIR
            $tipoLog = ($dados_old) ? 'EDITAR' : 'INCLUIR';

            /**
             * IMPORTANTE: Removi o varDump2() e o die() daqui.
             * Se eles permanecerem, em casos de múltiplos VIDES, o sistema processaria 
             * apenas o primeiro e travaria a execução do script.
             */
            if (function_exists('geraORCLOGVIDE')) {
                geraORCLOGVIDE($tipoLog, $dados_old, $dados_new);
            }

            // Retorna o ID único gerado pelo Oracle
            return (int) $dados_new['IDVIDE'];
        }
    }

    return false;
}

function vide_validaExiste(array $dados): array|false {
    // Sanitização e uppercase via PHP antes da query
    $codPeca    = sanitizeOracleString($dados['CODPECA'] ?? '', 0, true);
    $vide       = sanitizeOracleString($dados['VIDE'] ?? '', 0, true);
    $aplicMarca = sanitizeOracleString($dados['APLICMARCA'] ?? '', 0, true);
    $nomeOpcao  = sanitizeOracleString($dados['NOMEOPCAO'] ?? '', 0, true);

    // Tratamento nativo de IS NULL substituindo a gambiarra do str_replace
    $matchCodPeca = empty($codPeca) ? "CODPECA IS NULL" : "TRIM(CODPECA) = '$codPeca'";
    $matchVide = empty($vide) ? "VIDE IS NULL" : "TRIM(VIDE) = '$vide'";
    $matchAplicMarca = empty($aplicMarca) ? "APLICMARCA IS NULL" : "TRIM(APLICMARCA) = '$aplicMarca'";
    $matchNomeOpcao = empty($nomeOpcao) ? "NOMEOPCAO IS NULL" : "TRIM(NOMEOPCAO) = '$nomeOpcao'";

    $sql = "SELECT * FROM ORCVIDE 
            WHERE DTEXCLUSAO IS NULL
            AND $matchCodPeca
            AND $matchVide
            AND $matchAplicMarca
            AND $matchNomeOpcao";

    $ret = selectOracle($sql);

    return $ret ? reset($ret) : false;
}

function buscaDadosORCVIDE(int|string $IDVIDE): array|false {
    // Cast obrigatório para INT para blindar contra SQL Injection na PK
    $id = (int)$IDVIDE;
    if ($id <= 0) return false;

    $sql = "SELECT * FROM ORCVIDE WHERE IDVIDE = " . $id;
    $ret = selectOracle($sql);
    return $ret ? reset($ret) : false;
}

function geraORCLOGVIDE(string $tipo, array|false $dados_old, array|false $dados_new): bool {

    $idUsuario = (int)($_SESSION['login']['IDUSUARIO'] ?? 0);
    $nomeUsuario = sanitizeOracleString($_SESSION['login']['NOME'] ?? '', 0, true);

    // Dados Antigos (seguros)
    $oldCod = $dados_old ? sanitizeOracleString($dados_old['CODPECA'] ?? '', 0, true) : 'NULL';
    $oldApl = $dados_old ? sanitizeOracleString($dados_old['APLICMARCA'] ?? '', 0, true) : 'NULL';
    $oldVid = $dados_old ? sanitizeOracleString($dados_old['VIDE'] ?? '', 0, true) : 'NULL';
    $oldDes = $dados_old ? sanitizeOracleString($dados_old['DESCRICAO'] ?? '', 0, true) : 'NULL';
    $oldNom = $dados_old ? sanitizeOracleString($dados_old['NOMEOPCAO'] ?? '', 0, true) : 'NULL';
    $idVideOld = $dados_old ? (int)($dados_old['IDVIDE'] ?? 0) : 0;

    // Dados Novos (seguros)
    $newCod = $dados_new ? sanitizeOracleString($dados_new['CODPECA'] ?? '', 0, true) : 'NULL';
    $newApl = $dados_new ? sanitizeOracleString($dados_new['APLICMARCA'] ?? '', 0, true) : 'NULL';
    $newVid = $dados_new ? sanitizeOracleString($dados_new['VIDE'] ?? '', 0, true) : 'NULL';
    $newDes = $dados_new ? sanitizeOracleString($dados_new['DESCRICAO'] ?? '', 0, true) : 'NULL';
    $newNom = $dados_new ? sanitizeOracleString($dados_new['NOMEOPCAO'] ?? '', 0, true) : 'NULL';
    $idVideNew = $dados_new ? (int)($dados_new['IDVIDE'] ?? 0) : $idVideOld;

    if ($tipo === 'INCLUIR' || $tipo === 'EXCLUIR' || $tipo === 'EDITAR') {
        $sql = "INSERT INTO ORCLOGVIDE 
                (TIPO, IDUSUARIO, USUARIO, IDVIDE, 
                	CODPECA_OLD, CODPECA_NEW, 
                	APLICMARCA_OLD, APLICMARCA_NEW, 
                	VIDE_OLD, VIDE_NEW, 
                	DESCRICAO_OLD, DESCRICAO_NEW, 
                	NOMEOPCAO_OLD, NOMEOPCAO_NEW
                ) VALUES (
                 '$tipo', $idUsuario, '$nomeUsuario', $idVideNew, 
                 '$oldCod', '$newCod', 
                 '$oldApl', '$newApl', 
                 '$oldVid', '$newVid', 
                 '$oldDes', '$newDes', 
                 '$oldNom', '$newNom'
                )";

        return executarOracle($sql);
    } else {
        insereModal("danger", "Tipo informado não é válido: " . sanitizeOracleString($tipo, 0, true));
        return false;
    }
}

function vide_excluir(array $dados_old): void {
    if (empty($dados_old['IDVIDE']) || !is_numeric($dados_old['IDVIDE'])) {
        throw new Exception("IDVIDE não identificado ou inválido!");
    }

    $idVide = (int)$dados_old['IDVIDE'];
    $usuario = sanitizeOracleString($_SESSION['login']['NOME'] ?? '', 0, true);

    $sql = "UPDATE ORCVIDE SET 
            DTEXCLUSAO = SYSDATE, 
            USUARIOEXCLUSAO = '$usuario' 
            WHERE IDVIDE = $idVide";

    executarOracle($sql);

    if (isset($_SESSION['VIDE'][$idVide])) {
        unset($_SESSION['VIDE'][$idVide]);
    }

    geraORCLOGVIDE('EXCLUIR', $dados_old, false);
}

function buscaResumoVides(): array|false 
{
    // 1. Definição da Query Base (Agregação Condicional)
    // Mantemos os SUM(CASE) para que, mesmo filtrando, os contadores internos sejam consistentes
    $sql = "SELECT 
                COUNT(*) AS QT_TOTAL,
                SUM(CASE WHEN v.dtexclusao IS NULL THEN 1 ELSE 0 END) AS QT_ATIVOS,
                SUM(CASE WHEN v.dtexclusao IS NOT NULL THEN 1 ELSE 0 END) AS QT_INATIVOS
            FROM orcvide v";

    // 3. Execução via OCI8 (Utilizando sua infraestrutura procedural)
    $ret = selectOracle($sql);

    // Retorna a primeira linha do resultado ou false
    return ($ret && count($ret) > 0) ? reset($ret) : false;
}

function pesquisaNotasEntrega(array $dados): array|false {

	$sql = "SELECT 
        n.numnota,
        c.codcli as cli_codcli,
        c.cliente as cli_cliente,
        f.codigo as fil_codfilial,
        f.razaosocial as fil_filial
    FROM pcnfsaid n
    INNER JOIN pcclient c ON n.codcli = c.codcli
    INNER JOIN pcfilial f ON n.codfilial = f.codigo
    WHERE n.dtsaida >= trunc(SYSDATE - 30)
    AND n.codfilial = {$dados['CODFILIAL']}".PHP_EOL;

  if (isset($dados['CODCLI']) && $dados['CODCLI'] <> "") {
  	$sql .= " AND c.codcli = {$dados['CODCLI']}".PHP_EOL;
  } else  if (isset($dados['NUMNOTA']) && $dados['NUMNOTA'] <> "") {
  	$sql .= " AND n.numnota = {$dados['NUMNOTA']}".PHP_EOL;
  } else {
  	exibeMensagem("ERRO. Nenhum filtro válido informado!");
  	return false;
  }
  
  // varDump2($sql);
  $ret = selectOracle($sql);
  return $ret;
}

function buscaDadosEtiquetaEntrega(int $NUMNOTA): array|false {

	$sql = "WITH param AS (
        SELECT 
            3 AS volumes, 
            'teste' AS obs 
        FROM DUAL
    )
    SELECT 
        n.numnota,
        p.volumes,
        p.obs,
        c.codcli as cli_codcli,
        c.cliente as cli_cliente,
        c.fantasia as cli_fantasia,
        c.enderent as cli_endereco,
        c.numeroent as cli_numero,
        c.bairroent as cli_bairro,
        c.pontorefer as cli_pontoref,
        c.municent as cli_cidade,
        c.estent as cli_uf,
        c.cepent as cli_cep,
        f.codigo as fil_codfilial,
        f.razaosocial as fil_razao,
        f.endereco as fil_endereco,
        f.numero2 as fil_numero,
        f.bairro as fil_bairro,
        f.cep as fil_cep,
        f.cidade as fil_cidade,
        f.uf as fil_uf,
        f.telefone as fil_telefone
    FROM pcnfsaid n
    INNER JOIN pcclient c ON n.codcli = c.codcli
    CROSS JOIN param p
    CROSS JOIN pcfilial f
    WHERE f.codigo = 1
    AND n.numnota = {$NUMNOTA}";
    
    $ret = selectOracle($sql);
    return ($ret && count($ret) > 0) ? reset($ret) : false;
}