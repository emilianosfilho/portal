CREATE OR REPLACE VIEW VIEW_OMEGA_EXTRATOPRODUTO AS
SELECT M.SEQMOV,
       M.DTMOV,
       P.CODPROD,
       P.DV,
       P.DESCRICAO,
       MA.MARCA,
       P.NUMORIGINAL,
       P.INFORMACOESTECNICAS AS LOCACAO,
       TO_CHAR(M.DTMOVLOG, 'DD-MON-YYYY HH24:MI') AS DTMOVLOG,
       M.DTCANCEL,
       M.CODOPER || ' ' || DECODE(M.CODOPER,
                                  'E',
                                  (CASE
                                    WHEN M.QT > 0 THEN
                                     'Entrada Merc'
                                    ELSE
                                     'Entrada Cancelada'
                                  END),
                                  'ED',
                                  'Dev. Cliente',
                                  'ER',
                                  'Simples Remessa',
                                  'EI',
                                  'Ajuste Invent',
                                  'S',
                                  (CASE
                                    WHEN M.QT > 0 THEN
                                     'Saida'
                                    ELSE
                                     'NF Cancelada'
                                  END),
                                  'SD',
                                  'Saida Devol',
                                  'SR',
                                  'Simples Remessa',
                                  'SI',
                                  'Saida Invent') AS CODOPER,
       M.NUMTRANSENT,
       M.NUMTRANSVENDA,
       M.NUMNOTA,
       M.QT,
       DECODE(M.CODOPER,
              'E',
              F.FORNECEDOR,
              'ER',
              F.FORNECEDOR,
              'ED',
              C.CLIENTE,
              'EI',
              E.NOME,
              'SI',
              E.NOME,
              'SR',
              E.NOME,
              C.CLIENTE) AS CLIENTE_FORNEC,
       CASE WHEN M.CODOPER LIKE 'S%' THEN (SELECT SUBSTR(U.NOME,1,(INSTR(U.NOME, ' ', 1))) FROM PCUSUARI U WHERE U.CODUSUR = M.CODUSUR) END AS VENDEDOR,

       DECODE(M.CODOPER,
              'E',
              (CASE
                WHEN M.QT > 0 THEN
                 M.QT
              END),
              'ER',
              (CASE
                WHEN M.QT > 0 THEN
                 M.QT
              END),
              'S',
              (CASE
                WHEN M.QT < 0 THEN
                 M.QT *(-1)
              END),
              'SR',
              (CASE
                WHEN M.QT < 0 THEN
                 M.QT *(-1)
              END),
              'ED',
              M.QT,
              'EI',
              M.QT) AS QTENTRADA,

       DECODE(M.CODOPER,
              'E',
              (CASE
                WHEN M.QT < 0 THEN
                 M.QT * (-1)
              END),
              'ER',
              (CASE
                WHEN M.QT < 0 THEN
                 M.QT * (-1)
              END),
              'S',
              (CASE
                WHEN M.QT > 0 THEN
                 M.QT
              END),
              'SR',
              (CASE
                WHEN M.QT > 0 THEN
                 M.QT
              END),
              'SD',
              M.QT,
              'SI',
              M.QT) * (-1) AS QTSAIDA

  FROM PCMOV M, PCPRODUT P, PCMARCA MA, PCFORNEC F, PCCLIENT C, PCEMPR E
 WHERE M.CODPROD = P.CODPROD
   AND P.CODMARCA = MA.CODMARCA
   AND M.CODOPER IN ('S', 'SD', 'SR', 'SI', 'E', 'ED', 'EI', 'ER')
   AND M.CODFORNEC = F.CODFORNEC(+)
   AND M.CODCLI = C.CODCLI(+)
   AND M.CODFUNCLANC = E.MATRICULA(+)
 ORDER BY M.DTMOVLOG;
