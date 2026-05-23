CREATE OR REPLACE VIEW VIEW_PORTAL_CONSULTAPRODUTO AS
SELECT
       BASE."CODPROD",
       BASE."DV",
       BASE."NUMORIGINAL",
       BASE."DESCRICAO",
       BASE."DESCRICAO"||' '||BASE."NUMORIGINAL" AS NOME,
       BASE."MARCA",
       BASE."LOCACAO",
       BASE."PROCEDENCIA",
       (SELECT DISTINCT CASE D.DESCRICAO
                  WHEN S.DESCRICAO THEN
                   D.DESCRICAO
                  ELSE
                   D.DESCRICAO || ' ' || S.DESCRICAO
                END AS CATEGORIA
            FROM PCPRODUT P, PCDEPTO D, PCSECAO S
           WHERE P.CODEPTO = D.CODEPTO
             AND P.CODSEC = S.CODSEC
             AND D.CODEPTO NOT IN (1, 65)
             AND S.CODSEC NOT IN (1)
             AND P.CODPROD = BASE."CODPROD") AS CATEGORIA,
       BASE."SALDO",
       BASE."PTABELA",
       ROUND((BASE."PTABELA"*1.08),2) AS PVENDA
FROM
(SELECT TRUNC(P.CODPROD) AS CODPROD,
       TRUNC(P.DV) AS DV,
       P.NUMORIGINAL,
       TRIM( REPLACE( REPLACE( P.DESCRICAO, CHR(96), ' '), CHR(39), ' ')) AS DESCRICAO,
       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
           P.MARCA) AS MARCA,
       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
       TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
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
           AND E.CODFILIAL = '1') AS SALDO,
       (SELECT ROUND(DECODE('1',
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
                            PR.PVENDA) * (1 - ((NVL(0, 0) * (-1)) / 100)),
                     2) + 0.01
          FROM PCTABPR PR
         WHERE PR.CODPROD = P.CODPROD
           AND PR.NUMREGIAO = '1') AS PTABELA
  FROM PCPRODUT P
 WHERE P.DTEXCLUSAO IS NULL
UNION
SELECT TRUNC(P.CODPROD) AS CODPROD,
       TRUNC(P.DV) AS DV,
       P.NUMORIGINAL,
       TRIM( REPLACE( REPLACE( P.DESCRICAO, CHR(96), ' '), CHR(39), ' ')) AS DESCRICAO,
       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
           P.MARCA) AS MARCA,
       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
       TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
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
       (SELECT ROUND(DECODE(1,
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
                            PR.PVENDA) * (1 - ((NVL(0, 0) * (-1)) / 100)),
                     2) + 0.01
          FROM PCTABPR PR
         WHERE PR.CODPROD = P.CODPROD
           AND PR.NUMREGIAO = '1') AS PTABELA
  FROM PCPRODUT P
 WHERE P.DTEXCLUSAO IS NULL
   AND P.NUMORIGINAL IN
       (SELECT V.VIDE AS PECA
          FROM ORCVIDE V
         WHERE V.DTEXCLUSAO IS NULL
           AND V.VIDE IS NOT NULL
           AND V.APLICMARCA NOT LIKE '%INFO%'
           AND V.APLICMARCA NOT LIKE '%OPCAO%'
        UNION
        SELECT V.CODPECA AS PECA
          FROM ORCVIDE V
         WHERE V.DTEXCLUSAO IS NULL
           AND V.CODPECA IS NOT NULL
           AND V.APLICMARCA NOT LIKE '%INFO%'
           AND V.APLICMARCA NOT LIKE '%OPCAO%')

UNION
SELECT TRUNC(P.CODPROD) AS CODPROD,
       TRUNC(P.DV) AS DV,
       P.NUMORIGINAL,
       TRIM( REPLACE( REPLACE( P.DESCRICAO, CHR(96), ' '), CHR(39), ' ')) AS DESCRICAO,
       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
           P.MARCA) AS MARCA,
       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
       TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
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
       (SELECT ROUND(DECODE(1,
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
                            PR.PVENDA) * (1 - ((NVL(0, 0) * (-1)) / 100)),
                     2) + 0.01
          FROM PCTABPR PR
         WHERE PR.CODPROD = P.CODPROD
           AND PR.NUMREGIAO = '1') AS PTABELA
  FROM PCPRODUT P
 WHERE P.DTEXCLUSAO IS NULL
   AND P.NUMORIGINAL IN
       (SELECT V.VIDE AS PECA
          FROM ORCVIDE V
         WHERE V.DTEXCLUSAO IS NULL
           AND V.VIDE IS NOT NULL
           AND V.APLICMARCA NOT LIKE '%INFO%'
           AND V.APLICMARCA NOT LIKE '%OPCAO%'
        UNION
        SELECT V.CODPECA AS PECA
          FROM ORCVIDE V
         WHERE V.DTEXCLUSAO IS NULL
           AND V.CODPECA IS NOT NULL
           AND V.APLICMARCA NOT LIKE '%INFO%'
           AND V.APLICMARCA NOT LIKE '%OPCAO%')
UNION
SELECT TRUNC(P.CODPROD) AS CODPROD,
       TRUNC(P.DV) AS DV,
       P.NUMORIGINAL,
       TRIM( REPLACE( REPLACE( P.DESCRICAO, CHR(96), ' '), CHR(39), ' ')) AS DESCRICAO,
       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
           P.MARCA) AS MARCA,
       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
       TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
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
       (SELECT ROUND(DECODE(1,
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
                            PR.PVENDA) * (1 - ((NVL(0, 0) * (-1)) / 100)),
                     2) + 0.01
          FROM PCTABPR PR
         WHERE PR.CODPROD = P.CODPROD
           AND PR.NUMREGIAO = '1') AS PTABELA
  FROM PCPRODUT P
 WHERE P.DTEXCLUSAO IS NULL
   AND P.NUMORIGINAL IN
       (SELECT V.VIDE AS PECA
          FROM ORCVIDE V
         WHERE V.DTEXCLUSAO IS NULL
           AND V.VIDE IS NOT NULL
           AND V.APLICMARCA LIKE '%INFO%')
UNION
SELECT TRUNC(P.CODPROD) AS CODPROD,
       TRUNC(P.DV) AS DV,
       P.NUMORIGINAL,
       TRIM( REPLACE( REPLACE( P.DESCRICAO, CHR(96), ' '), CHR(39), ' ')) AS DESCRICAO,
       NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA),
           P.MARCA) AS MARCA,
       TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
       TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
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
       (SELECT ROUND(DECODE(1,
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
                            PR.PVENDA) * (1 - ((NVL(0, 0) * (-1)) / 100)),
                     2) + 0.01
          FROM PCTABPR PR
         WHERE PR.CODPROD = P.CODPROD
           AND PR.NUMREGIAO = '1') AS PTABELA
  FROM PCPRODUT P
 WHERE P.DTEXCLUSAO IS NULL
   AND P.NUMORIGINAL IN
       (SELECT V.VIDE AS PECA
          FROM ORCVIDE V
         WHERE V.DTEXCLUSAO IS NULL
           AND V.VIDE IS NOT NULL
           AND V.APLICMARCA LIKE '%OPCAO%')) BASE
WHERE BASE.SALDO > 0 AND PTABELA >= 1 AND BASE.MARCA NOT IN ('MAT DIVERSO') AND BASE.DESCRICAO NOT LIKE 'LOTE %';
