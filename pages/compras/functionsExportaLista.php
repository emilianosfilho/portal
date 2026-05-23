<?php
/*#################################################################################*/
/*#################################################################################*/
function gerarPCEST($codprod){

	$sql = "INSERT INTO PCEST (CODPROD, CODFILIAL)
				SELECT   PCPRODUT.CODPROD, PCFILIAL.CODIGO
				FROM   PCPRODUT, PCFILIAL
				WHERE       PCFILIAL.CODIGO <> '99'
				      AND PCFILIAL.CODIGO IS NOT NULL
				      AND PCPRODUT.CODPROD = ".$codprod."
				      AND NOT EXISTS
				             (SELECT   PCEST.CODPROD
				                FROM   PCEST
				               WHERE   PCEST.CODPROD = PCPRODUT.CODPROD
				                 AND   PCEST.CODFILIAL = PCFILIAL.CODIGO)";
	//VarDump2($sql);
	if(executarOracle($sql) === false){
		exibeMensagem("ERRO ao executar a função gerarPcest");
		return false;
	} else {
		return true;
	}
}


function buscaDadosPCEST($codprod){

    $sql = "SELECT DISTINCT PCPRODUT.CODPROD,
         PCPRODUT.DV,
         PCPRODUT.DESCRICAO,
         PCPRODUT.DESCRICAO AS PRODUTO,
         PCPRODUT.EMBALAGEM,
         NVL((SELECT MARCA FROM PCMARCA WHERE PCMARCA.CODMARCA = PCPRODUT.CODMARCA),PCPRODUT.MARCA) AS MARCA,
         PCEST.CODFILIAL,
         NVL(PCEST.ESTMIN,0) AS ESTMIN,
         NVL(PCEST.QTEST,0) AS QTEST,
         NVL(PCEST.QTESTGER,0) AS QTESTGER,
         NVL(PCEST.CUSTOCONT,0) AS CUSTOCONT,
         NVL(PCEST.CUSTOREAL,0) AS CUSTOREAL,
         NVL(PCEST.CUSTOFIN,0) AS CUSTOFIN,
         NVL(PCEST.CUSTOREP,0) AS CUSTOREP
    FROM PCEST, PCPRODUT
   WHERE PCEST.CODPROD =  PCPRODUT.CODPROD
     AND PCPRODUT.CODPROD = ".$codprod;
    //VarDump2($sql); die;

    $retorno = selectOracle($sql);
    return $retorno[0];

}


/*#################################################################################*/
/*#################################################################################*/

function geraPCTABPR($CODPROD){

  $sql1 = "SELECT ".$CODPROD." AS CODPROD, R.NUMREGIAO FROM PCREGIAO R
            WHERE R.STATUS =  'A'
            AND ".$CODPROD."||'-'||R.NUMREGIAO NOT IN
            (SELECT CODPROD||'-'||NUMREGIAO FROM PCTABPR WHERE CODPROD = ".$CODPROD.")";
  $ret1 = selectOracle($sql1);
  // VarDump2($sql1);
  // VarDump2($ret1);

  if ($ret1 && count($ret1)>0) {

    $sql = "SELECT VALOR AS MARGEM FROM parametros WHERE parametro = 'MARGEM' AND tipo in ('CAD_PRECO')";
    $ret = selectMysql($sql);
    //varDump2($retorno); die;
    if ($ret) {
      $MARGEM = $ret[0]['MARGEM'];
    } else {
      $MARGEM = floatval(28.79);
    }

    foreach ($ret1 as $key => $value) {
      $sql = "INSERT INTO PCTABPR (CODPROD, NUMREGIAO, MARGEM, PTABELA, PTABELA1, PTABELA2, PTABELA3, PTABELA4, PTABELA5, PTABELA6, PTABELA7, PVENDA, PVENDA1, PVENDA2, PVENDA3, PVENDA4, PVENDA5, PVENDA6, PVENDA7) VALUES (
      ".$value['CODPROD'].", ".$value['NUMREGIAO'].", ".$MARGEM.",
      0.0001, 0.0001, 0.0001, 0.0001, 0.0001, 0.0001, 0.0001, 0.0001,
      0.0001, 0.0001, 0.0001, 0.0001, 0.0001, 0.0001, 0.0001, 0.0001)";
      $ret = executarOracle($sql);
      // VarDump2($sql);
      // VarDump2($ret);
      
      if($ret){
        return true;
      } else {
        exibeMensagem("ERRO ao executar a função validaPCTABPR do produto ".$value['CODPROD']."<br/>".$sql);
        return false;
      }
    }
  } else {
    return true;
  }
}

function gerarPCTABPR($CODPROD){

    $sql = "SELECT VALOR AS MARGEM FROM `parametros` WHERE parametro = 'MARGEM' AND tipo in ('CAD_PRECO')";
    $retorno = selectMysql($sql);
    //varDump2($retorno); die;

    if ($retorno == false) {
    	varDump2($sql);
    	exibeMensagem("Não foi possível determinar a margem padrão!");
    	die();
    } else {
	    $MARGEM = $retorno[0]['MARGEM'];

		$sql = "INSERT INTO PCTABPR (CODPROD,
	                     NUMREGIAO,
	                     MARGEM,
	                     PTABELA,
	                     PTABELA1,
	                     PTABELA2,
	                     PTABELA3,
	                     PTABELA4,
	                     PTABELA5,
	                     PTABELA6,
	                     PTABELA7,
	                     PVENDA,
	                     PVENDA1,
	                     PVENDA2,
	                     PVENDA3,
	                     PVENDA4,
	                     PVENDA5,
	                     PVENDA6,
	                     PVENDA7)
	    SELECT   PCPRODUT.CODPROD,
	             PCREGIAO.NUMREGIAO,
	             ".$MARGEM." AS MARGEM,
	             0.0001 AS PTABELA,
	             0.0001 AS PTABELA1,
	             0.0001 AS PTABELA2,
	             0.0001 AS PTABELA3,
	             0.0001 AS PTABELA4,
	             0.0001 AS PTABELA5,
	             0.0001 AS PTABELA6,
	             0.0001 AS PTABELA7,
	             0.0001 AS PVENDA,
	             0.0001 AS PVENDA1,
	             0.0001 AS PVENDA2,
	             0.0001 AS PVENDA3,
	             0.0001 AS PVENDA4,
	             0.0001 AS PVENDA5,
	             0.0001 AS PVENDA6,
	             0.0001 AS PVENDA7
	      FROM   PCPRODUT, PCREGIAO
	     WHERE   PCREGIAO.NUMREGIAO IS NOT NULL 
	     		 AND PCREGIAO.STATUS = 'A' 
	     		 AND PCPRODUT.CODPROD = ".$CODPROD."
	             AND NOT EXISTS
	                    (SELECT   PCTABPR.CODPROD
	                       FROM   PCTABPR
	                      WHERE   PCTABPR.CODPROD = PCPRODUT.CODPROD
	                              AND PCTABPR.NUMREGIAO = PCREGIAO.NUMREGIAO)";
		// VarDump2($sql); die();
	    $ret = executarOracle($sql);

		if($ret === false){
			exibeMensagem("ERRO ao executar a função gerarPCTABPR");
			return false;
		} else {
			return true;
		}
    }
}



function buscaDadosPCTABPR($codprod){
    $sql  = "SELECT PCTABPR.CODPROD, PCPRODUT.DV,
                MAX(NVL(PCTABPR.PVENDA, 0.001)) AS PVENDA
			  FROM PCTABPR, PCPRODUT, PCREGIAO
			 WHERE PCTABPR.CODPROD = PCPRODUT.CODPROD
			   AND PCTABPR.NUMREGIAO = PCREGIAO.NUMREGIAO
			   AND PCREGIAO.STATUS = 'A'
			   AND PCTABPR.CODPROD = ".$codprod."
			 GROUP BY PCTABPR.CODPROD, PCPRODUT.DV";
	$ret = selectOracle($sql);
	// varDump2($sql); 
	// varDump2($ret); 
	// die();
   return $ret; 
}

/*#################################################################################*/
/*#################################################################################*/

function gerarPCTABTRIB($codprod){

	$estadosTributados = array(0=>'AM',1=>'RR',2=>'PA',3=>'TO');
	$contaErro=0;

	foreach ($estadosTributados as $keyT => $valueT) {
		$sql  ="INSERT INTO PCTABTRIB (CODPROD, CODFILIALNF, UFDESTINO, CODST, CODTRIBPISCOFINS, DTULTALTER)
        SELECT PCPRODUT.CODPROD AS CODPROD, 1 AS CODFILIALNF, '".$valueT."' AS UFDESTINO, '1' AS CODST, '1' AS CODTRIBPISCOFINS, SYSDATE as DTULTALTER
          FROM PCPRODUT
         WHERE PCPRODUT.CODPROD = '".$codprod."'
           AND NOT EXISTS (SELECT CODPROD FROM PCTABTRIB WHERE PCTABTRIB.CODPROD = '".$codprod."' AND PCTABTRIB.CODFILIALNF = 1 AND PCTABTRIB.UFDESTINO = '".$valueT."')";
		// varDump2($sql);
		if (executarOracle($sql) == false) {
			$contaErro++;
		}
	}
	return true;

}


function buscaDadosPCTABTRIB($CODPROD){
    $sql = "SELECT DISTINCT PCPRODUT.CODPROD,
			                PCPRODUT.DV,
			                PCTABTRIB.CODFILIALNF,
			                PCTABTRIB.UFDESTINO,
			                (CASE PCTABTRIB.UFDESTINO
			                      WHEN 'AM' THEN 2
			                      WHEN 'DF' THEN 3
			                      WHEN 'GO' THEN 3
			                      WHEN 'MG' THEN 3
			                      WHEN 'MS' THEN 3
			                      WHEN 'MT' THEN 3
			                      WHEN 'PR' THEN 3
			                      WHEN 'RJ' THEN 3
			                      WHEN 'RS' THEN 3
			                      WHEN 'SC' THEN 3
			                      WHEN 'SP' THEN 3
			                      ELSE 4
			                      END) AS CODST,
			                2 AS CODTRIBPISCOFINS
			  FROM PCTABTRIB, PCPRODUT
			 WHERE PCTABTRIB.CODPROD = PCPRODUT.CODPROD
			   AND PCPRODUT.CODPROD = ".$CODPROD;

	// varDump2($sql); die();
	$retorno = selectOracle($sql);
	//varDump2($retorno); //die;
   return $retorno; 
}

/*#################################################################################*/
/*#################################################################################*/

function gerarPCTRIBENTRADA($CODPROD){
	// varDump2($_SESSION['login']); die();

    $sql = "INSERT INTO PCTRIBENTRADA
			  (NCM, CODFILIAL, UFORIGEM, TIPOFORNEC, CODFUNCCAD, DTCADASTRO)
			  SELECT DISTINCT PCPRODUT.CODNCMEX AS NCM,
			                  PCFILIAL.CODIGO AS CODFILIAL,
			                  PCFORNEC.ESTADO AS UFORIGEM,
			                  PCFORNEC.TIPOFORNEC,
			                  '".$_SESSION['login']['MATRICULA']."' AS CODFUNCCAD,
			                  SYSDATE
			    FROM PCPRODUT, PCFORNEC, PCFILIAL
			   WHERE PCPRODUT.CODFORNEC = PCFORNEC.CODFORNEC
			     AND PCPRODUT.NBM IS NOT NULL
			     AND PCFILIAL.CODIGO NOT IN (99)
			     AND PCPRODUT.CODPROD = '".$CODPROD."'
			     AND NOT EXISTS
			   (SELECT E.NCM, E.CODFILIAL, E.UFORIGEM, E.TIPOFORNEC
                  FROM PCTRIBENTRADA E
                 WHERE E.NCM = PCPRODUT.CODNCMEX
                   AND E.CODFILIAL = PCFILIAL.CODIGO
                   AND E.UFORIGEM = PCFORNEC.ESTADO
                   AND E.TIPOFORNEC = PCFORNEC.TIPOFORNEC)";
	// varDump2($sql); //die();
	if(executarOracle($sql) === false){
		exibeMensagem("ERRO ao executar a função gerarPCTRIBENTRADA");
		return false;
	} else {
		return true;
	}

}


function buscaNcmPCTRIBENTRADA($CODPROD){

    $sql = "SELECT PCTRIBENTRADA.NCM||'-'||
       PCTRIBENTRADA.CODFILIAL||'-'||
       PCTRIBENTRADA.UFORIGEM||'-'||
       PCTRIBENTRADA.TIPOFORNEC AS KEY,
       PCTRIBENTRADA.NCM,
       PCTRIBENTRADA.CODFILIAL,
       PCTRIBENTRADA.UFORIGEM,
       PCTRIBENTRADA.TIPOFORNEC,
     (CASE PCTRIBENTRADA.UFORIGEM 
        WHEN 'AM' THEN 3
        WHEN 'MG' THEN 1
        WHEN 'ES' THEN 1
        WHEN 'RJ' THEN 1
        WHEN 'SP' THEN 1
        WHEN 'SC' THEN 1
        WHEN 'PR' THEN 1
        WHEN 'RS' THEN 1
        ELSE 2 
        END) AS CODFIGURA,
     2 AS CODTRIBPISCOFINS,
     1 AS CODEXCECAOPISCOFINS
  FROM PCTRIBENTRADA, PCPRODUT, PCFORNEC
 WHERE PCTRIBENTRADA.NCM = PCPRODUT.CODNCMEX
   AND PCPRODUT.CODFORNEC = PCFORNEC.CODFORNEC
   AND PCTRIBENTRADA.UFORIGEM = PCFORNEC.ESTADO
   AND PCTRIBENTRADA.TIPOFORNEC = PCFORNEC.TIPOFORNEC
   AND PCPRODUT.CODPROD = ".$CODPROD;

	$retorno = selectOracle($sql);
	// varDump2($sql);
	// varDump2($retorno);
	// die;

   return $retorno; 
}


function buscaDadosProdutoPedido($CODPROD){
  $sql = "SELECT NULL AS NUMPEDIDO,
                1 AS CODFILIAL,
                NULL AS CODFORNEC,
                CODPROD,
                DV,
                NULL AS QTPEDIDA,
                NULL AS PCOMPRA
           FROM PCPRODUT 
          WHERE CODPROD = ".$CODPROD;
  $ret = selectOracle($sql);
  if ($ret) {
    return $ret[0];
  } else {
    return false;
  }
}
?>