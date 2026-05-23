<?php
function listaRelatórios(){
  $idUsuario = (int) $_SESSION['login']['IDUSUARIO'];
	$sql = "SELECT * FROM ORCRELATORIOS WHERE 1=1";
  if ($idUsuario <> 1) {
    $sql .= "AND ATIVO = 'S'";
  }
	return selectOracle($sql);
}

function buscaDadosORCRELATORIOS(int $idRelatorio): array|false 
{
    // Sanitização adicional garantindo que é inteiro
    $id = (int)$idRelatorio;

    $sql = "SELECT * FROM ORCRELATORIOS WHERE IDRELATORIO = {$id} AND ROWNUM <= 1";
    
    $ret = selectOracle($sql);

    // No PHP 8, reset() retorna o valor do primeiro elemento ou false se o array estiver vazio.
    // Verificamos se $ret é um array e se não está vazio.
    if (is_array($ret) && !empty($ret)) {
        return reset($ret);
    }

    return false;
}

// Função para Salvar ou Atualizar o Relatório Dinâmico
function salvarRelatorioDinamico($dados) {
    // varDump2($dados); die();
    $descricao    = sanitizeOracleString($dados['DESCRICAO']);
    $tipo         = sanitizeOracleString($dados['TIPO']);
    $comandoSql   = sanitizeOracleString($dados['COMANDO_SQL']); // Escapa aspas simples para o INSERT
    $idRelatorio  = (int) $dados['IDRELATORIO'];
    $idUsuario    = (int) $_SESSION['login']['IDUSUARIO'];
    
    try {
        if ($idRelatorio > 0) {
            // Update
            $sqlRel = "UPDATE ORCRELATORIOS 
                          SET DTULTALTER = SYSDATE, 
                              IDUSUARIOALTER = $idUsuario, 
                              DESCRICAO = '$descricao', 
                              TIPO = '{$tipo}', 
                              COMANDO_SQL = '$comandoSql' 
                        WHERE IDRELATORIO = $idRelatorio";
            // varDump2($sqlRel); die();
            return executarOracle($sqlRel);
        } else {
            $novoId = buscaProxIDRELATORIO();
            // Namefunction padrão para indicar que é dinâmico
            $sqlRel = "INSERT INTO ORCRELATORIOS (IDRELATORIO, DESCRICAO, TIPO, COMANDO_SQL) VALUES (
                      {$novoId}, 
                      '{$descricao}', 
                      '{$tipo}', 
                      '{$comandoSql}'
                      )";
            // varDump2($sqlRel); die();
            return executarOracle($sqlRel);
        }
    } catch (Exception $e) {
        insereModal('danger', 'Erro ao salvar relatório: ' . $e->getMessage());
        return false;
    }
}

function buscaProxIDRELATORIO() {
  // Busca o próximo ID (Sequence simulada)
  $sqlId = "SELECT NVL(MAX(IDRELATORIO), 0) + 1 AS PROX_ID FROM ORCRELATORIOS";
  $res = selectOracle($sqlId);
  return (int) $res[0]['PROX_ID'];
}


function buscaFiltrosReltarorio($idRelatorio){
	$sql = "SELECT * FROM ORCRELATORIOSFILTROS 
					 WHERE IDRELATORIO = ".$idRelatorio."
				ORDER BY IDRELATORIOFILTRO ASC";
	return selectOracle($sql);
}

function extrairFiltrosScript(string $string): array {
    // Inicializa o array de retorno
    $filtrosEncontrados = [];

    // Regex explicada:
    // \{      -> Procura o caractere literal '{'
    // ([^}]+) -> Grupo de captura: qualquer caractere que NÃO seja '}' (um ou mais)
    // \}      -> Procura o caractere literal '}'
    $padrao = '/\{([^}]+)\}/';

    // Executa a busca global na string
    if (preg_match_all($padrao, $string, $matches)) {
        // $matches[0] contém as strings completas (ex: {DATA_INICIO})
        // $matches[1] contém apenas o conteúdo capturado (ex: DATA_INICIO)
        
        // Utilizamos array_unique para evitar duplicidade caso o filtro apareça 2x na query
        $filtrosEncontrados = array_unique($matches[1]);
    }

    return array_values($filtrosEncontrados);
}


function buscaDadosFiltroRelatorio(int $IDRELATORIOFILTRO):array|false {
  $idRelatorioFiltro = (int) $IDRELATORIOFILTRO;
  if ($idRelatorioFiltro == 0){
    exibeMensagem("ERRO ao executar buscaDadosFiltroRelatorio. idRelatorioFiltro inválido.");
    return false;
  }
  $sql = "select * from orcrelatoriosfiltros where IDRELATORIOFILTRO = $IDRELATORIOFILTRO";
  $ret = selectOracle($sql);
  return ($ret)?reset($ret):false;
}

function aplicaradicionarFiltro(array $dados) {
  $idRelatorio  = (int) $dados['IDRELATORIO'];
  $campo        = sanitizeOracleString($dados['CAMPO']);
  $tipo         = sanitizeOracleString($dados['TIPO']);
  $funcao       = sanitizeOracleString($dados['FUNCAO'], 0, false);
  if ($idRelatorio == 0){
    exibeMensagem("ERRO ao executar aplicaradicionarFiltro. idRelatorio inválido.");
    return false;
  }
  $sql = "insert into orcrelatoriosfiltros (IDRELATORIOFILTRO, IDRELATORIO, CAMPO, TIPO, FUNCAO) values (
    (SELECT MAX(IDRELATORIOFILTRO)+1 FROM orcrelatoriosfiltros),
    $idRelatorio,
    '$campo',
    '$tipo',
    '$funcao'    
  )";
  // varDump2($sql); die();
  if (executarOracle($sql)){
    insereModal("success", "Filtro adicionado com sucesso!");
  } else {
    insereModal("danger", "ERRO ao adicionar o Filtro!");
  }
}

function aplicareditarFiltro(array $dados) {
  $idRelatorio        = (int) $dados['IDRELATORIO'];
  $idRelatorioFiltro  = (int) $dados['IDRELATORIOFILTRO'];
  $campo              = sanitizeOracleString($dados['CAMPO']);
  $tipo               = sanitizeOracleString($dados['TIPO']);
  $funcao             = sanitizeOracleString($dados['FUNCAO'], 0, false);
  if ($idRelatorio == 0){
    exibeMensagem("ERRO ao executar aplicareditarFiltro. idRelatorio inválido.");
    return false;
  }
  if ($idRelatorioFiltro == 0){
    exibeMensagem("ERRO ao executar aplicareditarFiltro. idRelatorioFiltro inválido.");
    return false;
  }
  $sql = "UPDATE orcrelatoriosfiltros 
             SET CAMPO = '$campo', 
                 TIPO = '$tipo', 
                 FUNCAO = '$funcao'
           WHERE IDRELATORIOFILTRO = $idRelatorioFiltro
             AND IDRELATORIO = $idRelatorio";
  // varDump2($sql); die();
  if (executarOracle($sql)){
    insereModal("success", "Filtro editado com sucesso!");
  } else {
    insereModal("danger", "ERRO ao editar o Filtro!");
  }
}

function aplicarexcluirFiltro(array $dados) {
  $idRelatorioFiltro = (int) $dados['IDRELATORIOFILTRO'];
  if ($idRelatorioFiltro == 0){
    exibeMensagem("ERRO ao executar aplicarexcluirFiltro. idRelatorioFiltro inválido.");
    return false;
  }
  $sql = "delete from orcrelatoriosfiltros where IDRELATORIOFILTRO = $idRelatorioFiltro";
  if (executarOracle($sql)){
    insereModal("success", "Filtro excluído com sucesso!");
  } else {
    insereModal("danger", "ERRO ao excluir o Filtro!");
  }
}

function ativarRelatorio(array $dados) {
  $idRelatorio = (int) $dados['IDRELATORIO'];
  if ($idRelatorio == 0){
    exibeMensagem("ERRO ao executar ativarRelatorio. idRelatorio inválido.");
    return false;
  }
  $sql = "UPDATE orcrelatorios
             SET ATIVO = 'S'
           WHERE IDRELATORIO = $idRelatorio";
  // varDump2($sql); die();
  if (executarOracle($sql)){
    insereModal("success", "Relatório ativado com sucesso!");
  } else {
    insereModal("danger", "ERRO ao ativar o relatório!");
  }
}

function inativarRelatorio(array $dados) {
  $idRelatorio  = (int) $dados['IDRELATORIO'];
  if ($idRelatorio == 0){
    exibeMensagem("ERRO ao executar ativarRelatorio. idRelatorio inválido.");
    return false;
  }
  $sql = "UPDATE orcrelatorios
             SET ATIVO = 'N'
           WHERE IDRELATORIO = $idRelatorio";
  // varDump2($sql); die();
  if (executarOracle($sql)){
    insereModal("success", "Relatório inativado com sucesso!");
  } else {
    insereModal("danger", "ERRO ao inativar o relatório!");
  }
}


// function validaEstoque($produto){
// 	$sql = "SELECT T1.CODPROD, 
// 					 TRIM(T1.NUMORIGINAL) AS NUMORIGINAL, 
// 					 TRIM(T1.DESCRICAO) AS DESCRICAO, 
// 					 T1.EMBALAGEM, 
// 					 SUM(NVL(T2.QTESTGER,0)) AS QTDESTOQUE, 
// 					 SUM(NVL(T2.QTESTGER,0) - NVL(T2.QTBLOQUEADA,0) - NVL(T2.QTRESERV,0) - NVL(T2.QTPENDENTE,0)) AS QTDESTDISPONIVEL,
// 					 ".$produto['quantidadePedida']." AS QTPEDIDA
// 			FROM PCPRODUT T1
// 			LEFT JOIN PCEST T2 ON T1.CODPROD = T2.CODPROD
// 			WHERE T2.CODFILIAL = 1
// 			AND T1.CODPROD = ".$produto['codprod']."
// 			GROUP BY T1.CODPROD, T1.NUMORIGINAL, T1.DESCRICAO, T1.EMBALAGEM
// 			HAVING SUM(NVL(T2.QTESTGER,0) - NVL(T2.QTBLOQUEADA,0) - NVL(T2.QTRESERV,0) - NVL(T2.QTPENDENTE,0)) = 0";
// 	$ret = selectOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);
// 	return $ret;
// }

// function listaContatosOrcamento(){
// 	$sql = "SELECT C.IDORCAMENTO, 
// 								 C.IDUSUARIO, 
// 								 U.NOME AS USUARIO, 
// 								 C.CODCLI, 
// 								 CLI.CLIENTE,
// 								 C.DATA, 
// 								 C.CONTATO, 
// 								 C.TELCELULAR, 
// 								 C.TELFIXO
// 					FROM ORCORCAMENTOC C, ORCUSUARIO U, PCCLIENT CLI
// 					WHERE C.IDUSUARIO = U.IDUSUARIO
// 					AND C.CODCLI = CLI.CODCLI
// 					AND C.DATA >= TRUNC(SYSDATE-7)
// 					AND C.IDUSUARIO NOT IN (1)
// 					AND C.CODCLI NOT IN (1031)
// 					ORDER BY C.CODCLI, C.DATA ASC";
// 	$ret = selectOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);

// 	return $ret;
// }

// function buscaLogVides($dados){
// 	$sql = "SELECT L.DATA,
// 			 U.IDUSUARIO,
// 			 U.NOME AS USUARIO,
// 			 L.TIPO,
// 			 L.CODPECA_OLD,
// 			 L.CODPECA_NEW,
// 			 L.APLICMARCA_OLD,
// 			 L.APLICMARCA_NEW,
// 			 L.VIDE_OLD,
// 			 L.VIDE_NEW,
// 			 L.DESCRICAO_OLD,
// 			 L.DESCRICAO_NEW
// 	FROM ORCLOGVIDE L, ORCUSUARIO U
//  WHERE L.IDUSUARIO = U.IDUSUARIO
// 	 AND U.IDUSUARIO NOT IN (1)
// 	 AND TRUNC(L.DATA) >= TRUNC(TO_DATE('".$dados['dataini']."', 'DD/MM/YYYY'))
// 	 AND TRUNC(L.DATA) <= TRUNC(TO_DATE('".$dados['datafim']."', 'DD/MM/YYYY'))
//  ORDER BY L.TIPO, L.DATA DESC
// ";
// 	$ret = selectOracle($sql);
// 	// varDump2($sql);
// 	// varDump2($ret);

// 	return $ret;

// }

// function buscaTitulosPagosFornecedorRevenda(array $dados):array|false {

//   $filtros = "";

//   // Tratamento de Datas com TO_DATE para segurança e precisão
//   if (!empty($dados['DATAINI'])) {
//       // Assume-se formato YYYY-MM-DD vindo do input date do Bootstrap
//       $dataIni = sanitizeOracleString($dados['DATAINI']); 
//       $filtros .= " AND L.DTPAGTO >= ".formataDataBRtoOracle($dataIni);
//   }

//   if (!empty($dados['DATAFIM'])) {
//       $dataFim = sanitizeOracleString($dados['DATAFIM']);
//       $filtros .= " AND L.DTPAGTO <= ".formataDataBRtoOracle($dataFim);
//   }

//   // Tratamento de Inteiros (Cast seguro)
//   if (isset($dados['CODFORNEC']) && (int)$dados['CODFORNEC'] > 0) {
//       $codFornec = (int)$dados['CODFORNEC'];
//       $filtros .= " AND F.CODFORNEC = $codFornec";
//   }

//   $sql = "SELECT DISTINCT
//           L.RECNUM,
//           L.CODFILIAL,
//           F.CODFORNEC,
//           F.FORNECEDOR,
//           NVL(F.REVENDA, 'N') AS REVENDA,
//           L.NUMNOTA,
//           NVL(L.PARCELA, 1) AS PARCELA,
//           L.DTEMISSAO,
//           L.DTLANC,
//           L.DTVENC,
//           L.DTPAGTO,
//           L.VALOR,
//           L.VALORDEV,
//           L.DESCONTOFIN,
//           L.TXPERM,
//           L.VPAGO,
//           L.HISTORICO
//       FROM 
//           PCLANC L
//       INNER JOIN PCFORNEC F ON (L.CODFORNEC = F.CODFORNEC)
//       INNER JOIN PCCONTA C  ON (L.CODCONTA = C.CODCONTA)
//       WHERE 
//           L.DTPAGTO IS NOT NULL
//           AND L.TIPOPARCEIRO = 'F'
//           AND NVL(F.REVENDA, 'N') = 'S'
//           AND NOT (L.VPAGO = 0 AND L.DTESTORNOBAIXA IS NOT NULL)
//           $filtros
//       ORDER BY L.DTPAGTO";
//   // varDump2($sql); die();
//   Return selectOracle($sql);
// }

function buscaProdutosCotados($dados){
	$sql = "SELECT C.ORIGEM,
             DECODE (
             NVL (
                 (SELECT   pc.posicao
                    FROM   pcpedc pc, pcpedi pi
                   WHERE pc.numped = pi.numped  
                     AND pc.dtcancel IS NULL
                     AND pc.numpedcli = to_char(c.idorcamento)
                     AND pi.codprod = i.codprod),
                 'O'),
             'O',
             'ORCAMENTO',
             'M',
             'MONTADO',
             'L',
             'LIBERADO',
             'F',
             'FATURADO')
             AS status_orcamento,
             TO_CHAR(NVL(I.DATA, C.DATA), 'DD/MM/YYYY HH24:MI:SS') AS DATA,
             C.IDUSUARIO,
             U.NOME,
             C.CODCLI,
             CLI.CLIENTE,
             C.IDORCAMENTO,
             TRIM(C.MAQUINA) AS EQUIPAMENTO,
             TRIM(C.OBSERVACAO3) AS OBSERVACAO3,
             TRIM(C.CONTATO||' '||C.TELCELULAR) AS CONTATO,
             I.STATUS,
             I.IDORCAMENTOI,
             I.VALIDACAO,
             I.CODPECA,
                CASE
                    WHEN ((LENGTH(I.CODPECA) = 7) AND
                             (UPPER(I.CODPECA) = LOWER(I.CODPECA)) AND
                             (SUBSTR(I.CODPECA, 1, 1) IN
                             ('0', '1', '2', '3', '4', '5'))) THEN
                     'CAT'
                    ELSE
                     CASE
                         WHEN ((LENGTH(I.CODPECA) = 6) AND
                                    (UPPER(SUBSTR(I.CODPECA, 1, 1)) =  LOWER(SUBSTR(I.CODPECA, 1, 1))) AND
                                    (UPPER(SUBSTR(I.CODPECA, 2, 1)) <> LOWER(SUBSTR(I.CODPECA, 2, 1))) AND
                                    (UPPER(SUBSTR(I.CODPECA, 3, 4)) =  LOWER(SUBSTR(I.CODPECA, 3, 4)))) THEN
                            'CAT'
                         ELSE
                            'OUTROS'
                     END
                END AS TIPOCODPECA,
             P.CODPROD,
             P.DV,
             I.DESCRICAO,
             P.MARCA,
             I.QTPEDIDA,
             I.QTDISPONIVEL,
             I.DISPONIBILIDADE,
             I.ICMS,
             I.PVENDA,
             I.PROCEDENCIA,
             I.OBSERVACAO,
             MAX(V.DESCRICAO) AS DESCRICAONPR,
             MAX(V.APLICMARCA) AS MARCANPR
    FROM ORCORCAMENTOC C,
             ORCORCAMENTOI I,
             ORCUSUARIO    U,
             PCCLIENT      CLI,
             ORCVIDE       V,
             PCPRODUT      P
 WHERE C.IDORCAMENTO = I.IDORCAMENTO
     AND C.IDUSUARIO = U.IDUSUARIO
     AND C.CODCLI = CLI.CODCLI
     AND I.CODPROD = P.CODPROD(+)
     AND I.CODPECA = V.CODPECA(+)
     AND C.CODCLI NOT IN (16837)
	   AND TRUNC(C.DATA) >= TRUNC(TO_DATE('".$dados['DATAINI']."', 'DD/MM/YYYY'))
	   AND TRUNC(C.DATA) <= TRUNC(TO_DATE('".$dados['DATAFIM']."', 'DD/MM/YYYY'))
     AND NVL(I.QTDISPONIVEL, 0) > 0
 GROUP BY C.ORIGEM,
            C.STATUS,
            NVL(I.DATA, C.DATA),
            C.IDUSUARIO,
            U.NOME,
            C.CODCLI,
            C.MAQUINA,
            C.OBSERVACAO3,
            CLI.CLIENTE,
            C.IDORCAMENTO,
            I.STATUS,
            I.IDORCAMENTOI,
            I.VALIDACAO,
            I.CODPECA,
            P.CODPROD,
            P.DV,
            I.DESCRICAO,
            P.MARCA,
            I.QTPEDIDA,
            I.QTDISPONIVEL,
            I.DISPONIBILIDADE,
            I.ICMS,
            I.PVENDA,
            I.PROCEDENCIA,
            I.OBSERVACAO,
            i.codprod,
            TRIM(C.CONTATO||' '||C.TELCELULAR)
 ORDER BY 15, 12 ASC";
	$ret = selectOracle($sql);
	// varDump2($dados); 
	varDump2($sql); 
	varDump2($ret); 
	// die();
	return $ret;
}

function consultaSugestaoCompraArquivo($dados){

  $sql = "SELECT   TRIM (estoque.numoriginal) numoriginal,
               TRIM (MAX (p.descricao)) descricao,
               MAX (estoque.qtestger) saldo,
               MAX(CASE movimento.numoriginal
                       WHEN p.numoriginal
                       THEN
                           CASE movimento.ano WHEN 2022 THEN movimento.qt END
                   END)
                   AS \"2022\",
               MAX(CASE movimento.numoriginal
                       WHEN p.numoriginal
                       THEN
                           CASE movimento.ano WHEN 2023 THEN movimento.qt END
                   END)
                   AS \"2023\",
               MAX(CASE movimento.numoriginal
                       WHEN p.numoriginal
                       THEN
                           CASE movimento.ano WHEN 2024 THEN movimento.qt END
                   END)
                   AS \"2024\",
               MAX(CASE movimento.numoriginal
                       WHEN p.numoriginal
                       THEN
                           CASE movimento.ano WHEN 2025 THEN movimento.qt END
                   END)
                   AS \"2025\",
               SUM(CASE movimento.numoriginal
                       WHEN p.numoriginal THEN movimento.qt
                   END)
                   AS TODOS,
               TO_CHAR (MAX (estoque.dt_entrada), 'DD/MM/YYYY') dt_ultima_ent,
               ROUND (MAX (ent.preco_compra), 2) pcompra,
               TRIM (MAX (ent.marca)) marca,
               TRIM (MAX (ent.fornecedor)) fornecedor
        FROM   pcprodut p,
               (  SELECT   p2.numoriginal,
                           SUM (e.qtestger) AS qtestger,
                           MAX (e.dtultent) AS dt_entrada
                    FROM   pcest e, pcprodut p2
                   WHERE   p2.codprod = e.codprod AND e.codfilial = 1
                GROUP BY   p2.numoriginal) estoque,
               /*-----------------------------------*/

               (SELECT   pcprodut.codprod,
                         pcprodut.descricao,
                         pcprodut.numoriginal,
                         pcprodut.codmarca,
                         pcmov.codfornec,
                         (SELECT   fornecedor
                            FROM   pcfornec
                           WHERE   pcfornec.codfornec = pcmov.codfornec)
                             fornecedor,
                         (SELECT   marca
                            FROM   pcmarca
                           WHERE   pcmarca.codmarca = pcprodut.codmarca)
                             marca,
                         pcmov.ptabela preco_compra,
                         pcmov.numtransent
                  FROM   pcmov, (  SELECT   p.numoriginal, MAX (m.numtransent) maxent
                                     FROM   pcmov m, pcprodut p
                                    WHERE   p.codprod = m.codprod AND m.codoper like 'E%'
                                 GROUP BY   p.numoriginal) a, pcprodut
                 WHERE       pcprodut.codprod = pcmov.codprod
                         AND pcmov.numtransent = a.maxent
                         AND pcprodut.numoriginal = a.numoriginal
                         AND pcmov.codoper like 'E%') ent,
               /*-----------------------------------*/

               (  SELECT   p5.numoriginal,
                           EXTRACT (YEAR FROM TRUNC (m.dtmov, 'YEAR')) AS ano,
                           SUM (NVL (m.qt, 0)) AS qt
                    FROM   pcmov m, pcprodut p5
                   WHERE       p5.codprod = m.codprod
                           AND m.codfilial = 1
                           AND m.codoper = 'S'
                           AND m.dtcancel IS NULL
                GROUP BY   p5.numoriginal, TRUNC (m.dtmov, 'YEAR')
                ORDER BY   2 ASC) movimento
       WHERE       estoque.numoriginal = movimento.numoriginal (+)
               AND p.numoriginal = estoque.numoriginal (+)
               AND p.numoriginal = movimento.numoriginal (+)
               AND p.numoriginal = ent.numoriginal (+)
               AND p.numoriginal IN (
                    SELECT I.CODPECA
                        FROM ORCORCAMENTOC C,
                                 ORCORCAMENTOI I,
                                 ORCUSUARIO    U,
                                 PCCLIENT      CLI,
                                 ORCVIDE       V,
                                 PCPRODUT      P
                     WHERE C.IDORCAMENTO = I.IDORCAMENTO
                         AND C.IDUSUARIO = U.IDUSUARIO
                         AND C.CODCLI = CLI.CODCLI
                         AND I.CODPROD = P.CODPROD(+)
                         AND I.CODPECA = V.CODPECA(+)
                         AND C.CODCLI NOT IN (16837)
												 AND TRUNC(I.DATA) >= TRUNC(TO_DATE('".$dados['DATAINI']."', 'DD/MM/YYYY'))
												 AND TRUNC(I.DATA) <= TRUNC(TO_DATE('".$dados['DATAFIM']."', 'DD/MM/YYYY'))
                         AND NVL(I.QTDISPONIVEL, 0) > 0
                     GROUP BY I.CODPECA
               )
    GROUP BY   estoque.numoriginal
    ORDER BY   estoque.numoriginal";
  $return = selectOracle($sql);
  // varDump2($sql); 
  // varDump2($return); 
  // die();
  return $return;
}

// function buscaProdutosComEstoqueAnaliseCompras($dados){

// }



// function buscaVides($NUMORIGINAL){
// 	$sql = "SELECT BASE.VIDE FROM (
// 			    SELECT '{$NUMORIGINAL}' AS VIDE FROM DUAL
// 			    UNION 
// 			    SELECT VIDE FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND CODPECA = '{$NUMORIGINAL}'
// 			    UNION 
// 			    SELECT VIDE FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND CODPECA IN (SELECT VIDE FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND CODPECA = '{$NUMORIGINAL}')
// 			    UNION 
// 			    SELECT VIDE FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND CODPECA IN (SELECT CODPECA FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND VIDE = '{$NUMORIGINAL}')
// 			    UNION 
// 			    SELECT CODPECA AS VIDE FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND VIDE = '{$NUMORIGINAL}'
// 			    UNION 
// 			    SELECT CODPECA AS VIDE FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND VIDE IN (SELECT VIDE FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND CODPECA = '{$NUMORIGINAL}')
// 			    UNION 
// 			    SELECT CODPECA AS VIDE FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND VIDE IN (SELECT CODPECA FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND VIDE = '{$NUMORIGINAL}')
// 			) BASE 
// 			WHERE BASE.VIDE IS NOT NULL
// 			  AND BASE.VIDE NOT LIKE 'W%'
// 			GROUP BY BASE.VIDE
// 			ORDER BY BASE.VIDE ASC";
// 	// varDump2($sql); die();
// 	$ret = selectOracle($sql);
// 	if ($ret && !empty($ret)) {
// 		$ret2=[];
// 		foreach ($ret as $key => $value) {
// 			if (!in_array($value['VIDE'], $ret2)) {
// 				array_push($ret2, $value['VIDE']);
// 			}
// 		}
// 		return $ret2;
// 	} else {
// 		return false;
// 	}
// }


// function buscaProdutosEquipamento($dados){
// 	$sql = "SELECT DISTINCT I.DATA,
// 								C.IDUSUARIO,
// 								U.NOME,
// 								C.ORIGEM,
// 								C.CODCLI,
// 								C.MAQUINA AS EQUIPAMENTO,
// 								C.OBSERVACAO2 AS ORDEMDECOMPRA,
// 								CLI.CLIENTE,
// 								I.IDORCAMENTO,
// 								I.IDORCAMENTOI,
// 								I.VALIDACAO,
// 								I.CODPECA,
// 								I.CODVIDE,
// 								I.CODPROD,
// 								TRIM(REPLACE(I.DESCRICAO, '=', ' ')) AS DESCRICAO,
// 								I.MARCA,
// 								I.QTPEDIDA,
// 								I.QTDISPONIVEL,
// 								I.DISPONIBILIDADE,
// 								I.ICMS,
// 								I.PTABELA,
// 								I.PVENDA,
// 								I.PROCEDENCIA,
// 								I.OBSERVACAO,
// 								I.STATUS,
// 								I.LOCACAO,
// 								I.DV
// 	FROM ORCORCAMENTOC C, ORCORCAMENTOI I, ORCUSUARIO U, PCCLIENT CLI
//  WHERE C.IDORCAMENTO = I.IDORCAMENTO
// 	 AND C.IDUSUARIO = U.IDUSUARIO
// 	 AND C.CODCLI = CLI.CODCLI
// 	 AND I.CODPECA IS NOT NULL
// 	 AND C.MAQUINA IS NOT NULL
// 	 AND TRUNC(I.DATA) >= TRUNC(TO_DATE('".$dados['dataini']."', 'DD/MM/YYYY'))
// 	 AND TRUNC(I.DATA) <= TRUNC(TO_DATE('".$dados['datafim']."', 'DD/MM/YYYY'))
//  ORDER BY I.CODPROD, I.DATA ASC
// ";
// 	$ret = selectOracle($sql);
// 	// varDump2($dados);
// 	// varDump2($sql); 
// 	// varDump2($ret); 
// 	// die();
// 	return $ret;
// }




// function listaCadastrosDuplicados(){

// 	$sql = "select   pcprodut.codprod, 
// 			pcprodut.dv,
// 			pcprodut.descricao, 
// 			case when DESCRICAO2 like 'CADASTRO AUTOMATIZADO%' then 'APLICATIVO' else 'WINTHOR' end as CADASTRO,
// 			pcprodut.numoriginal, 
// 			pcmarca.codmarca, 
// 			pcmarca.marca, 
// 			pcprodut.dtcadastro,
// 			pcprodut.DTULTALTER,
// 			pcprodut.informacoestecnicas as locacao,
// 			NVL(pcest.qtest,0) AS SALDO
// 		from  pcprodut, pcmarca, pcest
// 		where   pcprodut.codmarca = pcmarca.codmarca 
// 			and   pcprodut.codprod = pcest.codprod
// 			and   dtexclusao is null  
// 			and   pcest.codfilial = 1 
// 			and   numoriginal||'-'||pcmarca.codmarca in (select p2.numoriginal||'-'||p2.codmarca 
// 			from pcprodut p2 
// 			where dtexclusao is null 
// 			group by p2.numoriginal||'-'||p2.codmarca 
// 			having count(p2.numoriginal||'-'||p2.codmarca)>1)
// 		order by numoriginal, pcmarca.codmarca";
// 		// varDump2($sql);
// 	return  selectOracle($sql);
// }

// function listaAlteracaoPreco(){
// 	$sql = "SELECT U.IDUSUARIO,
// 			 U.NOME,
// 			 C.CODCLI,
// 			 C.CLIENTE,
// 			 A.DATA,
// 			 A.IDORCAMENTO,
// 			 A.CODPECA,
// 			 A.CODPROD,
// 			 A.DESCRICAO_NEW,
// 			 A.DESCRICAO_OLD,
// 			 A.MARCA_OLD,
// 			 A.MARCA_NEW,
// 			 A.QTPEDIDA_NEW,
// 			 A.QTPEDIDA_OLD,
// 			 A.PVENDA_OLD,
// 			 A.PVENDA_NEW
// 	FROM ORCLOGALTPRECO A, ORCUSUARIO U, PCCLIENT C
//  WHERE A.IDUSUARIO = U.IDUSUARIO
// 	 AND A.CODCLI = C.CODCLI
// 	 AND A.DATA >= TRUNC(SYSDATE - 7)
// 	 ORDER BY A.DATA";
// 		// varDump2($sql);
// 	return  selectOracle($sql);	
// }


// function listaAcessoPermitidos(){
// 	$sql = "SELECT CAST(DATA AS TIMESTAMP) AS DATA,
// 			 CASE WHEN A.NAVEGADOR LIKE '%Android%' THEN 'ANDROID'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 5.1%' THEN 'WINDOWS XP'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 5.2%' THEN 'WINDOWS SERVER 2003'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.0%' THEN 'WINDOWS VISTA'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.1%' THEN 'WINDOWS 7'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.2%' THEN 'WINDOWS 8'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.3%' THEN 'WINDOWS 8.1'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 10.0%' THEN 'WINDOWS 10'
// 						ELSE A.NAVEGADOR
// 						END AS NAVEGADOR,
// 			 A.ENDERECOIP,
// 			 A.ENDERECOACESSADO,
// 			 A.LOGIN,
// 			 A.SENHA,
// 			 SUCESSO      
// FROM ORCACESSO  A
// WHERE DATA >= TRUNC(SYSDATE-30)
// AND SUCESSO = 'S'";
// 	return  selectOracle($sql); 
// }


// function listaAcessoNegado(){
// 	$sql = "SELECT CAST(DATA AS TIMESTAMP) AS DATA,
// 			 CASE WHEN A.NAVEGADOR LIKE '%Android%' THEN 'ANDROID'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 5.1%' THEN 'WINDOWS XP'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 5.2%' THEN 'WINDOWS SERVER 2003'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.0%' THEN 'WINDOWS VISTA'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.1%' THEN 'WINDOWS 7'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.2%' THEN 'WINDOWS 8'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.3%' THEN 'WINDOWS 8.1'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 10.0%' THEN 'WINDOWS 10'
// 						ELSE A.NAVEGADOR
// 						END AS NAVEGADOR,
// 			 A.ENDERECOIP,
// 			 A.ENDERECOACESSADO,
// 			 A.LOGIN,
// 			 A.SENHA,
// 			 SUCESSO      
// FROM ORCACESSO  A
// WHERE DATA >= TRUNC(SYSDATE-30)
// AND SUCESSO = 'N'
// UNION 
// SELECT CAST(DATA AS TIMESTAMP) AS DATA,
// 			 CASE WHEN A.NAVEGADOR LIKE '%Android%' THEN 'ANDROID'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 5.1%' THEN 'WINDOWS XP'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 5.2%' THEN 'WINDOWS SERVER 2003'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.0%' THEN 'WINDOWS VISTA'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.1%' THEN 'WINDOWS 7'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.2%' THEN 'WINDOWS 8'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 6.3%' THEN 'WINDOWS 8.1'
// 						WHEN A.NAVEGADOR LIKE '%Windows NT 10.0%' THEN 'WINDOWS 10'
// 						ELSE A.NAVEGADOR
// 						END AS NAVEGADOR,
// 			 A.ENDERECOIP,
// 			 A.ENDERECOACESSADO,
// 			 A.LOGIN,
// 			 A.SENHA,
// 			 SUCESSO      
// FROM ORCACESSO  A
// WHERE DATA >= TRUNC(SYSDATE-30)
// AND SUCESSO = 'S'
// AND A.ENDERECOIP NOT LIKE '192.168.%'";
// 	return  selectOracle($sql);	
// }

// function buscaLogPecaCliente(){
// 	$sql = "SELECT L.DATA,
// 								 L.IDORCAMENTO,
// 								 U.IDUSUARIO,
// 								 U.IDUSUARIO||' '||U.NOME AS USUARIO,
// 								 C.CODCLI,
// 								 C.CODCLI||' '||C.CLIENTE AS CLIENTE,
// 								 L.CODPECA,
// 								 L.QTPEDIDA
// 					FROM ORCLOGCONSULTA L, ORCUSUARIO U, PCCLIENT C 
// 					WHERE L.IDUSUARIO = U.IDUSUARIO
// 					AND L.CODCLI = C.CODCLI
// 					AND L.DATA >= TRUNC(SYSDATE-15)";
// 	return selectOracle($sql);
// }

// function buscaAcessoAppCliente($dados){
// 	$sql = "SELECT * FROM CLILOGACESSO 
// 				 WHERE 1=1
// 				 AND TRUNC(DATA) >= TRUNC(TO_DATE('".$dados['dataini']."', 'DD/MM/YYYY'))
// 				 AND TRUNC(DATA) <= TRUNC(TO_DATE('".$dados['datafim']."', 'DD/MM/YYYY'))
// 				 ORDER BY IDLOGACESSO DESC";
// 	// varDump2($sql); die();
// 	return selectOracle($sql);
// }


// function buscaProdutosAppCliente($dados){
// 	$sql = "SELECT DISTINCT
// 			 C.ORIGEM,
// 			 TO_CHAR(C.DATA, 'DD/MM/YYYY HH24:MI:SS') AS DATA,
// 			 C.IDUSUARIO,
// 			 U.NOME AS USUARIO,
// 			 C.CODCLI,
// 			 CLI.CLIENTE,
// 			 C.MAQUINA,
// 			 C.OBSENTREGA1,
// 			 I.IDORCAMENTO,
// 			 I.IDORCAMENTOI,
// 			 I.STATUS,
// 			 I.VALIDACAO,
// 			 I.CODPECA,
// 			 I.CODPROD,
// 			 I.DV,
// 			 I.DESCRICAO,
// 			 I.MARCA,
// 			 I.QTPEDIDA,
// 			 I.QTDISPONIVEL,
// 			 I.DISPONIBILIDADE,
// 			 I.PVENDA,
// 			 I.PROCEDENCIA
// 	FROM ORCORCAMENTOC C,
// 			 ORCORCAMENTOI I,
// 			 ORCUSUARIO    U,
// 			 PCCLIENT      CLI
//  WHERE C.IDORCAMENTO = I.IDORCAMENTO
// 	 AND C.IDUSUARIO = U.IDUSUARIO
// 	 AND C.CODCLI = CLI.CODCLI
// 	 AND C.ORIGEM = 'APP CLIENTE'
// 	 AND TRUNC(C.DATA) >= TRUNC(TO_DATE('".$dados['DATAINI']."', 'DD/MM/YYYY'))
// 	 AND TRUNC(C.DATA) <= TRUNC(TO_DATE('".$dados['DATAFIM']."', 'DD/MM/YYYY'))
// 	 ".(($dados['CODCLI']<>"")?'AND C.CODCLI = '.$dados['CODCLI']:'')."
//  ORDER BY I.IDORCAMENTO, I.IDORCAMENTOI ASC
// ";
// 	$ret = selectOracle($sql);
// 	// varDump2($sql); 
// 	// varDump2($ret); 
// 	// die();
// 	return $ret;
// }


// function buscaProdutosSemPreco(){
//   $sql = "SELECT   *
//           FROM   (SELECT   p.codprod,
//                            p.numoriginal,
//                            p.descricao,
//                            p.codmarca,
//                            p.marca,
//                            p.dtcadastro,
//                            e.dtultent,
//                            GREATEST (pkg_estoque.estoque_disponivel (p.codprod,
//                                                                      1,
//                                                                      'VP',
//                                                                      SYSDATE), 0)
//                                AS qtsaldo,
//                            e.qtbloqueada,
//                            pr.pvenda,
//                            pr.ptabela
//                     FROM       pcprodut p
//                            INNER JOIN
//                                pctabpr pr
//                            ON pr.codprod = p.codprod
//                            INNER JOIN
//                                pcest e
//                            ON e.codprod = p.codprod
//                    WHERE       pr.numregiao = 1
//                            AND e.codfilial = 1
//                            AND p.dtexclusao IS NULL
//                            AND p.numoriginal <> '0X0001'
//                            AND pr.pvenda <= 0.01)
//          WHERE   qtsaldo > 0";
//   return selectOracle($sql);
// }
