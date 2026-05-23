<?php 
// $debug = true;
if($debug) varDump2('Entrou no ConsultaPeca');
if($debug) varDump2($dados);


if (isset($dados['CODPECA'])) {

  if (strlen($dados['CODPECA']) < 2) {
    if($debug) varDump2('ERRO O campo Pesquisa deve conter ao menos 2 dígitos');
    insereModal('danger', "O campo Pesquisa deve conter ao menos 2 dígitos");
  } else {
    // varDump2($_SESSION['ORCAMENTO']['CLIENTE']);
    $QTPEDIDA     = intval($dados['QTPEDIDA']);
    $IDORCAMENTO  = intval($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']);
    $CODCLI       = intval($_SESSION['ORCAMENTO']['CAB']['CODCLI']);
    $CODUSUR      = intval($_SESSION['ORCAMENTO']['CAB']['CODUSUR']);
    $CODPLPAG     = intval($_SESSION['ORCAMENTO']['CAB']['CODPLPAG']);
    $NUMPR        = intval($_SESSION['ORCAMENTO']['PLPAG']['NUMPR']);
    $UFDESTINO    = strval($_SESSION['ORCAMENTO']['CLIENTE']['UF']);
    $CODFILIALNF  = strval($_SESSION['ORCAMENTO']['CLIENTE']['CODFILIALNF']);
    if ($CODFILIALNF == "") {
      $CODFILIALNF = "1";
    }
    $CODPECA      = mb_strtoupper(trim($dados['CODPECA']), 'UTF-8');
    $PERCDESC     = $_SESSION['ORCAMENTO']['CLIENTE']['FAST'];
    if ($PERCDESC == "" || $PERCDESC == null || $PERCDESC == false) {
      $PERCDESC = 0;
    } else {
      $PERCDESC = abs($PERCDESC);  
    }

    $NUMREGIAO  = $_SESSION['ORCAMENTO']['CLIENTE']['NUMREGIAO'];
    if ($NUMREGIAO == "" || $NUMREGIAO == null || $NUMREGIAO == false) {
      $NUMREGIAO = 1;
    }

    $sql = "SELECT DISTINCT BASE2.*
              FROM(
                SELECT DISTINCT BASE.*, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
                  (SELECT ROUND(DECODE('".$NUMPR."',
                                    '1',
                                    PR.PTABELA1,
                                    '2',
                                    PR.PTABELA2,
                                    '3',
                                    PR.PTABELA3,
                                    '4',
                                    PR.PTABELA4,
                                    '5',
                                    PR.PTABELA5,
                                    '6',
                                    PR.PTABELA6,
                                    '7',
                                    PR.PTABELA7,
                                    PR.PTABELA) *
                             (1 - ((NVL(".$PERCDESC.", 0) * (-1)) / 100)),
                             2) + 0.01
                  FROM PCTABPR PR
                 WHERE PR.CODPROD = BASE.CODPROD
                   AND PR.NUMREGIAO = '".$NUMREGIAO."')
                  ELSE 0.00 END)  AS PTABELA, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
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
                 WHERE PR.CODPROD = BASE.CODPROD
                   AND PR.NUMREGIAO = '".$NUMREGIAO."')
                  ELSE 0.00 END)  AS PVENDA, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
                  (SELECT NVL(MAX(ROUND((A.PVENDAATUAL - (A.PVENDAATUAL * (A.PERCDESCAUTOR / 100))),2)),0)
                    FROM PCAUTORI A
                   WHERE DATA_UTILIZACAO IS NULL
                     AND CODFILIAL = ".$CODFILIALNF."
                     AND CODUSUR = ".$CODUSUR."
                     AND CODPLPAG = ".$CODPLPAG."
                     AND CODCLI = ".$CODCLI."
                     AND CODPROD = BASE.CODPROD)
                  ELSE 0.00 END) AS PVENDA_301, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
                  (SELECT NVL(D2.PERCDESC, 0.00) AS PERCDESC_561
                      FROM PCDESCONTO D2, PCDESCONTOITEM DI, PCGRUPOSCAMPANHAI CI
                     WHERE D2.CODDESCONTO = DI.CODDESCONTO
                       AND DI.VALOR_NUM = CI.CODGRUPO
                       AND TRUNC(SYSDATE) BETWEEN TRUNC(D2.DTINICIO) AND TRUNC(D2.DTFIM)
                       AND CI.DTEXCLUSAO IS NULL
                       AND DI.TIPO = 'GP'
                       AND D2.NUMREGIAO = ".$NUMREGIAO."
                       AND CI.CODITEM = BASE.CODPROD)
                  ELSE 0.00 END) AS PERCDESC_561, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
                  (SELECT GREATEST(E1.QTESTGER, 0) 
                     FROM PCEST E1 
                    WHERE E1.CODPROD = BASE.CODPROD 
                      AND E1.CODFILIAL = ".$CODFILIALNF.")
                  ELSE 0.00 END) AS QTESTGER, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
                  (SELECT GREATEST(E1.QTRESERV, 0) 
                     FROM PCEST E1 
                    WHERE E1.CODPROD = BASE.CODPROD 
                      AND E1.CODFILIAL = ".$CODFILIALNF.")
                  ELSE 0.00 END) AS QTRESERV, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
                  (SELECT GREATEST(E1.QTBLOQUEADA, 0) 
                     FROM PCEST E1 
                    WHERE E1.CODPROD = BASE.CODPROD 
                      AND E1.CODFILIAL = ".$CODFILIALNF.")
                  ELSE 0.00 END) AS QTBLOQUEADA, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
                  (SELECT GREATEST(E1.QTESTGER, 0)-GREATEST(E1.QTRESERV, 0)-GREATEST(E1.QTBLOQUEADA, 0)
                     FROM PCEST E1 
                    WHERE E1.CODPROD = BASE.CODPROD 
                      AND E1.CODFILIAL = ".$CODFILIALNF.")
                  ELSE 0.00 END) AS QTDISP, 
                (CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
                  (SELECT TT.CODTRIBPISCOFINS
                  FROM PCTABTRIB TT
                 WHERE TT.CODPROD = BASE.CODPROD
                   AND TT.CODFILIALNF = '".$CODFILIALNF."'
                   AND TT.UFDESTINO = '".$UFDESTINO."')
                  ELSE NULL END) AS CODTRIBPISCOFINS
  FROM (
        /*BLOCO 01 - PRODUTOS CUJO O NUMORIGINAL SEJAM IGUAL AO CODPECA*/
        SELECT 1 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR('WINTHOR') AS ORIGEM,
               P.NUMORIGINAL,
               TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
               TRUNC(P.CODPROD) AS CODPROD,
               TRUNC(P.DV) AS DV,
               TRIM(P.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
               TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               NULL AS VIDE
        FROM PCPRODUT P
        WHERE P.DTEXCLUSAO IS NULL
          AND (P.NUMORIGINAL LIKE '".$CODPECA."%' OR 'W'||P.CODPROD = '".$CODPECA."' OR P.DESCRICAO LIKE '%".$CODPECA."%')

        UNION
        /*BLOCO 02.0 - LISTA DE VIDES MESMO QUE NÃO EXISTAM PRODUTOS*/
        SELECT 2 AS ORD,
               TRIM(V.codpeca) AS CODPECA,
               TO_CHAR('VIDE') AS ORIGEM,
               NULL AS NUMORIGINAL,
               NULL AS WINTHOR,
               NULL AS CODPROD,
               NULL AS DV,
               TRIM(V.descricao) AS DESCRICAO,
               V.aplicmarca AS MARCA,
               NULL AS LOCACAO,
               TO_CHAR(DECODE(V.importado, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               TRIM(V.vide) AS VIDE
        FROM ORCVIDE V
        WHERE V.DTEXCLUSAO IS NULL
          AND V.vide IS NOT NULL
          AND V.codpeca like '".$CODPECA."%'
        UNION       
        SELECT 2 AS ORD,
               TRIM(V.vide) AS CODPECA,
               TO_CHAR('VIDE') AS ORIGEM,
               NULL AS NUMORIGINAL,
               NULL AS WINTHOR,
               NULL AS CODPROD,
               NULL AS DV,
               TRIM(V.descricao) AS DESCRICAO,
               V.aplicmarca AS MARCA,
               NULL AS LOCACAO,
               TO_CHAR(DECODE(V.importado, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               TRIM(V.codpeca) AS VIDE
        FROM ORCVIDE V
        WHERE V.DTEXCLUSAO IS NULL
          AND V.codpeca IS NOT NULL
          AND V.vide like '".$CODPECA."%'
        UNION
        /*BLOCO 02.1 - PRODUTOS CUJO O NUMORIGINAL SEJAM IGUAL AO VIDE 1 NIVEL DA CODPECA*/
        SELECT 2 AS ORD,
               V.codpeca AS CODPECA,
               TO_CHAR('VIDE') AS ORIGEM,
               TRIM(P.numoriginal) AS NUMORIGINAL,
               TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
               TRUNC(P.CODPROD) AS CODPROD,
               TRUNC(P.DV) AS DV,
               TRIM(P.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
               TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               TRIM(P.numoriginal) AS VIDE
        FROM ORCVIDE V, PCPRODUT P
        WHERE TRIM(V.vide) = TRIM(P.numoriginal)
          AND P.DTEXCLUSAO IS NULL
          AND V.DTEXCLUSAO IS NULL
          AND V.vide IS NOT NULL
          AND V.codpeca like '".$CODPECA."%'
        UNION
        /*BLOCO 02.2 - PRODUTOS CUJO O NUMORIGINAL SEJAM IGUAL AO VIDE 1 NIVEL DA CODPECA*/
        SELECT 2 AS ORD,
               TO_CHAR('".$CODPECA."') AS CODPECA,
               TO_CHAR('VIDE') AS ORIGEM,
               TRIM(P.numoriginal) AS NUMORIGINAL,
               TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
               TRUNC(P.CODPROD) AS CODPROD,
               TRUNC(P.DV) AS DV,
               TRIM(P.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
               TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               TRIM(P.numoriginal) AS VIDE
        FROM ORCVIDE V, PCPRODUT P
        WHERE TRIM(V.codpeca) = TRIM(P.numoriginal)
          AND P.DTEXCLUSAO IS NULL
          AND V.DTEXCLUSAO IS NULL
          AND V.codpeca IS NOT NULL
          AND V.vide like '".$CODPECA."%'
        UNION
        /*BLOCO 03 - PRODUTOS CUJO O NUMORIGINAL SEJAM IGUAL AO VIDE 2 NIVEL DA CODPECA*/
        SELECT 3 AS ORD,
               '".$CODPECA."' AS CODPECA,
               TO_CHAR('VIDE2') AS ORIGEM,
               P.NUMORIGINAL,
               TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
               TRUNC(P.CODPROD) AS CODPROD,
               TRUNC(P.DV) AS DV,
               TRIM(P.DESCRICAO) AS DESCRICAO,
               NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
               TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
               TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
               P.NUMORIGINAL AS VIDE
        FROM PCPRODUT P
        WHERE P.DTEXCLUSAO IS NULL
          AND P.NUMORIGINAL IN
            (
              SELECT V2.VIDE AS VIDE
              FROM ORCVIDE V2
              WHERE V2.DTEXCLUSAO IS NULL
                 AND V2.VIDE IS NOT NULL
                 AND V2.CODPECA IN
                (
                SELECT V3.VIDE AS VIDE
                FROM ORCVIDE V3
                WHERE V3.DTEXCLUSAO IS NULL
                   AND V3.VIDE IS NOT NULL
                   AND V3.CODPECA LIKE '".$CODPECA."%'
                UNION
                SELECT V3.CODPECA AS VIDE
                FROM ORCVIDE V3
                WHERE V3.DTEXCLUSAO IS NULL
                   AND V3.CODPECA IS NOT NULL
                   AND V3.VIDE LIKE '".$CODPECA."%'
                ) 
              UNION
              SELECT V2.CODPECA AS VIDE
              FROM ORCVIDE V2
              WHERE V2.DTEXCLUSAO IS NULL
                 AND V2.VIDE IS NOT NULL
                 AND V2.VIDE IN
                (
                SELECT V3.VIDE AS VIDE
                FROM ORCVIDE V3
                WHERE V3.DTEXCLUSAO IS NULL
                   AND V3.VIDE IS NOT NULL
                   AND V3.CODPECA LIKE '".$CODPECA."%'
                UNION
                SELECT V3.CODPECA AS VIDE
                FROM ORCVIDE V3
                WHERE V3.DTEXCLUSAO IS NULL
                   AND V3.CODPECA IS NOT NULL
                   AND V3.VIDE LIKE '".$CODPECA."%'
                ) 
            )
        UNION
        /*BLOCO 04 - INFO | OPCAO | NPR */
        SELECT 4 AS ORD,
              '".$CODPECA."' AS CODPECA,
             TRIM(V2.NOMEOPCAO) AS ORIGEM,
             V2.CODPECA AS NUMORIGINAL,
             NULL AS WINTHOR,
             NULL AS CODPROD,
             NULL AS DV,
             TO_CHAR(NVL(V2.DESCRICAO, 'SOB CONSULTA')) AS DESCRICAO,
             TO_CHAR(NVL(NVL(V2.APLICMARCA, V2.MARCA),  'SOB CONSULTA')) AS MARCA,
             NULL AS LOCACAO,
             TO_CHAR(DECODE(V2.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
             TRIM(V2.VIDE) AS VIDE
         FROM ORCVIDE V2
        WHERE V2.DTEXCLUSAO IS NULL
           AND V2.NOMEOPCAO NOT IN ('VIDE')
           AND V2.CODPECA LIKE '".$CODPECA."%' 
        UNION
        SELECT 4 AS ORD,
              '".$CODPECA."' AS CODPECA,
             TRIM(V2.NOMEOPCAO) AS ORIGEM,
             V2.CODPECA AS NUMORIGINAL,
             NULL AS WINTHOR,
             NULL AS CODPROD,
             NULL AS DV,
             TO_CHAR(NVL(V2.DESCRICAO, 'SOB CONSULTA')) AS DESCRICAO,
             TO_CHAR(NVL(NVL(V2.APLICMARCA, V2.MARCA),  'SOB CONSULTA')) AS MARCA,
             NULL AS LOCACAO,
             TO_CHAR(DECODE(V2.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
             TRIM(V2.CODPECA) AS VIDE
         FROM ORCVIDE V2
        WHERE V2.DTEXCLUSAO IS NULL
           AND V2.NOMEOPCAO NOT IN ('VIDE')
           AND V2.VIDE LIKE '".$CODPECA."%' 
        ) BASE) BASE2
ORDER BY BASE2.QTDISP DESC, BASE2.ORD ASC";

    // varDump2($sql);

    $_SESSION['CONSULTA'] = selectOracle($sql);
    
    if($debug) varDump2($_SESSION['CONSULTA']);
  
    $QTDISPPECA = 0;
    $listaCODPROD = [];
    foreach ($_SESSION['CONSULTA'] as $key => $value) {
      if (!empty($value['CODPROD'])) {
        if (!in_array($value['CODPROD'], $listaCODPROD)) {
          $listaCODPROD[] = $value['CODPROD'];
          $QTDISPPECA += intval($value['QTDISP']);
        } else {
          unset($_SESSION['CONSULTA'][$key]);
        }
      }
    }
    if($debug) varDump2('QTDISPPECA: '.$QTDISPPECA);

    if ($_SESSION['CONSULTA'] && count($_SESSION['CONSULTA'])>0) {

    echo '
      <div class="modal fade" id="modalConsultaPeca" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xxl" role="document">
          <div class="modal-content">
            <div class="modal-header text-center bg-info">
              <h4 class="modal-title w-100 font-weight-bold">
                Consulta Peça da peça '.$CODPECA.'
              </h4>';
    if ($_SESSION['login']["IDUSUARIO"] == "1") {
      echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    }
    echo '
            </div>
            <div class="modal-body mx-3">

            <div class="table-responsive">
            <table class="table table-bordered table-striped" >
              <thead>
                <tr>
                <th style="text-align: center;">Origem</th>
                <th style="text-align: center;">Cod Peça</th>
                <th style="text-align: center;">Vide</th>
                <th style="text-align: center;">Cod. Prod.</th>
                <th style="text-align: center;">Num. Original</th>
                <th style="text-align: center;">Descrição</th>
                <th style="text-align: center;">Marca</th>
                <th style="text-align: center;">Estoque</th>
                <th style="text-align: center;">Reservado</th>
                <th style="text-align: center;">Bloqueado</th>
                <th style="text-align: center;">Disponível</th>
                <th style="text-align: center;">Preço Venda</th>
                <th style="text-align: center;">Preço 301</th>
                <th style="text-align: center;">Desconto 561</th>
                <th style="text-align: center;"></th>
                </tr>
              </thead>
              <tbody>
              <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">';

              foreach ($_SESSION['CONSULTA'] as $key => $value) {
                // varDump2($value); 
                if (intval($value['QTDISP']) == 0) {
                  $textColor = "text-danger";
                } else {
                  switch ($value['ORIGEM']) {
                    case 'WINTHOR':   $textColor = "text-primary";  break;
                    case 'INFO':      $textColor = "text-purple";   break;
                    case 'OPCAO':     $textColor = "text-purple";   break;
                    case 'NPR':       $textColor = "text-danger";   break;
                    default:          $textColor = "text-dark";     break;
                  }

                  $value['PVENDA_MIN'] = isset($value['PVENDA_MIN']) ? moedaPHP($value['PVENDA_MIN']) : moedaPHP($value['PVENDA']);
                  if ($value['PVENDA'] < $value['PVENDA_MIN']) {
                    $value['PVENDA'] = $value['PVENDA_MIN'];
                  } else {
                    $value['PVENDA'] = $value['PVENDA'];
                  }

                  if (moedaPHP($value['PVENDA']) <= moedaPHP(0.01)) {
                    if (moedaPHP($value['PVENDA_301']) > moedaPHP(0.01)) {
                      $value['PVENDA'] = moedaPHP($value['PVENDA_301']);
                    } else {
                      $textColor = "text-purple";
                    }
                  }

                  $value['SUBTOTAL']  = ($QTPEDIDA * $value['PVENDA']);
                  if ($value['PERCDESC_561'] > 0) {
                    $value['PVENDA_561']  = ($value['PVENDA'] * (1-($value['PERCDESC_561']/100)));
                  } else {
                    $value['PVENDA_561']  = 0;
                  }
                }


                $_SESSION['CONSULTA'][$key]['ICMS'] = buscaICMS($_SESSION['ORCAMENTO']['CLIENTE']['UF'], $value['PROCEDENCIA']);
                $_SESSION['CONSULTA'][$key]['QTPEDIDA'] = $QTPEDIDA;
                $_SESSION['CONSULTA'][$key]['IDORCAMENTO'] = $IDORCAMENTO;
                $_SESSION['CONSULTA'][$key]['PVENDA']     = $value['PVENDA'];
                $_SESSION['CONSULTA'][$key]['PVENDA_MIN'] = $value['PVENDA_MIN'];
                $_SESSION['CONSULTA'][$key]['PVENDA_301'] = $value['PVENDA_301'];
                $_SESSION['CONSULTA'][$key]['PVENDA_561'] = $value['PVENDA_561'];

                echo '<tr>';
                  echo '<td class="'.$textColor.'">'.$value['ORIGEM'].'</td>';
                  echo '<td class="'.$textColor.'" '.$backgColor.'>'.$value['CODPECA'].'</td>';
                  echo '<td class="'.$textColor.'" '.$backgColor.'>'.$value['VIDE'].'</td>';
                  echo '<td class="'.$textColor.'">'.$value['CODPROD'].'-'.$value['DV'].'</td>';
                  echo '<td class="'.$textColor.'" '.$backgColor.'>'.$value['NUMORIGINAL'].'</td>';
                  echo '<td class="'.$textColor.'" '.$backgColor.'>'.$value['DESCRICAO'].'</td>';
                  echo '<td class="'.$textColor.'" '.$backgColor.'>'.$value['MARCA'].'</td>';
                  echo '<td class="'.$textColor.'" style="text-align:center">'.moeda($value['QTESTGER'],0).'</td>';
                  echo '<td class="'.$textColor.'" style="text-align:center">'.moeda($value['QTRESERV'],0)*(-1).'</td>';
                  echo '<td class="'.$textColor.'" style="text-align:center">'.moeda($value['QTBLOQUEADA'],0)*(-1).'</td>';
                  echo '<td class="'.$textColor.'" style="text-align:center">'.moeda($value['QTDISP'],0).'</td>';
                  echo '<td class="'.$textColor.'" style="text-align:center">'.moeda($value['PVENDA'],2).'</td>';
                  echo '<td class="'.$textColor.'" style="text-align:center">'.(empty($value['PVENDA_301'])?'':moeda($value['PVENDA_301'],2)).'</td>';
                  echo '<td class="'.$textColor.'" style="text-align:center">'.(empty($value['PVENDA_561'])?'':moeda($value['PVENDA_561'],2).' ('.moeda(((1-($value['PVENDA_561']/$value['PVENDA']))*100),0).'%)').'</td>';
                  echo '<td>';
                            echo '    <input name="key['.$key.']" class="custom-control-input" type="checkbox" id="customCheckbox'.$key.'">';
                            echo '    <label for="customCheckbox'.$key.'" class="custom-control-label"></label>';
                  echo '</td>';
                echo '</tr>';
               
               } // <!-- foreach -->

              echo '
                <input type="hidden" name="op" value="62">
                <input type="hidden" name="IDORCAMENTO" value="'.$IDORCAMENTO.'">
                <input type="hidden" name="CODCLI" value="'.$_SESSION['ORCAMENTO']['CAB']['CODCLI'].'">
                <input type="hidden" name="CODPECA" value="'. $CODPECA .'">
                <input type="hidden" name="QTPEDIDA" value="'. $QTPEDIDA .'">
                <input type="hidden" name="QTDISP" value="'. $value['QTDISP'] .'">
                <input type="hidden" name="PVENDA" value="'. $value['PVENDA'] .'">
                <input type="hidden" name="PVENDA_301" value="'. $value['PVENDA_301'] .'">
                <input type="hidden" name="PVENDA_561" value="'. $value['PVENDA_561'] .'">
                <button type="submit" name="acao" value="inserePecaOrcamento"  title="Adicionar Pecas" class="btn btn-primary float-end">
                  <i class="fa fa-plus-square"></i> Adicionar Peça(s)
                </button>
                <br>
              </form>
              </tbody>
              </table>
              </div>
            </div>
          </div>
        </div>
      </div>';



    } else {
      if (validaCodprodExisteOrcamento($dados)) {
        ativaPecaExixtente($dados);
        insereModal('info', 'A peça '.$dados['CODPECA'].' já existe no orçamento');
      } else {
        adicionaPecaGenerica($dados);
      }
    }

  }
}

?>


<script>
  var meuModal = document.getElementById('modalConsultaPeca');
  // meuModal.addEventListener('hide.bs.modal', function (event) {
  //   event.preventDefault();
  // });
  $( document ).ready(function() {
    $('#modalConsultaPeca').modal('show');
  });
</script>