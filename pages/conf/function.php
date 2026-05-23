<?php

function buscaProdutosRadar(){
	$sql = "SELECT * FROM produtosRadar";
	return selectOracle($sql);
}

function buscaProdutosExistentes($produtos){

	$itensPorScript = 1000;
	$qtdProdutos = count($produtos);
	$qtdScripts = ceil($qtdProdutos / $itensPorScript);
	$produtosExistentes = array();

	for ($i=0; $i < $qtdScripts; $i++) { 
		$sql = "INSERT ALL ";
		$prodInicio = ($i*$itensPorScript);
		$prodFim = (($i+1)*$itensPorScript);
		$listaProd = "";
		if ($prodFim > $qtdProdutos) {
			$prodFim = $qtdProdutos;
		}
		for ($j=$prodInicio; $j < $prodFim; $j++) { 
			$sql .= " INTO TMPPRODUT (numoriginal) VALUES ('".trim($produtos[$j]['numoriginal'])."')";
		}
		$sql .= " select 1 from dual;";

    return executarOracle($sql);
	}

	// varDump2($produtosExistentes);
	return $produtosExistentes;
}

function  buscaFaturamentoRca(int $codUsur):array|false {
  if ($codUsur < 1) {
    throw new Exception("Error ao processar a função buscaFaturamentoRca, codUsur inválido!");
  }

  $sql = "SELECT 
          v.codusur,
          v.dataini,
          v.datafim,
          v.positivacao,
          v.mix,
          v.totalnf,
          v.vlvenda,
          nvl(d.vldevolucao, 0) as vldevolucao,
          -- Cálculo de Venda Líquida
          round((v.vlvenda - nvl(d.vldevolucao, 0)),2) AS vlvenda_liquida,
          v.ticketmedio
      FROM (
          SELECT 
              f.codusur,
              to_char((ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1) + 26), 'DD/MM/YYYY') AS dataini,
              to_char(TRUNC(SYSDATE, 'MM') + 25, 'DD/MM/YYYY') AS datafim,
              COUNT(DISTINCT f.codcli) AS positivacao,
              COUNT(DISTINCT f.codprod) AS mix,
              COUNT(DISTINCT f.numnota) AS totalnf,
              SUM(f.vlvenda) AS vlvenda,
              ROUND(SUM(f.vlvenda) / NULLIF(COUNT(DISTINCT f.numnota), 0), 2) AS ticketmedio
          FROM view_vendas_resumo_faturamento f
          WHERE f.codusur = {$codUsur}
            AND f.dtsaida BETWEEN ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1) + 26 AND TRUNC(SYSDATE, 'MM') + 25
            AND f.dtcancel IS NULL
          GROUP BY f.codusur
      ) v
      LEFT JOIN (
          SELECT 
              d.codusur,
              SUM(d.vldevolucao) AS vldevolucao
          FROM view_devol_resumo_faturamento d
          WHERE d.codusur = {$codUsur}
            AND d.dtent BETWEEN ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1) + 26 AND TRUNC(SYSDATE, 'MM') + 25
            AND d.dtcancel IS NULL
          GROUP BY d.codusur
      ) d ON v.codusur = d.codusur";
      // varDump2($sql);
  $ret = selectOracle($sql);
  return ($ret) ? reset($ret) : false;
}

function rankingVendedores(){
  $codUsur = (int) $_SESSION["login"]["CODUSUR"];
	$sql = "SELECT 
              v.codusur,
              ROUND((v.vlvenda - NVL(d.vldevolucao, 0)), 2) AS vlvenda_liquida
          FROM (
              SELECT 
                  f.codusur,
                  SUM(f.vlvenda) AS vlvenda
              FROM view_vendas_resumo_faturamento f
              WHERE f.dtsaida BETWEEN ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1) + 26 AND TRUNC(SYSDATE, 'MM') + 25
                AND f.dtcancel IS NULL
              GROUP BY f.codusur
          ) v
          LEFT JOIN (
              SELECT 
                  d.codusur,
                  SUM(d.vldevolucao) AS vldevolucao
              FROM view_devol_resumo_faturamento d
              WHERE d.dtent BETWEEN ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -1) + 26 AND TRUNC(SYSDATE, 'MM') + 25
                AND d.dtcancel IS NULL
              GROUP BY d.codusur
          ) d ON v.codusur = d.codusur
          ORDER BY vlvenda_liquida DESC";
	$ret = selectOracle($sql);
  if ($ret) {
    foreach ($ret as $key => $value) {
      if ((int) $value["CODUSUR"] === $codUsur) {
        return array(
          "posicao" => ($key+1),
          "total" =>count($ret)
        );
      }
    }
  }
}

function buscaOrcamentosRecentes($CODUSUR){

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
   AND T6.CODUSUR = ".$CODUSUR."
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

    if ($CODUSUR <> "") {
        return selectOracle($sql);
    } else {
        return false;
    }
}

function buscaHistFaturamento($CODUSUR){
    $sql = "SELECT PCNFSAID.CODUSUR,
                   TRUNC(PCNFSAID.DTSAIDA,'MM') AS MES,
                   SUM(ROUND(NVL(NVL((CASE
                                   WHEN ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2) IS NOT NULL THEN
                                    (CASE
                                      WHEN (SELECT TIPOVLVENDA FROM PCPARAMPLANOVOO) = 'DEDUZIRFRETE' THEN
                                       (DECODE(PCMOV.CODOPER,
                                               'S',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               'ST',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               'SM',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               0) - (ROUND(NVL(PCMOV.VLFRETE, 0), 2) * PCMOV.QT))
                                      WHEN (SELECT TIPOVLVENDA FROM PCPARAMPLANOVOO) =
                                           'DEDUZIROUTRASDESP' THEN
                                       (DECODE(PCMOV.CODOPER,
                                               'S',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               'ST',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               'SM',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               0) - (ROUND(NVL(PCMOV.vLOUTROS, 0), 2) * PCMOV.QT))
                                      WHEN (SELECT TIPOVLVENDA FROM PCPARAMPLANOVOO) = 'DEDUZIRAMBOS' THEN
                                       (DECODE(PCMOV.CODOPER,
                                               'S',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               'ST',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               'SM',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2))),
                                                    0)),
                                               0) - (ROUND(NVL(PCMOV.VLFRETE, 0), 2) * PCMOV.QT) -
                                       (ROUND(NVL(PCMOV.VLOUTROS, 0), 2) * PCMOV.QT))
                                      ELSE
                                       ROUND(PCMOVCOMPLE.VLSUBTOTITEM, 2)
                                    END)
                                   ELSE
                                    NULL
                                 END),
                                 (ROUND((DECODE(PCMOV.CODOPER,
                                                'S',
                                                (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                            5,
                                                            0,
                                                            6,
                                                            0,
                                                            11,
                                                            0,
                                                            12,
                                                            0,
                                                            DECODE(PCMOV.CODOPER,
                                                                   'SB',
                                                                   0,
                                                                   DECODE(PCMOV.TIPOITEM,
                                                                          'N',
                                                                          PCMOV.QTCONT,
                                                                          PCMOV.QT))),
                                                     0)),
                                                'ST',
                                                (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                            5,
                                                            0,
                                                            6,
                                                            0,
                                                            11,
                                                            0,
                                                            12,
                                                            0,
                                                            DECODE(PCMOV.CODOPER,
                                                                   'SB',
                                                                   0,
                                                                   DECODE(PCMOV.TIPOITEM,
                                                                          'N',
                                                                          PCMOV.QTCONT,
                                                                          PCMOV.QT))),
                                                     0)),
                                                'SM',
                                                (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                            5,
                                                            0,
                                                            6,
                                                            0,
                                                            11,
                                                            0,
                                                            12,
                                                            0,
                                                            DECODE(PCMOV.CODOPER,
                                                                   'SB',
                                                                   0,
                                                                   DECODE(PCMOV.TIPOITEM,
                                                                          'N',
                                                                          PCMOV.QTCONT,
                                                                          PCMOV.QT))),
                                                     0)),
                                                0)) *
                                        ((CASE
                                          WHEN (SELECT TIPOVLVENDA FROM PCPARAMPLANOVOO) = 'DEDUZIRFRETE' THEN
                                           (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                       7,
                                                       DECODE(PCMOV.TIPOITEM,
                                                              'N',
                                                              NVL(DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF,
                                                                             'N'),
                                                                         'S',
                                                                         PCMOV.PUNIT,
                                                                         PCMOV.PUNITCONT),
                                                                  0),
                                                              PCMOV.PUNIT) + NVL(PCMOV.VLOUTRASDESP, 0) +
                                                       NVL(PCMOV.VLFRETE_RATEIO, 0) + NVL(PCMOV.VLOUTROS, 0) -
                                                       DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF, 'N'),
                                                              'S',
                                                              NVL(PCMOV.VLREPASSE, 0),
                                                              0),
                                                       NVL(DECODE(PCMOV.TIPOITEM,
                                                                  'N',
                                                                  NVL(DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF,
                                                                                 'N'),
                                                                             'S',
                                                                             PCMOV.PUNIT,
                                                                             PCMOV.PUNITCONT),
                                                                      0),
                                                                  PCMOV.PUNIT),
                                                           0) + NVL(PCMOV.VLOUTRASDESP, 0) +
                                                       NVL(PCMOV.VLFRETE_RATEIO, 0) + NVL(PCMOV.VLOUTROS, 0) -
                                                       DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF, 'N'),
                                                              'S',
                                                              NVL(PCMOV.VLREPASSE, 0),
                                                              0)),
                                                0))
                                          WHEN (SELECT TIPOVLVENDA FROM PCPARAMPLANOVOO) =
                                               'DEDUZIROUTRASDESP' THEN
                                           (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                       7,
                                                       DECODE(PCMOV.TIPOITEM,
                                                              'N',
                                                              NVL(DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF,
                                                                             'N'),
                                                                         'S',
                                                                         PCMOV.PUNIT,
                                                                         PCMOV.PUNITCONT),
                                                                  0),
                                                              PCMOV.PUNIT) + NVL(PCMOV.VLFRETE, 0) +
                                                       NVL(PCMOV.VLFRETE_RATEIO, 0) +
                                                       NVL(PCMOV.VLOUTRASDESP, 0),
                                                       NVL(DECODE(PCMOV.TIPOITEM,
                                                                  'N',
                                                                  NVL(DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF,
                                                                                 'N'),
                                                                             'S',
                                                                             PCMOV.PUNIT,
                                                                             PCMOV.PUNITCONT),
                                                                      0),
                                                                  PCMOV.PUNIT),
                                                           0) + NVL(PCMOV.VLFRETE, 0) +
                                                       NVL(PCMOV.VLFRETE_RATEIO, 0) +
                                                       NVL(PCMOV.VLOUTRASDESP, 0)),
                                                0))
                                          WHEN (SELECT TIPOVLVENDA FROM PCPARAMPLANOVOO) = 'DEDUZIRAMBOS' THEN
                                           (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                       7,
                                                       DECODE(PCMOV.TIPOITEM,
                                                              'N',
                                                              NVL(DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF,
                                                                             'N'),
                                                                         'S',
                                                                         PCMOV.PUNIT,
                                                                         PCMOV.PUNITCONT),
                                                                  0),
                                                              PCMOV.PUNIT) + NVL(PCMOV.VLFRETE_RATEIO, 0) +
                                                       NVL(PCMOV.VLOUTRASDESP, 0),
                                                       NVL(DECODE(PCMOV.TIPOITEM,
                                                                  'N',
                                                                  NVL(DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF,
                                                                                 'N'),
                                                                             'S',
                                                                             PCMOV.PUNIT,
                                                                             PCMOV.PUNITCONT),
                                                                      0),
                                                                  PCMOV.PUNIT),
                                                           0) + NVL(PCMOV.VLFRETE_RATEIO, 0) +
                                                       NVL(PCMOV.VLOUTRASDESP, 0)),
                                                0))
                                          ELSE
                                           (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                       7,
                                                       DECODE(PCMOV.TIPOITEM,
                                                              'N',
                                                              NVL(DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF,
                                                                             'N'),
                                                                         'S',
                                                                         PCMOV.PUNIT,
                                                                         PCMOV.PUNITCONT),
                                                                  0),
                                                              PCMOV.PUNIT) + NVL(PCMOV.VLFRETE, 0) +
                                                       NVL(PCMOV.VLOUTRASDESP, 0) +
                                                       NVL(PCMOV.VLFRETE_RATEIO, 0) + NVL(PCMOV.VLOUTROS, 0) -
                                                       DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF, 'N'),
                                                              'S',
                                                              NVL(PCMOV.VLREPASSE, 0),
                                                              0),
                                                       NVL(DECODE(PCMOV.TIPOITEM,
                                                                  'N',
                                                                  NVL(DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF,
                                                                                 'N'),
                                                                             'S',
                                                                             PCMOV.PUNIT,
                                                                             PCMOV.PUNITCONT),
                                                                      0),
                                                                  PCMOV.PUNIT),
                                                           0) + NVL(PCMOV.VLFRETE, 0) +
                                                       NVL(PCMOV.VLOUTRASDESP, 0) +
                                                       NVL(PCMOV.VLFRETE_RATEIO, 0) + NVL(PCMOV.VLOUTROS, 0) -
                                                       DECODE(NVL(PCNFSAID.SOMAREPASSEOUTRASDESPNF, 'N'),
                                                              'S',
                                                              NVL(PCMOV.VLREPASSE, 0),
                                                              0)),
                                                0))
                                        END) - NVL(PCMOV.VLIPI, 0) - NVL(PCMOV.ST, 0) - (NVL(PCMOVCOMPLE.VLFECP, 0) + NVL(PCMOVCOMPLE.VLFECPTRANSFCD, 0))),
                                        2)) + ROUND(NVL(PCMOV.VLIPI, 0) *
                                                    (DECODE(PCMOV.CODOPER,
                                                            'S',
                                                            (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                                        5,
                                                                        0,
                                                                        6,
                                                                        0,
                                                                        11,
                                                                        0,
                                                                        12,
                                                                        0,
                                                                        DECODE(PCMOV.CODOPER,
                                                                               'SB',
                                                                               0,
                                                                               DECODE(PCMOV.TIPOITEM,
                                                                                      'N',
                                                                                      PCMOV.QTCONT,
                                                                                      PCMOV.QT))),
                                                                 0)),
                                                            'ST',
                                                            (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                                        5,
                                                                        0,
                                                                        6,
                                                                        0,
                                                                        11,
                                                                        0,
                                                                        12,
                                                                        0,
                                                                        DECODE(PCMOV.CODOPER,
                                                                               'SB',
                                                                               0,
                                                                               DECODE(PCMOV.TIPOITEM,
                                                                                      'N',
                                                                                      PCMOV.QTCONT,
                                                                                      PCMOV.QT))),
                                                                 0)),
                                                            'SM',
                                                            (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                                        5,
                                                                        0,
                                                                        6,
                                                                        0,
                                                                        11,
                                                                        0,
                                                                        12,
                                                                        0,
                                                                        DECODE(PCMOV.CODOPER,
                                                                               'SB',
                                                                               0,
                                                                               DECODE(PCMOV.TIPOITEM,
                                                                                      'N',
                                                                                      PCMOV.QTCONT,
                                                                                      PCMOV.QT))),
                                                                 0)),
                                                            0)),
                                                    2) +
                                 ROUND(NVL(PCMOV.ST, 0) * (DECODE(PCMOV.CODOPER,
                                                                  'S',
                                                                  (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                                              5,
                                                                              0,
                                                                              6,
                                                                              0,
                                                                              11,
                                                                              0,
                                                                              12,
                                                                              0,
                                                                              DECODE(PCMOV.CODOPER,
                                                                                     'SB',
                                                                                     0,
                                                                                     DECODE(PCMOV.TIPOITEM,
                                                                                            'N',
                                                                                            PCMOV.QTCONT,
                                                                                            PCMOV.QT))),
                                                                       0)),
                                                                  'ST',
                                                                  (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                                              5,
                                                                              0,
                                                                              6,
                                                                              0,
                                                                              11,
                                                                              0,
                                                                              12,
                                                                              0,
                                                                              DECODE(PCMOV.CODOPER,
                                                                                     'SB',
                                                                                     0,
                                                                                     DECODE(PCMOV.TIPOITEM,
                                                                                            'N',
                                                                                            PCMOV.QTCONT,
                                                                                            PCMOV.QT))),
                                                                       0)),
                                                                  'SM',
                                                                  (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                                              5,
                                                                              0,
                                                                              6,
                                                                              0,
                                                                              11,
                                                                              0,
                                                                              12,
                                                                              0,
                                                                              DECODE(PCMOV.CODOPER,
                                                                                     'SB',
                                                                                     0,
                                                                                     DECODE(PCMOV.TIPOITEM,
                                                                                            'N',
                                                                                            PCMOV.QTCONT,
                                                                                            PCMOV.QT))),
                                                                       0)),
                                                                  0)),
                                       2) +
                                 ROUND((NVL(PCMOVCOMPLE.VLFECP, 0) + NVL(PCMOVCOMPLE.VLFECPTRANSFCD, 0)) * 
                                       (DECODE(PCMOV.CODOPER,
                                               'S',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  DECODE(PCMOV.TIPOITEM,
                                                                         'N',
                                                                         PCMOV.QTCONT,
                                                                         PCMOV.QT))),
                                                    0)),
                                               'ST',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  DECODE(PCMOV.TIPOITEM,
                                                                         'N',
                                                                         PCMOV.QTCONT,
                                                                         PCMOV.QT))),
                                                    0)),
                                               'SM',
                                               (NVL(DECODE(PCNFSAID.CONDVENDA,
                                                           5,
                                                           0,
                                                           6,
                                                           0,
                                                           11,
                                                           0,
                                                           12,
                                                           0,
                                                           DECODE(PCMOV.CODOPER,
                                                                  'SB',
                                                                  0,
                                                                  DECODE(PCMOV.TIPOITEM,
                                                                         'N',
                                                                         PCMOV.QTCONT,
                                                                         PCMOV.QT))),
                                                    0)),
                                               0)),
                                       2)),
                             0),
                         2) ) AS VLVENDA
              FROM PCNFSAID,
                   PCCLIENT,
                   PCPRACA,
                   PCMOV,
                   PCPRODUT,
                   PCMOVCOMPLE,
                   PCATIVI     ATIVI_PRINCIPAL,
                   PCATIVI     ATIVIMOV_PRINCIPAL
             WHERE PCNFSAID.NUMTRANSVENDA = PCMOV.NUMTRANSVENDA
               AND PCNFSAID.NUMNOTA = PCMOV.NUMNOTA
               AND PCMOV.CODPROD = PCPRODUT.CODPROD
               AND PCCLIENT.CODPRACA = PCPRACA.CODPRACA
               AND PCNFSAID.CODCLI = PCCLIENT.CODCLI
               AND PCCLIENT.CODATV1 = ATIVI_PRINCIPAL.CODATIV(+)
               AND PCNFSAID.CODATV1 = ATIVIMOV_PRINCIPAL.CODATIV(+)
               AND NVL(PCNFSAID.TIPOVENDA, 'X') NOT IN ('SR', 'DF')   
               AND PCNFSAID.DTSAIDA BETWEEN PKG_PARAMETRO_CONTABIL.GET_DATA1
                                AND PKG_PARAMETRO_CONTABIL.GET_DATA2   
               AND ((PCNFSAID.CODFILIAL = PKG_PARAMETRO_CONTABIL.GET_CODFILIAL) or
                        (PKG_PARAMETRO_CONTABIL.GET_CODFILIAL is null))        
               AND PCNFSAID.DTCANCEL IS NULL
               AND PCMOV.NUMTRANSITEM = PCMOVCOMPLE.NUMTRANSITEM(+)
               AND NVL(PCMOV.TIPOITEM, 'C') IN ('C', 'N')
               AND PCNFSAID.CODFISCAL <> 0
               
               AND PCNFSAID.CODUSUR = {$CODUSUR}
               AND PCNFSAID.DTSAIDA >= TRUNC(SYSDATE-180, 'MM')
               AND PCNFSAID.DTSAIDA < TRUNC(SYSDATE)
               
               AND NOT EXISTS
             (SELECT 1
                      FROM PCNFENT N
                     WHERE N.NUMTRANSENT = PCNFSAID.NUMTRANSENTNFESTORNADA
                       AND NVL(N.TIPOMOVGARANTIA, -1) = -1)
              GROUP BY PCNFSAID.CODUSUR,
                   TRUNC(PCNFSAID.DTSAIDA,'MM')
              ORDER BY 2 ASC";
    if (!empty($CODUSUR) && $CODUSUR) {
        return selectOracle($sql);
    } else {
        return false;
    }
}