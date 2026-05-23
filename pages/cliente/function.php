<?php  

function geraNovoOrcamentoCliente(){
	// varDump2($_SESSION['login']);
	$ORCAMENTO 							= array();
	$ORCAMENTO['CAB']      	= array();

	$ORCAMENTO['CLIENTE']   = buscaDadosCliente($_SESSION['login']['CODCLI']);
	$ORCAMENTO['VENDEDOR']  = buscaDadosVendedor(($_SESSION['login']['CODUSUR']<>"")?$_SESSION['login']['CODUSUR']:1);
	$ORCAMENTO['FILIAL']    = buscaDadosFilial($ORCAMENTO['CLIENTE']['CODFILIALNF']);
	$ORCAMENTO['COBRANCA']  = buscaDadosCobranca($ORCAMENTO['CLIENTE']['CODCOB']);
	$ORCAMENTO['PLPAG']     = buscaDadosPlpag($ORCAMENTO['CLIENTE']['CODPLPAG']);
	$ORCAMENTO['CAB']       = geraNovoCabecalho($ORCAMENTO);
	$ORCAMENTO['ITEM']      = array();
	return buscaUltimoIDORCAMENTOCliente($ORCAMENTO['CAB']);
}

function buscaUltimoIDORCAMENTOCliente($dados){
	$sql = "SELECT MAX(IDORCAMENTO) AS IDORCAMENTO FROM ORCORCAMENTOC WHERE CODCLI = '".$dados['CODCLI']."' AND CODUSUR = '".$dados['CODUSUR']."'";
	if($ret = selectOracle($sql)){
		return $ret[0]['IDORCAMENTO'];
	} else {
		return false;
	}
}


function buscaDadosCliente($CODCLI){
	if (is_array($CODCLI) && isset($CODCLI['CODCLI'])) {
		$CODCLI = $CODCLI['CODCLI'];
	} else {
		$CODCLI = (is_null($CODCLI)?$_SESSION['login']['CODCLI']:$CODCLI);
	}
	$sql = "SELECT C.CODCLI,
				C.CGCENT,
				C.CLIENTE,
				C.FANTASIA,
				NVL(C.CODFILIALNF, 1) AS CODFILIALNF,
				NVL(NVL(P.NUMREGIAO, C.NUMREGIAOCLI), 1) AS NUMREGIAO,
				NVL(C.CODPLPAG, 1) AS CODPLPAG,
        NVL(COB.CODCOB, 'DH') AS CODCOB,
        NVL(COB.NIVELVENDA, 5) AS NIVELVENDA,
				DECODE(C.TIPOFJ, 'J', 'JURÍDICA', 'FÍSICA') AS TIPOFJ,
				NVL(C.MUNICENT, C.MUNICCOB) AS MUNICIPIO,
				NVL(C.ESTENT, C.ESTCOB) AS UF,
		  DBMS_LOB.SUBSTR(C.MOTIVOBLOQ) AS MOTIVOBLOQ,
		  NVL(ABS(ROUND(D.PERCDESC,0)), 0) AS PERCDESC,
		  NVL(C.LIMCRED, 0) AS LIMCRED,
		  NVL((NVL(C.LIMCRED, 0) -
			  (SELECT NVL(SUM(P.VALOR), 0)
				  FROM PCPREST P
				 WHERE P.DTPAG IS NULL
				   AND P.CODCLI = C.CODCLI)),
			  0) AS LIMCREDDISP,
		  NVL((SELECT SUM(CRE.VALOR)
				FROM PCCRECLI CRE
			   WHERE CRE.CODCLI = C.CODCLI
				 AND CRE.DTDESCONTO IS NULL),
			  0) AS VLCREDITO
  FROM PCCLIENT C
	  LEFT JOIN PCTABPRCLI P ON C.CODCLI = P.CODCLI
	  LEFT JOIN PCDESCONTO D ON C.CODCLI = D.CODCLI
    LEFT JOIN PCCOB COB ON NVL(C.CODCOB, 'DH') = COB.CODCOB
	 WHERE C.DTEXCLUSAO IS NULL
	   AND C.CODCLI = ".$CODCLI;
	// varDump2($sql); die();
	return reset(selectOracle($sql));
}

function buscaDadosVendedor($CODUSUR){
	if ($CODUSUR == null) {
		exibeMensagem("ERRRO - Não foi possível buscar os dados do Vendedor pois o CODUSUR está vazio.");
		return false;
	} else {
		$sql = "SELECT U.CODUSUR,
				 U.NOME,
				 U.DTINICIO,
				 U.DTEXCLUSAO,
				 DECODE(U.TIPOVEND,'I', 'INTERNO','E', 'EXTERNO', U.TIPOVEND) AS TIPOVEND,
				 U.EMAIL,
				 NVL(U.CODFILIAL,1) AS CODFILIAL
			FROM PCUSUARI U
		   WHERE U.CODUSUR = ".$CODUSUR;
		// varDump2($sql);
	  if ($ret = selectOracle($sql)) {
			return reset($ret);
	  } else {
	  	return false;
	  }
	}
}

function buscaDadosFilial($CODFILIALNF){
	// varDump2($dados);
	$sql = "SELECT 
	F.CODIGO AS CODFILIALNF,
	F.RAZAOSOCIAL,
	F.FANTASIA,
	F.ENDERECO||', '||F.NUMERO||', '||F.BAIRRO AS ENDERECO, 
	F.CIDADE AS MUNICIPIO, 
	F.UF, 
	F.TELEFONE,
	F.EMAIL
FROM PCFILIAL F
WHERE F.CODIGO NOT IN (99)
  AND F.CODIGO = ".$CODFILIALNF;
	return reset(selectOracle($sql));
}

function buscaDadosCobranca($CODCOB){
	$sql = "SELECT COB.CODCOB,
	   COB.COBRANCA,
	   NVL(COB.BOLETO, 'N') AS BOLETO,
	   NVL(COB.CARTAO, 'N') AS CARTAO,
	   NVL(COB.NIVELVENDA, 0) AS NIVELVENDA,
	   NVL(COB.PERCACRESVENDA, 0) AS PERCDESC
  FROM PCCOB COB
  WHERE COB.CODCOB = '".$CODCOB."'";
	// varDump2($sql);
	if ($ret = selectOracle($sql)) {
		return reset($ret);
	} else {
		return false;
	}
}

function buscaDadosPlpag($CODPLPAG){
	// varDump2($CODPLPAG); die();
	if (empty($CODPLPAG)) {
		insereModal('danger', 'Não foi possível identificar o plano de pagamento');
		return false;
	} else {
		$sql = "SELECT P.CODPLPAG,
			   P.DESCRICAO,
			   TRUNC(NVL(P.NUMPR,0)) AS NUMPR,
			   NVL(P.VLMINPEDIDO, 0) AS VLMINPEDIDO,
			   NVL(P.PERTXFIM,0) AS PERCDESC
		  FROM PCPLPAG P
		 WHERE P.STATUS = 'A'
		   AND P.CODPLPAG = ".$CODPLPAG;
		// varDump2($sql);
		if ($ret = selectOracle($sql)) {
			return reset($ret);
		} else {
			return false;
		}
	}
}

function geraNovoCabecalho($dados){
	// varDump2($login); 	die();
	if ($dados['CLIENTE']['CODCLI'] == "") {
		insereModal("danger", "Código de Cliente não identificado, O orçamento não pôde ser gerado!");
		exit();
	}
	if ($dados['VENDEDOR']['CODUSUR'] == "") {
		insereModal("danger", "Código de RCA não identificado, O orçamento não pôde ser gerado!");
		exit();
	}
	if ($dados['FILIAL']['CODFILIALNF'] == "") {
		insereModal("danger", "Código de Filial não identificado, O orçamento não pôde ser gerado!");
		exit();
	}
	if ($dados['COBRANCA']['CODCOB'] == "") {
		insereModal("danger", "Código de Cobrança não identificado, O orçamento não pôde ser gerado!");
		exit();
	}
	if ($dados['PLPAG']['CODPLPAG'] == "") {
		insereModal("danger", "Código de Plano de pagamento não identificado, O orçamento não pôde ser gerado!");
		exit();
	}


	$sql = "INSERT INTO ORCORCAMENTOC (IDUSUARIO, CODCLI, PERCDESC, NUMREGIAO, CODFILIALNF, CODUSUR, CODCOB, CODPLPAG, ORIGEM, ATENDIMENTO, FRETEDESPACHO, VALORFRETE) 
			 VALUES (
			 /*IDUSUARIO*/".$_SESSION['login']['IDUSUARIO'].", 
			 /*CODCLI*/'".$dados['CLIENTE']['CODCLI']."', 
			 /*PERCDESC*/'".$dados['CLIENTE']['PERCDESC']."', 
			 /*NUMREGIAO*/'".$dados['CLIENTE']['NUMREGIAO']."', 
			 /*CODFILIALNF*/'".$dados['CLIENTE']['CODFILIALNF']."', 
			 /*CODUSUR*/'".$dados['VENDEDOR']['CODUSUR']."', 
			 /*CODCOB*/'".$dados['COBRANCA']['CODCOB']."',
			 /*CODPLPAG*/'".$dados['PLPAG']['CODPLPAG']."',
			 /*ORIGEM*/'APP CLIENTE',
			 /*ATENDIMENTO*/'PORTAL',
			 /*FRETEDESPACHO*/'G',
			 /*VALORFRETE*/'0.00')";
	if(executarOracle($sql)){
		return buscaUltimoOrcamentoCliente($dados);
	}
}

function buscaUltimoOrcamentoCliente($dados){
	$sql2 = "SELECT * FROM ORCORCAMENTOC C2 WHERE C2.IDORCAMENTO = 
						(SELECT MAX(C1.IDORCAMENTO) AS IDORCAMENTO 
							 FROM ORCORCAMENTOC C1 
							WHERE C1.IDUSUARIO = ".$_SESSION['login']['IDUSUARIO']."
							  AND C1.CODCLI = '".$dados['CLIENTE']['CODCLI']."'
							  AND C1.CODUSUR = '".$dados['VENDEDOR']['CODUSUR']."')";
	return reset(selectOracle($sql2));
}

function buscaOrcamentoCompleto($IDORCAMENTO){
	$ORCAMENTO['CAB']       = buscaDadosCabecalho($IDORCAMENTO);
	$ORCAMENTO['CLIENTE']   = buscaDadosCliente($ORCAMENTO['CAB']['CODCLI']);
	$ORCAMENTO['VENDEDOR']  = buscaDadosVendedor($ORCAMENTO['CAB']['CODUSUR']);
	$ORCAMENTO['FILIAL']    = buscaDadosFilial($ORCAMENTO['CAB']['CODFILIALNF']);
	$ORCAMENTO['COBRANCA']  = buscaDadosCobranca($ORCAMENTO['CAB']['CODCOB']);
	$ORCAMENTO['PLPAG']     = buscaDadosPlpag($ORCAMENTO['CAB']['CODPLPAG']);
	$ORCAMENTO['ITEM']			= buscaItensOrcamento($IDORCAMENTO);
	
	return $ORCAMENTO;
}

function buscaDadosCabecalho($IDORCAMENTO){
	$sql = "SELECT * FROM ORCORCAMENTOC WHERE IDORCAMENTO = ".$IDORCAMENTO;
	return reset(selectOracle($sql));
}

function buscaItensOrcamento($IDORCAMENTO){
	$sql = "SELECT * FROM ORCORCAMENTOI	
					 WHERE STATUS = 'A' 
					   AND IDORCAMENTO = ".$IDORCAMENTO." 
					 ORDER BY IDORCAMENTOI ASC" ;
	// varDump2($sql);
	return selectOracle($sql);
}


function buscaValorTotal($IDORCAMENTO){
	if ($IDORCAMENTO) {
		$sql = "SELECT ROUND(NVL(SUM(QTPEDIDA * PVENDA), 0), 2) AS VALORTOTAL
				  FROM ORCORCAMENTOI
				 WHERE IDORCAMENTO = ".$IDORCAMENTO."
				   AND STATUS = 'A'";
		$ret = selectOracle($sql);
		if ($ret) {
			return $ret[0]['VALORTOTAL'];
		} else {
			return false;
		}
	} else {
		return 0;
	}

}


function defineMaquina($dados){
	$sql = "UPDATE ORCORCAMENTOC SET MAQUINA = '".mb_strtoupper($dados['MAQUINA'], 'UTF-8')."'
			     WHERE IDORCAMENTO = ".$dados['IDORCAMENTO'];
	// varDump2($sql); 
	// varDump2($ret); 
	$ret = executarOracle($sql);
	$_SESSION['ORC_CLIENTE']['CAB']['MAQUINA'] = mb_strtoupper($dados['MAQUINA'], 'UTF-8');
}

function inserePecaOrcamento($IDORCAMENTO, $valuePeca){

  if (validaItemOrcamento($valuePeca)) {
    insereModal('danger', "A peça ".$valuePeca['CODPECA']." - ".$valuePeca['MARCA']." já existe no orçamento!");
  } else {

    $valuePeca['CODPECA']    = (!is_null($valuePeca['CODPECA']))?str_replace("'", " ", trim($valuePeca['CODPECA'])):"";
    $valuePeca['DESCRICAO']  = (!is_null($valuePeca['DESCRICAO']))?str_replace("'", " ", trim($valuePeca['DESCRICAO'])):"";
    $valuePeca['MARCA']      = (!is_null($valuePeca['MARCA']))?str_replace("'", " ", trim($valuePeca['MARCA'])):"";
    $valuePeca['LOCACAO']    = (!is_null($valuePeca['LOCACAO']))?str_replace("'", " ", trim($valuePeca['LOCACAO'])):"";
    // varDump2($valuePeca['LOCACAO']);

    if (intval($valuePeca['SALDO']) >= intval($valuePeca['QTPEDIDA'])) {
      $valuePeca['DISPONIBILIDADE'] = 'IMEDIATA';
    } else {
      if (intval($valuePeca['SALDO']) <= 0) {
        $valuePeca['DISPONIBILIDADE'] = 'SOB CONSULTA';
      } else {
        $valuePeca['DISPONIBILIDADE'] = 'IMEDIATA ('.$valuePeca['SALDO'].')';
      }
    }
    $valuePeca['ICMS'] = buscaICMS($_SESSION['ORC_CLIENTE']['CLIENTE']['UF'], $value['PROCEDENCIA']);

    $sql = "INSERT INTO ORCORCAMENTOI (IDORCAMENTO, VALIDACAO,  CODPECA,  NUMORIGINAL, STATUS,  CODVIDE,  CODPROD,  DV,  DESCRICAO, MARCA, QTPEDIDA,   QTDISPPECA, QTDISPONIVEL,  DISPONIBILIDADE, ICMS,   PTABELA,  PVENDA,  PROCEDENCIA, OBSERVACAO, LOCACAO)
      VALUES
        (/*IDORCAMENTO*/'".$IDORCAMENTO."',
         /*VALIDACAO*/'".$valuePeca['ORIGEM']."',
         /*CODPECA*/'".TRIM($valuePeca['CODPECA'])."',
         /*NUMORIGINAL*/'".TRIM($valuePeca['NUMORIGINAL'])."',
         /*STATUS*/'A',
         /*CODVIDE*/'".$valuePeca['CODVIDE']."',
         /*CODPROD*/'".$valuePeca['CODPROD']."',
         /*DV*/'".$valuePeca['DV']."',
         /*DESCRICAO*/'".$valuePeca['DESCRICAO']."',
         /*MARCA*/'".$valuePeca['MARCA']."',
         /*QTPEDIDA*/'".$valuePeca['QTPEDIDA']."',
         /*QTDISPPECA*/FUN_ORC_CONSULTA_SALDO_PECA('".$valuePeca['CODPECA']."%'),
         /*QTDISPONIVEL*/'".$valuePeca['SALDO']."',
         /*DISPONIBILIDADE*/'".$valuePeca['DISPONIBILIDADE']."',
         /*ICMS*/'".$valuePeca['ICMS']."',
         /*PTABELA*/'".$valuePeca['PTABELA']."',
         /*PVENDA*/'".$valuePeca['PTABELA']."',
         /*PROCEDENCIA*/'".$valuePeca['PROCEDENCIA']."',
         /*OBSERVACAO*/'".$valuePeca['OBSERVACAO']."',
         /*LOCACAO*/'".$valuePeca['LOCACAO']."'
      )";

    // varDump2($sql); 
    return executarOracle($sql);

  }


}

function inserePecaGenerica($IDORCAMENTO, $valuePeca){

  $valuePeca['CODPECA']    			= (!is_null($valuePeca['CODPECA']))?str_replace("'", " ", trim($valuePeca['CODPECA'])):"";
  $valuePeca['QTPEDIDA']      	= (!is_null($valuePeca['QTPEDIDA']))?str_replace("'", " ", trim($valuePeca['QTPEDIDA'])):'1';
  $valuePeca['NUMORIGINAL']  		= $valuePeca['CODPECA'];
  $valuePeca['ORIGEM']  				= "CLIENTE";
  $valuePeca['STATUS']  				= "A";
  $valuePeca['CODVIDE']  				= "";
  $valuePeca['CODPROD']  				= "";
  $valuePeca['DV']  						= "";
  $valuePeca['CODVIDE']  				= "";
  $valuePeca['DESCRICAO']  			= "SOB CONSULTA";
  $valuePeca['MARCA']      			= "SOB CONSULTA";
  $valuePeca['QTDISPPECA']  		= "0";
  $valuePeca['QTDISPONIVEL']		= "0";
  $valuePeca['DISPONIBILIDADE'] = 'SOB CONSULTA';
  $valuePeca['ICMS']						= "0";
  $valuePeca['PTABELA']  				= "0";
  $valuePeca['PVENDA']  				= "0";
  $valuePeca['PROCEDENCIA']  		= "";
  $valuePeca['OBSERVACAO']  		= "";
  $valuePeca['LOCACAO']    			= "";


  $sql = "INSERT INTO ORCORCAMENTOI (IDORCAMENTO, VALIDACAO,  CODPECA,  NUMORIGINAL, STATUS,  CODVIDE,  CODPROD,  DV,  DESCRICAO, MARCA, QTPEDIDA,   QTDISPPECA, QTDISPONIVEL,  DISPONIBILIDADE, ICMS,   PTABELA,  PVENDA,  PROCEDENCIA, OBSERVACAO, LOCACAO)
    VALUES
      (/*IDORCAMENTO*/'".$IDORCAMENTO."',
       /*VALIDACAO*/'".$valuePeca['ORIGEM']."',
       /*CODPECA*/'".TRIM($valuePeca['CODPECA'])."',
       /*NUMORIGINAL*/'".TRIM($valuePeca['NUMORIGINAL'])."',
       /*STATUS*/'A',
       /*CODVIDE*/'".$valuePeca['CODVIDE']."',
       /*CODPROD*/'".$valuePeca['CODPROD']."',
       /*DV*/'".$valuePeca['DV']."',
       /*DESCRICAO*/'".$valuePeca['DESCRICAO']."',
       /*MARCA*/'".$valuePeca['MARCA']."',
       /*QTPEDIDA*/'".$valuePeca['QTPEDIDA']."',
       /*QTDISPPECA*/FUN_ORC_CONSULTA_SALDO_PECA('".$valuePeca['CODPECA']."%'),
       /*QTDISPONIVEL*/'".$valuePeca['SALDO']."',
       /*DISPONIBILIDADE*/'".$valuePeca['DISPONIBILIDADE']."',
       /*ICMS*/'".$valuePeca['ICMS']."',
       /*PTABELA*/'".$valuePeca['PTABELA']."',
       /*PVENDA*/'".$valuePeca['PTABELA']."',
       /*PROCEDENCIA*/'".$valuePeca['PROCEDENCIA']."',
       /*OBSERVACAO*/'".$valuePeca['OBSERVACAO']."',
       /*LOCACAO*/'".$valuePeca['LOCACAO']."'
    )";

    // varDump2($sql); 
    return executarOracle($sql);

}


function validaItemOrcamento($dados){

	$sql = "SELECT *
			  FROM ORCORCAMENTOI I
			 WHERE I.STATUS = 'A'
			   AND I.IDORCAMENTO = '".$dados['IDORCAMENTO']."'
			   AND I.CODPECA = '".strtoupper($dados['CODPECA'])."'
			   AND I.MARCA = '".strtoupper($dados['MARCA'])."'";
	if ($dados['CODPROD'] != "") {
		$sql .= " AND I.CODPROD = ".$dados['CODPROD'];
	}
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	if ($ret) {
		return true;
	} else {
		return false;
	}
}


function buscaICMS($UF, $PROCEDENCIA){
	$array7porcento = array("MG", "RJ", "SP", "PR", "RS", "SC");

	if ($UF == 'AM') {
		return floatval('0.00');
	} else {
		// varDump2($PROCEDENCIA);
		if ($PROCEDENCIA == "IMP.") {
			return floatval('0.04');
		} else {
			if (in_array($UF, $array7porcento)) { 
				return floatval('0.07');
			} else {
				return floatval('0.12');
			}
		}
	}
}



function buscaPrecoAutorizado($CODUSUR, $CODCLI, $CODPLPAG, $CODPROD){
	if ($CODPROD != "") {
		$sql = "SELECT 	PCAUTORI.NRAUTORIZACAO, 
						PCAUTORI.DATAAUTORIZACAO, 
						PCAUTORI.CODUSUR, 
						PCAUTORI.CODCLI, 
						PCPLPAG.CODPLPAG, 
						PCPLPAG.DESCRICAO, 
						PCAUTORI.CODPROD, 
						PCAUTORI.PERCDESCAUTOR, 
						PCAUTORI.PVENDAATUAL
			FROM PCAUTORI, PCPLPAG
			WHERE PCAUTORI.CODPLPAG = PCPLPAG.CODPLPAG
			AND PCAUTORI.STATUSUTILIZ NOT IN ('S')
			AND PCAUTORI.DATA_UTILIZACAO IS NULL
			AND PCAUTORI.CODUSUR = ".$CODUSUR."
			AND PCAUTORI.CODCLI = ".$CODCLI."
			AND PCAUTORI.CODPLPAG = ".$CODPLPAG."
			AND PCAUTORI.CODPROD = ".$CODPROD;
		$ret = selectOracle($sql);
	} else {
		$ret = false;
	}
	// varDump2($sql);
	// varDump2($ret);
	if ($ret) {
		$precoAutorizado = floatval($ret[0]['PVENDAATUAL']);
	} else {
		$precoAutorizado = false;
	}
	return $precoAutorizado;
}



function excluirItemOrcamento($dados){
	$sql = "UPDATE ORCORCAMENTOI  SET STATUS = 'I' WHERE IDORCAMENTOI = ".$dados['IDORCAMENTOI'];
	return executarOracle($sql);
}


function buscaOrcamentosRecentesCliente($CODCLI){

    $sql = "SELECT T1.IDORCAMENTO,
       T5.NUMPEDRCA,
       T5.NUMPED,
       T1.IDUSUARIO,
       T1.CODCLI,
       T6.CODUSUR,
       T6.NOME AS RCA,
       T1.DATA,
       T1.ORIGEM,
       T1.CODCOB,
       T1.CODPLPAG,
       T5.DTABERTURAPEDPALM,
       T1.ATENDIMENTO,
       T5.OBSERVACAO_PC AS OBSERVACAO,
       T4.CLIENTE,
       T2.NOME AS USUARIO,
       T1.STATUS,
       T1.MAQUINA,
       DECODE(T5.IMPORTADO, NULL, 'ORCAMENTO', 1, 'PENDENTE',
              DECODE(T5.POSICAO_ATUAL, 
                     'R', 'REJEITADO',
                     'P', 'PENDENTE',
                     'M', 'MONTADO',
                     'F', 'FATURADO',
                     'B', 'BLOQUEADO',
                     'L', 'LIBERADO',
                     POSICAO_ATUAL)) AS POSICAO_ATUAL,
       SUM(CASE
             WHEN T3.STATUS = 'A' THEN
              1
             ELSE
              0
           END) AS QTITENS,
       SUM(CASE
             WHEN T3.STATUS = 'A' THEN
              T3.QTPEDIDA * T3.PVENDA
             ELSE
              0
           END) AS VALORTOTAL

  FROM ORCORCAMENTOC T1
  LEFT JOIN ORCUSUARIO T2 ON T1.IDUSUARIO = T2.IDUSUARIO
  LEFT JOIN ORCORCAMENTOI T3 ON T1.IDORCAMENTO = T3.IDORCAMENTO
  LEFT JOIN PCCLIENT T4 ON T1.CODCLI = T4.CODCLI
  LEFT JOIN PCPEDCFV T5 ON T1.IDORCAMENTO = T5.NUMPEDCLI
  LEFT JOIN PCUSUARI T6 ON T1.CODUSUR = T6.CODUSUR
 WHERE T1.DATA >= TRUNC(SYSDATE-15)
   AND T1.CODCLI = '".$CODCLI."'
 GROUP BY T1.IDORCAMENTO,
       T5.NUMPEDRCA,
       T5.NUMPED,
       T1.IDUSUARIO,
       T1.CODCLI,
       T6.CODUSUR,
       T6.NOME,
       T1.DATA,
       T1.STATUS,
       T1.MAQUINA,
       T1.ORIGEM,
       T1.CODCOB,
       T1.CODPLPAG,
       T5.DTABERTURAPEDPALM,
       T1.ATENDIMENTO,
       T5.OBSERVACAO_PC,
       T4.CLIENTE,
       T2.NOME,
       DECODE(T5.IMPORTADO, NULL, 'ORCAMENTO', 1, 'PENDENTE',
              DECODE(T5.POSICAO_ATUAL, 
                     'R', 'REJEITADO',
                     'P', 'PENDENTE',
                     'M', 'MONTADO',
                     'F', 'FATURADO',
                     'B', 'BLOQUEADO',
                     'L', 'LIBERADO',
                     POSICAO_ATUAL))
 ORDER BY T1.DATA, T1.IDORCAMENTO DESC";

    if ($CODCLI <> "") {
        return selectOracle($sql);
    } else {
        return false;
    }
}


function defineObservacao($dados){
	$sql = "UPDATE ORCORCAMENTOC 
			    SET MAQUINA = '".mb_strtoupper(trim($dados['MAQUINA']),'UTF-8')."',
			   	 		NUMPEDCOMP = '".intval($dados['NUMPEDCOMP'])."',
			   			OBSENTREGA1 = '".mb_strtoupper(trim($dados['OBSENTREGA1']),'UTF-8')."'
			 WHERE IDORCAMENTO = ".$dados['IDORCAMENTO'];
	// varDump2($dados); 
	// varDump2($sql); 
	// die();
	executarOracle($sql);
	$_SESSION['ORC_CLIENTE']['CAB']['MAQUINA'] 			= mb_strtoupper(trim($dados['MAQUINA']),'UTF-8');
	$_SESSION['ORC_CLIENTE']['CAB']['OBSENTREGA1'] 	= mb_strtoupper(trim($dados['OBSENTREGA1']),'UTF-8');
	$_SESSION['ORC_CLIENTE']['CAB']['NUMPEDCOMP'] 	= intval($dados['NUMPEDCOMP']);
	return $OBSERVACAO;
}