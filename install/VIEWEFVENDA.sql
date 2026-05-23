CREATE OR REPLACE VIEW VIEWEFVENDA AS
SELECT "ORIGEM","DATA","CODPECA","PRODUTO","CLIENTE","VENDEDOR","MARCAALFA","QUANTIDADE","PRECO" FROM
(SELECT   'WINT' AS ORIGEM,
              TRUNC (PCNFSAID.DTSAIDA) AS DATA,
              PCPRODUT.NUMORIGINAL AS CODPECA,
              PCPRODUT.CODPROD||'-'||PCPRODUT.DESCRICAO AS PRODUTO,
              PCCLIENT.CODCLI||'-'||PCCLIENT.CLIENTE AS CLIENTE,
              PCUSUARI.CODUSUR||'-'||PCUSUARI.NOME AS VENDEDOR,
              NVL ( (SELECT   PCMARCA.MARCA
                       FROM   PCMARCA
                      WHERE   PCMARCA.CODMARCA = PCPRODUT.CODMARCA),
                   PCPRODUT.MARCA)
                  AS MARCAALFA,
              NVL (
                  CASE
                      WHEN PCMOV.CODOPER NOT IN ('S', 'SM', 'ST', 'SB')
                      THEN
                          0
                      ELSE
                          (NVL (
                               (SELECT   PCFORMPROD.QTPRODMP
                                  FROM   PCFORMPROD
                                 WHERE   PCFORMPROD.CODPRODMP =
                                             PCPRODUT.CODPROD
                                         AND PCFORMPROD.CODPRODACAB =
                                                PCPRODUT.CODPROD),
                               1)
                           * CASE
                                 WHEN PCPRODUT.TIPOMERC = 'CB'
                                 THEN
                                     0
                                 ELSE
                                     CASE
                                         WHEN PCMOV.QT = 0 THEN PCMOV.QTCONT
                                         ELSE PCMOV.QT
                                     END
                             END)
                  END,
                  0)
                  AS QUANTIDADE,
              ROUND (
                  (NVL (
                       DECODE (
                           PCNFSAID.CONDVENDA,
                           7,
                             PCMOV.PUNITCONT
                           + NVL (PCMOV.VLFRETE, 0)
                           + NVL (PCMOV.VLOUTRASDESP, 0)
                           + NVL (PCMOV.VLFRETE_RATEIO, 0)
                           + NVL (PCMOV.VLOUTROS, 0),
                             NVL (PCMOV.PUNIT, 0)
                           + NVL (PCMOV.VLFRETE, 0)
                           + NVL (PCMOV.VLOUTRASDESP, 0)
                           + NVL (PCMOV.VLFRETE_RATEIO, 0)
                           + NVL (PCMOV.VLOUTROS, 0)),
                       0)),
                  2)
                  AS PRECO
       FROM   PCNFSAID,
              PCPRODUT,
              PCMOV,
              PCMOVCOMPLE,
              PCCLIENT,
              PCUSUARI
      WHERE       PCMOV.NUMTRANSVENDA = PCNFSAID.NUMTRANSVENDA
              AND PCMOV.CODPROD = PCPRODUT.CODPROD
              AND PCMOV.NUMTRANSITEM = PCMOVCOMPLE.NUMTRANSITEM(+)
              AND PCMOV.CODCLI = PCCLIENT.CODCLI
              AND PCMOV.CODUSUR = PCUSUARI.CODUSUR
              AND PCNFSAID.CODFISCAL NOT IN (522, 622, 722, 532, 632, 732)
              AND PCNFSAID.CONDVENDA NOT IN (4, 6, 8, 10, 11, 12, 13, 20, 98, 99)
              AND (PCNFSAID.DTCANCEL IS NULL)
              AND PCMOV.CODOPER <> 'SR'
     UNION
     SELECT   'RADAR' AS ORIGEM,
              TRUNC (V.DATA) AS DATA,
              V.PRODUTO AS CODPECA,
              V.PRODUTO,
              TO_NUMBER (V.CODCLI)||'- '||V.CLIENTE AS CLIENTE,
              '' AS VENDEDOR,
              V.MARCAALFA,
              TO_NUMBER (V.QUANTIDADE) AS QUANTIDADE,
              TO_NUMBER (V.PRECO) AS PRECO
       FROM   ORCVENDAS V) ORDER BY DATA DESC;
