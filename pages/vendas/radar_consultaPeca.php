
<?php 
// $debug = true;
if($debug) varDump2('Entrou no ConsultaPecaRadar');
if($debug) varDump2($dados);


if (!$dados["CODPECA"] || empty(trim($dados["CODPECA"]))) {
  insereModal("danger", "CODPECA inválido ao executar ConsultaPecaRadar!");
  return;
}

$str_peca = sanitizeOracleString($dados["CODPECA"]);
$str_peca = mb_strtoupper($str_peca, 'UTF-8');
// varDump2($str_peca);

$array_pecas = explode(" ", $str_peca);
// varDump2($array_pecas);

if ($array_pecas) {

  foreach ($array_pecas as $codpeca) {

      if (strlen($codpeca) < 3) {
        insereModal("warning", "a consulta precisa de ao menos 3 caracteres!");
        return;
      }

      $sql = "WITH
        prm AS (
            SELECT '$codpeca' AS peca_alvo,
                   1        AS regiao
              FROM dual
        ),

        prod_tab AS (
          SELECT p.codprod,
                 p.dv,
                 TRIM (p.numoriginal) AS numoriginal,
                 TRIM (p.descricao) AS descricao,
                 TRIM (p.marca) AS marca,
                 TRIM (p.informacoestecnicas) AS locacao,
                 t.pvenda,
                 fo.codfornec,
                 fo.fornecedor,
                 to_char(MAX (m.dtmov), 'DD/MM/YYYY') AS dtultent
          FROM               pcprodut p
                INNER JOIN pcfornec fo
                     ON (fo.codfornec = p.codfornec)
                LEFT JOIN pctabpr t
                     ON (t.codprod = p.codprod AND t.numregiao = (SELECT regiao FROM prm))
                LEFT JOIN pcmov m
                     ON (m.codprod = p.codprod 
                      AND m.codoper = 'E' 
                      AND m.dtcancel IS NULL)
         WHERE   p.dtexclusao IS NULL
      GROUP BY   p.codprod,
                 p.dv,
                 p.numoriginal,
                 p.descricao,
                 p.marca,
                 p.informacoestecnicas,
                 t.pvenda,
                 fo.codfornec,
                 fo.fornecedor),

        vide_rel AS (
            SELECT v.vide AS numoriginal
              FROM orcvide v
             WHERE v.dtexclusao IS NULL
               AND v.nomeopcao  = 'VIDE'
               AND v.codpeca    = (SELECT peca_alvo FROM prm)

            UNION

            SELECT v.codpeca AS numoriginal
              FROM orcvide v
             WHERE v.dtexclusao IS NULL
               AND v.nomeopcao  = 'VIDE'
               AND v.vide       = (SELECT peca_alvo FROM prm)
        ),

        orcvide_alvo AS (
            SELECT v.nomeopcao,
                   v.codpeca,
                   v.vide,
                   v.descricao,
                   v.aplicmarca,
                   v.preco
              FROM orcvide v
             WHERE v.dtexclusao IS NULL
               AND ((
                       v.vide    = (SELECT peca_alvo FROM prm)
                   ) OR 
                  (
                       v.codpeca = (SELECT peca_alvo FROM prm)
                    ))
              ORDER BY v.nomeopcao ASC
        )

    SELECT ord,
           origem,
           numoriginal,
           vide,
           wint,
           descricao,
           marca,
           codfornec,
           fornecedor,
           locacao,
           qtdisponivel,
           pvenda,
           dtultent
      FROM (
            SELECT 0 AS ord,
                   'WINT' AS origem,
                   p.numoriginal,
                   CAST(NULL AS VARCHAR2(50)) AS vide,
                   p.codprod || '-' || p.dv AS wint,
                   p.descricao,
                   p.marca,
                   p.codfornec,
                   p.fornecedor,
                   p.locacao,
                   p.dtultent,                    
                   GREATEST(
                       pkg_estoque.estoque_disponivel(
                           TRUNC(p.codprod),
                           '1',
                           'VP',
                           TRUNC(SYSDATE)
                       ),
                       0
                   ) AS qtdisponivel,
                   ROUND(p.pvenda, 2) + 0.01 AS pvenda
              FROM prod_tab p
             WHERE p.numoriginal LIKE (SELECT peca_alvo FROM prm)

            UNION

            SELECT 1 AS ord,
                   'VIDE' AS origem,
                   v.vide as numoriginal,
                   v.codpeca AS vide,
                   CAST(NULL AS VARCHAR2(50)) AS wint,
                   v.descricao,
                   v.aplicmarca AS marca,
                   null AS codfornec,
                   null AS fornecedor,
                   null AS locacao,                     
                   null AS dtultent,                    
                   null AS qtdisponivel,
                   null AS pvenda
              FROM orcvide_alvo v
             WHERE v.nomeopcao = 'VIDE'
               AND v.vide NOT LIKE 'W%' 
               AND v.codpeca NOT LIKE 'W%'

            UNION 

            SELECT 2 AS ord,
                   'WINT-VIDE' AS origem,
                   p.numoriginal as numoriginal,
                   (SELECT peca_alvo FROM prm) AS vide,
                   p.codprod || '-' || p.dv AS wint,
                   p.descricao,
                   p.marca,
                   p.codfornec,
                   p.fornecedor,
                   p.locacao,
                   p.dtultent,                    
                   GREATEST(
                       pkg_estoque.estoque_disponivel(
                           TRUNC(p.codprod),
                           '1',
                           'VP',
                           TRUNC(SYSDATE)
                       ),
                       0
                   ) AS qtdisponivel,
                   ROUND(p.pvenda, 2) + 0.01 AS pvenda
              FROM prod_tab p
             WHERE p.numoriginal IN (SELECT vr.numoriginal FROM vide_rel vr)

            UNION 

            SELECT 3 AS ord,
                   v.nomeopcao AS origem,
                   v.codpeca AS numoriginal,
                   v.vide,
                   CAST(NULL AS VARCHAR2(50)) AS wint,
                   v.descricao,
                   v.aplicmarca AS marca,
                   null AS codfornec,
                   null AS fornecedor,
                   null AS locacao,                     
                   null AS dtultent,                    
                   null AS qtdisponivel,
                   TO_NUMBER(v.preco) AS pvenda
              FROM orcvide_alvo v
             WHERE v.nomeopcao <> 'VIDE'

           )
         ORDER BY ord ASC, numoriginal ASC";
      // varDump2($sql); die();

      $ret = selectOracle($sql);

      if (!isset($_SESSION['RADAR'])) {
        $_SESSION['RADAR'] = [];
      }

      if (empty($ret)) {
        $map = "DIGITACAO|{$codpeca}|||";
        if (!isset($_SESSION['RADAR'][$map])) {
          $_SESSION['RADAR'][$map] = [
            'ORD' => 0,
            'ORIGEM' => 'DIGITACAO',
            'NUMORIGINAL' => $codpeca,
            'VIDE' => '',
            'WINT' => '',
            'DESCRICAO' => '**********',
            'MARCA' => '**********',
            'QTDISPONIVEL' => 0,
            'PVENDA' => 0
          ];
        }

      } else {

        foreach ($ret as $value) {
          $map = ($value['ORIGEM'] ?? '') . "|" .
                 ($value['NUMORIGINAL'] ?? '') . "|" .
                 ($value['VIDE'] ?? '') . "|" .
                 ($value['MARCA'] ?? '') . "|" .
                 ($value['WINT'] ?? '');

          if (!isset($_SESSION['RADAR'][$map])) {
            $_SESSION['RADAR'][$map] = $value;
          }
        }

      }
  }
}

