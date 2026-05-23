<?php
function decodeBoleano($campo){
	if (is_bool($campo)) {
		if ($campo) {
			return "SIM";
		} else {
			return "NÃO";
		}
	} else {
		if (is_string($campo)) {
			if (strtoupper($campo) == 'S') {
				return "SIM";
			} else {
				return "NÃO";
			}
		} else {
			return  "NÃO";
		} 
	}
}

function pesquisarCliente($dados){
		$sql = "with fast
        as (select   d2.dtinicio,
                     d2.dtfim,
                     d2.descricao,
                     d2.percdesc,
                     c.codcli,
                     c.fantasia,
                     c.cliente
              from   pcdesconto d2, pcclient c
             where       d2.codcli = c.codcli
                     and d2.codfilial = 1
                     and c.dtexclusao is null
                     and trunc (sysdate) between trunc (d2.dtinicio)
                                             and  trunc (d2.dtfim)),
    pendencias as (  select   p.codcli, nvl (sum (p.valor), 0) as vlareceber
                    from   pcprest p
                   where   p.dtpag is null
                group by   p.codcli),
    credito as (   SELECT   cre.codcli, nvl (sum(cre.valor), 0) as vlcredito
                     FROM   pccrecli cre
                    WHERE   cre.dtdesconto IS NULL
                 GROUP BY   cre.codcli)
		select   cli.codcli,
		         cli.cgcent,
		         cli.cliente,
		         cli.fantasia,
		         nvl (cli.codfilialnf, 1) as codfilialnf,
		         nvl (nvl (pr.numregiao, cli.numregiaocli), 1) as numregiao,
		         nvl (cli.codplpag, 1) as codplpag,
		         nvl (cob.codcob, 'dh') as codcob,
		         nvl (cob.nivelvenda, 5) as nivelvenda,
		         decode (cli.tipofj, 'j', 'jurÍdica', 'fÍsica') as tipofj,
		         nvl (cli.municent, cli.municcob) as municipio,
		         nvl (cli.estent, cli.estcob) as uf,
		         cli.obsentrega1,
		         cli.obsentrega2,
		         cli.obsentrega3,
		         cli.obsentrega4,
		         decode (cli.dtexclusao, null, 'n', 's') as excluido,
		         cli.bloqueio AS bloqueado,
		         dbms_lob.substr (cli.motivobloq) as motivobloq,
		         nvl (fast.percdesc, 0) as fast,
		         nvl (cli.limcred, 0) as vllimcred,
		         nvl (pendencias.vlareceber, 0) as vlareceber,
		         (nvl (cli.limcred, 0) - nvl (pendencias.vlareceber, 0)) as vlcreddisp,
		         nvl (credito.vlcredito, 0) as vlcredito
		  from   pcclient cli,
		         pctabprcli pr,
		         pccob cob,
		         fast,
		         pendencias,
		         credito
		 where       cli.codcob = cob.codcob
		         and cli.codcli = pr.codcli(+)
		         and cli.codcli = fast.codcli(+)
		         and cli.codcli = credito.codcli(+)
		         and cli.codcli = pendencias.codcli(+)".PHP_EOL;

	 $criterio = "";
	 if (isset($dados['CODCLI']) && $dados['CODCLI']<>"") {
		if ($dados['CODCLI'] == '16837') {
			if ( in_array($_SESSION['login']['IDUSUARIO'], array(1, 2, 761)) ) {
				$criterio .= " and cli.codcli = ".intval($dados['CODCLI']).PHP_EOL;
			} else {
				insereModal("danger", "Cliente não autorizado");
			}
		} else {
			$criterio .= " and cli.codcli = ".intval($dados['CODCLI']).PHP_EOL;
		}
	 }
	 if (isset($dados['CGCENT']) && $dados['CGCENT'] != "") {
			$dados['CGCENT'] = str_replace(".", "", $dados['CGCENT']);
			$dados['CGCENT'] = str_replace("/", "", $dados['CGCENT']);
			$dados['CGCENT'] = str_replace("-", "", $dados['CGCENT']);
			$dados['CGCENT'] = str_replace(" ", "", $dados['CGCENT']);
			$dados['CGCENT'] = str_pad($dados['CGCENT'], 14, "0", STR_PAD_LEFT);
			$criterio .= " AND LPAD(REPLACE(REPLACE(REPLACE(TRIM(cli.CGCENT), '.', ''), '-', ''), '/', ''), 14, '0')  = '".$dados['CGCENT']."'".PHP_EOL;
	 }
	 if (isset($dados['NOMECLIENTE']) && $dados['NOMECLIENTE'] != "") {
	 		$dados['NOMECLIENTE'] = mb_strtoupper(trim($dados['NOMECLIENTE']), 'UTF-8');
			$criterio .= " and (cli.cliente like '%{$dados['NOMECLIENTE']}%' OR cli.fantasia like '%{$dados['NOMECLIENTE']}%')".PHP_EOL;
	 }
	 if ($criterio == "") {
		if ( in_array($_SESSION['login']['IDUSUARIO'], array(1, 2, 761)) ) {
			$criterio .= " AND (cli.CLIENTE LIKE '%ANALISE%' OR cli.CLIENTE LIKE '%VEMAP%' )".PHP_EOL;
		} else {
			$criterio .= " AND cli.CLIENTE LIKE '%VEMAP%' ".PHP_EOL;
		}
	 }
	$sql = $sql . $criterio . " ORDER BY cli.FANTASIA ASC";
	// varDump2($sql); die();
	$ret = selectOracle($sql);
	return $ret;
}

function buscaDadosCliente($CODCLI){
	if (is_array($CODCLI)) {
		$CODCLI = $CODCLI['CODCLI'];
	}
	$sql = "WITH fast
				AS (SELECT   d2.dtinicio,
										 d2.dtfim,
										 d2.descricao,
										 d2.percdesc,
										 c.codcli,
										 c.fantasia,
										 c.cliente
							FROM   pcdesconto d2, pcclient c
						 WHERE   d2.codcli = c.codcli 
						  			 AND d2.codfilial = 1 
						  			 AND c.dtexclusao IS NULL
										 AND TRUNC (SYSDATE) BETWEEN TRUNC (d2.dtinicio) AND  TRUNC (d2.dtfim))
				SELECT   cli.codcli,
								 cli.cgcent,
								 cli.cliente,
								 cli.fantasia,
								 NVL (cli.codfilialnf, 1) AS codfilialnf,
								 NVL (NVL (pr.numregiao, cli.numregiaocli), 1) AS numregiao,
								 NVL (cli.codplpag, 1) AS codplpag,
								 NVL (cob.codcob, 'DH') AS codcob,
								 NVL (cob.nivelvenda, 5) AS nivelvenda,
								 DECODE (cli.tipofj, 'J', 'JURÍDICA', 'FÍSICA') AS tipofj,
								 NVL (cli.municent, cli.municcob) AS municipio,
								 NVL (cli.estent, cli.estcob) AS uf,
								 cli.obsentrega1,
								 cli.obsentrega2,
								 cli.obsentrega3,
								 cli.obsentrega4,
								 DECODE(cli.dtexclusao, null, 'N', 'S') AS EXCLUIDO,
								 cli.bloqueio,
								 DBMS_LOB.SUBSTR (cli.motivobloq) AS motivobloq,
								 NVL (cli.limcred, 0) AS limcred,
								 NVL (fast.percdesc, 0) AS fast
					FROM   pcclient cli,
								 pctabprcli pr,
								 pccob cob,
								 fast
				 WHERE       cli.codcob = cob.codcob
								 AND cli.codcli = pr.codcli (+)
								 AND cli.codcli = fast.codcli(+)
								 AND cli.codcli = {$CODCLI}";
	// varDump2($sql); die();
	return reset(selectOracle($sql));
}

function buscaCGCENT($CODCLI){
	$sql = "SELECT TRIM(C.CGCENT) AS CGCENT
						FROM PCCLIENT C
					 WHERE C.DTEXCLUSAO IS NULL
						 AND C.CODCLI = {$CODCLI}";
	// varDump2($sql); die();
	if (!empty($CODCLI)) {
		if($ret = selectOracle($sql)){
			return $ret[0]['CGCENT'];
		} else {
			return false;
		}
	} else {
		return false;
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


function buscaMaquinas(){
	$sql =  "SELECT M.IDMAQUINA, M.MODELO AS MAQUINA
				FROM ORCMAQUINA M
			 ORDER BY M.MODELO ASC";
	$ret = selectOracle($sql);
	return $ret;
}

function buscaVendedores(){
	$sql =  "SELECT   codusur, REGEXP_SUBSTR(pcusuari.nome, '[^ ]+', 1, 1) AS nome
    FROM   pcusuari
   WHERE   pcusuari.dttermino IS NULL
     AND   pcusuari.nome not like 'LOJA%' 
union 
  SELECT   codusur, 'ONLINE- '||REGEXP_SUBSTR(pcusuari.nome, '[^ ]+', 1, 4) AS nome
    FROM   pcusuari
   WHERE   pcusuari.dttermino IS NULL
     AND   pcusuari.nome like 'LOJA%' 
ORDER BY NOME";
	$ret = selectOracle($sql);
	return $ret;
}

function listaClientes(){
	$sql =  "SELECT C.CODCLI, C.CLIENTE, C.FANTASIA, C.MUNICENT, C.ESTENT, C.*
				FROM PCCLIENT C
			 WHERE C.DTEXCLUSAO IS NULL
			 order by C.CLIENTE";
	$ret = selectOracle($sql);
	return $ret;
}


function buscaPecas($maquina){
	$sql = "SELECT M.MODELO AS MAQUINA,
		 C.COMPONENTE,
		 S.SUBCOMPONENTE,
		 P.IDPECA,
		 P.CODPECA,
		 P.DESCRICAO,
		 P.QUANTIDADE
	FROM ORCVINC_MAQCOM   V1,
		 ORCVINC_COMSUB   V2,
		 ORCVINC_SUBPEC   V3,
		 ORCMAQUINA       M,
		 ORCCOMPONENTE    C,
		 ORCSUBCOMPONENTE S,
		 ORCPECA          P
 WHERE V1.IDMAQUINA = M.IDMAQUINA
	 AND V1.IDCOMPONENTE = C.IDCOMPONENTE
	 AND V2.IDCOMPONENTE = C.IDCOMPONENTE
	 AND V2.IDSUBCOMPONENTE = S.IDSUBCOMPONENTE
	 AND V3.IDSUBCOMPONENTE = S.IDSUBCOMPONENTE
	 AND V3.IDPECA = P.IDPECA
	 AND M.IDMAQUINA = ".$maquina."
 ORDER BY M.MODELO, C.COMPONENTE, S.SUBCOMPONENTE, P.DESCRICAO";
	$ret = selectOracle($sql);
	return $ret;
}


/*##########################################################################################*/
function buscaCotacoes($CODPECA){
	$sql = "SELECT C.ORIGEM,
		             C.CODPECA,
		             C.NUMORIGINAL,
		             C.IDCOTACOES,
		             TO_CHAR(C.DATA, 'DD/MM/YYYY') as DATA,
		             SUBSTR(C.VENDEDOR, 1, 15) as VENDEDOR,
		             SUBSTR(C.CLIENTE, 1, 20) as CLIENTE,
		             SUBSTR(C.PRODUTO, 1, 40) as PRODUTO,
		             C.MARCAALFA,
		             C.QUANTIDADE,
		             C.PRECO
		    FROM VIEWEFCOTACAO C
		 WHERE (C.CODPECA = '$CODPECA' OR C.NUMORIGINAL = '$CODPECA')
		 ORDER BY c.data DESC
		 FETCH FIRST 10 ROWS ONLY";
	if($ret = selectOracle($sql)){
		// varDump2($sql);
		// varDump2($ret);
		// die();
		return $ret;
	} else {
		return false;
	}
}


function insereModalCotacoes($NUMORIGINAL, $dados){
	echo PHP_EOL;
	echo '<div class="modal fade" id="modalCotacao" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
			<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header bg-info">
				<h5 class="modal-title" id="exampleModalToggleLabel">Últimas 10 COTAÇÕES da Peça: '.$NUMORIGINAL.'</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<table class="table">
					<tr>
						<th>ORIGEM</th>
						<th>NR ORC</th>
						<th>DATA</th>
						<th>VENDEDOR</th>
						<th>CLIENTE</th>
						<th>PRODUTO</th>
						<th>MARCA</th>
						<th>PREÇO</th>
					</tr>'.PHP_EOL;
			foreach ($dados as $key => $value) {
						echo '<tr>';
						echo '  <td>'.$value['ORIGEM'].'</td>';
						echo '  <td>'.$value['IDCOTACOES'].'</td>';
						echo '  <td>'.formataDataOracletoBr($value['DATA']).'</td>';
						echo '  <td>'.substr($value['VENDEDOR'],0,20).'</td>';
						echo '  <td>'.substr($value['CLIENTE'],0,40).'</td>';
						echo '  <td>'.substr($value['PRODUTO'],0,40).'</td>';
						echo '  <td>'.substr($value['MARCAALFA'],0,20).'</td>';
						echo '  <td>'.moeda($value['PRECO']).'</td>';
						echo '</tr>'.PHP_EOL;
			}
			echo '  </table>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
				</div>
			</div>
			</div>
		</div>';
	echo PHP_EOL;

}


/*##########################################################################################*/
function buscaVendas($CODPECA){
	$sql = "SELECT v.origem,
					       v.numtrans,
					       v.codpeca,
					       TO_CHAR(v.data, 'DD/MM/YYYY') AS data,
					       SUBSTR(TRIM(v.vendedor), 1, 15) AS vendedor,
					       SUBSTR(TRIM(v.cliente), 1, 20) AS cliente,
					       SUBSTR(TRIM(v.produto), 1, 40) AS produto,
					       v.marcaalfa,
					       v.quantidade,
					       ROUND(v.preco, 2) AS preco
					FROM viewefvenda v
					WHERE v.codpeca = '{$CODPECA}'
					ORDER BY v.data DESC
					FETCH FIRST 10 ROWS ONLY";
	// varDump2($sql); DIE();
	return selectOracle($sql);
}

function insereModalVendas($dados){
	echo PHP_EOL;
	echo '<div class="modal fade" id="modalVendas" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
			<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header bg-info">
				<h5 class="modal-title" id="exampleModalToggleLabel">Últimas 10 VENDAS da Peça: '.$dados[0]['CODPECA'].'</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<table class="table">
					<tr>
						<th>ORIGEM</th>
						<th>DATA</th>
						<th>PRODUTO</th>
						<th>VENDEDOR</th>
						<th>CLIENTE</th>
						<th>MARCA</th>
						<th>QTD</th>
						<th>PREÇO</th>
					</tr>'.PHP_EOL;
		foreach ($dados as $key => $value) {
					echo '<tr>';
					echo '  <td>'.$value['ORIGEM'].'</td>';
					echo '  <td>'.formataDataOracletoBr($value['DATA']).'</td>';
					echo (!is_null($value['PRODUTO']))?'<td>'.substr($value['PRODUTO'],0,40).'</td>':'<td></td>';
					echo (!is_null($value['VENDEDOR']))?'<td>'.substr($value['VENDEDOR'],0,40).'</td>':'<td></td>';
					echo (!is_null($value['CLIENTE']))?'<td>'.substr($value['CLIENTE'],0,40).'</td>':'<td></td>';
					echo (!is_null($value['MARCAALFA']))?'<td>'.substr($value['MARCAALFA'],0,40).'</td>':'<td></td>';
					echo '  <td>'.intval($value['QUANTIDADE']).'</td>';
					echo '  <td>'.moeda($value['PRECO']).'</td>';
					echo '</tr>'.PHP_EOL;
		}
			echo '  </table>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
				</div>
			</div>
			</div>
		</div>';
	echo PHP_EOL;
}


/*##########################################################################################*/
function buscaCompras($CODPECA){
	$sql = "SELECT   c.origem,
					         c.codpeca,
					         c.numtrans,
					         TO_CHAR (c.data, 'DD/MM/YYYY') AS data,
					         SUBSTR (TRIM (c.fornecedor), 1, 20) AS fornecedor,
					         SUBSTR (TRIM (c.produto), 1, 40) AS produto,
					         c.marcaalfa,
					         c.quantidade,
					         ROUND (c.preco, 2) AS preco
					  FROM   viewefcompras c
					 WHERE   c.codpeca = '$CODPECA' 
					ORDER BY c.data DESC
					FETCH FIRST 10 ROWS ONLY";
	// varDump2($sql);
	return selectOracle($sql);
}
function insereModalCompras($dados){
	echo PHP_EOL;
	echo '<div class="modal fade" id="modalCompras" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
			<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header bg-info">
				<h5 class="modal-title" id="exampleModalToggleLabel">Últimas 10 COMPRAS da Peça: '.$dados[0]['CODPECA'].'</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<table class="table">
					<tr>
						<th>ORIGEM</th>
						<th>DATA</th>
						<th>FORNECEDOR</th>
						<th>PRODUTO</th>
						<th>MARCA</th>
						<th>QTD</th>
						<th>PREÇO</th>
					</tr>'.PHP_EOL;
		foreach ($dados as $key => $value) {
					echo '<tr>';
					echo '  <td>'.$value['ORIGEM'].'</td>';
					echo '  <td>'.formataDataOracletoBr($value['DATA']).'</td>';
					echo '  <td>'.substr($value['FORNECEDOR'],0,40).'</td>';
					echo '  <td>'.substr($value['PRODUTO'],0,20).'</td>';
					echo '  <td>'.substr($value['MARCAALFA'],0,20).'</td>';
					echo '  <td>'.intval($value['QUANTIDADE']).'</td>';
					echo '  <td>'.moeda($value['PRECO']).'</td>';
					echo '</tr>'.PHP_EOL;
		}
			echo '  </table>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
				</div>
			</div>
			</div>
		</div>';
	echo PHP_EOL;
}



/*##########################################################################################*/
function buscaInformacoes($CODPECA){
	$sql = "SELECT V.CODPECA,
			 V.CODPECA AS PRODUTO,
			 V.DESCRICAO,
			 V.APLICMARCA,
			 V.MARCA,
			 NVL(V.APLICMARCA, V.MARCA) AS MARCAALFA,
			 V.IMPORTADO,
			 V.PRECO,
			 V.DATA,
			 V.NOMEOPCAO
	FROM ORCVIDE V
 WHERE V.VIDE IS NULL
	 AND V.CODPECA LIKE '%".$CODPECA."%'
	 AND ROWNUM <= 10";
		// varDump2($sql);
	return selectOracle($sql);
}
function insereModalInformacoes($dados){
	echo PHP_EOL;
	echo '<div class="modal fade" id="modalInformacoes" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
			<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header bg-info">
				<h5 class="modal-title" id="exampleModalToggleLabel">Informações NPR da Peça: '.$dados[0]['CODPECA'].'</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<table class="table">
					<tr>
						<th>DATA</th>
						<th>MARCA</th>
						<th>PRODUTO</th>
						<th>IMPORTADO</th>
						<th>PREÇO</th>
					</tr>'.PHP_EOL;
		foreach ($dados as $key => $value) {
					echo '<tr>';
					echo '  <td>'.formataDataVidetoBr($value['DATA']).'</td>';
					echo '  <td>'.substr($value['MARCAALFA'],0,20).'</td>';
					echo '  <td>'.substr($value['DESCRICAO'],0,20).'</td>';
					echo '  <td>'.intval($value['IMPORTADO']).'</td>';
					echo '  <td>'.moeda($value['PRECO']).'</td>';
					echo '</tr>'.PHP_EOL;
		}
			echo '  </table>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
				</div>
			</div>
			</div>
		</div>';
	echo PHP_EOL;
}



function buscaClientesWinthor(){
	$sql = "SELECT C.CODCLI, TRIM(C.CLIENTE) AS CLIENTE, NVL(C.ESTENT, C.ESTCOB) AS UF
			FROM PCCLIENT C 
			WHERE C.DTEXCLUSAO IS NULL
			AND C.CLIENTE NOT LIKE '%INATIVO%'
			AND C.CLIENTE NOT LIKE '%DESATIVADO%'
			AND C.CLIENTE NOT LIKE '.%'
			AND C.CLIENTE NOT LIKE 'CLIENTE DE TESTES%'
			ORDER BY TRIM(C.CLIENTE) ASC";
	return selectOracle($sql);
}


function geraNovoCabecalho($dados){
  
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
  
  $idUsuario = (int) ($_SESSION['login']['IDUSUARIO'] ?? 1);/** ADMINISTRADOR */

	$sql = "INSERT INTO ORCORCAMENTOC (IDUSUARIO, CODCLI, PERCDESC, NUMREGIAO, CODFILIALNF, CODUSUR, CODCOB, CODPLPAG, ATENDIMENTO, FRETEDESPACHO, VALORFRETE, OBSENTREGA1, OBSENTREGA2) 
			 VALUES (
			 /*IDUSUARIO*/".$idUsuario.", 
			 /*CODCLI*/'".$dados['CLIENTE']['CODCLI']."', 
			 /*PERCDESC*/'".$dados['CLIENTE']['PERCDESC']."', 
			 /*NUMREGIAO*/'".$dados['CLIENTE']['NUMREGIAO']."', 
			 /*CODFILIALNF*/'".$dados['CLIENTE']['CODFILIALNF']."', 
			 /*CODUSUR*/'".$dados['VENDEDOR']['CODUSUR']."', 
			 /*CODCOB*/'".$dados['COBRANCA']['CODCOB']."',
			 /*CODPLPAG*/'".$dados['PLPAG']['CODPLPAG']."',
			 /*ATENDIMENTO*/'RADAR',
			 /*FRETEDESPACHO*/'G',
			 /*VALORFRETE*/'0.00',
			 /*CLIENTEBALCAO*/'N',
			 /*SEPARARPEDIDO*/'N')";
	if(executarOracle($sql)){
		$sql2 = "SELECT C.*, PL.NUMPR FROM ORCORCAMENTOC C, PCPLPAG PL 
							WHERE C.CODPLPAG = PL.CODPLPAG
								AND C.IDORCAMENTO = (SELECT MAX(C1.IDORCAMENTO) AS IDORCAMENTO 
																			 FROM ORCORCAMENTOC C1 
																			WHERE C1.IDUSUARIO = ".$idUsuario."
																				AND C1.CODCLI = '".$dados['CLIENTE']['CODCLI']."'
																				AND C1.CODUSUR = '".$dados['VENDEDOR']['CODUSUR']."')";
		return reset(selectOracle($sql2));
	} else {
		return false;
	}
}

function buscaDadosCabecalho($IDORCAMENTO){
	$sql = "SELECT C.*, PL.NUMPR FROM ORCORCAMENTOC C, PCPLPAG PL 
					WHERE C.CODPLPAG = PL.CODPLPAG
						AND C.IDORCAMENTO = " . $IDORCAMENTO;
	return reset(selectOracle($sql));
}



function buscaCabecalhoPCPEDCFV($IDORCAMENTO, $NUMPEDRCA){
	$sql = "SELECT CFV.NUMPEDCLI AS IDORCAMENTO,
		 CFV.NUMPEDRCA,
		 CFV.NUMPED,
		 CFV.CODCLI,
		 CFV.CODUSUR,
		 CFV.CODCOB,
		 CFV.CODPLPAG,
		 CFV.DTABERTURAPEDPALM AS DATA,
		 CFV.OBSERVACAO_PC,
		 DECODE(CFV.IMPORTADO,
				NULL,
				'ORCAMENTO',
				1,
				'PENDENTE',
				DECODE(CFV.POSICAO_ATUAL,
					 'R',
					 'REJEITADO',
					 'P',
					 'PENDENTE',
					 'M',
					 'MONTADO',
					 'F',
					 'FATURADO',
					 'B',
					 'BLOQUEADO',
					 'L',
					 'LIBERADO',
					 CFV.POSICAO_ATUAL)) AS STATUS
FROM PCPEDCFV CFV
 WHERE CFV.NUMPEDCLI = ".$IDORCAMENTO."  
	 AND CFV.NUMPEDRCA = ".$NUMPEDRCA;
	$ret = selectOracle($sql);
	// varDump2('IDORCAMENTO '.$IDORCAMENTO);
	// varDump2('NUMPEDRCA '.$NUMPEDRCA);
	// varDump2($sql);
	// varDump2($ret);
	return $ret[0];
}

function buscaDadosCabecalhoStatus($IDORCAMENTO){
	// varDump2($dados);

	$sql = "SELECT T1.IDORCAMENTO,
		 T2.IDUSUARIO,
		 T2.NOME AS USUARIO,
		 T4.CODCLI,
		 T4.CLIENTE,
		 T6.CODUSUR,
		 T6.NOME AS VENDEDOR,
		 T1.DATA,
		 T1.STATUS,
		 T1.ORIGEM,
		 T5.NUMPEDRCA,
		 T5.NUMPED,
		 T1.CODCOB,
		 T1.CODPLPAG,
		 T5.DTABERTURAPEDPALM,
		 T1.ATENDIMENTO,
		 T5.OBSERVACAO_PC AS OBSERVACAO,
		 DECODE(T5.POSICAO_ATUAL,
				NULL,
				'ORCAMENTO',
				'F',
				'FATURADO',
				'M',
				'MONTADO',
				'B',
				'BLOQUEADO',
				'L',
				'LIBERADO',
				'C',
				'CANCELADO',
				'R',
				'REJEITADO',
				T5.POSICAO_ATUAL) AS POSICAO_ATUAL,
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
	LEFT JOIN ORCUSUARIO T2
	ON T1.IDUSUARIO = T2.IDUSUARIO
	LEFT JOIN ORCORCAMENTOI T3
	ON T1.IDORCAMENTO = T3.IDORCAMENTO
	LEFT JOIN PCCLIENT T4
	ON T1.CODCLI = T4.CODCLI
	LEFT JOIN PCPEDCFV T5
	ON T1.IDORCAMENTO = T5.NUMPEDCLI
	LEFT JOIN PCUSUARI T6
	ON T1.CODUSUR = T6.CODUSUR
 WHERE T1.IDORCAMENTO = ".$IDORCAMENTO."
 GROUP BY T1.IDORCAMENTO,
		 T2.IDUSUARIO,
		 T2.NOME,
		 T4.CODCLI,
		 T4.CLIENTE,
		 T6.CODUSUR,
		 T6.NOME,
		 T1.DATA,
		 T1.STATUS,
		 T1.ORIGEM,
		 T5.NUMPEDRCA,
		 T5.NUMPED,
		 T1.CODCOB,
		 T1.CODPLPAG,
		 T5.DTABERTURAPEDPALM,
		 T1.ATENDIMENTO,
		 T5.OBSERVACAO_PC,
		 DECODE(T5.POSICAO_ATUAL,
				NULL,
				'ORCAMENTO',
				'F',
				'FATURADO',
				'M',
				'MONTADO',
				'B',
				'BLOQUEADO',
				'L',
				'LIBERADO',
				'C',
				'CANCELADO',
				'R',
				'REJEITADO',
				T5.POSICAO_ATUAL)
 ORDER BY T1.DATA, T1.IDORCAMENTO DESC";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret[0];
}

function buscaDadosVendedorUsuario($IDUSUARIO){
	$sql = "SELECT U.CODUSUR,
			 U.NOME,
			 U.DTINICIO,
			 U.DTEXCLUSAO,
			 DECODE(U.TIPOVEND,'I', 'INTERNO','E', 'EXTERNO', U.TIPOVEND) AS TIPOVEND,
			 U.EMAIL,
			 NVL(U.CODFILIAL,1) AS CODFILIAL
		FROM PCUSUARI U, ORCUSUARIO OU
		 WHERE U.CODUSUR = OU.CODUSUR
		 AND OU.IDUSUARIO = ".$IDUSUARIO;
	// varDump2($sql);
	$ret = selectOracle($sql);
	// varDump2($ret);
	return $ret[0];
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
			$ret = reset($ret);
			$ret['FILIALVENDA'] = $_SESSION['login']['FILIALVENDA'];
			return $ret;  
		} else {
			return false;
		}
	}
}

function excluiOrcamentosVazios(){
	$sql = "DELETE FROM ORCORCAMENTOC C 
			WHERE NOT EXISTS (SELECT * FROM ORCORCAMENTOI I WHERE C.IDORCAMENTO = I.IDORCAMENTO)";
	return executarOracle($sql);
}




function buscaOrcamentos(){
	
	// excluiOrcamentosVazios();

	$sql = "SELECT * FROM `orcamento` WHERE qtitens>0 ";
	if ($_SESSION['login']['perfil'] == "CLIENTE" && $_SESSION['login']['codcli'] <> NULL) {
		$sql .= " AND `orcamento`.`codcli` = ".$_SESSION['login']['codcli'];
	}
	$sql .= " ORDER BY `orcamento`.`id` DESC";

	$ret = selectOracle($sql);

	if ($ret == false) {
		return false;
	} else {
		return $ret;
	}
}



function atualizaPrecoItensOrcamento($PERCDESC, $NUMREGIAO, $IDORCAMENTO){

	$sql = "SELECT I.IDORCAMENTOI,
		 I.IDORCAMENTO,
		 I.VALIDACAO,
		 I.CODPECA,
		 I.CODVIDE,
		 I.CODPROD,
		 I.DESCRICAO,
		 I.MARCA,
		 I.QTPEDIDA,
		 I.QTDISPONIVEL,
		 I.DISPONIBILIDADE,
		 I.ICMS,
		 I.PVENDA,
		 I.PROCEDENCIA,
		 I.OBSERVACAO,
		 I.STATUS,
		 I.LOCACAO,
		 I.DV,
		 I.CST,
		 I.NCM,
		 I.PTABELA AS PTABELA_1,
		 (ROUND(T.PVENDA * (1 - (".$PERCDESC." / 100)), 2) + 0.01) AS PTABELA_2,
		 CASE WHEN (ROUND(T.PVENDA * (1 - (".$PERCDESC." / 100)), 2) + 0.01) < I.PTABELA 
			THEN I.PTABELA
			ELSE (ROUND(T.PVENDA * (1 - (".$PERCDESC." / 100)), 2) + 0.01)
			END AS PTABELA,
		 CASE WHEN (ROUND(T.PVENDA * (1 - (".$PERCDESC." / 100)), 2) + 0.01) < I.PTABELA 
			THEN I.PTABELA
			ELSE (ROUND(T.PVENDA * (1 - (".$PERCDESC." / 100)), 2) + 0.01)
			END AS PVENDA
			
	FROM ORCORCAMENTOI I, PCPRODUT P, PCTABPR T
 WHERE I.CODPROD = P.CODPROD
	 AND P.CODPROD = T.CODPROD
	 AND P.DTEXCLUSAO IS NULL
	 /*FILTROS*/
	 AND T.NUMREGIAO = ".$NUMREGIAO."
	 AND I.IDORCAMENTO = ".$IDORCAMENTO;
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	
	foreach ($ret as $key => $value) {
		atualizaPrecoItemOrcamento($value);
	}
	return $ret;
}


function atualizaPrecoItemOrcamento($dados){
	$sql = "UPDATE ORCORCAMENTOI I SET
		I.PVENDA = ".$dados['PTABELA'].",
		I.PTABELA = ".$dados['PTABELA_2']."
	 WHERE I.IDORCAMENTO = ".$dados['IDORCAMENTO']."
		 AND I.IDORCAMENTOI = ".$dados['IDORCAMENTOI'];
	// varDump2($sql);
	// die();
	$ret = executarOracle($sql);
	// varDump2($ret);


	$sql = "INSERT INTO ORCLOGALTPRECO (
			IDLOG,
			DATA,
			IDUSUARIO,
			CODCLI,
			IDORCAMENTO,
			CODPECA,
			CODPROD,
			PVENDA_OLD,
			PVENDA_NEW,
			QTPEDIDA_NEW
		) VALUES (
			/*IDLOG*/NULL,
			/*DATA*/SYSDATE,
			/*IDUSUARIO*/".$_SESSION['login']['IDUSUARIO'].",
			/*CODCLI*/".$_SESSION['ORCAMENTO']['CLIENTE']['CODCLI'].",
			/*IDORCAMENTO*/".$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'].",
			/*CODPECA*/'".$dados['CODPECA']."',
			/*CODPROD*/'".$dados['CODPROD']."',
			/*PVENDA_OLD*/".$dados['PTABELA_1'].",
			/*PVENDA_NEW*/".$dados['PTABELA'].",
			/*QTPEDIDA_NEW*/".$dados['QTPEDIDA']."
		)";
	$ret = executarOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
}



function buscaCabecalhoExcel($IDORCAMENTO){
	$sql = "SELECT C.IDORCAMENTO,
			 C.DATA,
			 C.CONTATO,
			 C.MAQUINA,
			 CLI.CODCLI||'-'||CLI.CLIENTE AS CLIENTE,
			 USU.CODUSUR||'-'||USU.NOME AS RCA,
			 COB.CODCOB||'-'||COB.COBRANCA AS COBRANCA,
			 PAG.CODPLPAG||'-'||PAG.DESCRICAO AS PLANOPAG
FROM ORCORCAMENTOC C, PCCLIENT CLI, PCUSUARI USU, PCCOB COB, PCPLPAG PAG
WHERE C.CODCLI = CLI.CODCLI
	AND C.CODUSUR = USU.CODUSUR
	AND C.CODCOB = COB.CODCOB
	AND C.CODPLPAG = PAG.CODPLPAG
	AND C.IDORCAMENTO = ".$IDORCAMENTO ;
	return reset(selectOracle($sql));
}

function buscaORCORCAMENTOC($IDORCAMENTO){
	$sql = "SELECT * FROM ORCORCAMENTOC
					 WHERE IDORCAMENTO = {$IDORCAMENTO}" ;
	// varDump2($sql);
	return reset(selectOracle($sql));
}

function buscaORCORCAMENTOI($IDORCAMENTO){
	$sql = "SELECT * FROM ORCORCAMENTOI
					 WHERE STATUS = 'A'
						 AND IDORCAMENTO = {$IDORCAMENTO}" ;
	// varDump2($sql);
	return selectOracle($sql);
}


function buscaItensOrcamento($IDORCAMENTO){
	$sql = "  SELECT   i.*,
					 (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(TRUNC(i.codprod),
																																					 c.codfilialnf,
																																					 'VP',
																																					 TRUNC(SYSDATE)),
																						0)
																	from dual) AS qtsaldoatual
				FROM   orcorcamentoi i, orcorcamentoc c
			 WHERE   i.idorcamento = c.idorcamento
				 AND   i.status = 'A'
				 AND   i.idorcamento = {$IDORCAMENTO}
		ORDER BY   i.idorcamentoi ASC";
	// varDump2($sql);
	if ($IDORCAMENTO) {
		return selectOracle($sql);
	} else {
		return false;
	}
}


function buscaORCORCAMENTOIFaturar($IDORCAMENTO){
	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
								 TRIM(P.CODAUXILIAR) AS CODAUXILIAR,
								 MAX(NVL(I.PTABELA, 0)) AS PTABELA,
								 MAX(NVL(I.PVENDA, 0)) AS PVENDA,
								 SUM(I.QTPEDIDA) AS QTPEDIDA,
								 (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(TRUNC(P.CODPROD),
																																 C.CODFILIALNF,
																																 'VP',
																																 TRUNC(SYSDATE)),
																	0)
										from dual) AS SALDO
						FROM ORCORCAMENTOC C, ORCORCAMENTOI I, PCPRODUT P
					 WHERE C.IDORCAMENTO = I.IDORCAMENTO
						 AND I.CODPROD = P.CODPROD
						 AND P.DTEXCLUSAO IS NULL
						 AND I.STATUS = 'A'
						 AND I.IDORCAMENTO = {$IDORCAMENTO}
					 GROUP BY TRUNC(P.CODPROD), TRIM(P.CODAUXILIAR), C.CODFILIALNF, P.INFORMACOESTECNICAS
					 ORDER BY P.INFORMACOESTECNICAS ASC" ;
	// varDump2($sql);
	return selectOracle($sql);
}

function buscaItensExcel($IDORCAMENTO){
	$sql = "SELECT I.CODPECA,        
								 I.CODPROD||'-'||I.DV AS CODPROD,
								 I.DESCRICAO,
								 I.MARCA, 
								 I.NCM,
								 I.CST,
								 I.LOCACAO,
								 I.DISPONIBILIDADE,
								 NVL(I.QTDISPONIVEL,0) AS QTDISPONIVEL,
								 NVL(I.QTPEDIDA,0) AS QTPEDIDA,
								 NVL(I.PVENDA,0) AS PVENDA,
								 (I.QTPEDIDA * I.PVENDA) AS SUBTOTAL
						FROM ORCORCAMENTOI	I
					 WHERE I.STATUS = 'A' 
						 AND I.IDORCAMENTO = ".$IDORCAMENTO." 
					 ORDER BY I.IDORCAMENTOI ASC" ;
	// varDump2($sql);
	return selectOracle($sql);
}





function buscaItensAtualizados($IDORCAMENTO){
	$sql = "SELECT TO_CHAR(TRIM(P.CODPROD)) AS CODPROD,
					 TO_CHAR(TRIM(P.DESCRICAO)) AS DESCRICAO,
					 TRIM(M.MARCA) AS MARCA,
					 NVL(CL.CODFILIALNF, 1) AS CODFILIALNF,
					 NVL(CL.NUMREGIAOCLI, 1) AS NUMREGIAO,
					 NVL(CL.ESTCOB, CL.ESTENT) AS UF,
					 NVL(D.PERCDESC, 0) AS PERCDESC,
					 CASE WHEN NVL(TT.CODST,0) = 0 THEN 'N' ELSE 'S' END AS TRIBUT,
					 (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD, NVL(CL.CODFILIALNF, 1), 'VP', TRUNC(SYSDATE)),0) from dual) AS SALDO,
					 ROUND(DECODE(PL.NUMPR,
												1,
												PR.PVENDA1,
												2,
												PR.PVENDA2,
												3,
												PR.PVENDA3,
												4,
												PR.PVENDA4,
												5,
												PR.PVENDA5,
												6,
												PR.PVENDA6,
												7,
												PR.PVENDA7,
												PR.PVENDA) * (1 - ((NVL(D.PERCDESC, 0) * (-1)) / 100)),
								 2) + 0.01 AS PMINIMO
			FROM ORCORCAMENTOC C
			LEFT JOIN ORCORCAMENTOI I
				ON C.IDORCAMENTO = I.IDORCAMENTO
			LEFT JOIN PCPRODUT P
				ON I.CODPROD = P.CODPROD
			LEFT JOIN PCMARCA M
				ON P.CODMARCA = M.CODMARCA
			LEFT JOIN PCCLIENT CL
				ON C.CODCLI = CL.CODCLI
			LEFT JOIN PCEST E
				ON (P.CODPROD = E.CODPROD AND E.CODFILIAL = NVL(CL.CODFILIALNF, 1))
			LEFT JOIN PCDESCONTO D
				ON C.CODCLI = D.CODCLI
			LEFT JOIN PCTABPR PR
				ON (P.CODPROD = PR.CODPROD AND PR.NUMREGIAO = NVL(CL.NUMREGIAOCLI, 1))
			LEFT JOIN PCPLPAG PL
				ON C.CODPLPAG = PL.CODPLPAG
			LEFT JOIN PCTABTRIB TT
				ON (P.CODPROD = TT.CODPROD AND NVL(CL.ESTCOB, CL.ESTENT) = TT.UFDESTINO AND
					 TT.CODFILIALNF = NVL(CL.CODFILIALNF, 1))
		 WHERE I.CODPROD IS NOT NULL
			 AND P.DTEXCLUSAO IS NULL
			 AND C.IDORCAMENTO = ".$IDORCAMENTO;
	 return selectOracle($sql);
}

function buscaItensPCPEDIFV($IDORCAMENTO, $NUMPEDRCA){

	$sql = "SELECT * FROM PCPEDIFV
			WHERE NUMPEDCLI = ".$IDORCAMENTO."  
			AND NUMPEDRCA = ".$NUMPEDRCA." 
			ORDER BY NUMSEQ ASC";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret); die();
	if ($ret <> false) {
		foreach ($ret as $key => $value) {
			if ($value['CODPROD'] <> "") {
				// varDump2($value); 
				$PCEST = buscaPCEST($value['CODPROD'], "1");
				// varDump2($PCEST);
				// die();
				$ret[$key]['QTDISPONIVEL'] = $PCEST['QTDISPONIVEL'];
			}
		}
		// die();
	}
	return $ret;
}



function buscaItemWinthor($codpeca){
	$sql = "SELECT TRUNC(PCPRODUT.CODPROD) AS CODPROD,
					 TRUNC(PCPRODUT.DV) AS DV
				FROM PCPRODUT
			WHERE DTEXCLUSAO IS NULL
			AND PCPRODUT.NUMORIGINAL LIKE '".trim($codpeca)."%'";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret;
}

function buscaItemVide($codpeca){
	$sql = "SELECT ORCVIDE.IDVIDE,
		 ORCVIDE.CODPECA,
		 ORCVIDE.APLICMARCA,
		 ORCVIDE.VIDE,
		 ORCVIDE.DESCRICAO,
		 ORCVIDE.MARCA,
		 ORCVIDE.IMPORTADO,
		 ORCVIDE.PRECO,
		 ORCVIDE.DATA,
		 ORCVIDE.NOMEOPCAO
	FROM ORCVIDE
 WHERE (ORCVIDE.VIDE IS NOT NULL OR ORCVIDE.VIDE <> '')
	 AND ORCVIDE.CODPECA LIKE '".$codpeca."%'";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();

	return $ret;
}

function buscaItemNpr($codpeca){
	$sql = "SELECT V.IDVIDE,
					 V.CODPECA,
					 V.APLICMARCA,
					 V.VIDE,
					 V.DESCRICAO,
					 V.MARCA,
					 V.IMPORTADO,
					 V.PRECO,
					 V.DATA,
					 V.NOMEOPCAO
				FROM ORCVIDE V
			 WHERE CODPECA LIKE '".$codpeca."%'
				 AND (V.VIDE IS NULL OR V.VIDE = '')";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	
	return $ret;
}


function buscaPCPRODUT($CODPROD){

	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
		 TRUNC(P.DV) AS DV,
		 TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
		 TRIM(P.DESCRICAO) as DESCRICAO,
		 TRIM(P.CODMARCA) AS CODMARCA,
		 TRIM(NVL((SELECT MAX(M.MARCA) FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA)) AS MARCA,
		 DECODE(P.IMPORTADO, 'S', 'IMP.', 'NAC.') AS PROCEDENCIA,
		 NVL(TRIM(P.INFORMACOESTECNICAS),'9999') AS LOCACAO,
		 P.CODAUXILIAR
	FROM PCPRODUT P
 WHERE P.CODPROD IN (".$CODPROD.")";
	
	$ret = selectOracle($sql);
	// VarDump2($sql);
	// varDump2($ret);
	// die();
	if ($ret == false) {
		return false;
	} else {
		return $ret[0];
	}
}

function buscaPCTABPR($CODPROD, $NUMREGIAO){

	$sql = "SELECT DISTINCT PR.CODPROD,
					 PR.NUMREGIAO,
					 (ROUND(NVL(PR.PTABELA1, 0),2)+0.01) AS PTABELA,
					 (ROUND(NVL(PR.PVENDA1, 0),2)+0.01) AS PVENDA
				FROM PCTABPR PR
			 WHERE PR.NUMREGIAO = ".$NUMREGIAO."
				 AND PR.CODPROD = ".$CODPROD;
	$ret = selectOracle($sql);
	// varDump2($sql); 
	// varDump2($ret);
	// die(); 

	if ($ret == false) {
		exibeMensagem("O produto ".$CODPROD." não possui preço na região ".$NUMREGIAO);
		// varDump2($sql); die();
		insertPCTABPR($CODPROD, $NUMREGIAO);
		return false;
	} else {
		if ($ret[0]['PVENDA'] <= '0.01') {
			exibeMensagem("O produto ".$CODPROD." está com preço na região ".$NUMREGIAO." igual a 0.0001. Favor providenciar o ajuste de preço no winthor");
			return false;
		} else {
			return $ret[0];
		}
	}

}

function insertPCTABPR($CODPROD, $NUMREGIAO){

	$sql = "INSERT INTO PCTABPR (CODPROD, NUMREGIAO)
			SELECT PCPRODUT.CODPROD, PCREGIAO.NUMREGIAO
				FROM PCPRODUT, PCREGIAO
			 WHERE PCPRODUT.CODPROD = ".$CODPROD."
			 AND PCREGIAO.NUMREGIAO = ".$NUMREGIAO."
			 AND NOT EXISTS
			 (SELECT PCTABPR.NUMREGIAO
						FROM PCTABPR
					 WHERE PCTABPR.CODPROD = PCPRODUT.CODPROD
						 AND PCTABPR.NUMREGIAO = PCREGIAO.NUMREGIAO)";
	//varDump2($sql); die;
	executarOracle($sql);    
}

function buscaPCEST($codprod, $codfilial){

	if ($codfilial == '' || is_null($codfilial)) {
		$codfilial = 1;
	}

	$sql = "SELECT E.CODPROD,
					 E.CODFILIAL,
					 NVL(E.QTEST,0) AS QTEST, 
					 NVL(E.QTESTGER,0) AS QTESTGER, 
					 NVL(E.QTRESERV,0) AS QTRESERV, 
					 NVL(E.QTINDENIZ,0) AS QTINDENIZ, 
					 NVL(E.QTBLOQUEADA,0) AS QTBLOQUEADA, 
					 NVL((E.QTESTGER - E.QTRESERV - E.QTINDENIZ - E.QTBLOQUEADA),0) AS QTDISPONIVEL
			FROM PCEST E
			WHERE E.CODPROD = ".$codprod."
			AND E.CODFILIAL = ".$codfilial;
	
	// varDump2($sql);
	$ret = selectOracle($sql);
	if ($ret == false) {
		return false;
	} else {
		return $ret[0];
	}
}

function inserePCEST($CODPROD, $CODFILIAL){
	 $sql = "INSERT INTO PCEST
				(CODPROD,
				 CODFILIAL,
				 ESTMIN,
				 QTEST,
				 QTESTGER,
				 CUSTOCONT,
				 CUSTOREAL,
				 CUSTOFIN,
				 CUSTOREP)
			VALUES
				('".$CODPROD."',
				 nvl(".$CODFILIAL.",1),
				 '0',
				 '0',
				 '0',
				 '0',
				 '0',
				 '0',
				 '0');";

	 // VarDump2($sql); die;

	 if(executarOracle($sql) === false){
		exibeMensagem("Erro ao executar a função insereRegistroPcest");
		return false;
	 } else {
		return true;
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


function validaCodprodExisteOrcamento($dados){
	$sql = "SELECT * FROM ORCORCAMENTOI I WHERE I.STATUS='A' AND I.IDORCAMENTO = ".$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']." AND I.CODPECA = '".mb_strtoupper($dados['CODPECA'],'UTF-8')."' ";
	if ($dados['CODPROD'] != '')
		$sql .= " AND I.CODPROD = '".$dados['codprod']."'";
	$ret = selectOracle($sql);
	if ($ret && count($ret)>0) {
		return true;
	} else {
		return false;
	}
}


function proxNumOrcamento(){
	$sql = "SELECT max(`id`) as id FROM `orcamento` WHERE `qtitens` > 0 ";
	$ret = selectOracle($sql);
	return (intval($ret[0]['id'])+1);
}






function inativaItensOrcamento($IDORCAMENTO){
	$sql = "UPDATE ORCORCAMENTOI I SET I.STATUS = 'I' WHERE I.IDORCAMENTO = ".$IDORCAMENTO;
	// varDump2($sql);
	return executarOracle($sql);
}

function limpaPedidosInvalidos(){
	$sql = "DELETE FROM PCPEDCFV C WHERE C.NUMPEDRCA NOT IN (SELECT I.NUMPEDRCA FROM PCPEDIFV I)";
	executarOracle($sql);
	$sql = "DELETE FROM PCPEDIFV I WHERE I.NUMPEDRCA NOT IN (SELECT C.NUMPEDRCA FROM PCPEDCFV C)";
	executarOracle($sql);
}


function buscaUltimoPedido($codrca){
	limpaPedidosInvalidos();
	
	$sql = "SELECT   MAX (NUMPED) AS NUMPED
				FROM   (SELECT   MAX (PC.NUMPEDRCA) AS NUMPED
						FROM   PCPEDC PC
						 WHERE   PC.CODUSUR = ".$codrca."
						UNION
						SELECT   MAX (C.NUMPEDRCA) AS NUMPED
						FROM   PCNFCAN C
						 WHERE   C.CODUSUR = ".$codrca."
						UNION
						SELECT   U.PROXNUMPED AS NUMPED
						FROM   PCUSUARI U
						 WHERE   U.CODUSUR = ".$codrca."
						UNION
						SELECT   MAX(U.NUMPEDRCA) AS NUMPED
						FROM   PCPEDCFV U
						 WHERE   U.CODUSUR = ".$codrca.")";
	// varDump2($sql);
	$ret = selectOracle($sql);
	// varDump2($ret);
	//die();
	
	$numpedrca = $ret[0]['NUMPED'];

	do {
		$ret2 = validaNumpedUsado($numpedrca);
		if ($ret2 == true) {
			$numpedrca++;
		}
	} while ( $ret2 == true);
	return $numpedrca;
}

function validaNumpedUsado($numpedrca){
	$sql = "SELECT NUMPEDRCA FROM PCPEDCFV WHERE NUMPEDRCA = '".$numpedrca."'";
	// varDump2($sql);
	$ret = selectOracle($sql);
	// varDump2($ret);
	//die();
	
	if ($ret == false) {
		return false;
	} else {
		return true;
	}
	 
}


function alteraClienteOrcamento($IDORCAMENTO, $CODCLI){
	$sql = "UPDATE ORCORCAMENTOC 
				 SET CODCLI = '".$CODCLI."'
			 WHERE IDORCAMENTO = ".$IDORCAMENTO;
	// varDump2($sql); die();
	executarOracle($sql);
	return $CODCLI;
}

function alteraVendedorOrcamento($IDORCAMENTO, $CODUSUR){
	$sql = "UPDATE ORCORCAMENTOC 
				 SET CODUSUR = '".$CODUSUR."'
			 WHERE IDORCAMENTO = ".$IDORCAMENTO;
	// varDump2($sql); die();
	executarOracle($sql);
	return $CODUSUR;
}

function alteraCobrancaOrcamento($IDORCAMENTO, $CODCOB){
	$sql = "UPDATE ORCORCAMENTOC 
				 SET CODCOB = '".$CODCOB."'
			 WHERE IDORCAMENTO = ".$IDORCAMENTO;
	// varDump2($sql); die();
	return executarOracle($sql);
}

function alteraPlanopagamentoOrcamento($IDORCAMENTO, $CODPLPAG){
	$sql = "UPDATE ORCORCAMENTOC 
				 SET CODPLPAG = '".$CODPLPAG."'
			 WHERE IDORCAMENTO = ".$IDORCAMENTO;
	// varDump2($sql); 
	return executarOracle($sql);
}

function buscaProdutoWinthor($codplpag, $numregiao, $codprod){
	
	$sql = "SELECT P.CODPROD AS CODPROD,
		 P.DESCRICAO AS PRODUTO,
		 P.EMBALAGEM,
		 P.CODAUXILIAR,
		 PR.NUMREGIAO AS NUMREGIAO,
		 PG.CODPLPAG,
		 PG.NUMPR,
		 PG.PERTXFIM,
		 CASE PG.NUMPR
		 WHEN 1 THEN
			TO_CHAR(ROUND((PR.PVENDA1 * (1 + (PG.PERTXFIM / 100))), 4), 'FM999G999G999D999990')
		 WHEN 2 THEN
			TO_CHAR(ROUND((PR.PVENDA2 * (1 + (PG.PERTXFIM / 100))), 4), 'FM999G999G999D999990')
		 WHEN 3 THEN
			TO_CHAR(ROUND((PR.PVENDA3 * (1 + (PG.PERTXFIM / 100))), 4), 'FM999G999G999D999990')
		 WHEN 4 THEN
			TO_CHAR(ROUND((PR.PVENDA4 * (1 + (PG.PERTXFIM / 100))), 4), 'FM999G999G999D999990')
		 WHEN 5 THEN
			TO_CHAR(ROUND((PR.PVENDA5 * (1 + (PG.PERTXFIM / 100))), 4), 'FM999G999G999D999990')
		 WHEN 6 THEN
			TO_CHAR(ROUND((PR.PVENDA6 * (1 + (PG.PERTXFIM / 100))), 4), 'FM999G999G999D999990')
		 WHEN 7 THEN
			TO_CHAR(ROUND((PR.PVENDA7 * (1 + (PG.PERTXFIM / 100))), 4), 'FM999G999G999D999990')
		 END AS PTABELA
	FROM PCTABPR PR, PCPRODUT P, PCPLPAG PG
 WHERE PR.CODPROD = P.CODPROD
	 AND PR.PTABELA1 IS NOT NULL
	 AND PR.NUMREGIAO IN (".$numregiao.")
	 AND PR.CODPROD IN (".$codprod.")
	 AND PG.CODPLPAG IN (".$codplpag.")";
	
	// varDump2($sql); 
	// die();
	
	$ret = selectOracle($sql);

	$produto['CODPROD']       = intval($ret[0]['CODPROD']);
	$produto['NUMREGIAO']     = intval($ret[0]['NUMREGIAO']);
	$produto['QT_PEDIDA']     = intval($ret[0]['QT_PEDIDA']);
	$produto['PRODUTO']       = sanitizeOracleString($ret[0]['PRODUTO']);
	$produto['EMBALAGEM']     = sanitizeOracleString($ret[0]['EMBALAGEM']);
	$produto['CODAUXILIAR']   = sanitizeOracleString($ret[0]['CODAUXILIAR']);
	$produto['PTABELA']       = moedaPHP($ret[0]['PTABELA']);

	return $produto;
	
}


function buscaPrecoNpr($codpeca, $aplicmarca){
	$sql =  "SELECT NVL(preco,0) as preco 
			 FROM ORCVIDE t1 
			 WHERE t1.codpeca = '".$codpeca."' 
			 AND t1.aplicmarca = '".$aplicmarca."'";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret;
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

function excluirItemOrcamento($dados){
	$sql = "UPDATE ORCORCAMENTOI  SET STATUS = 'I' WHERE IDORCAMENTOI = ".$dados['IDORCAMENTOI'];
	return executarOracle($sql);
}

function exibeModalExclusaoSucesso(){
	echo '<div class="modal fade modal-success" id="modalItemExcluido" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-body text-center">
		<h1><i class="fa fa-check-circle fa-3x"></i></h1>
		<p>Ítem excluído com sucesso!</p>
		</div>
		<div class="modal-footer">
		<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default btn-lg">
			<span aria-hidden="true">Ok</span>
		</button>
		</div>
	</div>
	</div>
</div>';
}

function ativaPecaExixtente($dados){
	$sql = "UPDATE ORCORCAMENTOI SET STATUS = 'A' WHERE IDORCAMENTO = ".$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']." AND CODPECA = UPPER('".$dados['CODPECA']."') ";
			if ($dados['CODPROD'] != '') {
				$sql .= " AND CODPROD = '".$dados['CODPROD']."'";
			}
	executarOracle($sql);
}


function adicionaPecaGenerica($dados){

	$OBS 					    = "SOB CONSULTA";
	$VALIDACAO 			  = "SOB CONSULTA";
	$STATUS 			    = 'A'; 
	$CODVIDE 		    	= "";
	$CODPROD 			    = "";
	$DV 				      = "";
	$DESCRICAO 			  = "SOB CONSULTA";
	$MARCA 				    = "SOB CONSULTA";
	$DISPONIBILIDADE 	= 'SEM ESTOQUE';
	$PROCEDENCIA 		  = "NAC.";
	$LOCACAO 			    = "9999";
	$QTDISPPECA	 		  = intval(0);
	$QTDISPONIVEL 		= intval(0);
	$PTABELA 					= intval(0);
	$PVENDA 					= intval(0);
	$ICMS 						= intval(0);


	$sql = "INSERT INTO ORCORCAMENTOI
				(IDORCAMENTO,
				 VALIDACAO,
				 CODPECA,
				 STATUS,			   
				 CODVIDE,
				 CODPROD,
				 DV,
				 DESCRICAO,
				 MARCA,
				 QTDISPPECA,
				 QTPEDIDA,
				 QTDISPONIVEL,
				 DISPONIBILIDADE,
				 ICMS,
				 PTABELA,
				 PVENDA,
				 PROCEDENCIA,
				 OBSERVACAO,
				 LOCACAO)
			VALUES
				(/*IDORCAMENTO*/'".$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']."',
				 /*VALIDACAO*/'".$VALIDACAO."',
				 /*CODPECA*/UPPER('".$dados['CODPECA']."'),
				 /*STATUS*/'".$STATUS."',
				 /*CODVIDE*/'".$CODVIDE."',
				 /*CODPROD*/'".$CODPROD."',
				 /*DV*/'".$DV."',
				 /*DESCRICAO*/'".$DESCRICAO."',
				 /*MARCA*/'".$MARCA."',
				 /*QTDISPPECA*/".$QTDISPPECA.",
				 /*QTPEDIDA*/'".$dados['QTPEDIDA']."',
				 /*QTDISPONIVEL*/'".$QTDISPONIVEL."',
				 /*DISPONIBILIDADE*/'".$DISPONIBILIDADE."',
				 /*ICMS*/'".$ICMS."',
				 /*PTABELA*/'".$PTABELA."',
				 /*PVENDA*/'".$PVENDA."',
				 /*PROCEDENCIA*/'".$PROCEDENCIA."',
				 /*OBSERVACAO*/'".$OBS."',
				 /*LOCACAO*/'".$LOCACAO."'
			)";
	// varDump2($sql); 		die();	
	return executarOracle($sql);

}


function AlterarPreco($dados){
	// varDump2($dados); 
	// die();

	if (isset($dados['IDORCAMENTOI']) && !empty($dados['IDORCAMENTOI'])) {
		foreach ($dados['IDORCAMENTOI'] as $key => $value) {
			if ($value['check'] == "on") {
				$status = "A";
			} else {
				$status = "I";
			}
			$qtpedida 	= intval($value['qtpedida']);
			$novoPreco 	= str_replace(".", "", $value['preco']);
			$novoPreco 	= str_replace(",", ".", $novoPreco);
			$descricao 	= strtoupper(str_replace("'", " ", $value['descricao']));
			$marca 		= strtoupper(str_replace("'", " ", $value['marca']));
			$DISPONIBILIDADE 		= strtoupper(str_replace("'", " ", $value['DISPONIBILIDADE']));
			$sql = "UPDATE ORCORCAMENTOI I SET
				I.STATUS = '".$status."',
				I.QTPEDIDA = ".$qtpedida.",
				I.PVENDA = ".$novoPreco.",
				I.DESCRICAO = '".$descricao."',
				I.MARCA = '".$marca."',
				I.DISPONIBILIDADE = '".$DISPONIBILIDADE."'
			 WHERE I.IDORCAMENTO = ".$dados['IDORCAMENTO']."
				 AND I.IDORCAMENTOI = ".$dados['ORCAMENTOIID'];

			$ret = executarOracle($sql);
			// varDump2($dados);
			// varDump2($sql);
			// varDump2($ret);
			// die();

			if(!$ret){
				insereModal('danger', 'Ocorreu um erro ao alterar o Status e/ou Preço');
				exibeModal();
			} else {

				$sql = "INSERT INTO ORCLOGALTPRECO (
						IDLOG,
						DATA,
						IDUSUARIO,
						CODCLI,
						IDORCAMENTO,
						CODPECA,
						CODPROD,
						DESCRICAO,
						QTPEDIDA,
						PVENDA_OLD,
						PVENDA_NEW,
						MARCA_OLD,
						MARCA_NEW
					) VALUES (
						/*IDLOG*/NULL,
						/*DATA*/SYSDATE,
						/*IDUSUARIO*/".$_SESSION['login']['IDUSUARIO'].",
						/*CODCLI*/".$_SESSION['login']['IDUSUARIO'].",
						/*IDORCAMENTO*/".$dados['IDORCAMENTO'].",
						/*CODPECA*/'".$value['CODPECA']."',
						/*CODPROD*/'".$dados['CODPROD']."',
						/*DESCRICAO*/'".$descricao."',
						/*QTPEDIDA*/".$qtpedida.",
						/*PVENDA_OLD*/".$dados['PVENDA_OLD'].",
						/*PVENDA_NEW*/".$novoPreco.",
						/*MARCA_OLD*/'".$dados['MARCA_OLD']."',
						/*MARCA_NEW*/'".$marca."'
					)";

				varDump2($sql); die();
				executarOracle($sql);

			}
		}
	}
}

function buscaAcrescimoCliente($CODCLI){
	$sql = "SELECT D.CODCLI, D.PERCDESC
				FROM PCDESCONTO D
			 WHERE D.CODCLI = ".$CODCLI."
				 AND D.APLICADESCONTO = 'S'
				 AND TRUNC(SYSDATE) BETWEEN D.DTINICIO AND D.DTFIM";
	
	// varDump2($sql);
	$ret = selectOracle($sql);
	if ($ret == false) {
		return false;
	} else {
		return $ret[0];
	}
}

function buscaProximoNUMPEDRCA(){
	$sql = "SELECT MAX(BASE.NUMPED) AS NUMPED
	FROM (SELECT MAX(PC.NUMPED) AS NUMPED
			FROM PCPEDC PC
		UNION
		SELECT MAX(PC.NUMPEDRCA) AS NUMPED
			FROM PCPEDC PC
		UNION
		SELECT MAX(PI.NUMPED) AS NUMPED
			FROM PCPEDI PI
		UNION
		SELECT MAX(C.NUMPEDRCA) AS NUMPED
			FROM PCNFCAN C
		UNION
		SELECT U.PROXNUMPED AS NUMPED
			FROM PCUSUARI U
		UNION
		SELECT MAX(U.NUMPEDRCA) AS NUMPED
			FROM PCPEDCFV U
		UNION
		SELECT MAX(U.NUMPEDRCA) AS NUMPED
			FROM PCPEDIFV U) BASE";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	
	if ($ret == false) {
		return intval('990000000');
	} else {
		return (intval($ret[0]['NUMPED'])+1);
	}
}

function buscaListaCobrancasNivel($NIVELVENDA){
	$sql = "SELECT COB.CODCOB,
		 COB.COBRANCA,
		 NVL(COB.BOLETO, 'N') AS BOLETO,
		 NVL(COB.CARTAO, 'N') AS CARTAO,
		 NVL(COB.NIVELVENDA, 0) AS NIVELVENDA,
		 NVL(COB.PERCACRESVENDA, 0) AS PERCDESC
	FROM PCCOB COB
 WHERE COB.CODCOB NOT IN ('D', 'CANC', 'BNF', '9999', 'EMCT', 'PGCT', 'PROT')
	 AND COB.COBRANCA NOT LIKE '%A RECEBER%'
	 AND COB.COBRANCA NOT LIKE '%DESATIVADO%'
	 AND NVL(COB.NIVELVENDA, 0) >= ".$NIVELVENDA."
	 ORDER BY COB.CODCOB";
	// varDump2($sql);
	$ret = selectOracle($sql);
	if ($ret && !empty($ret)) {
		return $ret;
	} else {
		insereModal("danger", "não foi possivel carregar lista de cobranças");
	}
	
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



function buscaDadosCobrancaDefault($CODCLI){
	$sql = "  SELECT C.CODCOB, C.COBRANCA, C.NIVELVENDA
	FROM PCCOB C, PCCLIENT CLI
 WHERE C.CODCOB = CLI.CODCOB
	 AND CLI.CODCLI = ".$CODCLI;
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret[0];
}

function buscaVLMINPEDIDO($CODPLPAG){
	$sql = "SELECT NVL(P.VLMINPEDIDO, 0) AS VLMINPEDIDO
			FROM PCPLPAG P
		 WHERE P.CODPLPAG = {$CODPLPAG}";
	// varDump2($sql);
	if (!empty($CODPLPAG)) {
		if ($ret = selectOracle($sql)){
			return moedaPHP($ret[0]['VLMINPEDIDO']);
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function buscaDadosPlpagDefault($CODCLI){
	$sql = "SELECT P.CODPLPAG,
			P.DESCRICAO,
			NVL(P.VLMINPEDIDO, 0) AS VLMINPEDIDO,
				P.NUMPR
			FROM PCCLIENT CLI
			LEFT JOIN PCCOBPLPAG CP ON CLI.CODCOB = CP.CODCOB
			LEFT JOIN PCPLPAG P ON NVL(CP.CODPLPAG,1) = P.CODPLPAG
		 WHERE CLI.CODCLI = ".$CODCLI."
		 ORDER BY P.DESCRICAO ASC";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret[0];
}

function buscaDadosPlpagCobranca($dados){
	$sql = "SELECT P.CODPLPAG,
		 P.DESCRICAO,
		 NVL(P.VLMINPEDIDO, 0) AS VLMINPEDIDO,
		 P.NUMPR
	FROM PCCOBPLPAG CP, PCPLPAG P
 WHERE CP.CODPLPAG = P.CODPLPAG
	 AND CP.CODCOB = '".$dados['CODCOB']."'";
	$ret = selectOracle($sql);
	varDump2($dados);
	varDump2($sql);
	varDump2($ret);
	die();
	if ($ret) {
		return reset($ret);
	} else {
		$sql2 = "SELECT P.CODPLPAG,
			 P.DESCRICAO,
			 NVL(P.VLMINPEDIDO, 0) AS VLMINPEDIDO,
			 P.NUMPR
		FROM PCPLPAG P
	 WHERE P.CODPLPAG = 1";
		if ($ret2 = selectOracle($sql2)) {
			return $ret2[0];
		} else {
			return false;
		}
	}
}


function buscaListaPlanosCodcob($CODCOB){

	$sql = "SELECT DISTINCT P.CODPLPAG,
							P.DESCRICAO,
							P.NUMPR,
							NVL(P.VLMINPEDIDO, 0) AS VLMINPEDIDO
				FROM PCPLPAG P, PCCOBPLPAG CP
			 WHERE P.CODPLPAG = CP.CODPLPAG
				 AND P.CODPLPAG NOT IN (8)
				 AND P.DESCRICAO NOT LIKE '%DESATIV%'
				 AND NVL(P.STATUS, 'A') = 'A'
				 AND CP.CODCOB = '".$CODCOB."'";
	$ret = selectOracle($sql);
	if ($ret && !empty($ret)) {
		return $ret;
	} else {
		$sql2 = "SELECT DISTINCT P.CODPLPAG,
						 P.DESCRICAO,
						 P.NUMPR,
						 NVL(P.VLMINPEDIDO, 0) AS VLMINPEDIDO
					FROM PCPLPAG P
				 WHERE P.CODPLPAG NOT IN (8)
					 AND P.DESCRICAO NOT LIKE '%DESATIV%'
					 AND NVL(P.STATUS, 'A') = 'A'
				 ORDER BY P.DESCRICAO";
		return selectOracle($sql2);
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

function alteraAtendimentoOrcamento($ATENDIMENTO, $IDORCAMENTO){
	$sql = "UPDATE ORCORCAMENTOC 
				 SET ATENDIMENTO = '".$ATENDIMENTO."'
			 WHERE IDORCAMENTO = ".$IDORCAMENTO;
	// varDump2($sql); die();
	executarOracle($sql);
	return $ATENDIMENTO;
}


function defineOrdemdecompra(array $dados){
	if (!isset($dados['IDORCAMENTO']) || empty($dados['IDORCAMENTO'])) {
		insereModal("danger", "IDORCAMENTO inválido!");
		return;
	}
	$ORDEMDECOMPRA = mb_strtoupper(trim($dados['ORDEMDECOMPRA']),'UTF-8');
	$sql = "UPDATE ORCORCAMENTOC 
				 SET ORDEMDECOMPRA = '".$ORDEMDECOMPRA."'
			 WHERE IDORCAMENTO = ".$dados['IDORCAMENTO'];
	// varDump2($sql); die();
	if (executarOracle($sql) ){
		$_SESSION['ORCAMENTO']['CAB']['ORDEMDECOMPRA'] = $ORDEMDECOMPRA;
	}
	return;
}


function buscaEquipamentoNome($EQUIPAMENTO){
	$sql = "select * from vmp_equipamento where dtexclusao is null and equipamento = '{$EQUIPAMENTO}'";
	return selectOracle($sql);
}

function defineMaquina($dados){
	// varDump2($dados); die();
	$MAQUINA = trim(mb_strtoupper($dados['MAQUINA'],'UTF-8'));
	if ($MAQUINA == "OUTRO") {
		$MAQUINA = trim(mb_strtoupper($dados['OUTRAMAQUINA'],'UTF-8'));
	}

	$sql = "UPDATE ORCORCAMENTOC 
						 SET MAQUINA = '".$MAQUINA."'
					 WHERE IDORCAMENTO = {$dados['IDORCAMENTO']}";
	// varDump2($dados);
	// varDump2($sql);
	// die();
	executarOracle($sql);
	$_SESSION['ORCAMENTO']['CAB']['MAQUINA'] = $MAQUINA;
}

function defineObsGerais($dados){
	$OBSGERAIS = trim(mb_strtoupper($dados['OBSGERAIS'],'UTF-8'));
	$sql = "UPDATE ORCORCAMENTOC 
						 SET OBSGERAIS = '".$OBSGERAIS."'
					 WHERE IDORCAMENTO = {$dados['IDORCAMENTO']}";
	executarOracle($sql);
	$_SESSION['ORCAMENTO']['CAB']['OBSGERAIS'] = $OBSGERAIS;
}

function defineCLIENTEBALCAO($dados){
	$_SESSION['ORCAMENTO']['CAB']['CLIENTEBALCAO'] = $dados['CLIENTEBALCAO'];
	$sql = "UPDATE ORCORCAMENTOC 
						 SET CLIENTEBALCAO = '".$dados['CLIENTEBALCAO']."'
					 WHERE IDORCAMENTO = ".$dados['IDORCAMENTO'];
	return executarOracle($sql);
}


function defineSEPARARPEDIDO($dados){
	$SEPARARPEDIDO = $dados['SEPARARPEDIDO'];
	if ($SEPARARPEDIDO == "S") {
		$MOTIVONAOSEPARAR = "";
	} else {
		$MOTIVONAOSEPARAR = mb_strtoupper(trim($dados['MOTIVONAOSEPARAR']), 'UTF-8');
		if (empty($MOTIVONAOSEPARAR)) {
			insereModal("danger", "Ao Selecionar para não separar o pedido é obrigatório que informe o motivo.");
			$SEPARARPEDIDO = "S";
			$MOTIVONAOSEPARAR = "";
		}
	}
	
	$_SESSION['ORCAMENTO']['CAB']['SEPARARPEDIDO'] = $SEPARARPEDIDO;
	$_SESSION['ORCAMENTO']['CAB']['MOTIVONAOSEPARAR'] = $MOTIVONAOSEPARAR;

	$sql = "UPDATE ORCORCAMENTOC 
						 SET SEPARARPEDIDO = '".$SEPARARPEDIDO."'
						   , MOTIVONAOSEPARAR = '".$MOTIVONAOSEPARAR."'
					 WHERE IDORCAMENTO = ".$dados['IDORCAMENTO'];
	return executarOracle($sql);
}


function defineEXIBIRCODPECAETIQUETA($EXIBIRCODPECAETIQUETA, $IDORCAMENTO){
	$_SESSION['ORCAMENTO']['CAB']['EXIBIRCODPECAETIQUETA'] = trim(mb_strtoupper($EXIBIRCODPECAETIQUETA, 'UTF-8'));
	$sql = "UPDATE ORCORCAMENTOC 
						 SET EXIBIRCODPECAETIQUETA = '".$_SESSION['ORCAMENTO']['CAB']['EXIBIRCODPECAETIQUETA']."'
					 WHERE IDORCAMENTO = ".$IDORCAMENTO;
	return executarOracle($sql);
}


function defineFrete($dados){
	if (is_null($dados['FRETEDESPACHO']) || !isset($dados['FRETEDESPACHO'])) {
		$_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO'] = "G";
		$_SESSION['ORCAMENTO']['CAB']['VALORFRETE'] = moedaPHP(0.00, 4);
	} else {
		$_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO'] = $dados['FRETEDESPACHO'];
		$_SESSION['ORCAMENTO']['CAB']['VALORFRETE'] = moedaPHP($dados['VALORFRETE'], 4);
		
		if ($_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO'] == "G") {
			$_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO'] = "G";
			$_SESSION['ORCAMENTO']['CAB']['VALORFRETE'] = moedaPHP(0.00, 4);
		} else {
			if ($_SESSION['ORCAMENTO']['CAB']['VALORFRETE'] <= 0) {
				$_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO'] = "G";
				$_SESSION['ORCAMENTO']['CAB']['VALORFRETE'] = moedaPHP(0.00, 4);
				insereModal('danger', 'Para frete despacho diferente de Retira na loja, o valor de frete deve ser maior que zero! ');
			}
		}
	}

	$_SESSION['ORCAMENTO']['CAB']['OBSENTREGA3'] 	 = trim(mb_strtoupper($dados['OBSENTREGA3'], 'UTF-8'));
	$_SESSION['ORCAMENTO']['CAB']['OBSENTREGA4'] 	 = trim(mb_strtoupper($dados['OBSENTREGA4'], 'UTF-8'));

	$sql = "UPDATE ORCORCAMENTOC 
					SET FRETEDESPACHO = '".$_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO']."', 
							VALORFRETE    = '".$_SESSION['ORCAMENTO']['CAB']['VALORFRETE']."',
							OBSENTREGA3   = '".$_SESSION['ORCAMENTO']['CAB']['OBSENTREGA3']."',
							OBSENTREGA4   = '".$_SESSION['ORCAMENTO']['CAB']['OBSENTREGA4']."'
					WHERE IDORCAMENTO =  ".$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'];
	// varDump2($dados);
	// varDump2($sql);
	// die();
	return executarOracle($sql);
}



function consultaDescricaoColaborador($PECA){
	
	$PECA = TRIM($PECA);
	$PECA = mb_strtoupper($PECA,'UTF8');
	$PECA = str_replace(" ", "%", $PECA);

	$sql = "SELECT 'DESC_DET' AS ORIGEM,
					 CODPECA AS PRODUTO,
					 DESCRICAO AS DESCRICAO,
					 'DESC_DET' AS MARCA,
					 NULL AS VIDE,
					 0 AS SALDO,
					 0 AS PTABELA,
					 NULL AS WINTHOR,
					 NULL AS CODPROD,
					 NULL AS DV,
					 NULL AS LOCACAO,
					 'NAC.' AS PROCEDENCIA
			FROM ORCDESCRICAO
			WHERE DESCRICAO LIKE '%".$PECA."%' ";
	$ret = selectOracle($sql);
	// varDump2($sql);	
	// varDump2($ret);	
	// die();
	return $ret;

}


function buscaDescontoCliente($CODCLI){
	$sql = "SELECT   NVL(D.PERCDESC,1) AS PERCDESC  
				FROM   PCDESCONTO D
			 WHERE   TRUNC (SYSDATE) BETWEEN D.DTINICIO AND D.DTFIM
				 AND 	 D.APLICADESCONTO = 'S'
				 AND   CODCLI = ".$CODCLI;
	$ret = selectOracle($sql);
	if ($ret == false) {
		return 1;
	} else {
		return floatval($ret[0]['PERCDESC']);
	}
}

function geraModalConsulta($dados){

			// varDump2($value); die();


	
}


function geraModalConsultaDetalhada($CODPECADIG){

			// varDump2($value); die();
			echo '<div class="modal fade bd-example-modal-lg" id="modalConsulta" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
				<div class="modal-body">
					<h3 class="modal-title">Consulta Detalhada Peça da peça '.$CODPECADIG.'</h3>
					<h4>Região Cliente: '.$_SESSION['ORCAMENTO']['CLIENTE']['NUMREGIAO'].'</h4>
					<table id="tb_peca" class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>#</th>
							<th>PRODUTO</th>
							<th>DESCRIÇÃO</th>
							<th></th>
						</tr>
						</thead>
						<tbody>';
			
				if ($_SESSION['CONSULTA'] != false) {

					foreach ($_SESSION['CONSULTA'] as $key => $value) {

						$_SESSION['CONSULTA'][$key]['CODPECA'] = $CODPECADIG;

						echo '<tr>';
						echo '	<td>'.$key.'</td>';
						echo '	<td>'.$value['PRODUTO'].'</td>';
						echo '	<td>'.$value['DESCRICAO'].'</td>';
						echo '	<td><a href="index.php?op=62&ConsultaPeca&IDORCAMENTO='.$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'].'&codpeca='.$value['PRODUTO'].'" class="btn btn-primary">Selecionar</a></td>';
						echo '</tr>';
					}

				}
	
				echo '</tbody>
					</table>';

				echo '<button class="btn btn-default pull-left" data-dismiss="modal" aria-label="Close">
						Cancelar
						</button>';
			echo '	</div>
				<div class="modal-footer">
				</div>
			</div>
			</div>
		</div>';

		echo '<script type="text/javascript">
				$(function () {
					$("#tb_peca").DataTable({
						"searching": true,
						"paging": false,
						"info": false,
						"ordering": true
						})
				})
				</script>';
	
}


function buscaDadosCabRca($NUMPEDRCA){
	$sql = "SELECT DECODE(P.IMPORTADO,
							1,        'AGUARDANDO',
							2,        'SUCESSO',
							3,        'FALHA',
							'PROCESSANDO') AS IMPORTADO,
					 P.POSICAO_ATUAL || '-' || DECODE(P.POSICAO_ATUAL,
													'R',             'REJITADO',
													'L',             'LIBERADO',
													'B',             'BLOQUEADO',
													'F',             'FATURADO',
													'M',             'MONTADO',
													'OUTROS') AS POSICAO,
					 U.CODUSUR || '-' || U.NOME AS RCA,
					 C.CODCLI || '-' || C.CLIENTE AS CLIENTE,
					 P.NUMPEDCLI AS ORCAMENTOID,
					 P.NUMPED,
					 P.NUMPEDRCA,
					 P.NUMPEDCLI,
					 P.OBSERVACAO_PC AS RETORNO,
					 to_CHAR(P.DTABERTURAPEDPALM, 'dd/mm/yyyy hh24:mi:ss') as DTABERTURAPEDPALM
				FROM ORCORCAMENTOC O, PCPEDCFV P, PCUSUARI U, PCCLIENT C
			 WHERE O.IDORCAMENTO = P.NUMPEDCLI
				 AND P.CODUSUR = U.CODUSUR
				 AND P.CODCLI = C.CODCLI
				 AND P.OBS1 LIKE 'IMPORTADO VIA APP ORCAMENTO%'
				 AND P.POSICAO_ATUAL <> 'C'
				 AND P.NUMPEDRCA = ".$NUMPEDRCA;
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	return $ret;
}



function buscaPCPEDC($dados){
	$sql = "SELECT
							    NVL(c.codfilial, 1)                            AS codfilial,
							    f.razaosocial                                 AS filial,
							    c.numped,
							    c.numpedrca,
							    c.numpedcli,
							    c.data,

							    CASE c.posicao
							        WHEN 'F' THEN 'FATURADO'
							        WHEN 'M' THEN 'MONTADO'
							        WHEN 'B' THEN 'BLOQUEADO'
							        WHEN 'P' THEN 'PENDENTE'
							        WHEN 'C' THEN 'CANCELADO'
							        ELSE c.posicao
							    END                                           AS posicao,

							    c.dtfat,
							    c.numregiao,
							    c.condvenda                                   AS tv,
							    c.numcar,
							    c.fretedespacho,
							    c.freteredespacho,
							    c.vlfrete,
							    c.codtransp || '-' || c.transportadora        AS transportadora,

							    CASE
							        WHEN c.codemitente = 8888
							        THEN '8888 Força de Vendas'
							        ELSE u.nome || '-' || u.nome
							    END                                           AS emitente,

							    c.obs1 || ' - ' || c.obs2                     AS observacao,
							    c.obsentrega1,
							    c.obsentrega2,

							    CASE NVL(c.tipoembalagem, 'M')
							        WHEN 'M' THEN 'CAIXA'
							        ELSE 'UNIDADE'
							    END                                           AS embalagem,

							    u.codusur,
							    u.nome                                        AS rca,
							    co.codcob,
							    co.cobranca,
							    pl.codplpag,
							    pl.descricao                                  AS plpag,
							    pl.numdias                                    AS prazomedio,
							    NVL(cl.prazoadicional, 0)                     AS prazoadicional,

							    NVL(pl.prazo1,0)||' / '||NVL(pl.prazo2,0)||' / '||NVL(pl.prazo3,0)||' / '||
							    NVL(pl.prazo4,0)||' / '||NVL(pl.prazo5,0)||' / '||NVL(pl.prazo6,0)||' / '||
							    NVL(pl.prazo7,0)||' / '||NVL(pl.prazo8,0)||' / '||NVL(pl.prazo9,0)||' / '||
							    NVL(pl.prazo10,0)||' / '||NVL(pl.prazo11,0)||' / '||NVL(pl.prazo12,0)
							                                                  AS prazopagamento,

							    cl.codcli,
							    TRIM(cl.cliente)                              AS cliente,
							    TRIM(cl.enderent)                             AS endereco,
							    TRIM(cl.numeroent)                            AS numero,
							    TRIM(cl.bairroent)                            AS bairro,
							    TRIM(cl.pontorefer)                           AS pontopref,
							    cl.telent,
							    cl.cepent,
							    cl.cgcent                                     AS cnpj,
							    TRIM(cl.ieent)                                AS iestadual,
							    TRIM(ci.nomecidade)                           AS cidade,
							    ci.uf,
							    a.codativ,
							    TRIM(a.ramo)                                  AS ramo,

							    CASE
							        WHEN c.posicao = 'C'
							        THEN nfc.motivo
							        ELSE NULL
							    END                                           AS motivo_cancelamento,

							    (
							        SELECT REGEXP_SUBSTR(ou.nome, '(\w+)', 1, 1)
							        FROM   omgcheckoutc cc
							               JOIN orcusuario ou
							                 ON ou.idusuario = cc.idusurconferente
							        WHERE  cc.numped = c.numped
							        FETCH FIRST 1 ROWS ONLY
							    )                                             AS separador

							FROM   pcpedc   c
							JOIN   pcfilial f   ON f.codigo     = c.codfilial
							JOIN   pcclient cl  ON cl.codcli    = c.codcli
							JOIN   pccidade ci  ON ci.codcidade = cl.codcidade
							JOIN   pcativi  a   ON a.codativ    = cl.codatv1
							JOIN   pccob    co  ON co.codcob    = c.codcob
							JOIN   pcusuari u   ON u.codusur    = c.codusur
							JOIN   pcplpag  pl  ON pl.codplpag  = c.codplpag

							LEFT JOIN pcnfcan nfc
							       ON nfc.numped = c.numped
							      AND c.posicao = 'C'

							WHERE  1=1";
	 if ($dados['NUMPED']<>'') {
		 $sql .= PHP_EOL."  AND c.numped = '".$dados['NUMPED']."'";
	 }
	 if ($dados['NUMPEDCLI']<>'') {
		 $sql .= PHP_EOL."  AND c.numpedcli = '".$dados['NUMPEDCLI']."'";
	 }
	 if ($dados['NUMPEDRCA']<>'') {
		 $sql .= PHP_EOL."  AND c.numpedrca = '".$dados['NUMPEDRCA']."'";
	 }

	$ret = selectOracle($sql);
	// varDump2($sql); 
	// varDump2($ret); 
	// die();

	if ($ret) {
		return reset($ret);
	} else {
		return false;
	}
}

function buscaPCPEDCFV($dados){
	$sql = "SELECT NVL(C.CODFILIAL, 1) AS CODFILIAL,
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
		 C.OBSENTREGA1,
		 C.OBSENTREGA2,
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

function buscaPCPEDI($dados){
	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
			 P.DV,
			 TRIM(P.DESCRICAO) AS PRODUTO,
			 P.MARCA,
			 P.INFORMACOESTECNICAS AS LOCACAO,
			 P.RUA||' '||P.NUMERO||' '||P.APTO AS ENDERECO,
			 P.NUMORIGINAL,
				P.UNIDADE,
				TRUNC(I.QT) AS QTFATURADA,
				ROUND(I.PTABELA, 4) AS PTABELA,
				ROUND((I.PERDESC), 2) AS PERCDESC,
				ROUND((NVL(P.PESOLIQ,0.001)*NVL(I.QT,0)), 2) AS PESOLIQ,
				ROUND(((I.PTABELA * (I.PERDESC / 100)) * I.QT), 2) AS TOTALDESC,
				ROUND(((I.PTABELA - (I.PTABELA * (I.PERDESC / 100))) * I.QT),
						4) AS VALORTOTAL,
				(SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD, E.CODFILIAL, 'VP', TRUNC(SYSDATE)),0) from dual) AS SALDO,
				(SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD, E.CODFILIAL, 'VP', TRUNC(SYSDATE)),0) from dual) AS SALDODISP
	FROM PCPEDI I, PCPRODUT P, PCEST E
 WHERE I.CODPROD = P.CODPROD
	 AND I.CODPROD = E.CODPROD
	 AND E.CODFILIAL = '1'
	 AND I.NUMPED = '".$dados['NUMPED']."'";
	 if ($dados['NUMPEDCLI']<>'') {
			$sql .= "  AND I.NUMPEDCLI = '".$dados['NUMPEDCLI']."'";
	 }
	 $sql .= "  ORDER BY P.INFORMACOESTECNICAS";
	// varDump2($sql);
	return selectOracle($sql);
}

function buscaProdutosAtualizados($IDORCAMENTO, $NUMREGIAO, $PERCDESC){
	$sql = "SELECT TO_CHAR(TRIM(P.CODPROD)) AS CODPROD,
			 TO_CHAR(TRIM(P.DESCRICAO)) AS DESCRICAO,
			 TRIM(M.MARCA) AS MARCA,
			 ROUND(T.PVENDA * (".$PERCDESC." - ((1 * (-1)) / 100)), 2) + 0.01 AS PTABELA,
			 (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD, E.CODFILIAL, 'VP', TRUNC(SYSDATE)),0) from dual) AS SALDO
	FROM ORCORCAMENTOI I, PCPRODUT P, PCMARCA M, PCEST E, PCTABPR T
 WHERE I.CODPROD = P.CODPROD
	 AND P.CODMARCA = M.CODMARCA
	 AND P.CODPROD = E.CODPROD
	 AND P.CODPROD = T.CODPROD
	 AND P.DTEXCLUSAO IS NULL
	 AND I.CODPROD IS NOT NULL
	 AND E.CODFILIAL = 1
	 AND T.NUMREGIAO = ".$NUMREGIAO."
	 AND I.IDORCAMENTO = ".$IDORCAMENTO;
	 // varDump2($sql);
	 return selectOracle($sql);
}

function buscaPCPEDIFVCortados($NUMPEDRCA){
	$sql = "SELECT I.NUMSEQ,
					 DECODE(I.OBSERVACAO_PC, NULL, 'OK', 'RESALVAS') AS STATUS,
					 P.CODPROD,
					 P.DESCRICAO,
					 P.NUMORIGINAL,
					 P.MARCA,
					 I.QT,
					 I.PVENDA,
					 I.QT_FATURADA,
					 I.OBSERVACAO_PC
				FROM PCPEDIFV I, PCPRODUT P
			 WHERE I.CODPROD = P.CODPROD
				 AND I.OBSERVACAO_PC IS NOT NULL
				 AND I.NUMPEDRCA = ".$NUMPEDRCA."
			 ORDER BY I.NUMSEQ ASC";
	return selectOracle($sql);
}


function buscaTribProd($CODPROD, $UFDESTINO, $CODFILIALNF){
	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
		 TRIM(P.DESCRICAO) AS DESCRICAO,
		 P.NBM AS NCM,
		 NVL(PF.ORIGMERCTRIB, '0') || NVL(T.SITTRIBUT, '00') AS CST
	FROM PCPRODUT P, PCTABTRIB TT, PCTRIBUT T, PCPRODFILIAL PF
 WHERE P.CODPROD = TT.CODPROD
	 AND P.CODPROD = PF.CODPROD
	 AND TT.CODST = T.CODST
	 AND PF.CODFILIAL = ".$CODFILIALNF."
	 AND P.CODPROD = ".$CODPROD."
	 AND TT.UFDESTINO = '".$UFDESTINO."'";
	 // varDump2($sql); die();

	 $ret = selectOracle($sql);
	 if ($ret == false) {
		return false;
	 } else {
		return $ret[0];
	 }
}

function defineContato($dados){
	// varDump2($dados); die();
	$executar = true;

	if (strlen($dados['CONTATO'])<3) {
		$executar = false;
		insereModal('danger', 'O campo Nome do Contato deve conter no mínimo 3 caracteres');
	}
	$TELCELULAR = $dados['TELCELULAR'];
	$TELCELULAR = str_replace("(", "", $TELCELULAR);
	$TELCELULAR = str_replace(")", "", $TELCELULAR);
	$TELCELULAR = str_replace(" ", "", $TELCELULAR);
	$TELCELULAR = str_replace("-", "", $TELCELULAR);
	$TELCELULAR = str_replace("_", "", $TELCELULAR);
	$TELFIXO = $dados['TELFIXO'];
	$TELFIXO = str_replace("(", "", $TELFIXO);
	$TELFIXO = str_replace(")", "", $TELFIXO);
	$TELFIXO = str_replace(" ", "", $TELFIXO);
	$TELFIXO = str_replace("-", "", $TELFIXO);
	$TELFIXO = str_replace("_", "", $TELFIXO);

	if ((strlen($TELCELULAR)<11) and (strlen($dados['TELFIXO'])<10)) {
		$executar = false;
		insereModal('danger', 'Ao menos um dos campos de telefone devem ser preenchidos corretamente');
	}

	if ($executar) {
				
		$_SESSION['ORCAMENTO']['CAB']['CONTATO'] 	= mb_strtoupper($dados['CONTATO'], 'UTF-8');
		$_SESSION['ORCAMENTO']['CAB']['TELCELULAR'] = $dados['TELCELULAR'];
		$_SESSION['ORCAMENTO']['CAB']['TELFIXO'] 	= $dados['TELFIXO'];

		$sql = "UPDATE ORCORCAMENTOC 
					 SET CONTATO = '".mb_strtoupper($dados['CONTATO'], 'UTF-8')."', 
						 TELCELULAR = '".$dados['TELCELULAR']."' , 
						 TELFIXO = '".$dados['TELFIXO']."' 
				 WHERE IDORCAMENTO = ".$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'];
		// varDump2($sql); 
		executarOracle($sql);
		insereModal("success", "Contato atualizado com sucesso");
	} else {
		insereModal("danger", "Houve um erro ao definir o contato");
	}
}

function contaItensPCPEDIFV($NUMPEDRCA){
	$sql = "SELECT COUNT(*) AS QTITENS FROM PCPEDIFV WHERE NUMPEDRCA = ".$NUMPEDRCA;
	$ret = selectOracle($sql);
	// varDump2($ret);
	return intval($ret[0]['QTITENS']);
}



// function validaPCPEDIFV($ITENS, $VLMINPEDIDO){
// 	$NUMSEQ = 0; 
// 	$VLTOTAL = 0; 
// 	foreach ($ITENS as $key1 => $ITEM) {
// 		if ($ITEM["CODPROD"] != null) {
// 			if (buscaPCPRODUT($ITEM["CODPROD"])) {
// 				$SUBTOTAL = (intval($ITEM["QTPEDIDA"]) * str_replace(',', '.', $ITEM["PVENDA"]));
// 				if ($SUBTOTAL > 0) {
// 					$NUMSEQ++;
// 					$VLTOTAL += $SUBTOTAL;
// 				}
// 			}
// 		}
// 	}
// 	if ($NUMSEQ == 0) {
// 		exibeMensagem('O Orçamento não pode ser faturado. Pois não possui nenhum produto winthor válido');
// 		redireciona("index.php?op=62");
// 		exit();
// 	} else {
// 		if($VLTOTAL < $VLMINPEDIDO) {
// 			exibeMensagem('O Orçamento não pode ser faturado. O valor do pedido R$ '.$VLTOTAL.' é menor que o valor mínimo do plano de pagamento R$ '.$VLMINPEDIDO.'.');
// 			redireciona("index.php?op=62");
// 			exit();
// 		} else {
// 			return true;	
// 		}
// 	}
// }

function inserePCPEDIFV($orcamento){
	// varDump2($dados);
	$NUMSEQ 	= 0; 
	$sql 			= "INSERT ALL ";

	foreach ($orcamento['ITE'] as $key1 => $item) {
		$QT							= moedaPHP($item["QTPEDIDA"]);
		$PTABELA				= moedaPHP($item["PTABELA"]);
		$PVENDA					= moedaPHP($item["PVENDA"]);

		$sql .= PHP_EOL."	INTO PCPEDIFV (DTINCLUSAO, DTABERTURAPEDPALM, QT_FATURADA, NUMSEQ, NUMPEDRCA, NUMPEDCLI, CGCCLI, CODUSUR, CODPROD, CODAUXILIAR, QT, PVENDA ) VALUES (
					/*DTINCLUSAO*/SYSDATE, 
					/*DTABERTURAPEDPALM*/TRUNC(SYSDATE), 
					/*QT_FATURADA*/0 , 
					/*NUMSEQ*/'".++$NUMSEQ."', 
					/*NUMPEDRCA*/'{$orcamento['CAB']['NUMPEDRCA']}', 
					/*NUMPEDCLI*/'{$orcamento['CAB']['NUMPEDCLI']}', 
					/*CGCCLI*/'{$orcamento['CAB']['CGCCLI']}', 
					/*CODUSUR*/'{$orcamento['CAB']['CODUSUR']}', 
					/*CODPROD*/'{$item['CODPROD']}', 
					/*CODAUXILIAR*/'{$item['CODAUXILIAR']}', 
					/*QT*/'{$QT}', 
					/*PVENDA*/'{$PVENDA}'
				)";
	}

	$sql .= PHP_EOL."	SELECT * FROM dual";

	// varDump2($sql); 
	// return true;

	return executarOracle($sql);

}


function inserePCPEDCFV($orcamento){

	$sql = "INSERT INTO PCPEDCFV (DTINCLUSAO, DTABERTURAPEDPALM, DTFECHAMENTOPEDPALM, IMPORTADO, ORIGEMPED, CODEMITENTE, CONDVENDA, NUMPEDRCA, NUMPEDCLI, CODUSUR, CGCCLI, CODCLI, CODFILIAL, CODFILIALNF, CODCOB, CODPLPAG, FRETEDESPACHO, VLFRETE, OBS1, OBS2, OBSENTREGA1, OBSENTREGA2, OBSENTREGA3, OBSENTREGA4 )  VALUES ( 
	 /*DTINCLUSAO*/SYSDATE,
	 /*DTABERTURAPEDPALM*/TRUNC(SYSDATE),
	 /*DTFECHAMENTOPEDPALM*/TRUNC(SYSDATE),
	 /*IMPORTADO*/{$orcamento['CAB']['IMPORTADO']},
	 /*ORIGEMPED*/'{$orcamento['CAB']['ORIGEMPED']}',
	 /*CODEMITENTE*/'{$orcamento['CAB']['CODEMITENTE']}',
	 /*CONDVENDA*/{$orcamento['CAB']['CONDVENDA']},
	 /*NUMPEDRCA*/'{$orcamento['CAB']['NUMPEDRCA']}',
	 /*NUMPEDCLI*/'{$orcamento['CAB']['NUMPEDCLI']}',
	 /*CODUSUR*/'{$orcamento['CAB']['CODUSUR']}',
	 /*CGCCLI*/'{$orcamento['CAB']['CGCCLI']}',
	 /*CODCLI*/'{$orcamento['CAB']['CODCLI']}',
	 /*CODFILIAL*/'{$orcamento['CAB']['CODFILIALNF']}',
	 /*CODFILIALNF*/'{$orcamento['CAB']['CODFILIALNF']}',
	 /*CODCOB*/'{$orcamento['CAB']['CODCOB']}',
	 /*CODPLPAG*/'{$orcamento['CAB']['CODPLPAG']}',
	 /*FRETEDESPACHO*/'{$orcamento['CAB']['FRETEDESPACHO']}',
	 /*VLFRETE*/'{$orcamento['CAB']['VLFRETE']}',
	 /*OBS1*/'{$orcamento['CAB']['OBS1']}',
	 /*OBS2*/'{$orcamento['CAB']['OBS2']}',
	 /*OBSENTREGA1*/'{$orcamento['CAB']['OBSENTREGA1']}',
	 /*OBSENTREGA2*/'{$orcamento['CAB']['OBSENTREGA2']}',
	 /*OBSENTREGA3*/'{$orcamento['CAB']['OBSENTREGA3']}',
	 /*OBSENTREGA4*/'{$orcamento['CAB']['OBSENTREGA4']}'
	)";

	// varDump2($sql);
	// die(); 
	
	return executarOracle($sql);
}


function validaOrcamentoJaFaturado($IDORCAMENTO){
	$sql = "SELECT C.*, CAN.MOTIVO AS MOTIVO_CANCELAMENTO
						FROM PCPEDCFV C 
						LEFT JOIN PCNFCAN CAN ON C.numped = CAN.NUMPED
					 WHERE C.NUMPEDCLI = {$IDORCAMENTO}";
	// varDump2($sql); 
	if($ret = selectOracle($sql)){
		return $ret;
	} else {
		return false;
	}
}


function marcaOrcamentoComoFaturado($NUMPEDRCA, $IDORCAMENTO){
	$sql = "UPDATE ORCORCAMENTOC 
				 SET STATUS = 'FATURADO', 
					 DATAFATURAMENTO = SYSDATE , 
					 NUMPEDRCA = ".$NUMPEDRCA."
			 WHERE IDORCAMENTO = ".$IDORCAMENTO;

	// varDump2($sql); 
	return executarOracle($sql);
}


/**
 * Processa/importa pedidos pela integradora.
 *
 * Permite executar a procedure com ou sem os parâmetros opcionais:
 * - CODUSUR
 * - NUMPEDRCA
 *
 * @param mixed $CODUSUR    Código do usuário/RCA.
 * @param mixed $NUMPEDRCA  Número do pedido RCA.
 * @return mixed
 */
function prodessaIntegradora(mixed $CODUSUR = null, mixed $NUMPEDRCA = null)
{
    $CODUSUR   = is_numeric($CODUSUR) ? (int) $CODUSUR : 'NULL';
    $NUMPEDRCA = is_numeric($NUMPEDRCA) ? (int) $NUMPEDRCA : 'NULL';

    $sql = "
        BEGIN
            INTEGRADORA.IMPORTARPEDIDO(
                /* p_tipoleitura */ 1,
                /* p_datainicial */ TRUNC(SYSDATE),
                /* p_datafinal   */ TRUNC(SYSDATE),
                /* p_codfilial   */ '99',
                /* p_tiporeg     */ NULL,
                /* p_codusur     */ {$CODUSUR},
                /* p_numpedrca   */ {$NUMPEDRCA}
            );
        END;
    ";

    return executarOracle($sql);
}


function decodeFreteDespacho($FRETEDESPACHO){
	switch ($FRETEDESPACHO) {
	case 'G': $ret = "Retira na loja"; break;
	case 'C': $ret = "[CIF] Frete VEMAP"; break;
	case 'F': $ret = "[FOB] Frete Cliente"; break;
	case 'T': $ret = "Frete Terceiros"; break;
	case 'R': $ret = "Transporte Próprio VEMAP"; break;
	case 'D': $ret = "Transporte Próprio Cliente"; break;
	default:  $ret = $FRETEDESPACHO; break;
	}
	return $ret;

}

function defineValorTotalOrcamento($IDORCAMENTO){
	$sql = "SELECT ROUND(NVL(SUM(QTPEDIDA*PVENDA),0),2) AS VALORTOTAL 
			FROM ORCORCAMENTOI 
			WHERE STATUS = 'A' AND IDORCAMENTO = ".$IDORCAMENTO;
	$ret = selectOracle($sql);
	
	echo '<script>';
	echo '	 document.getElementById("valorTotal").innerHTML = "'.moeda($ret[0]['VALORTOTAL']).'";';
	echo '</script>';
}


function atualizaStatusOrcamento($IDORCAMENTO){
	$sql = "SELECT DECODE(C.IMPORTADO, 
							NULL, 'ORCAMENTO',
							1, 'PENDENTE',
							DECODE(C.POSICAO_ATUAL, 
								 'R', 'REJEITADO',
								 'P', 'PENDENTE',
								 'M', 'MONTADO',
								 'F', 'FATURADO',
								 'B', 'BLOQUEADO',
								 'L', 'LIBERADO',
								 POSICAO_ATUAL) ) AS POSICAO
				FROM PCPEDCFV C
			 WHERE NUMPEDCLI = ".$IDORCAMENTO;

	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);

	if ($ret) {
		$STATUS = $ret[0]['POSICAO'];
	} else {
		$STATUS = 'ORCAMENTO';
	}

	$sql2 = "UPDATE ORCORCAMENTOC SET STATUS = '".$STATUS."' WHERE IDORCAMENTO = ".$IDORCAMENTO;
	$ret2 = executarOracle($sql2);
	// varDump2($sql2);
	if ($ret2) {
		return 'sucesso';
	} else {
		return 'FALHA AO ATUALIZAR O CABECALHO DO ORCAMENTO';
	}

}

function buscaItensOrcamentoConsulta($IDORCAMENTO){
	$sql = "SELECT OC.IDORCAMENTO,
		 OI.IDORCAMENTOI,
		 OI.DESCRICAO,
		 OI.MARCA,
		 C.CODCLI,
		 NVL(D.PERCDESC, 0) AS ACRECIMO_FAST,
		 NVL(NVL(PC.NUMREGIAO, C.NUMREGIAOCLI), 1) AS NUMREGIAO,
		 PG.CODPLPAG,
		 NVL(PG.PERTXFIM,0) AS ACRECIMO_PLPAG,
		 TRUNC(PG.NUMPR) AS TABELA_NUMPR,
		 TRUNC(PR.CODPROD) AS CODPROD,
		 ROUND(OI.PTABELA, 4) AS PTABELA_ORC,
		 ROUND(OI.PVENDA, 4) AS PVENDA_ORC,
		 ROUND(CASE PG.NUMPR
				 WHEN 1 THEN (PR.PVENDA1 * (1 + (NVL(PG.PERTXFIM,0) / 100)))
				 WHEN 2 THEN (PR.PVENDA2 * (1 + (NVL(PG.PERTXFIM,0) / 100)))
				 WHEN 3 THEN (PR.PVENDA3 * (1 + (NVL(PG.PERTXFIM,0) / 100)))
				 WHEN 4 THEN (PR.PVENDA4 * (1 + (NVL(PG.PERTXFIM,0) / 100)))
				 WHEN 5 THEN (PR.PVENDA5 * (1 + (NVL(PG.PERTXFIM,0) / 100)))
				 WHEN 6 THEN (PR.PVENDA6 * (1 + (NVL(PG.PERTXFIM,0) / 100)))
				 WHEN 7 THEN (PR.PVENDA7 * (1 + (NVL(PG.PERTXFIM,0) / 100)))
			 END, 4)+0.0001 AS PVENDA_ATUAL
	FROM PCCLIENT      C,
		 PCTABPRCLI    PC,
		 PCDESCONTO    D,
		 PCTABPR       PR,
		 ORCORCAMENTOC OC,
		 ORCORCAMENTOI OI,
		 PCPLPAG       PG
 WHERE C.CODCLI = PC.CODCLI(+)
	 AND C.CODCLI = D.CODCLI(+)
	 AND PR.NUMREGIAO = NVL(PC.NUMREGIAO, NVL(C.NUMREGIAOCLI, 1))
	 AND PR.CODPROD = OI.CODPROD
	 AND OI.CODPROD IS NOT NULL
	 AND C.CODCLI = OC.CODCLI
	 AND OC.IDORCAMENTO = OI.IDORCAMENTO
	 AND OC.CODPLPAG = PG.CODPLPAG
	 AND OC.IDORCAMENTO = ".$IDORCAMENTO."
 ORDER BY OI.IDORCAMENTOI ASC
";
	if ($IDORCAMENTO == null) {
		insereModal('danger', 'A função buscaItensOrcamentoConsulta não identificou um IDORCAMENTO válido');
		return false;
	} else {
		return selectOracle($sql);
	}
}

function ajustaPrecoDefasado($dados){
	// varDump2($dados);
	$sql = "UPDATE ORCORCAMENTOI 
			SET PTABELA = ".$dados['PVENDA_ATUAL'].", PVENDA = ".$dados['PVENDA_ATUAL']."
			WHERE IDORCAMENTO = ".$dados['IDORCAMENTO']."
			AND IDORCAMENTOI = ".$dados['IDORCAMENTOI']; 
	return executarOracle($sql);
}


function buscaInvoiceCab($IDORCAMENTO, $NUMPEDRCA){
	$sql = "SELECT CFV.NUMPEDCLI AS IDORCAMENTO,
		 CFV.NUMPEDRCA,
		 CFV.NUMPED,
		 CFV.CODCOB,
		 CFV.CODPLPAG,
		 CFV.DTABERTURAPEDPALM AS DATA,
		 CFV.OBSERVACAO_PC,
		 DECODE(CFV.IMPORTADO,
				NULL,
				'ORCAMENTO',
				1,
				'PENDENTE',
				DECODE(CFV.POSICAO_ATUAL,
					 'R',
					 'REJEITADO',
					 'P',
					 'PENDENTE',
					 'M',
					 'MONTADO',
					 'F',
					 'FATURADO',
					 'B',
					 'BLOQUEADO',
					 'L',
					 'LIBERADO',
					 CFV.POSICAO_ATUAL)) AS STATUS,
		COB.COBRANCA, 
		PAG.DESCRICAO AS PLPAG, 
		CFV.IMPORTADO, 
		CFV.POSICAO_ATUAL, 
		CFV.OBSERVACAO_PC, 

		CLI.CODCLI||' - '||CLI.CLIENTE AS CLIENTE_NOME, 
		CLI.ENDERENT||', NR '||CLI.NUMEROENT||' '||CLI.BAIRROENT AS CLIENTE_ENDERECO, 
		CLI.MUNICENT||' / '||CLI.ESTENT AS CLIENTE_CIDADE, 
		CLI.TELENT AS CLIENTE_FONE, 
		CLI.EMAIL AS CLIENTE_EMAIL,

		F.CODIGO||' - '||F.RAZAOSOCIAL AS FILIAL_NOME, 
		F.ENDERECO||' '||F.BAIRRO AS FILIAL_ENDERECO, 
		F.CIDADE||' / '||F.UF AS FILIAL_CIDADE, 
		F.TELEFONE AS FILIAL_FONE, 
		F.EMAIL AS FILIAL_EMAIL, 

		USU.CODUSUR AS RCA_CODUSUR, 
		USU.NOME AS RCA_NOME

			FROM PCPEDCFV CFV, PCUSUARI USU, PCCLIENT CLI, PCFILIAL F, PCCOB COB, PCPLPAG PAG
			WHERE F.CODIGO = CFV.CODFILIALNF
				AND CFV.CODCOB = COB.CODCOB(+)
				AND CFV.CODPLPAG= PAG.CODPLPAG(+)
				AND CFV.NUMPEDCLI = ".$IDORCAMENTO."
				AND CFV.NUMPEDRCA = ".$NUMPEDRCA;
	if ($IDORCAMENTO != null) {
		$ret = selectOracle($sql);
		if ($ret) {
			return $ret[0];
		} else {
			die("Erro ao executar a função buscaInvoiceCab, nenhum registro retornou a consulta!");
			return false;
		}
	} else {
		die("Erro ao executar a função buscaInvoiceCab, IDORCAMENTO não informado!");
		return false;
	}
}


function buscaInvoiceItem($IDORCAMENTO, $NUMPEDRCA){
	$sql = "SELECT  P.CODPROD, 
					P.DESCRICAO AS PRODUTO, 
					P.MARCA, 
					I.QT, 
					I.PVENDA, 
					DECODE(NVL(I.OBSERVACAO_PC,''),'','OK',I.OBSERVACAO_PC) AS OBSERVACAO_PC
			FROM PCPEDIFV I, PCPRODUT P
			WHERE P.CODPROD = I.CODPROD
				AND I.NUMPEDCLI = ".$IDORCAMENTO."
				AND I.NUMPEDRCA = ".$NUMPEDRCA;
	if ($IDORCAMENTO != null) {
		$ret = selectOracle($sql);
		// varDump2($sql);
		// varDump2($ret); 
		// die();

		if ($ret) {
			return $ret;
		} else {
			die("Erro ao executar a função buscaInvoiceItem, nenhum registro retornou a consulta!");
			return false;
		}
	} else {
		die("Erro ao executar a função buscaInvoiceItem, IDORCAMENTO não informado!");
		return false;
	}
}

function buscaMotivoCancelamento($NUMPEDRCA){
	$sql = "select motivo from PCNFCAN WHERE NUMPEDRCA = ".$NUMPEDRCA;
	$ret = selectOracle($sql);
	if ($ret) {
		return $ret[0]['MOTIVO'];
	} else {
		return false;
	}
}


function copiarCabecalhoCotacaoCliente($NRCOTACAO){
	$sql = "SELECT * FROM CLIORCAMENTOC WHERE IDORCAMENTO = ".$NRCOTACAO;
	$ret = selectOracle($sql);
	if ($CAB = $ret[0]) {
		if ($CAB['CODCLI'] == "") {
			$CAB['CODCLI'] = 1031;
		}
		$sql2 = "INSERT INTO ORCORCAMENTOC (IDUSUARIO, CODCLI, CODUSUR, CONTATO, TELCELULAR, TELFIXO, ATENDIMENTO, FRETEDESPACHO, VALORFRETE, CODCOB, CODPLPAG, OBSERVACAO2) 
				 VALUES (
				 /*IDUSUARIO*/".$_SESSION['login']['IDUSUARIO'].", 
				 /*CODCLI*/'".$CAB['CODCLI']."', 
				 /*CODUSUR*/'".$_SESSION['login']['CODUSUR']."', 
				 /*CONTATO*/'', 
				 /*TELCELULAR*/'', 
				 /*TELFIXO*/'',
				 /*ATENDIMENTO*/'APP CLIENTE',
				 /*FRETEDESPACHO*/'G',
				 /*VALORFRETE*/'0.00',
				 /*CODCOB*/'D',
				 /*CODPLPAG*/'1',
				 /*OBSERVACAO2*/'NÃO')";
		// varDump2($sql2); die();

		executarOracle($sql2);
		$sql3 = "SELECT MAX(IDORCAMENTO) AS IDORCAMENTO 
				 FROM ORCORCAMENTOC 
				 WHERE IDUSUARIO = ".$_SESSION['login']['IDUSUARIO']."
					 AND CODUSUR = '".$_SESSION['login']['CODUSUR']."'";
		$ret3 = selectOracle($sql3);
		// varDump2($sql3);
		// varDump2($ret3);
		// die();

		return buscaDadosCabecalho($ret3[0]['IDORCAMENTO']);

	}
}

function copiarItensCotacaoCliente($NRCOTACAO, $IDORCAMENTO){
	$sql = "SELECT * FROM CLIORCAMENTOI WHERE IDORCAMENTO = ".$NRCOTACAO;
	$ret = selectOracle($sql);
	foreach ($ret as $key => $value) {
		// varDump2($value); die();
		$sql2 = "INSERT INTO ORCORCAMENTOI
					(IDORCAMENTO,
					 VALIDACAO,
					 STATUS,			   
					 CODPECA,
					 CODVIDE,
					 CODPROD,
					 DV,
					 DESCRICAO,
					 MARCA,
					 QTPEDIDA,
					 QTDISPONIVEL,
					 DISPONIBILIDADE,
					 ICMS,
					 PTABELA,
					 PVENDA,
					 PROCEDENCIA,
					 OBSERVACAO,
					 LOCACAO)
				VALUES
					(/*IDORCAMENTO*/'".$IDORCAMENTO."',
					 /*VALIDACAO*/'APP CLIENTE',
					 /*STATUS*/'A',
					 /*CODPECA*/'".$value['CODPECA']."',
					 /*CODVIDE*/'".$value['CODVIDE']."',
					 /*CODPROD*/'".$value['CODPROD']."',
					 /*DV*/'".$value['DV']."',
					 /*DESCRICAO*/'".$value['DESCRICAO']."',
					 /*MARCA*/'".$value['MARCA']."',
					 /*QTPEDIDA*/'".$value['QTPEDIDA']."',
					 /*QTDISPONIVEL*/'".$value['QTDISPONIVEL']."',
					 /*DISPONIBILIDADE*/'".$value['DISPONIBILIDADE']."',
					 /*ICMS*/'0',
					 /*PTABELA*/'".$value['PTABELA']."',
					 /*PVENDA*/'".$value['PVENDA']."',
					 /*PROCEDENCIA*/'".$value['PROCEDENCIA']."',
					 /*OBSERVACAO*/'".$value['OBS']."',
					 /*LOCACAO*/'".$value['LOCACAO']."'
				)";
		// varDump2($sql2); die();
		executarOracle($sql2);
	}
	return buscaItensOrcamento($IDORCAMENTO);

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

function buscaPedidosWinthor($IDORCAMENTO){
	$sql = "SELECT CFV.NUMPED,
								 CFV.NUMPEDCLI,
								 CFV.NUMPEDRCA,
								 CFV.CODUSUR,
								 CFV.CODUSUR,
								 U.NOME AS VENDEDOR,
								 CFV.CODCLI,
								 C.CLIENTE,
								 CFV.DTABERTURAPEDPALM AS DATA,
								 CFV.CODCOB,
								 CFV.CODPLPAG,
								 CFV.OBSERVACAO_PC,
								 DECODE(CFV.IMPORTADO, 
										1, 'AGUARDANDO', 
										2, 'SUCESSO', 
										3, 'REJEITADO', 
										4, 'PROCESSANDO') AS IMPORTADO,
								 DECODE(CFV.POSICAO_ATUAL,
										'M', 'MONTADO',
										'F', 'FATURADO',
										'C', 'CANCELADO',
										'P', 'PENDENTE',
										'R', 'REJEITADO',
										'B', 'BONIFICADO') AS POSICAO,
								 DECODE(CC.DTINICIO,
										NULL,
										'PENDENTE',
										DECODE(CC.DTEMTRANSITO,
													 NULL,
													 DECODE(CC.DTFIM, 
																	NULL, 
																	'SEPARANDO', 
																	'FINALIZADO'),
													 'EM TRANSITO')) AS SEPARACAO,
								 SUBSTR(OU.NOME, 0, INSTR(TRIM(OU.NOME), ' ') - 1) AS SEPARADOR,
								 COUNT(IFV.NUMSEQ) AS QTD_ITENS,
								 SUM(IFV.QT_FATURADA*IFV.PVENDA) AS VLTOTAL
							FROM PCPEDCFV CFV
							LEFT JOIN PCPEDIFV IFV ON IFV.NUMPEDRCA = CFV.NUMPEDRCA
							LEFT JOIN PCUSUARI U ON CFV.CODUSUR = U.CODUSUR
							LEFT JOIN PCCLIENT C ON CFV.CODCLI = C.CODCLI
							LEFT JOIN OMGCHECKOUTC CC ON CFV.NUMPED = CC.NUMPED
							LEFT JOIN ORCUSUARIO OU ON CC.IDUSURCONFERENTE = OU.IDUSUARIO
						 WHERE CFV.NUMPEDCLI = ".$IDORCAMENTO."
						 GROUP BY CFV.NUMPED,
									CFV.NUMPEDCLI,
									CFV.NUMPEDRCA,
									CFV.IMPORTADO,
									CFV.CODUSUR,
									U.NOME,
									CFV.CODCLI,
									C.CLIENTE,
									CFV.DTABERTURAPEDPALM,
									CFV.CODCOB,
									CFV.CODPLPAG,
									CFV.OBSERVACAO_PC,
									CFV.POSICAO_ATUAL,
									OU.NOME,
									DECODE(CC.DTINICIO,
										NULL,
										'PENDENTE',
										DECODE(CC.DTEMTRANSITO,
													 NULL,
													 DECODE(CC.DTFIM, 
																	NULL, 
																	'SEPARANDO', 
																	'FINALIZADO'),
													 'EM TRANSITO'))
							ORDER BY CFV.NUMPEDRCA ASC";
			// varDump2($sql); 
	return selectOracle($sql);
}


function insereModalRejeitado($dados){
	echo '<div class="modal fade" id="modalRejeitado'.$dados['NUMPEDRCA'].'" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
			<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header bg-danger">
				<h5 class="modal-title" id="exampleModalToggleLabel">MOTIVO REJEIÇÃO PEDIDO DO PEDIDO '.$dados['NUMPED'].'</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<h3>'.$dados['OBSERVACAO_PC'].'</h3>';
		echo '<table class="table">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">CODPROD</th>
								<th scope="col">DESCRICAO</th>
								<th scope="col">MARCA</th>
								<th scope="col">QT</th>
								<th scope="col">PVENDA</th>
								<th scope="col">TOTAL</th>
								<th scope="col">OBSERVACAO_PC</th>
							</tr>
						</thead>
						<tbody>';
			if ($itens = buscaRetornoItens($dados['NUMPEDRCA'])) {
				foreach ($itens as $key => $value) {
					echo '<tr>
									<th scope="row">'.($key+1).'</th>
									<td>'.$value['CODPROD'].'</td>
									<td>'.$value['DESCRICAO'].'</td>
									<td>'.$value['MARCA'].'</td>
									<td>'.$value['QT'].'</td>
									<td>'.moeda($value['PVENDA']).'</td>
									<td>'.moeda($value['PVENDA']*$value['QT']).'</td>
									<td>'.$value['OBSERVACAO_PC'].'</td>
								</tr>';
				}
			}

				echo '</tbody>
					</table>';

	echo '</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
				</div>
			</div>
			</div>
		</div>';
	echo PHP_EOL;
}

function buscaRetornoItens($NUMPEDRCA){
	$sql = "SELECT P.CODPROD, P.DESCRICAO, P.MARCA, I.QT, I.PVENDA, I.OBSERVACAO_PC 
					FROM PCPEDIFV I, PCPRODUT P 
					WHERE I.CODPROD = P.CODPROD 
						AND I.OBSERVACAO_PC IS NOT NULL
						AND I.NUMPEDRCA = ".$NUMPEDRCA;
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


function buscaPCPEDIFV($dados){
	$sql = "SELECT I.NUMPED,
			 I.NUMPEDCLI,
			 I.NUMPEDRCA,
			 P.INFORMACOESTECNICAS AS LOCACAO,
			 TRUNC(P.CODPROD) AS CODPROD,
			 P.DV,
			 TRIM(P.DESCRICAO) AS PRODUTO,
			 TRIM(M.MARCA) AS MARCA,
			 P.UNIDADE,
			 TRUNC(I.QT) AS QTPEDIDA,
			 TRUNC(I.QT_FATURADA) AS QTFATURADA,
			 ROUND(NVL(I.PVENDA,0), 2) AS PVENDA,
			 ROUND((NVL(I.PVENDA,0) * NVL(I.QT_FATURADA, 0)), 2) AS VALORTOTAL,
			 TRIM(I.OBSERVACAO_PC) AS OBSERVACAO_PC
	FROM PCPEDIFV I, PCPRODUT P, PCMARCA M
 WHERE I.CODPROD = P.CODPROD
	 AND P.CODMARCA = M.CODMARCA";
	 if (!empty($dados['NUMPED'])) {
		 $sql .= PHP_EOL."  AND I.NUMPED = ".$dados['NUMPED'];
	 }
	 if (!empty($dados['NUMPEDCLI'])) {
		 $sql .= PHP_EOL."  AND I.NUMPEDCLI = ".$dados['NUMPEDCLI'];
	 }
	 if (!empty($dados['NUMPEDRCA'])) {
		 $sql .= PHP_EOL."  AND I.NUMPEDRCA = ".$dados['NUMPEDRCA'];
	 }
	 $sql .= PHP_EOL."  ORDER BY P.INFORMACOESTECNICAS";
	// varDump2($sql);
	return selectOracle($sql);
}


function buscaPCPEDIConferencia($dados){
	$sql = "SELECT I.NUMPED,
			 I.NUMPEDCLI,
			 P.INFORMACOESTECNICAS AS LOCACAO,
			 TRUNC(P.CODPROD) AS CODPROD,
			 P.DV,
			 TRIM(P.DESCRICAO) AS PRODUTO,
			 TRIM(M.MARCA) AS MARCA,
			 P.UNIDADE,
			 TRUNC(I.QT) AS QTFATURADA,
			 ROUND(NVL(I.PVENDA,0), 2) AS PVENDA,
			 ROUND((NVL(I.PVENDA,0) * NVL(I.QT, 0)), 2) AS VALORTOTAL
	FROM PCPEDI I, PCPRODUT P, PCMARCA M
 WHERE I.CODPROD = P.CODPROD
	 AND P.CODMARCA = M.CODMARCA
	 AND I.CODPROD IS NOT NULL";
	if (isset($dados['NUMPED']) && !empty($dados['NUMPED'])) {
		$sql .= PHP_EOL."	AND I.NUMPED = ".$dados['NUMPED'];
	}
	if (isset($dados['NUMPEDCLI']) && !empty($dados['NUMPEDCLI'])) {
		$sql .= PHP_EOL."	AND I.NUMPEDCLI = ".$dados['NUMPEDCLI'];
	}
	$sql .= PHP_EOL."	ORDER BY P.INFORMACOESTECNICAS";
	
	$ret = selectOracle($sql);
	// varDump2($sql); 
	// varDump2($ret); 
	// die();
	return $ret;
}

function buscaDadosCodprod($CODPROD, $CODFILIALNF, $NUMPR, $PERCDESC, $NUMREGIAO){
	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
							 TRUNC(P.DV) AS DV,
							 TRIM(P.DESCRICAO) AS DESCRICAO,
							 TRIM(M.MARCA) AS MARCA,
							 TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
							 TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
							 NULL AS VIDE,
							 (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD, ".$CODFILIALNF.", 'VP', TRUNC(SYSDATE)),0) from dual) AS SALDO,
							 (SELECT ROUND(DECODE(".$NUMPR.",
																		1,
																		PR.PVENDA1,
																		2,
																		PR.PVENDA2,
																		3,
																		PR.PVENDA3,
																		4,
																		PR.PVENDA4,
																		5,
																		PR.PVENDA5,
																		6,
																		PR.PVENDA6,
																		7,
																		PR.PVENDA7,
																		PR.PVENDA) *
														 (1 - ((NVL(".$PERCDESC.", 0) * (-1)) / 100)),
														 2) + 0.01
									FROM PCTABPR PR
								 WHERE PR.CODPROD = P.CODPROD
									 AND PR.NUMREGIAO = ".$NUMREGIAO.") AS PTABELA,
							 (SELECT TT.CODTRIBPISCOFINS
									FROM PCTABTRIB TT
								 WHERE TT.CODPROD = P.CODPROD
									 AND TT.CODFILIALNF = ".$CODFILIALNF."
									 AND TT.UFDESTINO = 'AM') AS CODTRIBPISCOFINS
					FROM PCPRODUT P, PCMARCA M
				 WHERE P.CODMARCA = M.CODMARCA
					 AND P.DTEXCLUSAO IS NULL
					 AND P.CODPROD = '".$CODPROD."'";
	 return reset(selectOracle($sql));
}



function pesquisaOrcamento($dados){
	$criterio = "";

	if (!isset($dados['acao'])) {
		$criterio .= " AND T1.CODUSUR = '".$_SESSION['login']['CODUSUR']."'";	
		$criterio .= " AND trunc(T1.DATA) = trunc(SYSDATE)";
	} else {
		if (isset($dados['IDORCAMENTO']) && $dados['IDORCAMENTO']<>"") {
			$criterio .= " AND T1.IDORCAMENTO = '".$dados['IDORCAMENTO']."'";
		} else {
			if (isset($dados['CODCLI']) && $dados['CODCLI']<>"") {
				$criterio .= " AND T1.CODCLI = '".$dados['CODCLI']."'";
			}
			if (isset($dados['CODUSUR']) && $dados['CODUSUR'] <> 'all') {
				$criterio .= " AND T1.CODUSUR = '".$dados['CODUSUR']."'";	
			}
			if (isset($dados['dataini']) && $dados['dataini']<>"") {
				$criterio .= " AND T1.DATA >= ".formataDataBRtoOracle($dados['dataini'])."";
			}
			if (isset($dados['datafim']) && $dados['datafim']<>"") {
				$criterio .= " AND trunc(T1.DATA) <= ".formataDataBRtoOracle($dados['datafim'])."";
			}
		}
	}

	$sql = "SELECT T1.IDORCAMENTO,
						 T1.DATA,
						 T1.CODCLI,
						 T4.CLIENTE,
						 NVL(T4.ESTENT,T4.ESTCOB) AS UF,
						 T6.CODUSUR,
						 T6.NOME AS RCA,
						 T1.MAQUINA,
						 SUM(CASE
									 WHEN T3.STATUS = 'A' THEN
										T3.QTPEDIDA * T3.PVENDA
									 ELSE
										0
								 END) AS VALORTOTAL,
						 DECODE(T5.IMPORTADO, NULL, 'ORCAMENTO', 1, 'PENDENTE',
										DECODE(T5.POSICAO_ATUAL, 
													 'C', 'CANCELADO',
													 'R', 'REJEITADO',
													 'P', 'PENDENTE',
													 'M', 'MONTADO',
													 'F', 'FATURADO',
													 'B', 'BLOQUEADO',
													 'L', 'LIBERADO',
													 POSICAO_ATUAL)) AS POSICAO_ATUAL
				FROM ORCORCAMENTOC T1
				LEFT JOIN ORCUSUARIO T2 ON T1.IDUSUARIO = T2.IDUSUARIO
				LEFT JOIN ORCORCAMENTOI T3 ON T1.IDORCAMENTO = T3.IDORCAMENTO
				LEFT JOIN PCCLIENT T4 ON T1.CODCLI = T4.CODCLI
				LEFT JOIN PCPEDCFV T5 ON T1.IDORCAMENTO = T5.NUMPEDCLI
				LEFT JOIN PCUSUARI T6 ON T1.CODUSUR = T6.CODUSUR
			 WHERE 1 = 1 ".$criterio."
			 GROUP BY T1.IDORCAMENTO,
						 T1.DATA,
						 T1.CODCLI,
						 T4.CLIENTE,
						 NVL(T4.ESTENT,T4.ESTCOB),
						 T6.CODUSUR,
						 T6.NOME,
						 T1.MAQUINA,
						 DECODE(T5.IMPORTADO, NULL, 'ORCAMENTO', 1, 'PENDENTE',
										DECODE(T5.POSICAO_ATUAL, 
													 'C', 'CANCELADO',
													 'R', 'REJEITADO',
													 'P', 'PENDENTE',
													 'M', 'MONTADO',
													 'F', 'FATURADO',
													 'B', 'BLOQUEADO',
													 'L', 'LIBERADO',
													 POSICAO_ATUAL))
			 ORDER BY T1.DATA, T1.IDORCAMENTO DESC";
	// varDump2($dados);
	// varDump2($sql);
	// die();
	$ret = selectOracle($sql);
	return $ret;
}


function buscaItensCortados($NUMPEDRCA){
	$sql = "SELECT P.CODPROD, P.DV, P.DESCRICAO, P.NUMORIGINAL, M.MARCA, UNIDADE, I.PVENDA, I.QT, (I.PVENDA * I.QT) AS VALORTOTAL, I.QT_FATURADA, I.OBSERVACAO_PC
						FROM PCPEDIFV I, PCPRODUT P, PCMARCA M
					 WHERE I.CODPROD = P.CODPROD
						 AND NVL(P.CODMARCA,1) = M.CODMARCA
						 AND I.CORTE = 'S'
						 AND I.NUMPEDRCA = ".$NUMPEDRCA;
	return selectOracle($sql);
}

function buscaMarcasBGPRODUCT(){
	$sql = "SELECT DISTINCT MARCA FROM BGPRODUCT";
	return selectOracle($sql);
}

function defineOBSERVACAO3($OBSERVACAO3, $IDORCAMENTO){
	$OBSERVACAO3 = trim(mb_strtoupper($OBSERVACAO3,'UTF-8'));
	$_SESSION['ORCAMENTO']['CAB']['OBSERVACAO3'] = $OBSERVACAO3;
	$sql = "UPDATE ORCORCAMENTOC 
						 SET OBSERVACAO3 = '".$OBSERVACAO3."'
					 WHERE IDORCAMENTO = ".$IDORCAMENTO;
	return executarOracle($sql);
}

function pesquisaPecaRadar($dados){
	return $dados;
}

function consultaSaldoPeca($codfilial, $codpeca){
	$sql = "SELECT
	NVL(SUM(CASE WHEN NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) - NVL(E.QTBLOQUEADA, 0) < 0 THEN 0 ELSE NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) - NVL(E.QTBLOQUEADA, 0) END), 0) AS SALDOGERAL
FROM
	PCPRODUT P,
	PCEST E
WHERE
	P.CODPROD = E.CODPROD
	AND P.DTEXCLUSAO IS NULL
	AND E.CODFILIAL LIKE '".$codfilial."'
	AND P.CODPROD IN (
	SELECT
		P.CODPROD
	FROM
		PCPRODUT P
	WHERE
		'W' || P.CODPROD = '".$codpeca."'
UNION
	SELECT
		P.CODPROD
	FROM
		PCPRODUT P
	WHERE
		P.NUMORIGINAL LIKE '".$codpeca."'
UNION
	SELECT
		P.CODPROD
	FROM
		PCPRODUT P
	WHERE
		P.NUMORIGINAL IN
										 (
		SELECT
			V.VIDE
		FROM
			ORCVIDE V
		WHERE
			V.VIDE IS NOT NULL
			AND V.CODPECA LIKE '".$codpeca."'));";
}

function insereItemOrcamento($IDORCAMENTO, $valuePeca){
	$sql = "INSERT INTO ORCORCAMENTOI (IDORCAMENTO, STATUS, VALIDACAO,  CODPECA,  NUMORIGINAL,  CODVIDE,  CODPROD,  DV,  DESCRICAO, MARCA, QTPEDIDA,   QTDISPPECA, QTDISPONIVEL,  DISPONIBILIDADE, ICMS,   PTABELA,  PVENDA,  PVENDAMIN,  PROCEDENCIA, OBSERVACAO, LOCACAO)
		VALUES
			(/*IDORCAMENTO*/'".$IDORCAMENTO."',
			 /*STATUS*/'A',
			 /*VALIDACAO*/'".$valuePeca['ORIGEM']."',
			 /*CODPECA*/'".TRIM($valuePeca['CODPECA'])."',
			 /*NUMORIGINAL*/'".TRIM($valuePeca['NUMORIGINAL'])."',
			 /*CODVIDE*/'".($valuePeca['CODVIDE'] ?? $valuePeca['VIDE'])."',
			 /*CODPROD*/'".$valuePeca['CODPROD']."',
			 /*DV*/'".$valuePeca['DV']."',
			 /*DESCRICAO*/'".$valuePeca['DESCRICAO']."',
			 /*MARCA*/'".$valuePeca['MARCA']."',
			 /*QTPEDIDA*/'".$valuePeca['QTPEDIDA']."',
			 /*QTDISPPECA*/FUN_ORC_CONSULTA_SALDO_PECA('".$valuePeca['CODPECA']."%'),
			 /*QTDISPONIVEL*/'".($valuePeca['QTDISP']??$valuePeca['QTDISPONIVEL'])."',
			 /*DISPONIBILIDADE*/'".$valuePeca['DISPONIBILIDADE']."',
			 /*ICMS*/'".($valuePeca['ICMS']??0)."',
			 /*PTABELA*/'".($valuePeca['PVENDAMIN']??$valuePeca['PVENDA'])."',
			 /*PVENDA*/'".($valuePeca['PTABELA']??$valuePeca['PVENDA'])."',
			 /*PVENDAMIN*/'".($valuePeca['PVENDAMIN']??$valuePeca['PVENDA'])."',
			 /*PROCEDENCIA*/'".($valuePeca['PROCEDENCIA']??'NAC')."',
			 /*OBSERVACAO*/'".$valuePeca['OBSERVACAO']."',
			 /*LOCACAO*/'".$valuePeca['LOCACAO']."'
		)";

	return executarOracle($sql);
}

function validaClienteAliquotaRR($CODCLI, $NUMPED){
	$sql = "SELECT  i.numped,
					i.codcli,
					i.codprod,
					i.codst
	FROM   	pcpedi i, orcaliquotarr a
	WHERE    i.codcli = a.codcli
        AND  a.dtexclusao IS NULL
        AND  i.codst <> 14
        AND  i.codcli = $CODCLI
        AND  i.numped = $NUMPED";
	return selectOracle($sql);
}

function ajustarAliquotaRR($CODCLI, $NUMPED){
	$sql = "UPDATE PCPEDI SET CODST = 14 WHERE CODCLI = $CODCLI AND NUMPED = $NUMPED";
	// varDump2($sql); 
	// die();
	if (executarOracle($sql)){
		insereModal("success", "Alíquota especial RR ajustada com sucesso!");
	} else {
		insereModal("danger", "ERRO ao ajustar a Alíquota especial RR!");
	}
}

function validaNumpedAliquotaRR($NUMPED){
	$sql = "SELECT I.CODST 
					FROM PCPEDI I, ORCALIQUOTARR A 
					WHERE I.CODCLI = A.CODCLI
					AND I.CODST NOT IN (14)
					AND I.NUMPED = {$NUMPED}";
	// varDump2($sql);
	if ($ret = selectOracle($sql)){
		$itensaajustar = 0;
		foreach ($ret as $key => $value) {
			$itensaajustar++;
		}
		if ($itensaajustar == 0) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}

}

#####################################################################################

function listarMontadoras(){
	$sql = "select * from vmp_montadora where dtexclusao is null order by idmontadora asc";
	return selectOracle($sql);
}

function listarTipoequip(){
	$sql = "select * from vmp_tipoequip where dtexclusao is null order by idtipoequip asc";
	return selectOracle($sql);
}

function listarEquipamentos(){
	$sql = "SELECT   DISTINCT e.equipamento
						FROM   vmp_equipamento e, vmp_montadora m
					 WHERE   e.idmontadora = m.idmontadora
						 AND   e.dtexclusao IS NULL
						 AND   m.dtexclusao IS NULL
				ORDER BY   e.equipamento ASC";
	if ($ret = selectOracle($sql) ){
		return $ret;
	} else {
		return $sql;
	}
}

function buscaEquipamento($maquina){
	$sql = "SELECT   e.equipamento as maquina, e.equipamento||' '||m.montadora as equipamento
				    FROM   vmp_montadora m, vmp_equipamento e
				   WHERE   m.idmontadora = e.idmontadora
				     AND   m.dtexclusao IS NULL
				     AND   e.dtexclusao IS NULL
				     AND   e.equipamento = '{$maquina}'";
	if ($ret = selectOracle($sql) ){
		// varDump2($sql);
		// varDump2($ret);
		return $ret[0]['EQUIPAMENTO'];
	} else {
		return $maquina;
	}
}

function buscaEquipamentoMontadora(){
	$sql = "SELECT   e.equipamento AS maquina,
					         e.equipamento || ' ' || m.montadora AS equipamento
					  FROM   vmp_montadora m, vmp_equipamento e
					 WHERE       m.idmontadora = e.idmontadora
					         AND m.dtexclusao IS NULL
					         AND e.dtexclusao IS NULL
					         AND e.equipamento <> 'OUTRO'";
	if ($ret = selectOracle($sql) ){
		// varDump2($sql);
		// varDump2($ret);
		return $ret;
	} else {
		return false;
	}
}

function getTipoequip($idMontadora){
	$sql = "SELECT   DISTINCT t.idtipoequip, t.tipoequipamento
						FROM   vmp_equipamento e, vmp_tipoequip t
					 WHERE   e.idtipoequip = t.idtipoequip
						 AND   t.dtexclusao IS NULL
						 AND   e.idMontadora = ".$idMontadora."
				ORDER BY   t.tipoequipamento ASC";
	if ($ret = selectOracle($sql) ){
		return $ret;
	} else {
		return $sql;
	}
}




function getEquipamento($idMontadora){
	$sql = "SELECT   e.idequipamento, e.equipamento
							FROM   vmp_equipamento e, vmp_tipoequip t
						 WHERE       e.idtipoequip = t.idtipoequip
										 AND e.dtexclusao IS NULL
										 AND e.idmontadora = {$idMontadora}
					GROUP BY   e.idequipamento, e.equipamento
					ORDER BY   e.equipamento ASC";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	return $ret;
}


function radar_consultaPeca(string $codpeca):array|bool{
  if (!$codpeca || empty($codpeca)) {
    return false;
  }

  $codpeca = sanitizeOracleString($codpeca);

  $sql = "WITH
        prm AS (
            SELECT '{$codpeca}' AS peca_alvo,
                   1 AS regiao
            FROM dual
        ),
        prod_tab AS (
            SELECT p.codprod,
                   p.dv,
                   TRIM(p.numoriginal) AS numoriginal,
                   TRIM(p.descricao) AS descricao,
                   TRIM(p.marca) AS marca,
                   TRIM(p.informacoestecnicas) AS locacao,
                   t.pvenda,
                   fo.codfornec,
                   fo.fornecedor,
                   MAX(m.dtmov) AS dtultent_raw
            FROM pcprodut p
            INNER JOIN pcfornec fo ON (fo.codfornec = p.codfornec)
            LEFT JOIN pctabpr t ON (t.codprod = p.codprod AND t.numregiao = (SELECT regiao FROM prm))
            LEFT JOIN pcmov m ON (m.codprod = p.codprod AND m.codoper = 'E' AND m.dtcancel IS NULL)
            WHERE p.dtexclusao IS NULL
            GROUP BY p.codprod, p.dv, p.numoriginal, p.descricao, p.marca, p.informacoestecnicas, t.pvenda, fo.codfornec, fo.fornecedor
        ),
        vide_rel AS (
            SELECT v.vide AS numoriginal FROM orcvide v, prm WHERE v.dtexclusao IS NULL AND v.nomeopcao = 'VIDE' AND v.codpeca = prm.peca_alvo
            UNION
            SELECT v.codpeca AS numoriginal FROM orcvide v, prm WHERE v.dtexclusao IS NULL AND v.nomeopcao = 'VIDE' AND v.vide = prm.peca_alvo
        ),
        orcvide_alvo AS (
            SELECT v.nomeopcao, v.codpeca, v.vide, v.descricao, v.aplicmarca, v.preco
            FROM orcvide v, prm
            WHERE v.dtexclusao IS NULL 
              AND (v.vide = prm.peca_alvo OR v.codpeca = prm.peca_alvo)
        )
    SELECT 
        ord, origem, numoriginal, vide, wint, descricao, marca, 
        codfornec, fornecedor, locacao, qtdisponivel, pvenda,
        TO_CHAR(dtultent_raw, 'DD/MM/YYYY') AS dtultent
    FROM (
        /* 0: Busca Direta Winthor */
        SELECT 0 AS ord, 'WINT' AS origem, p.numoriginal, CAST(NULL AS VARCHAR2(50)) AS vide,
               p.codprod || '-' || p.dv AS wint, p.descricao, p.marca, p.codfornec, p.fornecedor, p.locacao,
               p.dtultent_raw,
               GREATEST(pkg_estoque.estoque_disponivel(p.codprod, '1', 'VP', TRUNC(SYSDATE)), 0) AS qtdisponivel,
               ROUND(p.pvenda, 2) + 0.01 AS pvenda
        FROM prod_tab p, prm
        WHERE p.numoriginal = prm.peca_alvo

        UNION ALL

        /* 1: Busca na VIDE (Sem correlação direta WINT) */
        SELECT 1 AS ord, 'VIDE' AS origem, v.vide, v.codpeca, CAST(NULL AS VARCHAR2(50)) AS wint,
               v.descricao, v.aplicmarca, NULL, NULL, NULL, NULL, NULL, NULL
        FROM orcvide_alvo v
        WHERE v.nomeopcao = 'VIDE' AND v.vide NOT LIKE 'W%' AND v.codpeca NOT LIKE 'W%'

        UNION ALL

        /* 2: Correlação WINT-VIDE (Peças equivalentes no Winthor) */
        SELECT 2 AS ord, 'WINT-VIDE' AS origem, p.numoriginal, (SELECT peca_alvo FROM prm),
               p.codprod || '-' || p.dv AS wint, p.descricao, p.marca, NULL, NULL, NULL,
               p.dtultent_raw,
               GREATEST(pkg_estoque.estoque_disponivel(p.codprod, '1', 'VP', TRUNC(SYSDATE)), 0) AS qtdisponivel,
               ROUND(p.pvenda, 2) + 0.01 AS pvenda
        FROM prod_tab p
        WHERE p.numoriginal IN (SELECT vr.numoriginal FROM vide_rel vr)

        UNION ALL

        /* 3: Outras Opções (Serviços/Marcas) */
        SELECT DISTINCT 3 AS ord, v.nomeopcao, v.codpeca, v.vide, CAST(NULL AS VARCHAR2(50)) AS wint,
               v.descricao, v.aplicmarca, NULL, NULL, NULL, NULL, NULL, TO_NUMBER(v.preco)
        FROM orcvide_alvo v
        WHERE v.nomeopcao <> 'VIDE'
    )
    ORDER BY ord ASC, numoriginal ASC";
    // varDump2($sql); die();

  return selectOracle($sql);
}