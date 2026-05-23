<?php  
function consultarNumoriginal($NUMORIGINAL){
	if ($NUMORIGINAL == "") {
		return false;
	} else {
		$NUMORIGINAL = mb_strtoupper(trim($NUMORIGINAL), 'UTF-8');
		$sql = "SELECT *
						FROM VIEW_PORTAL_CONSULTAPRODUTO
					 WHERE PTABELA >= 20
						 AND MARCA NOT LIKE 'VMP'
						 AND TRIM(NUMORIGINAL) LIKE '".$NUMORIGINAL."'";
		$ret = selectOracle($sql);
		if($ret && count($ret)>0){
			return $ret;
		} else {
			return false;
		}
	}
}

function buscaListaNUMORIGINAL($CODPECA){
	if ($CODPECA == "") {
		return false;
	} else {
		$CODPECA = mb_strtoupper(trim($CODPECA), 'UTF-8');
		$sql = "SELECT *
						FROM VIEW_PORTAL_CONSULTAPRODUTO
					 WHERE PTABELA >= 20
						 AND MARCA NOT LIKE 'VMP'
						 AND TRIM(NUMORIGINAL) LIKE '".$CODPECA."'";
		if($ret = selectOracle($sql)){
			return $ret;
		} else {
			return false;
		}
	}
}

function buscaProdutosMovimentadosInicial($dias, $inicial) {
	$sql = "SELECT TRIM(UPPER(CAST(REPLACE(P.NUMORIGINAL, CHR(160), '') AS VARCHAR2(20)))) AS NUMORIGINAL
		  FROM PCPRODUT P, PCTABPR T, PCEST E
		 WHERE P.CODPROD = T.CODPROD(+)
		   AND P.CODPROD = E.CODPROD
		   AND P.DTEXCLUSAO IS NULL
		   AND T.NUMREGIAO = 1
		   AND E.CODFILIAL = 1
		   AND NVL(P.REVENDA, 'S') = 'S'
		   AND P.NUMORIGINAL NOT LIKE '%VMP%'
		   AND P.NUMORIGINAL NOT LIKE '%0X0001%'
		   AND P.NUMORIGINAL NOT LIKE '%LOTE%'
		   AND P.CODEPTO NOT IN (65 /*IMOBILIZADOS*/)
		   AND P.CODMARCA NOT IN (32905 /*VMP*/, 36765 /*PATRIMONIO*/, 84 /*CONSUMO*/)
		   AND LENGTH(TRIM(UPPER(CAST(REPLACE(P.NUMORIGINAL, CHR(160), '') AS VARCHAR2(20))))) > 2
		   AND TRIM(UPPER(CAST(REPLACE(P.NUMORIGINAL, CHR(160), '') AS VARCHAR2(20)))) NOT IN (SELECT NUMORIGINAL FROM BGPRODUCT2 WHERE DATA >= TRUNC(SYSDATE-2))
		   AND P.NUMORIGINAL LIKE '{$inicial}%'
		  GROUP BY TRIM(UPPER(CAST(REPLACE(P.NUMORIGINAL, CHR(160), '') AS VARCHAR2(20))))
		  order by 1 asc";
	// varDump2($sql);
	$ret = selectOracle($sql);
	if ($ret && !empty($ret)) {
		return $ret;
	} else {
		return false;
	}
}



function buscaProdutosWinthorListaNumoriginal($LISTANUMORIGINAL){
	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
			       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
			       TRIM(P.DESCRICAO) AS DESCRICAO,
			       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
			           P.MARCA) AS MARCA,
			       GREATEST(NVL(P.PESOBRUTO, 0), 1) AS PESOBRUTO,
			       GREATEST(NVL(P.ALTURAM3, 0), 1) AS ALTURAM3,
			       GREATEST(NVL(P.LARGURAM3, 0), 1) AS LARGURAM3,
			       GREATEST(NVL(P.COMPRIMENTOM3, 0), 1) AS COMPRIMENTOM3,
			       (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
			                                                       1,
			                                                       'VP',
			                                                       TRUNC(SYSDATE)),
			                        0)
			          FROM DUAL) AS SALDO,
			       (SELECT CASE
			                 WHEN T.PVENDA <= 0.01 THEN
			                  0
			                 ELSE
			                  (ROUND(T.PVENDA, 2) + 0.01)
			               END
			          FROM PCTABPR T
			         WHERE T.NUMREGIAO = 1
			           AND T.CODPROD = P.CODPROD) AS PVENDA
			  FROM PCPRODUT P
			 WHERE P.DTEXCLUSAO IS NULL
			   AND P.NUMORIGINAL IN ({$LISTANUMORIGINAL})
			 ORDER BY 9 DESC";
	// varDump2($sql);
	return selectOracle($sql);
}

function atualizarDimensoesProdutoWinthor($Product){
	// $debug = true;

	if($debug) varDump2("atualizarDimensoesProdutoWinthor");
	if($debug) varDump2($Product);

	$vides = buscaVides($Product['reference']);
	if (!empty($vides)) {
		$vides = substr($vides, 3);
		$vides = str_replace(" | ", ", ", $vides);
	}
	$vides = "'".str_replace(", ", "', '", $vides) ."'";
	if($debug) varDump2($vides);

	$produtos = buscaProdutosWinthorListaNumoriginal($vides);
	if($debug) varDump2($produtos);

	foreach ($produtos as $key => $value) {
		$sql = "UPDATE PCPRODUT SET 
					   ALTURAM3 = ".moedaPHP($Product['height']).",
					   LARGURAM3 = ".moedaPHP($Product['width']).",
					   COMPRIMENTOM3 = ".moedaPHP($Product['length']).",
					   PESOBRUTO = ".moedaPHP($Product['weight'])."
				WHERE CODPROD = ".$value["CODPROD"];
		executarOracle($sql);
	}

	if($debug) die();
}


function produtoPossuiCadBagy($NUMORIGINAL){
	if (!empty($NUMORIGINAL)) {
		$sql = "SELECT MAX(PRODUCT_ID) AS PRODUCT_ID 
						FROM BGPRODUCT WHERE EXTERNAL_ID = '".$NUMORIGINAL."'";
		$ret = selectOracle($sql);
		if ($ret = reset($ret)) {
			// varDump2($ret); die();
			return $ret['PRODUCT_ID'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function bgproduct2_inserir($product_id, $external_id, $name, $dtcadastro, $dtatualizacao, $categoria_id){
	$sql = "INSERT INTO BGPRODUCT2 (
			 PRODUCT_ID,
			 NUMORIGINAL,
			 NAME,
			 DTCADASTRO,
			 DTATUALIZACAO,
			 CATEGORIA_ID
			) VALUES (
			 '".$product_id."', 
			 '".$external_id."', 
			 '".$name."', 
			 '".$dtcadastro."', 
			 '".$dtatualizacao."', 
			 '".$categoria_id."'
			)";
	return executarOracle($sql);
}

function bgproduct2_deletar($product_id){
	$sql = "DELETE FROM BGPRODUCT2 WHERE PRODUCT_ID = ".$product_id;
	return executarOracle($sql);
}

function bgproduct2_buscaProduct_id($product_id){
	$sql = "SELECT B2.NUMORIGINAL,
			 B2.PRODUCT_ID,
			 B2.NAME,
			 B2.DTCADASTRO,
			 B2.DTATUALIZACAO,
			 B2.CATEGORIA_ID,
			 C2.NAME AS CATEGORIA
	FROM BGPRODUCT2 B2, BGCATEGORIA C2
 WHERE B2.CATEGORIA_ID = C2.ID(+)
	 AND B2.PRODUCT_ID = ".$product_id;
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	return $ret;
}

function bgproduct2_buscaNumoriginal($numoriginal){
	$sql = "SELECT B2.NUMORIGINAL,
			 B2.PRODUCT_ID,
			 B2.NAME,
			 B2.DTCADASTRO,
			 B2.DTATUALIZACAO,
			 B2.CATEGORIA_ID,
			 C2.NAME AS CATEGORIA
	FROM BGPRODUCT2 B2, BGCATEGORIA C2
 WHERE B2.CATEGORIA_ID = C2.ID(+)
	 AND B2.NUMORIGINAL LIKE '".$numoriginal."%'";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	return $ret;
}

function bgproduct2_buscaName($name){
	$sql = "SELECT B2.NUMORIGINAL,
			 B2.PRODUCT_ID,
			 B2.NAME,
			 B2.DTCADASTRO,
			 B2.DTATUALIZACAO,
			 B2.CATEGORIA_ID,
			 C2.NAME AS CATEGORIA
	FROM BGPRODUCT2 B2, BGCATEGORIA C2
 WHERE B2.CATEGORIA_ID = C2.ID(+)
	 AND B2.NAME LIKE '%".$name."%'";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	return $ret;
}

function bgproduct2_buscaCategoria_id($categoria_id){
	$sql = "SELECT B2.NUMORIGINAL,
			 B2.PRODUCT_ID,
			 B2.NAME,
			 B2.DTCADASTRO,
			 B2.DTATUALIZACAO,
			 B2.CATEGORIA_ID,
			 C2.NAME AS CATEGORIA
	FROM BGPRODUCT2 B2, BGCATEGORIA C2
 WHERE B2.CATEGORIA_ID = C2.ID(+)
	 AND B2.CATEGORIA_ID = ".$categoria_id;
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	if (empty($ret)) {
		excluirBgcategoria($categoria_id);
	}
	return $ret;
}


function buscaCategorias(){
	$sql = "SELECT MIN(ID) AS ID, NAME FROM BGCATEGORIA GROUP BY NAME ORDER BY NAME";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	return $ret;
}


function excluirBgcategoria($categoria_id){
	$sql = "DELETE FROM BGCATEGORIA WHERE ID = ".$categoria_id;
	$ret = executarOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	return $ret;
}


function buscarBGATRIBUTO(){
	$sql = "SELECT * FROM BGATRIBUTO";
	$ret = selectOracle($sql);
	varDump2($sql);
	varDump2($ret);
	return $ret;
}


function produtoPesquisaBGPRODUCT($dados){
	$valido = false;
	$sql = "SELECT PROD_ID, PROD_EXTERNAL_ID, PROD_DTATUALIZACAO, MAX(PROD_NAME) AS PROD_NAME, COUNT(VAR_EXTERNAL_ID) AS VARIANTES
			FROM BGPRODUCT
			WHERE 1=1";
	
	if (!empty($dados['CODPECA'])) {
		$sql .= PHP_EOL . " AND PROD_NAME LIKE '%" . trim(mb_strtoupper($dados['CODPECA'], 'UTF-8')) . "%'";
		$valido = true;
	}
	if (!empty($dados['PROD_ID'])) {
		$sql .= PHP_EOL . " AND PROD_ID = " . $dados['PROD_ID'];
		$valido = true;
	}
	$sql .= PHP_EOL . "GROUP BY PROD_ID, PROD_EXTERNAL_ID, PROD_DTATUALIZACAO";

	if ($valido) {
		$ret = selectOracle($sql);	
		// varDump2($sql);
		// varDump2($ret);
		return $ret;
	} else {
		insereModal('warning', "Nenhum filtro selecionado");
		return false;
	}
}

function buscaDadosProduct_id($product_id){
	$sql = "SELECT * FROM BGPRODUCT WHERE product_id = ".$product_id;
	return reset(selectOracle($sql));
}

function buscaQtVariantes(){
	$sql = "SELECT DISTINCT VARIANTES FROM BGPRODUCT order by VARIANTES asc";
	return selectOracle($sql);
}