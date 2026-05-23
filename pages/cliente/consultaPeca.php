<?php 
// $debug = true;
$debug = false;

if($debug) varDump2('Entrou no ConsultaPeca');
if($debug) varDump2($dados);


if (isset($dados['CODPECA'])) {

  if (strlen($dados['CODPECA']) < 2) {
   
    if($debug) varDump2('ERRO O campo Pesquisa deve conter ao menos 2 dígitos');
    insereModal('danger', "O campo Pesquisa deve conter ao menos 2 dígitos");
  
  } else {

    $QTPEDIDA     = intval($dados['QTPEDIDA']);
    $IDORCAMENTO  = intval($_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']);
    $CODCLI       = intval($_SESSION['ORC_CLIENTE']['CAB']['CODCLI']);
    $CODPLPLAG    = intval($_SESSION['ORC_CLIENTE']['CAB']['CODPLPLAG']);
    $NUMPR        = intval($_SESSION['ORC_CLIENTE']['PLPAG']['NUMPR']);
    $UFDESTINO    = strval($_SESSION['ORC_CLIENTE']['CLIENTE']['UF']);
    $CODFILIALNF  = strval($_SESSION['ORC_CLIENTE']['CLIENTE']['CODFILIALNF']);
    if ($CODFILIALNF == "") {
      $CODFILIALNF = "1";
    }
    $CODPECA      = mb_strtoupper(trim($dados['CODPECA']), 'UTF-8');
    $PERCDESC     = $_SESSION['ORC_CLIENTE']['CLIENTE']['PERCDESC'];
    if ($PERCDESC == "" || $PERCDESC == null || $PERCDESC == false) {
      $PERCDESC = 0;
    }

    $NUMREGIAO  = $_SESSION['ORC_CLIENTE']['CLIENTE']['NUMREGIAO'];
    if ($NUMREGIAO == "" || $NUMREGIAO == null || $NUMREGIAO == false) {
      $NUMREGIAO = 1;
    }

    $sql = "SELECT DISTINCT BASE.*
        FROM (SELECT 0 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR('WINTHOR') AS ORIGEM,
               PCPRODUT.NUMORIGINAL,
               TRUNC(PCPRODUT.CODPROD) || '-' || TRUNC(PCPRODUT.DV) AS WINTHOR,
               TRUNC(PCPRODUT.CODPROD) AS CODPROD,
               TRUNC(PCPRODUT.DV) AS DV,
               TRIM(PCPRODUT.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = PCPRODUT.CODMARCA), PCPRODUT.MARCA) AS MARCA,
               TRIM(PCPRODUT.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(PCPRODUT.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               NULL AS VIDE,
               (SELECT CASE
                         WHEN (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                              NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0)) < 0 THEN
                          0
                         ELSE
                          (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                          NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0))
                       END AS SALDO
                  FROM PCEST E
                 WHERE E.CODPROD = PCPRODUT.CODPROD
                   AND E.CODFILIAL = '".$CODFILIALNF."') AS SALDO,
               (SELECT ROUND(DECODE('".$NUMPR."',
                                    '1',
                                    PR.PVENDA1,
                                    '2',
                                    PR.PVENDA2,
                                    '3',
                                    PR.PVENDA3,
                                    '4',
                                    PR.PVENDA4,
                                    '5',
                                    PR.PVENDA5,
                                    '6',
                                    PR.PVENDA6,
                                    '7',
                                    PR.PVENDA7,
                                    PR.PVENDA) *
                             (1 - ((NVL(".$PERCDESC.", 0) * (-1)) / 100)),
                             2) + 0.01
                  FROM PCTABPR PR
                 WHERE PR.CODPROD = PCPRODUT.CODPROD
                   AND PR.NUMREGIAO = '".$NUMREGIAO."') AS PTABELA,
               (SELECT TT.CODTRIBPISCOFINS
                  FROM PCTABTRIB TT
                 WHERE TT.CODPROD = PCPRODUT.CODPROD
                   AND TT.CODFILIALNF = '".$CODFILIALNF."'
                   AND TT.UFDESTINO = '".$UFDESTINO."') AS CODTRIBPISCOFINS
        FROM PCPRODUT 
        WHERE PCPRODUT.DTEXCLUSAO IS NULL
          AND PCPRODUT.NUMORIGINAL LIKE '".$CODPECA."%' OR 'W'||PCPRODUT.CODPROD = '".$CODPECA."' OR PCPRODUT.DESCRICAO LIKE '%".$CODPECA."%'

        UNION
        SELECT 0 AS ORD,
              '".$CODPECA."' AS CODPECA,
             DECODE(TRIM(V2.APLICMARCA), 'OPCAO', 'OPCAO', 'INFO', 'INFO', 'VIDE') AS ORIGEM,
             V2.CODPECA AS NUMORIGINAL,
             NULL AS WINTHOR,
             NULL AS CODPROD,
             NULL AS DV,
             TO_CHAR(NVL(V2.DESCRICAO, 'SOB CONSULTA')) AS DESCRICAO,
             TO_CHAR(NVL(NVL(V2.APLICMARCA, V2.MARCA),  'SOB CONSULTA')) AS MARCA,
             NULL AS LOCACAO,
             TO_CHAR(DECODE(V2.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
             V2.VIDE AS VIDE,
             TO_NUMBER(0) AS SALDO,
             TO_NUMBER(V2.PRECO) AS PTABELA,
             NULL AS CODTRIBPISCOFINS
         FROM ORCVIDE V2
        WHERE V2.DTEXCLUSAO IS NULL
          AND V2.VIDE IS NOT NULL
          AND V2.CODPECA IS NOT NULL
          AND V2.VIDE IS NOT NULL 
          AND V2.CODPECA LIKE '".$CODPECA."%' 

        UNION
        SELECT 0 AS ORD,
              '".$CODPECA."' AS CODPECA,
             DECODE(TRIM(V2.APLICMARCA), 'OPCAO', 'OPCAO', 'INFO', 'INFO', 'VIDE') AS ORIGEM,
             V2.VIDE AS NUMORIGINAL,
             NULL AS WINTHOR,
             NULL AS CODPROD,
             NULL AS DV,
             TO_CHAR(NVL(V2.DESCRICAO, 'SOB CONSULTA')) AS DESCRICAO,
             TO_CHAR(NVL(NVL(V2.APLICMARCA, V2.MARCA),  'SOB CONSULTA')) AS MARCA,
             NULL AS LOCACAO,
             TO_CHAR(DECODE(V2.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
             V2.CODPECA AS VIDE,
             TO_NUMBER(0) AS SALDO,
             TO_NUMBER(V2.PRECO) AS PTABELA,
             NULL AS CODTRIBPISCOFINS
         FROM ORCVIDE V2
        WHERE V2.DTEXCLUSAO IS NULL
          AND V2.VIDE IS NOT NULL
          AND V2.CODPECA IS NOT NULL
          AND V2.CODPECA IS NOT NULL 
          AND V2.VIDE LIKE '".$CODPECA."%' 

        UNION
        SELECT 1 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR('VIDE') AS ORIGEM,
               P.NUMORIGINAL,
               TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
               TRUNC(P.CODPROD) AS CODPROD,
               TRUNC(P.DV) AS DV,
               TRIM(P.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
               TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               P.NUMORIGINAL AS VIDE,
               (SELECT CASE
                         WHEN (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                              NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0)) < 0 THEN
                          0
                         ELSE
                          (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                          NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0))
                       END AS SALDO
                  FROM PCEST E
                 WHERE E.CODPROD = P.CODPROD
                   AND E.CODFILIAL = 1) AS SALDO,
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
                   AND TT.UFDESTINO = '".$UFDESTINO."') AS CODTRIBPISCOFINS
        FROM PCPRODUT P
        WHERE P.DTEXCLUSAO IS NULL
          AND P.NUMORIGINAL IN
             (SELECT V.VIDE AS PECA
                FROM ORCVIDE V
               WHERE V.DTEXCLUSAO IS NULL
                 AND V.CODPECA LIKE '".$CODPECA."%'
                 AND V.VIDE IS NOT NULL
                 AND V.APLICMARCA NOT LIKE '%INFO%'
                 AND V.APLICMARCA NOT LIKE '%OPCAO%'
              UNION
              SELECT V.CODPECA AS PECA
                FROM ORCVIDE V
               WHERE V.DTEXCLUSAO IS NULL
                 AND V.VIDE LIKE '".$CODPECA."%'
                 AND V.CODPECA IS NOT NULL
                 AND V.APLICMARCA NOT LIKE '%INFO%'
                 AND V.APLICMARCA NOT LIKE '%OPCAO%')

        UNION
        SELECT 1 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR('VIDE') AS ORIGEM,
               P.NUMORIGINAL,
               TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
               TRUNC(P.CODPROD) AS CODPROD,
               TRUNC(P.DV) AS DV,
               TRIM(P.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
               TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               P.NUMORIGINAL AS VIDE,
               (SELECT CASE
                         WHEN (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                              NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0)) < 0 THEN
                          0
                         ELSE
                          (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                          NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0))
                       END AS SALDO
                  FROM PCEST E
                 WHERE E.CODPROD = P.CODPROD
                   AND E.CODFILIAL = 1) AS SALDO,
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
                   AND TT.UFDESTINO = '".$UFDESTINO."') AS CODTRIBPISCOFINS
        FROM PCPRODUT P
        WHERE P.DTEXCLUSAO IS NULL
          AND P.NUMORIGINAL IN
             (SELECT V.VIDE AS PECA
                FROM ORCVIDE V
               WHERE V.DTEXCLUSAO IS NULL
                 AND V.CODPECA LIKE '".$CODPECA."%'
                 AND V.VIDE IS NOT NULL
                 AND V.APLICMARCA NOT LIKE '%INFO%'
                 AND V.APLICMARCA NOT LIKE '%OPCAO%'
              UNION
              SELECT V.CODPECA AS PECA
                FROM ORCVIDE V
               WHERE V.DTEXCLUSAO IS NULL
                 AND V.VIDE LIKE '".$CODPECA."%'
                 AND V.CODPECA IS NOT NULL
                 AND V.APLICMARCA NOT LIKE '%INFO%'
                 AND V.APLICMARCA NOT LIKE '%OPCAO%')
        UNION
        SELECT 2 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR('INFO') AS ORIGEM,
               P.NUMORIGINAL,
               TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
               TRUNC(P.CODPROD) AS CODPROD,
               TRUNC(P.DV) AS DV,
               TRIM(P.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
               TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               P.NUMORIGINAL AS VIDE,
               (SELECT CASE
                         WHEN (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                              NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0)) < 0 THEN
                          0
                         ELSE
                          (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                          NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0))
                       END AS SALDO
                  FROM PCEST E
                 WHERE E.CODPROD = P.CODPROD
                   AND E.CODFILIAL = 1) AS SALDO,
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
                   AND TT.UFDESTINO = '".$UFDESTINO."') AS CODTRIBPISCOFINS
        FROM PCPRODUT P
        WHERE P.DTEXCLUSAO IS NULL
          AND P.NUMORIGINAL IN
             (SELECT V.VIDE AS PECA
                FROM ORCVIDE V
               WHERE V.DTEXCLUSAO IS NULL
                 AND V.CODPECA LIKE '".$CODPECA."%'
                 AND V.VIDE IS NOT NULL
                 AND V.APLICMARCA LIKE '%INFO%')
        UNION
        SELECT 3 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR('OPCAO') AS ORIGEM,
               P.NUMORIGINAL,
               TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
               TRUNC(P.CODPROD) AS CODPROD,
               TRUNC(P.DV) AS DV,
               TRIM(P.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
               TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               P.NUMORIGINAL AS VIDE,
               (SELECT CASE
                         WHEN (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                              NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0)) < 0 THEN
                          0
                         ELSE
                          (NVL(E.QTESTGER, 0) - NVL(E.QTINDENIZ, 0) -
                          NVL(E.QTBLOQUEADA, 0) - NVL(E.QTRESERV, 0))
                       END AS SALDO
                  FROM PCEST E
                 WHERE E.CODPROD = P.CODPROD
                   AND E.CODFILIAL = 1) AS SALDO,
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
                   AND TT.UFDESTINO = '".$UFDESTINO."') AS CODTRIBPISCOFINS
        FROM PCPRODUT P
        WHERE P.DTEXCLUSAO IS NULL
          AND P.NUMORIGINAL IN
             (SELECT V.VIDE AS PECA
                FROM ORCVIDE V
               WHERE V.DTEXCLUSAO IS NULL
                 AND V.CODPECA LIKE '".$CODPECA."%'
                 AND V.VIDE IS NOT NULL
                 AND V.APLICMARCA LIKE '%OPCAO%')
        UNION
        SELECT 4 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR(ORCVIDE.APLICMARCA) AS ORIGEM,
               NULL AS NUMORIGINAL,
               NULL AS WINTHOR,
               NULL AS CODPROD,
               NULL AS DV,
               TO_CHAR(NVL(ORCVIDE.DESCRICAO, 'SOB CONSULTA')) AS DESCRICAO,
               TO_CHAR(NVL(NVL(ORCVIDE.APLICMARCA, ORCVIDE.MARCA),
                           'SOB CONSULTA')) AS MARCA,
               NULL AS LOCACAO,
               TO_CHAR(DECODE(ORCVIDE.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               NULL AS VIDE,
               TO_NUMBER(0) AS SALDO,
               TO_NUMBER(ORCVIDE.PRECO) AS PTABELA,
               NULL AS CODTRIBPISCOFINS
          FROM ORCVIDE
         WHERE ORCVIDE.DTEXCLUSAO IS NULL
           AND ORCVIDE.VIDE IS NULL
           AND (ORCVIDE.APLICMARCA LIKE '%INFO%' OR ORCVIDE.APLICMARCA LIKE '%OPCAO%')
           AND ORCVIDE.CODPECA LIKE '".$CODPECA."'
        UNION
        
        SELECT 5 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR('NPR') AS ORIGEM,
               NULL AS NUMORIGINAL,
               NULL AS WINTHOR,
               NULL AS CODPROD,
               NULL AS DV,
               TO_CHAR(NVL(ORCVIDE.DESCRICAO, 'SOB CONSULTA')) AS DESCRICAO,
               TO_CHAR(NVL(NVL(ORCVIDE.APLICMARCA, ORCVIDE.MARCA),
                           'SOB CONSULTA')) AS MARCA,
               NULL AS LOCACAO,
               TO_CHAR(DECODE(ORCVIDE.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               NULL AS VIDE,
               TO_NUMBER(0) AS SALDO,
               TO_NUMBER(ORCVIDE.PRECO) AS PTABELA,
               NULL AS CODTRIBPISCOFINS
          FROM ORCVIDE
         WHERE ORCVIDE.VIDE IS NULL
           AND ORCVIDE.CODPECA LIKE '".$CODPECA."') BASE
WHERE BASE.PTABELA > 0.01
ORDER BY (CASE 
              WHEN (BASE.SALDO > 0) THEN 1
              ELSE 0
          END) DESC,
          BASE.PTABELA ASC";

    if($debug) varDump2($sql);
    
    if( $valuePeca = selectOracle($sql) ){
      
      if($debug) varDump2($valuePeca);
      if($debug) varDump2("Total pecas: "  .count($valuePeca));

      $peca = false;
      $i = 0;
      
      do {
        if($debug) varDump2(array('Contador' => $i, 'Saldo' => $valuePeca[$i]['SALDO'], 'Qt Pedida' => $dados['QTPEDIDA']));

        if(intval($valuePeca[$i]['SALDO']) >= intval($dados['QTPEDIDA'])){
          if($debug) varDump2('saldo sufuciente');
          $peca = $valuePeca[$i];
          break;
        } else {
          if($debug) varDump2('saldo insufuciente');
        }
        $i++;
      } while ($i < count($valuePeca));

      if ($peca === false) {
        $peca = reset($valuePeca);
      }
      $peca['QTPEDIDA'] = $dados['QTPEDIDA'];

      if($debug) varDump2($peca);
      
      if(!$debug) inserePecaOrcamento($IDORCAMENTO, $peca); 
    
    } else {
    
      if(!$debug) inserePecaGenerica($IDORCAMENTO, $dados);

    }

  }
}

