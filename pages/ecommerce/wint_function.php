<?php 

function tray_buscaProdutosElegiveis($inicial = null){
	$sql = "SELECT   TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20)))) AS numoriginal
		 FROM pcprodut p
		WHERE p.dtexclusao IS NULL
			AND NVL(P.REVENDA, 'N') = 'S'
			AND p.numoriginal NOT LIKE '%VMP%'
			AND p.numoriginal NOT LIKE '%0X0001%'
			AND p.numoriginal NOT LIKE '%LOTE%'
			AND p.codepto NOT IN (65)                          /*IMOBILIZADOS*/
			AND p.codmarca NOT IN (32905, 36765, 84) /*VMP  PATRIMONIO  CONSUMO*/
			AND LENGTH(TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20))))) > 2";
	if (!is_null($inicial)) {
		$sql .= PHP_EOL." AND TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20)))) LIKE '".strtoupper(TRIM($inicial))."%'";	
	}
	$sql .= PHP_EOL." AND TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20)))) NOT IN (SELECT   tp.numoriginal FROM   tray_product tp)
		GROUP BY TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20))))
		ORDER BY 1 ASC";
	// varDump2($sql);
	return selectOracle($sql);
}

function bagy_buscaProdutosElegiveis($inicial = null){
	$sql = "SELECT   TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20)))) AS numoriginal
		 FROM pcprodut p
		WHERE p.dtexclusao IS NULL
			AND NVL(P.REVENDA, 'N') = 'S'
			AND p.numoriginal NOT LIKE '%VMP%'
			AND p.numoriginal NOT LIKE '%0X0001%'
			AND p.numoriginal NOT LIKE '%LOTE%'
			AND p.codepto NOT IN (65)                          /*IMOBILIZADOS*/
			AND p.codmarca NOT IN (32905, 36765, 84) /*VMP  PATRIMONIO  CONSUMO*/
			AND LENGTH(TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20))))) > 2";
	if (!is_null($inicial)) {
		$sql .= PHP_EOL." AND TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20)))) LIKE '".strtoupper(TRIM($inicial))."%'";	
	}
	$sql .= PHP_EOL." AND TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20)))) NOT IN (SELECT bg.external_id FROM bgproduct2 bg)
		GROUP BY TRIM(UPPER(CAST ( REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20))))
		ORDER BY 1 ASC";
	// varDump2($sql);
	return selectOracle($sql);
}

function buscaProdutosSemCadastroTray(){
	$sql = "SELECT a.numoriginal
					FROM vmp_view_produtos_elegiveis a
					LEFT JOIN tray_product b ON a.numoriginal = b.reference
					WHERE b.reference IS NULL";

	$listaNumoriginal = [];
	if ($ret = selectOracle($sql)) {
    foreach ($ret as $value) {
      $num = strval($value['NUMORIGINAL']);
      if (!in_array($num, $listaNumoriginal)) {
        $listaNumoriginal[] = $num;
      }
    }
	}
	return $listaNumoriginal;
}

function buscaProdutosDesatualizados(int $maxRegistros = 0) {
    // 1. Definição da Query Base
    // Ajustei a subquery para que o ROWNUM filtre o resultado final da união/join
    $sql = "SELECT numoriginal FROM (
                SELECT DISTINCT a.numoriginal
                FROM vmp_view_produtos_elegiveis a
                LEFT JOIN tray_product b ON a.numoriginal = b.reference
                WHERE b.reference IS NULL 
                   OR a.dtultalter > b.modified
                ORDER BY a.numoriginal
            )";

    // 2. Filtro de registros (Padrão Oracle para versões anteriores ao 12c)
    if ($maxRegistros > 0) {
        // CORREÇÃO: Variável era $SQL (maiúsculo), alterado para $sql
        $sql .= PHP_EOL . " WHERE ROWNUM <= {$maxRegistros}";
    }

    $listaNUMORIGINAL = [];
    
    // 3. Execução via OCI8 (utilizando sua função selectOracle)
    $ret = selectOracle($sql);

    if ($ret && is_array($ret)) {
        foreach ($ret as $value) {
            // Higienização dos dados vindos do Oracle
            $num = sanitizeOracleString($value['NUMORIGINAL']);
            
            if (!empty($num)) {
                $listaNUMORIGINAL[] = $num;
            }
        }
        
        // Removido o sort() interno se a query já trouxer ordenado (ganho de performance)
        // Mas mantido caso a lógica de negócio exija re-ordenação PHP
        sort($listaNUMORIGINAL);
    }

    return $listaNUMORIGINAL;
}


function bagy_buscaProdutosMovimentadosInicial($dias, $inicial) {
	$sql = "WITH BASE AS 
			(SELECT    TRIM(UPPER(CAST (  REPLACE (p.numoriginal, CHR (160), '') AS VARCHAR2 (20))))  AS numoriginal,
								 GREATEST(
											COALESCE(p.dtcadastro, p.dtultalter, m.dtmov, e.dtultent, t.dtultaltpvenda, t.dtultaltptabela),
											COALESCE(p.dtultalter, m.dtmov, e.dtultent, t.dtultaltpvenda, t.dtultaltptabela, p.dtcadastro),
											COALESCE(m.dtmov, e.dtultent, t.dtultaltpvenda, t.dtultaltptabela, p.dtcadastro, p.dtultalter),
											COALESCE(e.dtultent, t.dtultaltpvenda, t.dtultaltptabela, p.dtcadastro, p.dtultalter, m.dtmov),
											COALESCE(t.dtultaltpvenda, t.dtultaltptabela, p.dtcadastro, p.dtultalter, m.dtmov, e.dtultent),
											COALESCE(t.dtultaltptabela, p.dtcadastro, p.dtultalter, m.dtmov, e.dtultent, t.dtultaltpvenda)
									) as dtultalter
					FROM   pcprodut p,
								 pcmov m,
								 pcest e,
								 pctabpr t
				 WHERE       p.codprod = m.codprod
								 AND p.codprod = e.codprod
								 AND p.codprod = t.codprod
								 AND p.dtexclusao IS NULL
								 AND m.dtcancel IS NULL
								 AND e.codfilial = 1
								 AND t.numregiao = 1
								 AND NVL(P.REVENDA, 'N') = 'S'
								 AND p.numoriginal NOT LIKE '%VMP%'
								 AND p.numoriginal NOT LIKE '%0X0001%'
								 AND p.numoriginal NOT LIKE '%LOTE%'
								 AND p.codepto NOT IN (65)                          /*IMOBILIZADOS*/
								 AND p.codmarca NOT IN (32905, 36765, 84) /*VMP  PATRIMONIO  CONSUMO*/
					GROUP BY   p.numoriginal, p.dtcadastro, p.dtultalter, m.dtmov, e.dtultent, t.dtultaltpvenda, t.dtultaltptabela)
			SELECT   base.numoriginal
				FROM   base
			 WHERE   LENGTH(base.numoriginal) > 2
				 AND   base.numoriginal like '{$inicial}%'
				 AND   base.dtultalter >= trunc(SYSDATE -{$dias})
				 /*AND   base.numoriginal NOT IN (SELECT   numoriginal
																					FROM   bgproduct2
																				 WHERE   data >= TRUNC (SYSDATE - 3))*/
		GROUP BY   base.numoriginal
		ORDER BY   base.numoriginal ASC";
	// varDump2($sql); die();
	if($ret = selectOracle($sql)){
		$data = [];
		foreach ($ret as $key => $value) {
			$data[] = $value['NUMORIGINAL'];
		}
		return $data;
	} else {
		return false;
	}
}

function buscaVides($NUMORIGINAL){
	$sql = "WITH 
				BASE AS (SELECT '{$NUMORIGINAL}' as codpeca FROM DUAL),
				V1 AS (
						SELECT codpeca, vide
							FROM orcvide
						 WHERE vide is not null
							 AND vide not like 'W%'),
				V2 AS (
						SELECT vide as codpeca, codpeca as vide
							FROM orcvide
						 WHERE codpeca is not null
							 AND codpeca not like 'W%'),
				V3 AS (
						SELECT ov1.codpeca, ov1.vide
							FROM orcvide ov1
						 WHERE ov1.vide is not null
							 AND ov1.vide not like 'W%'
							 AND ov1.vide in (select ov2.vide from orcvide ov2 where ov2.codpeca = ov1.codpeca)),
				V4 AS (
						SELECT ov1.codpeca, ov1.vide
							FROM orcvide ov1
						 WHERE ov1.vide is not null
							 AND ov1.vide not like 'W%'
							 AND ov1.vide in (select ov2.codpeca from orcvide ov2 where ov2.vide = ov1.codpeca)),
				V5 AS (
						SELECT ov1.vide as codpeca, ov1.codpeca as vide
							FROM orcvide ov1
						 WHERE ov1.codpeca is not null
							 AND ov1.codpeca not like 'W%'
							 AND ov1.codpeca in (select ov2.vide from orcvide ov2 where ov2.codpeca = ov1.codpeca)),
				V6 AS (
						SELECT ov1.vide as codpeca, ov1.codpeca as vide
							FROM orcvide ov1
						 WHERE ov1.codpeca is not null
							 AND ov1.codpeca not like 'W%'
							 AND ov1.codpeca in (select ov2.codpeca from orcvide ov2 where ov2.vide = ov1.codpeca))

				SELECT base.codpeca as vide from base
				union
				SELECT V1.vide from base, v1 where base.codpeca = v1.codpeca
				union
				SELECT V2.vide from base, v2 where base.codpeca = v2.codpeca
				union
				SELECT V3.vide from base, v3 where base.codpeca = v3.codpeca
				union
				SELECT V4.vide from base, v4 where base.codpeca = v4.codpeca
				union
				SELECT V4.vide from base, v4 where base.codpeca = v4.codpeca
				union
				SELECT V5.vide from base, v5 where base.codpeca = v5.codpeca
				union
				SELECT V6.vide from base, v6 where base.codpeca = v6.codpeca";
	// varDump2($sql); die();
	$ret = selectOracle($sql);
	if ($ret && !empty($ret)) {
		$ret2=[];
		foreach ($ret as $key => $value) {
			$vide = strval($value['VIDE']);
			if (!in_array($vide, $ret2)) {
				array_push($ret2, $vide);
			}
		}
		return $ret2;
	} else {
		return false;
	}
}


function buscaDadosProdutoWinthor($NUMORIGINAL) {

	$NUMORIGINAL = mb_strtoupper(trim($NUMORIGINAL), 'UTF-8');

	if ($NUMORIGINAL <> "") {
		$sql = "WITH
			BASE AS (
			    SELECT '{$NUMORIGINAL}' AS codpeca FROM DUAL
			),
			V1 AS (
			    SELECT codpeca, vide
			      FROM orcvide
			     WHERE vide IS NOT NULL
			       AND vide NOT LIKE 'W%'
			),
			V2 AS (
			    SELECT vide AS codpeca, codpeca AS vide
			      FROM orcvide
			     WHERE codpeca IS NOT NULL
			       AND codpeca NOT LIKE 'W%'
			),
			V3 AS (
			    SELECT ov1.codpeca, ov1.vide
			      FROM orcvide ov1
			     WHERE ov1.vide IS NOT NULL
			       AND ov1.vide NOT LIKE 'W%'
			       AND EXISTS (
			           SELECT 1
			             FROM orcvide ov2
			            WHERE ov2.codpeca = ov1.codpeca
			              AND ov2.vide    = ov1.vide
			       )
			),
			V4 AS (
			    SELECT ov1.codpeca, ov1.vide
			      FROM orcvide ov1
			     WHERE ov1.vide IS NOT NULL
			       AND ov1.vide NOT LIKE 'W%'
			       AND EXISTS (
			           SELECT 1
			             FROM orcvide ov2
			            WHERE ov2.vide = ov1.codpeca
			       )
			),
			V5 AS (
			    SELECT ov1.vide AS codpeca, ov1.codpeca AS vide
			      FROM orcvide ov1
			     WHERE ov1.codpeca IS NOT NULL
			       AND ov1.codpeca NOT LIKE 'W%'
			       AND EXISTS (
			           SELECT 1
			             FROM orcvide ov2
			            WHERE ov2.codpeca = ov1.codpeca
			              AND ov2.vide    = ov1.vide
			       )
			),
			V6 AS (
			    SELECT ov1.vide AS codpeca, ov1.codpeca AS vide
			      FROM orcvide ov1
			     WHERE ov1.codpeca IS NOT NULL
			       AND ov1.codpeca NOT LIKE 'W%'
			       AND EXISTS (
			           SELECT 1
			             FROM orcvide ov2
			            WHERE ov2.vide = ov1.codpeca
			       )
			),
			LISTA_VIDES AS (
			    SELECT codpeca AS vide FROM BASE
			    UNION
			    SELECT v1.vide FROM BASE b JOIN v1 ON b.codpeca = v1.codpeca
			    UNION
			    SELECT v2.vide FROM BASE b JOIN v2 ON b.codpeca = v2.codpeca
			    UNION
			    SELECT v3.vide FROM BASE b JOIN v3 ON b.codpeca = v3.codpeca
			    UNION
			    SELECT v4.vide FROM BASE b JOIN v4 ON b.codpeca = v4.codpeca
			    UNION
			    SELECT v5.vide FROM BASE b JOIN v5 ON b.codpeca = v5.codpeca
			    UNION
			    SELECT v6.vide FROM BASE b JOIN v6 ON b.codpeca = v6.codpeca
			),
			PRODUTO_BASE AS (
			    SELECT
			        P.CODPROD,
			        P.CODMARCA,
			        P.CODEPTO,
			        P.DESCRICAO,
			        P.INFORMACOESTECNICAS,
			        P.NUMORIGINAL,
			        P.PESOBRUTO,
			        P.ALTURAM3,
			        P.LARGURAM3,
			        P.COMPRIMENTOM3
			    FROM PCPRODUT P
			    WHERE NVL(P.REVENDA, 'N') = 'S'
			      AND P.DTEXCLUSAO IS NULL
			      AND P.NUMORIGINAL NOT LIKE '%VMP%'
			      AND P.NUMORIGINAL NOT LIKE '%0X0001%'
			      AND P.NUMORIGINAL NOT LIKE '%LOTE%'
			      AND P.CODEPTO <> 65
			      AND P.CODMARCA NOT IN (32905, 36765, 84)
			      AND UPPER(TRIM(REPLACE(REPLACE(P.NUMORIGINAL, CHR(10), ''), CHR(160), '')))
			          IN (SELECT vide FROM LISTA_VIDES)
			),
			ESTOQUE AS (
			    SELECT
			        CODPROD,
			        GREATEST(
			            PKG_ESTOQUE.ESTOQUE_DISPONIVEL(CODPROD, 1, 'VP', TRUNC(SYSDATE)),
			            0
			        ) AS QTSALDO
			    FROM PRODUTO_BASE
			)
			SELECT
			    '{$NUMORIGINAL}'                                    AS NUMORIGINAL,
			    PB.CODPROD                                          AS CODPROD,
			    MAX(UPPER(TRIM(PB.DESCRICAO)))                      AS DESCRICAO,
			    NVL(PB.CODMARCA, 1)                                 AS CODMARCA,
			    UPPER(TRIM(M.MARCA))                                AS MARCA,
			    MAX(UPPER(TRIM(PB.INFORMACOESTECNICAS)))            AS LOCACAO,
			    SUM(E.QTSALDO)                                      AS QTSALDO,
			    MAX(CASE WHEN T.PVENDA < 1 THEN 0 ELSE ROUND(T.PVENDA, 2) + 0.01 END)
			                                                       AS PVENDA,
			    MAX(CASE WHEN D.CODEPTO = 1 THEN NULL ELSE TRIM(D.DESCRICAO) END)
			                                                       AS CATEGORIA,
			    MAX(NVL(NULLIF(PB.PESOBRUTO, 0), 0.1) * 1000)       AS PESOBRUTO,
			    MAX(NVL(NULLIF(PB.ALTURAM3, 0), 0.05) * 100)        AS ALTURAM3,
			    MAX(NVL(NULLIF(PB.LARGURAM3, 0), 0.05) * 100)       AS LARGURAM3,
			    MAX(NVL(NULLIF(PB.COMPRIMENTOM3, 0), 0.05) * 100)   AS COMPRIMENTOM3
			FROM PRODUTO_BASE PB
			JOIN PCMARCA M
			  ON M.CODMARCA = NVL(PB.CODMARCA, 1)
			LEFT JOIN PCTABPR T
			  ON T.CODPROD   = PB.CODPROD
			 AND T.NUMREGIAO = 38
			LEFT JOIN PCDEPTO D
			  ON D.CODEPTO = PB.CODEPTO
			LEFT JOIN ESTOQUE E
			  ON E.CODPROD = PB.CODPROD
      GROUP BY
          PB.CODPROD,
          PB.CODMARCA,
          M.MARCA
      ORDER BY 
          SUM(E.QTSALDO) DESC";

		$produto_tmp = selectOracle($sql);
		// varDump2($sql); 
		// varDump2($produto_tmp);
		// die();

		if($produto_tmp){
			// varDump2($produto_tmp);
			// die();
			$produtos_validos 	= array();
			$produtos_invalidos = array();

			foreach ($produto_tmp as $key => $value) {
			
				//************************************************************************************
				// VALIDAÇÃO DE PRODUTOS DO PÁTIO REMOVIDA EM 09/12/2025 POR SOLICITAÇÃO DO SEU HAROLDO
				//************************************************************************************
				// if (strripos($value["LOCACAO"], 'PT')  || 
				// 		strripos($value["LOCACAO"], 'PAT') || 
				// 		strripos($value["LOCACAO"], 'PAL')) {
				// 	$prod_patio["PESOBRUTO"] 			= moedaPHP(20000);
				// 	$prod_patio["ALTURAM3"] 			= moedaPHP(201);
				// 	$prod_patio["LARGURAM3"] 			= moedaPHP(201);
				// 	$prod_patio["COMPRIMENTOM3"] 	= moedaPHP(201);
				// } else {
				// 	$produto_tmp[0]["PESOBRUTO"] 			= moedaPHP($value['PESOBRUTO']);
				// 	$produto_tmp[0]["ALTURAM3"] 			= moedaPHP($value['ALTURAM3']);
				// 	$produto_tmp[0]["LARGURAM3"] 			= moedaPHP($value['LARGURAM3']);
				// 	$produto_tmp[0]["COMPRIMENTOM3"] 	= moedaPHP($value['COMPRIMENTOM3']);
				// }

				if (moedaPHP($value['QTSALDO']) >= 1 && moedaPHP($value['PVENDA']) >= 1) {
					$value['QTSALDO'] = moedaPHP($value['QTSALDO']);
					$value['PVENDA'] = moedaPHP($value['PVENDA']);
					$produtos_validos[] = $value;
				} else {
					$value['QTSALDO'] = 0;
					$value['PVENDA'] = 0;
					$produtos_invalidos[] = $value;
				}

			}

			$produtos_winthor = (!empty($produtos_validos)) 
													? $produtos_validos
													: $produtos_invalidos;
			// varDump2($produtos_winthor);
			// die();

			// bloco adicionado para remover os produtos com mesma marca, deixando o de maior estoque
			$variantes = array();
			$lista_codmarca = array();

			foreach ($produtos_winthor as $key => $value) {
				if ( !in_array($value['MARCA'], $lista_codmarca)) {
					array_push($lista_codmarca, $value['MARCA']);
					array_push($variantes, $value);
				} else {
					$value['MARCA'] = $value['MARCA'].".";
					if ( !in_array($value['MARCA'], $lista_codmarca)) {
						array_push($lista_codmarca, $value['MARCA']);
						array_push($variantes, $value);
					} else {
						$value['MARCA'] = $value['MARCA']."..";
						if ( !in_array($value['MARCA'], $lista_codmarca)) {
							array_push($lista_codmarca, $value['MARCA']);
							array_push($variantes, $value);
						}
					}
				}
			}

			// varDump2("===============================");
			$CAD_PROD['PRODUTO'] = reset($produtos_winthor);

			/*VALIDA SE O PRODUTO POSSUI PREÇO PROMOCIONAL CADASTRADO NA 561 POR GRUPO DE PRODUTOS*/
			// varDump2($variantes);
			foreach ($variantes as $varWint) {
				if ($DESC_561 = validaDesc561($varWint['CODPROD'])) {
					$varWint['DTINICIO'] = $DESC_561['DTINICIO'];
					$varWint['DTFIM'] 	 = $DESC_561['DTFIM'];
					$varWint['PERCDESC'] = $DESC_561['PERCDESC'];
					$varWint['VLPROMO']  = moedaPHP(round($varWint['PVENDA'] * (1- ($DESC_561['PERCDESC']/100)),2));
				} else {
					$varWint['DTINICIO'] = '';
					$varWint['DTFIM'] 	 = '';
					$varWint['PERCDESC'] = '';
					$varWint['VLPROMO']  = '';
				}
				$CAD_PROD['VARIANTES'][] = $varWint;
			}

			// verifica se o produto já possui cadastro na TRAY
			$CAD_PROD['PRODUTO']['NUMORIGINAL'] = mb_strtoupper(trim($NUMORIGINAL), "UTF-8");
			$CAD_PROD['PRODUTO']['CATEGORIA'] = ucfirst(mb_strtolower(trim($CAD_PROD['PRODUTO']['CATEGORIA']), "UTF-8"));
			$CAD_PROD['PRODUTO']['MARCA'] = mb_strtoupper(trim($CAD_PROD['PRODUTO']['MARCA']), "UTF-8");
			$CAD_PROD['PRODUTO']['PVENDA'] = moedaPHP($CAD_PROD['PRODUTO']['PVENDA']);
			$CAD_PROD['PRODUTO']['QTSALDO'] = moedaPHP($CAD_PROD['PRODUTO']['QTSALDO']);
			$CAD_PROD['PRODUTO']['PESOBRUTO'] = moedaPHP($CAD_PROD['PRODUTO']['PESOBRUTO']);
			$CAD_PROD['PRODUTO']['ALTURAM3'] = moedaPHP($CAD_PROD['PRODUTO']['ALTURAM3']);
			$CAD_PROD['PRODUTO']['LARGURAM3'] = moedaPHP($CAD_PROD['PRODUTO']['LARGURAM3']);
			$CAD_PROD['PRODUTO']['COMPRIMENTOM3'] = moedaPHP($CAD_PROD['PRODUTO']['COMPRIMENTOM3']);

			$listaVides = "";
			if ($vides = buscaVides($CAD_PROD['PRODUTO']['NUMORIGINAL'])){
				foreach ($vides as $valueVide) {
					if (!empty($listaVides)) {
						$listaVides .= " | " . $valueVide;
					} else {
						$listaVides .= $valueVide;
					}
				}
			}
			
			$CAD_PROD['PRODUTO']['NOME'] = $CAD_PROD['PRODUTO']['DESCRICAO']." | ".$listaVides; 
			$CAD_PROD['PRODUTO']['NOME'] = str_replace("'", "", str_replace('"', '', $CAD_PROD['PRODUTO']['NOME']));

			$CAD_PROD['PRODUTO']['DESCRICAO'] = "<p><b>Seja bem-vindo à Loja Oficial da VEMAP PEÇAS PARA TRATORES E MÁQUINAS PESADAS.</b></p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p>Part Number: <b>".$CAD_PROD['PRODUTO']['NUMORIGINAL'] . "</b></p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p>Entrega Imediata! Podendo ser retirado na unidade da empresa.</p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p><b>** Produto de Qualidade **</b></p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p>Nossos produtos são enviados com nota fiscal.</p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p>O comprador se responsabiliza pela compatibilidade do produto anunciado e com a sua aplicação na máquina/equipamento. A VEMAP PEÇAS não se responsabiliza por qualquer tipo de prejuízo oriundo da instalação incorreta do produto anunciado. Antes de comprar, sempre consulte o fabricante da sua máquina/equipamento para confirmar a referência/compatibilidade com o seu mecânico/instalador.</p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p><b>** IMPORTANTE **</b></p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p>Para saber o valor do seu frete, use a calculadora de frete ou faça uma pergunta para a nossa equipe, informando o produto e o seu CEP de entrega.</p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p><b>** TROCAS e DEVOLUÇÕES **</b></p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p>Saiba mais clicando <a href="https://vemap.commercesuite.com.br/trocas-e-devolucoes" target="_blanck">Aqui.</a></p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= "<p><b>Welcome to the Official VEMAP PARTS FOR TRACTORS AND HEAVY MACHINERY Store.</b></p>".PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p>Part Number: '.$CAD_PROD['PRODUTO']['NUMORIGINAL'].'</p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p>Immediate delivery! It can be picked up at the company unit.</p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p><b>** Quality Product **</b></p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p>Our products are sent with an invoice.</p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p>The buyer is responsible for the compatibility of the advertised product and its application on the machine/equipment. VEMAP PEÇAS is not responsible for any type of damage arising from incorrect installation of the advertised product. Before purchasing, always consult the manufacturer of your machine/equipment to confirm the reference/compatibility with your mechanic/installer.</p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p><b>** IMPORTANT **</b></p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p>To find out the cost of your shipping, use a shipping calculator or ask our team a question, informing the product and your delivery zip code.</p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p><b>**EXCHANGES and RETURNS**</b></p>'.PHP_EOL;
			$CAD_PROD['PRODUTO']['DESCRICAO'] .= '<p>Find out more by clicking <a href="https://vemap.commercesuite.com.br/trocas-e-devolucoes" target="_blanck">here</a>.</p>'.PHP_EOL;

			$CAD_PROD['PRODUTO']['meta_description'] = substr("A peça ".$CAD_PROD['PRODUTO']['NOME']." tem qualidade garantida pela LOJA VEMAP - Solução de Peças para Tratores e Máquinas Pesadas, com vinte anos de mercado. Entrega para o Sudeste em até dois dias úteis. Aproveite também nossas ofertas.", 0, 254);
			$CAD_PROD['PRODUTO']['meta_keywords'] = substr("TRATOR CATERPILLAR,MARSSEY FEGUSON,TRATOR JOHN DEERE,TRATOR CASE,PEÇA PARA TRATOR,PEÇAS PARA TRATOR, PEÇA PARA TRATORES,VEMAP,PEÇA,TRATOR,COMPONENTE,REPOSIÇÃO,MOTOR,".str_replace(" ", ",", $CAD_PROD['PRODUTO']['NOME']), 0, 254);

			// varDump2($CAD_PROD['PRODUTO']); die();

			return $CAD_PROD;

		} else {
			return false;
		}
	} else {
		return false;
	}

}

function validaDesc561($CODPROD){
	$sql = "SELECT   ci.coditem AS codprod, 
					 TO_CHAR(d2.dtinicio, 'YYYY-MM-DD') dtinicio, 
					 TO_CHAR(d2.dtfim, 'YYYY-MM-DD') dtfim, 
					 NVL(d2.percdesc, 0) AS percdesc
				FROM pcdesconto d2, pcdescontoitem di, pcgruposcampanhai ci
			 WHERE d2.coddesconto = di.coddesconto
				 AND di.valor_num = ci.codgrupo
				 AND TRUNC (SYSDATE) BETWEEN TRUNC (d2.dtinicio) AND TRUNC (d2.dtfim)
				 AND ci.dtexclusao IS NULL
				 AND di.tipo = 'GP'
				 AND NVL(d2.percdesc, 0) > 0
				 AND d2.numregiao = 1
				 AND ci.coditem = {$CODPROD}";
	$ret = selectOracle($sql);
	if ($ret && !empty($ret)){
		return reset($ret);
	} else {
		return false;
	}
}

function buscaPromocao561(){
	$sql = "WITH 
				produto
					AS (SELECT   TRIM (p.codprod) AS codprod,
											 TRIM(UPPER(CAST (
																			REPLACE (
																					REPLACE (p.numoriginal, CHR (10), ''),
																					CHR (160),
																					'') AS VARCHAR2 (20))))
													 AS numoriginal
								FROM   pcprodut p
							 WHERE       p.dtexclusao IS NULL
											 AND NVL(P.REVENDA, 'N') = 'S'
											 AND p.numoriginal NOT LIKE '%VMP%'
											 AND p.numoriginal NOT LIKE '%0X0001%'
											 AND p.numoriginal NOT LIKE '%LOTE%'
											 AND p.codepto NOT IN (65                 /*IMOBILIZADOS*/
																							)
											 AND p.codmarca NOT IN (32905                      /*VMP*/
																							, 36765        /*PATRIMONIO*/
																							, 84       /*CONSUMO*/
																							)),
				desconto
					AS (SELECT   ci.coditem AS codprod,
											 d2.dtinicio,
											 d2.dtfim,
											 di.tipo,
											 d2.numregiao,
											 d2.percdesc	                     
								FROM   pcdesconto d2, pcdescontoitem di, pcgruposcampanhai ci
							 WHERE       d2.coddesconto = di.coddesconto
											 AND di.valor_num = ci.codgrupo
											 AND ci.dtexclusao IS NULL
											 AND di.tipo = 'GP'
											 AND d2.numregiao = 1
											 AND NVL (d2.percdesc, 0) > 0
											 AND TRUNC (SYSDATE) BETWEEN TRUNC (d2.dtinicio)
																							 AND TRUNC (d2.dtfim))
		SELECT   produto.numoriginal
			FROM   produto, desconto
		 WHERE   produto.codprod = desconto.codprod
			AND    produto.numoriginal NOT IN 
															(SELECT reference 
																  FROM tray_product 
																 WHERE modified >= trunc(sysdate))
	GROUP BY   produto.numoriginal
	ORDER BY   produto.numoriginal";
	$ret = selectOracle($sql);
	if ($ret && !empty($ret)){
		$ret2 = [];
		foreach ($ret as $key => $value) {
			$ret2[] = $value['NUMORIGINAL'];
		}
		return $ret2;
	} else {
		return false;
	}
}


//******************************************************************************************
//******************************************************************************************

function TRAY_PRODUCT_geraLog($dados){
	prompt(json_encode($dados));
	if ($dadosTray = TRAY_PRODUCT_search($dados)) {
		$dados['IDTRAY'] = $dadosTray['IDTRAY'];
		if(TRAY_PRODUCT_update($dados))
			prompt("SUCESSO ao executar TRAY_PRODUCT_update");
		else
			prompt("ERRO ao executar TRAY_PRODUCT_update");
	} else {
		if(TRAY_PRODUCT_insert($dados))
			prompt("SUCESSO ao executar TRAY_PRODUCT_insert");
		else
			prompt("ERRO ao executar TRAY_PRODUCT_insert");
	}
}

function TRAY_PRODUCT_search($dados){
	// varDump2("TRAY_PRODUCT_search");
	// varDump2($dados);	
	$sql = "SELECT * FROM TRAY_PRODUCT WHERE 1=1 ";
	if ($dados['NUMORIGINAL'] && !empty($dados['NUMORIGINAL'])) {
		$sql .= " AND NUMORIGINAL = '{$dados['NUMORIGINAL']}'";
		// varDump2($sql);
		if($ret = selectOracle($sql)){
			return reset($ret);
		} else {
			return false;
		}
	} else {
		if ($dados['IDTRAY'] && !empty($dados['IDTRAY'])) {
			$sql .= " AND IDTRAY = '{$dados['IDTRAY']}'";
			// varDump2($sql);
			if($ret = selectOracle($sql)){
				return reset($ret);
			} else {
				return false;
			}
		} else {
			return false;
		}	
	}
}

function TRAY_PRODUCT_insert($dados){
	$dados['reference'] = substr(mb_strtoupper($dados['reference'], 'UTF-8'), 0, 120);
	$dados['name'] = substr(mb_strtoupper($dados['name'], 'UTF-8'), 0, 200);

	$sql = "DELETE FROM TRAY_PRODUCT WHERE reference='{$dados['reference']}'";
	executarOracle($sql);
	
	$sql = "INSERT INTO TRAY_PRODUCT (modified, product_id, reference, name) VALUES (
		sysdate,
		{$dados['product_id']}, 
		'{$dados['reference']}', 
		'{$dados['name']}'
	)";
	executarOracle($sql);
}
 
//####################################################################

function bgproduct2_listar($inicial = null){
	$sql = "SELECT * FROM bgproduct2 tp where tp.external_id like '".strtoupper($inicial)."%'";
	// varDump2($sql);
	if ($ret = selectOracle($sql) ){
		return $ret;
	} else {
		return "ERRO ao executar bgproduct2_listar ".$sql;
	}
}
 
function bgproduct2_inserir($data){
	$sql = "INSERT INTO bgproduct2 (product_id, name, updated_at) 
					VALUES ({$data['ID']}, '{$data['NAME']}', SYSDATE)";
	// varDump2($sql);
	if (executarOracle($sql) ){
		return "SUCESSO ao executar bgproduct2_inserir.";
	} else {
		return "ERRO ao executar bgproduct2_inserir ".$sql;
	}
}
 
function bgproduct2_excluir($product_id){
	$sql = "DELETE FROM bgproduct2 
					WHERE product_id = {$product_id}";
	// varDump2($sql);
	if (executarOracle($sql) ){
		return "SUCESSO ao executar bgproduct2_excluir.";
	} else {
		return "ERRO ao executar bgproduct2_excluir ".$sql;
	}
}
 
function bgproduct2_atualizar($product_id, $name){
	$sql = "UPDATE bgproduct2 SET name = '{$name}', updated_at = SYSDATE 
					WHERE product_id = {$product_id}";
	// varDump2($sql);
	if (executarOracle($sql) ){
		return "SUCESSO ao executar bgproduct2_atualizar.";
	} else {
		return "ERRO ao executar bgproduct2_atualizar ".$sql;
	}
}

//####################################################################

function bgcategoria_listar(){
	$sql = "SELECT * FROM bgcategoria";
	return selectOracle($sql);
}


function bgcategoria_buscarNome($name){
	$sql = "SELECT * FROM bgcategoria WHERE NAME = '{$name}'";
	if ($ret =selectOracle($sql) ){
		return $ret[0]['ID'];
	} else {
		return "[]";
	}
}

function bgcategoria_inserir($categoria){
	$sql = "INSERT INTO bgcategoria (id, name) VALUES (".$categoria["id"].", '".$categoria["name"]."')";
	return executarOracle($sql);
}

function bgcategoria_truncate(){
	$sql = "TRUNCATE TABLE bgcategoria";
	return executarOracle($sql);
}

function bgcategoria_sysnc(){
	bgcategoria_truncate();
	$categorias = bagy_categoria_listar();
	foreach ($categorias as $key => $categoria) {
		bgcategoria_inserir($categoria);
	}
}

//####################################################################

// function consultarNumoriginal($NUMORIGINAL){
// 	if ($NUMORIGINAL == "") {
// 		return false;
// 	} else {
// 		$NUMORIGINAL = mb_strtoupper(trim($NUMORIGINAL), 'UTF-8');
// 		$sql = "SELECT *
// 						FROM VIEW_PORTAL_CONSULTAPRODUTO
// 					 WHERE PTABELA >= 20
// 						 AND MARCA NOT LIKE 'VMP'
// 						 AND TRIM(NUMORIGINAL) LIKE '".$NUMORIGINAL."'";
// 		$ret = selectOracle($sql);
// 		if($ret && count($ret)>0){
// 			return $ret;
// 		} else {
// 			return false;
// 		}
// 	}
// }

// function buscaListaNUMORIGINAL($CODPECA){
// 	if ($CODPECA == "") {
// 		return false;
// 	} else {
// 		$CODPECA = mb_strtoupper(trim($CODPECA), 'UTF-8');
// 		$sql = "SELECT *
// 						FROM VIEW_PORTAL_CONSULTAPRODUTO
// 					 WHERE PTABELA >= 20
// 						 AND MARCA NOT LIKE 'VMP'
// 						 AND TRIM(NUMORIGINAL) LIKE '".$CODPECA."'";
// 		if($ret = selectOracle($sql)){
// 			return $ret;
// 		} else {
// 			return false;
// 		}
// 	}
// }

// function buscaProdutosMovimentadosInicial($dias, $inicial) {
// 	$sql = "SELECT TRIM(UPPER(CAST(REPLACE(P.NUMORIGINAL, CHR(160), '') AS VARCHAR2(20)))) AS NUMORIGINAL
// 		  FROM PCPRODUT P, PCTABPR T, PCEST E
// 		 WHERE P.CODPROD = T.CODPROD(+)
// 		   AND P.CODPROD = E.CODPROD
// 		   AND P.DTEXCLUSAO IS NULL
// 		   AND T.NUMREGIAO = 1
// 		   AND E.CODFILIAL = 1
// 		   AND NVL(P.REVENDA, 'N') = 'S'
// 		   AND P.NUMORIGINAL NOT LIKE '%VMP%'
// 		   AND P.NUMORIGINAL NOT LIKE '%0X0001%'
// 		   AND P.NUMORIGINAL NOT LIKE '%LOTE%'
// 		   AND P.CODEPTO NOT IN (65 /*IMOBILIZADOS*/)
// 		   AND P.CODMARCA NOT IN (32905 /*VMP*/, 36765 /*PATRIMONIO*/, 84 /*CONSUMO*/)
// 		   AND LENGTH(TRIM(UPPER(CAST(REPLACE(P.NUMORIGINAL, CHR(160), '') AS VARCHAR2(20))))) > 2
// 		   AND TRIM(UPPER(CAST(REPLACE(P.NUMORIGINAL, CHR(160), '') AS VARCHAR2(20)))) NOT IN (SELECT NUMORIGINAL FROM BGPRODUCT2 WHERE DATA >= TRUNC(SYSDATE-2))
// 		   AND P.NUMORIGINAL LIKE '{$inicial}%'
// 		  GROUP BY TRIM(UPPER(CAST(REPLACE(P.NUMORIGINAL, CHR(160), '') AS VARCHAR2(20))))
// 		  order by 1 asc";
// 	// varDump2($sql);
// 	$ret = selectOracle($sql);
// 	if ($ret && !empty($ret)) {
// 		return $ret;
// 	} else {
// 		return false;
// 	}
// }



// function buscaProdutosWinthorListaNumoriginal($LISTANUMORIGINAL){
// 	$sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
// 			       TRIM(P.NUMORIGINAL) AS NUMORIGINAL,
// 			       TRIM(P.DESCRICAO) AS DESCRICAO,
// 			       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
// 			           P.MARCA) AS MARCA,
// 			       GREATEST(NVL(P.PESOBRUTO, 0), 1) AS PESOBRUTO,
// 			       GREATEST(NVL(P.ALTURAM3, 0), 1) AS ALTURAM3,
// 			       GREATEST(NVL(P.LARGURAM3, 0), 1) AS LARGURAM3,
// 			       GREATEST(NVL(P.COMPRIMENTOM3, 0), 1) AS COMPRIMENTOM3,
// 			       (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD,
// 			                                                       1,
// 			                                                       'VP',
// 			                                                       TRUNC(SYSDATE)),
// 			                        0)
// 			          FROM DUAL) AS SALDO,
// 			       (SELECT CASE
// 			                 WHEN T.PVENDA <= 0.01 THEN
// 			                  0
// 			                 ELSE
// 			                  (ROUND(T.PVENDA, 2) + 0.01)
// 			               END
// 			          FROM PCTABPR T
// 			         WHERE T.NUMREGIAO = 1
// 			           AND T.CODPROD = P.CODPROD) AS PVENDA
// 			  FROM PCPRODUT P
// 			 WHERE P.DTEXCLUSAO IS NULL
// 			   AND P.NUMORIGINAL IN ({$LISTANUMORIGINAL})
// 			 ORDER BY 9 DESC";
// 	// varDump2($sql);
// 	return selectOracle($sql);
// }

// function atualizarDimensoesProdutoWinthor($Product){
// 	// $debug = true;

// 	if($debug) varDump2("atualizarDimensoesProdutoWinthor");
// 	if($debug) varDump2($Product);

// 	$vides = buscaVides($Product['reference']);
// 	if (!empty($vides)) {
// 		$vides = substr($vides, 3);
// 		$vides = str_replace(" | ", ", ", $vides);
// 	}
// 	$vides = "'".str_replace(", ", "', '", $vides) ."'";
// 	if($debug) varDump2($vides);

// 	$produtos = buscaProdutosWinthorListaNumoriginal($vides);
// 	if($debug) varDump2($produtos);

// 	foreach ($produtos as $key => $value) {
// 		$sql = "UPDATE PCPRODUT SET 
// 					   ALTURAM3 = ".moedaPHP($Product['height']).",
// 					   LARGURAM3 = ".moedaPHP($Product['width']).",
// 					   COMPRIMENTOM3 = ".moedaPHP($Product['length']).",
// 					   PESOBRUTO = ".moedaPHP($Product['weight'])."
// 				WHERE CODPROD = ".$value["CODPROD"];
// 		executarOracle($sql);
// 	}

// 	if($debug) die();
// }


// function produtoPossuiCadBagy($NUMORIGINAL){
// 	if (!empty($NUMORIGINAL)) {
// 		$sql = "SELECT MAX(PRODUCT_ID) AS PRODUCT_ID 
// 						FROM BGPRODUCT WHERE EXTERNAL_ID = '".$NUMORIGINAL."'";
// 		$ret = selectOracle($sql);
// 		if ($ret = reset($ret)) {
// 			// varDump2($ret); die();
// 			return $ret['PRODUCT_ID'];
// 		} else {
// 			return false;
// 		}
// 	} else {
// 		return false;
// 	}
// }

// function bgproduct2_inserir($product_id, $external_id, $name, $dtcadastro, $dtatualizacao, $categoria_id){
// 	$sql = "INSERT INTO BGPRODUCT2 (
// 			 PRODUCT_ID,
// 			 NUMORIGINAL,
// 			 NAME,
// 			 DTCADASTRO,
// 			 DTATUALIZACAO,
// 			 CATEGORIA_ID
// 			) VALUES (
// 			 '".$product_id."', 
// 			 '".$external_id."', 
// 			 '".$name."', 
// 			 '".$dtcadastro."', 
// 			 '".$dtatualizacao."', 
// 			 '".$categoria_id."'
// 			)";
// 	return executarOracle($sql);
// }

// function bgproduct2_deletar($product_id){
// 	$sql = "DELETE FROM BGPRODUCT2 WHERE PRODUCT_ID = ".$product_id;
// 	return executarOracle($sql);
// }

// function bgproduct2_buscaProduct_id($product_id){
// 	$sql = "SELECT B2.NUMORIGINAL,
// 			 B2.PRODUCT_ID,
// 			 B2.NAME,
// 			 B2.DTCADASTRO,
// 			 B2.DTATUALIZACAO,
// 			 B2.CATEGORIA_ID,
// 			 C2.NAME AS CATEGORIA
// 	FROM BGPRODUCT2 B2, BGCATEGORIA C2
//  WHERE B2.CATEGORIA_ID = C2.ID(+)
// 	 AND B2.PRODUCT_ID = ".$product_id;
// 	$ret = selectOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);
// 	return $ret;
// }

// function bgproduct2_buscaNumoriginal($numoriginal){
// 	$sql = "SELECT B2.NUMORIGINAL,
// 			 B2.PRODUCT_ID,
// 			 B2.NAME,
// 			 B2.DTCADASTRO,
// 			 B2.DTATUALIZACAO,
// 			 B2.CATEGORIA_ID,
// 			 C2.NAME AS CATEGORIA
// 	FROM BGPRODUCT2 B2, BGCATEGORIA C2
//  WHERE B2.CATEGORIA_ID = C2.ID(+)
// 	 AND B2.NUMORIGINAL LIKE '".$numoriginal."%'";
// 	$ret = selectOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);
// 	return $ret;
// }

// function bgproduct2_buscaName($name){
// 	$sql = "SELECT B2.NUMORIGINAL,
// 			 B2.PRODUCT_ID,
// 			 B2.NAME,
// 			 B2.DTCADASTRO,
// 			 B2.DTATUALIZACAO,
// 			 B2.CATEGORIA_ID,
// 			 C2.NAME AS CATEGORIA
// 	FROM BGPRODUCT2 B2, BGCATEGORIA C2
//  WHERE B2.CATEGORIA_ID = C2.ID(+)
// 	 AND B2.NAME LIKE '%".$name."%'";
// 	$ret = selectOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);
// 	return $ret;
// }

// function bgproduct2_buscaCategoria_id($categoria_id){
// 	$sql = "SELECT B2.NUMORIGINAL,
// 			 B2.PRODUCT_ID,
// 			 B2.NAME,
// 			 B2.DTCADASTRO,
// 			 B2.DTATUALIZACAO,
// 			 B2.CATEGORIA_ID,
// 			 C2.NAME AS CATEGORIA
// 	FROM BGPRODUCT2 B2, BGCATEGORIA C2
//  WHERE B2.CATEGORIA_ID = C2.ID(+)
// 	 AND B2.CATEGORIA_ID = ".$categoria_id;
// 	$ret = selectOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);
// 	if (empty($ret)) {
// 		excluirBgcategoria($categoria_id);
// 	}
// 	return $ret;
// }


// function buscaCategorias(){
// 	$sql = "SELECT MIN(ID) AS ID, NAME FROM BGCATEGORIA GROUP BY NAME ORDER BY NAME";
// 	$ret = selectOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);
// 	return $ret;
// }


// function excluirBgcategoria($categoria_id){
// 	$sql = "DELETE FROM BGCATEGORIA WHERE ID = ".$categoria_id;
// 	$ret = executarOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);
// 	return $ret;
// }


// function buscarBGATRIBUTO(){
// 	$sql = "SELECT * FROM BGATRIBUTO";
// 	$ret = selectOracle($sql);
// 	varDump2($sql);
// 	varDump2($ret);
// 	return $ret;
// }


// function produtoPesquisaBGPRODUCT($dados){
// 	$valido = false;
// 	$sql = "SELECT PROD_ID, PROD_EXTERNAL_ID, PROD_DTATUALIZACAO, MAX(PROD_NAME) AS PROD_NAME, COUNT(VAR_EXTERNAL_ID) AS VARIANTES
// 			FROM BGPRODUCT
// 			WHERE 1=1";
	
// 	if (!empty($dados['CODPECA'])) {
// 		$sql .= PHP_EOL . " AND PROD_NAME LIKE '%" . trim(mb_strtoupper($dados['CODPECA'], 'UTF-8')) . "%'";
// 		$valido = true;
// 	}
// 	if (!empty($dados['PROD_ID'])) {
// 		$sql .= PHP_EOL . " AND PROD_ID = " . $dados['PROD_ID'];
// 		$valido = true;
// 	}
// 	$sql .= PHP_EOL . "GROUP BY PROD_ID, PROD_EXTERNAL_ID, PROD_DTATUALIZACAO";

// 	if ($valido) {
// 		$ret = selectOracle($sql);	
// 		// varDump2($sql);
// 		// varDump2($ret);
// 		return $ret;
// 	} else {
// 		insereModal('warning', "Nenhum filtro selecionado");
// 		return false;
// 	}
// }

// function buscaDadosProduct_id($product_id){
// 	$sql = "SELECT * FROM BGPRODUCT WHERE product_id = ".$product_id;
// 	return reset(selectOracle($sql));
// }

// function buscaQtVariantes(){
// 	$sql = "SELECT DISTINCT VARIANTES FROM BGPRODUCT order by VARIANTES asc";
// 	return selectOracle($sql);
// }