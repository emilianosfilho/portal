with str as (
  SELECT DISTINCT TRIM(UPPER(P.NUMORIGINAL)) AS s
            FROM PCPRODUT P
           WHERE NVL(P.REVENDA, 'S') = 'S'
             AND P.DTEXCLUSAO IS NULL
             AND P.NUMORIGINAL NOT LIKE '%VMP%'
             AND P.NUMORIGINAL NOT LIKE '%0X0001%'
             AND P.NUMORIGINAL NOT LIKE '%LOTE%'
             AND P.CODEPTO NOT IN (65 /*IMOBILIZADOS*/)
             AND P.CODMARCA NOT IN (36765 /*PATRIMONIO*/, 84 /*CONSUMO*/)
             AND LENGTH(TRIM(UPPER(P.NUMORIGINAL)))>1
             AND TRIM(UPPER(P.NUMORIGINAL)) NOT IN
                 (SELECT B.PROD_EXTERNAL_ID FROM BGPRODUCT B)
             AND P.NUMORIGINAL LIKE '1067691%'
), rws as (
  select level x from dual
  connect by level <= 8
)
  select x, substr ( s, x, 1 ),
         ascii ( substr ( s, x, 1 ) ) aci
  from   rws 
  cross join str;