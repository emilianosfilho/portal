<?php  

function buscaDadosPCPRODUT(int $CODPROD){
  $sql = "SELECT
            trunc(P.CODPROD) as CODPROD,
            P.DV,
            trim(P.CODAUXILIAR) as CODAUXILIAR,
            P.CODPRODPRINC,
            P.CODPRODMASTER,
            trim(P.NUMORIGINAL) as NUMORIGINAL,
            trim(P.DESCRICAO) as DESCRICAO,
            trim(P.DESCRICAO1) as DESCRICAO1,
            trim(P.DESCRICAO2) as DESCRICAO2,
            trim(P.CODFAB) as CODFAB,
            P.CODMARCA,
            P.MARCA,
            P.CODFORNEC,
            P.NBM,
            P.CODNCMEX,
            P.PCOMREP1,
            P.PCOMINT1,
            P.PCOMEXT1,
            P.CODEPTO,
            P.CODSEC,
            trim(P.INFORMACOESTECNICAS) AS LOCACAO,
            P.REVENDA,
            P.TIPOMERC,
            P.OBS2,
            P.DTEXCLUSAO,
            P.MULTIPLO
        FROM PCPRODUT P
        WHERE P.CODPROD = ".$CODPROD;
  if($ret = selectOracle($sql)){
    // varDump2($sql);
    // varDump2($ret);
    return reset($ret);
  } else {
    // varDump2($ret);
    return false;
  }
}

function validaPrecoProdut(int $CODPROD){
  $sql = "SELECT   p.descricao,
                   p.numoriginal,
                   m.marca,
                   NVL (pr.pvenda, pr.ptabela) AS pvenda_old
              FROM pcprodut p, pcmarca m, pctabpr pr
             WHERE p.codmarca = m.codmarca
               AND p.codprod = pr.codprod
               AND pr.numregiao = 1
               AND p.codprod =  {$CODPROD}";
  varDump2($sql);
  return reset(selectOracle($sql));
}

function validaCodprodDv($CODPROD, $DV){
  $sql = "SELECT   p.descricao,
                   p.numoriginal,
                   m.marca,
                   NVL (pr.pvenda, pr.ptabela) AS pvenda_old
              FROM pcprodut p, pcmarca m, pctabpr pr
             WHERE p.codmarca = m.codmarca
               AND p.codprod = pr.codprod
               AND pr.numregiao = 1
               AND p.codprod =  {$CODPROD}
               AND p.dv = {$DV}";
  // varDump2($sql);
  return reset(selectOracle($sql));
}

function buscaPROXNUMPRODUT(){
  $sql = "SELECT NVL(MAX(CODPROD),0)+1 AS PROXNUMPRODUT FROM PCPRODUT";
  $ret = selectOracle($sql);
  if (!empty($ret[0]['PROXNUMPRODUT'])) {
    return $ret[0]['PROXNUMPRODUT'];
  } else {
    exibeMensagem("ERRO AO BUSCAR O PROXIMO CODPROD VALIDO");
    die();
  }
}

function atualizaPROXNUMPRODUT(){
  $sql = "UPDATE PCCONSUM 
             SET PROXNUMPRODUT = (SELECT NVL(MAX(CODPROD),0)+1 AS PROXNUMPRODUT FROM PCPRODUT)
           WHERE ROWNUM = 1";
  // VarDump2($sql); 
  return executarOracle($sql);
}

function validaCODFORNEC($CODFORNEC){
  $sql = "SELECT f.codfornec,
                 UPPER (TRIM (f.fornecedor)) AS fornecedor,
                 f.estado,
                 f.tipofornec,
                 DECODE (f.dtexclusao, NULL, 'N', 'S') AS excluido
          FROM   pcfornec f
         WHERE   f.codfornec = {$CODFORNEC}";
  if ($ret = selectOracle($sql)) {
    return reset($ret);
  } else {
    return false;
  }
}

function validaProdutoExiste($NUMORIGINAL, $CODMARCA){
  $sql = "SELECT CODPROD
            FROM PCPRODUT
           WHERE DTEXCLUSAO IS NULL
             AND TRIM(NUMORIGINAL) = '".$NUMORIGINAL."'
             AND CODMARCA = ".$CODMARCA."";
  if ($ret = selectOracle($sql)) {
    return $ret[0]['CODPROD']; 
  } else {
    return 'NOVO';
  }
}


function validaNCM(int $nbm){
  $sql = "SELECT codncm,
                 codncmex,
                 descricao as CODNCMDESC
          FROM   pcncm
         WHERE   codncm = $nbm";
  if ($ret = selectOracle($sql) ){
    return reset($ret);
  } else {
    return false;
  }
}

function validaCodorigem(int $codorigem){
  $sql = "SELECT CODORIGEM, IMPORTADO, DESCRICAO as ORIGEM 
          FROM VMP_ORIGEMST 
          WHERE CODORIGEM = $codorigem";
  if ($ret = selectOracle($sql) ){
    return reset($ret);
  } else {
    return false;
  }
}


function salvarNovaMarca($dados){
  $sql1 = "SELECT * FROM PCMARCA WHERE trim(MARCA) = '".$dados['MARCA']."'";
  $ret1 = selectOracle($sql1);
  if (!$ret1) {
    $sql = "INSERT INTO PCMARCA
              (CODMARCA, MARCA, ATIVO, DTCADASTRO, DTULTALTER)
            VALUES
              ((SELECT (MAX(CODMARCA) + 1) FROM PCMARCA), '".$dados['MARCA']."', 'S', SYSDATE, SYSDATE)";
    $ret = executarOracle($sql);
    return $ret;
  } else {
    return true;
  }
}

function validaMARCA($MARCA){
  $sql = "SELECT CODMARCA, UPPER(TRIM(MARCA)) AS MARCA, NVL(ATIVO, 'N') AS ATIVO 
            FROM PCMARCA 
           WHERE UPPER(TRIM(MARCA)) = '".$MARCA."'
           ORDER BY ATIVO ASC, CODMARCA ASC";
  $ret = selectOracle($sql);
  // varDump2($sql);
  // varDump2($ret);
  if ($ret) {
    return reset($ret);
  } else {
    return false;
  }
}




function buscaCodMarca($MARCA){

    $MARCA = strtoupper(TRIM($MARCA));
    $sql = "SELECT CODMARCA FROM PCMARCA WHERE MARCA = '".$MARCA."'";
    //VarDump2($sql);
    $return = selectOracle($sql);
    //VarDump2($return);

    if($return === false){
      return false;
    } else {
        return $return[0]['CODMARCA'];
    }
}

function buscaDigitoVerificador($codprod){
  
  $codexplodido = strval($codprod);
  // varDump2($codexplodido);

  $tamanho = strlen($codexplodido);
  // varDump2($tamanho);

  $soma = 0;
  for ($i=0; $i < $tamanho; $i++) { 
      $soma += (($tamanho+1)-$i) * $codexplodido[$i];
  }
  // varDump2($soma);

  $resto = ($soma % 11);
  // varDump2($resto);

  if ($resto == 0) {
      $dv = 1;
  } else if ($resto == 1) {
      $dv = 0;
  } else {
      $dv = (11 - $resto);
  }
  // varDump2($dv);
  return $dv;

}

function buscaParametros(){
  $sql = "SELECT * FROM ORCPARAMETROS WHERE TIPO = 'CAD_PRODUTO'";
  $PARAM = array();

  if ($ret = selectOracle($sql)){
    foreach ($ret as $key => $value) {
      $PARAM[$value['PARAMETRO']] = $value['VALOR'];
    }
    if (count($PARAM)>0) {
      return $PARAM;
    } else {
      return false;
    }
  } else {
    return false;
  }

}

function insertPCPRODUT(array $PARAM, array $PROD_NEW) {
 
  if (!isset($PROD_NEW['CODPROD']) || trim($PROD_NEW['CODPROD']) === '') {
    throw new Exception('ERRO insertPCPRODUT: CODPROD inválido.');
  } 
  if (!isset($PROD_NEW['DV']) || trim($PROD_NEW['DV']) === '') {
    throw new Exception('ERRO insertPCPRODUT: DV inválido.');
  }
  
  $sql = "INSERT INTO PCPRODUT(DTCADASTRO,DTULTALTCOM,DESCRICAO2,CODFUNCCADASTRO,CODFUNCULTALTER,CODPROD,DV,CODAUXILIAR,CODPRODPRINC,CODPRODMASTER,NUMORIGINAL,DESCRICAO,DESCRICAO1,CODFAB,CODMARCA,CODFORNEC,NBM,CODNCMEX,MARCA,PCOMINT1,PCOMREP1,PCOMEXT1,CODEPTO,CODSEC,INFORMACOESTECNICAS,REVENDA,TIPOMERC,IMPORTADO,MULTIPLO,ACEITAVENDAFRACAO,ALTURAM3,ANTIDUMPING,APTO,AUTORIZATIPO4,CAMPANHA,CHECARMULTIPLOVENDABNF,CODNESTLETERCEIROS,COMISSAOFIXA,COMPRIMENTOM3,CONCILIAIMPORTACAO,CONFAZ,CONTROLAVALIDADEDOLOTE,CUSTOREP,CUSTOREPTAB,EMBALAGEM,ESTOQUEPORLOTE,EXPORTABALANCA,FRACAOSEPARACAO,FRETEESPECIAL,LARGURAM3,MEDICAMENTOHOSPITALAR,MODULO,MOEDA,NATUREZAPRODUTO,NUMERO,PASSELIVRE,PCKROTATIVO,PERCALIQEXT,PERCALIQINT,PERCBON,PERCBONOUTRAS,PERCCOMMOT,PERCDESC1,PERCDESC2,PERCDESC3,PERCDESC4,PERCDESC5,PERCDESC6,PERCDESC7,PERCDESC8,PERCDESC9,PERCDESC10,PERCDESPADICIONAL,PERCFRETE,PERCFRETEFOB,PERCICMRED,PERCIVA,PERCIPI,PERCOFINS,PERCOUTRASDESP,PERICM,PERICMSANTECIPADO,PERPIS,PESOBRUTO,PESOLIQ,PISCOFINSRETIDO,PRAZOEXPURGO,PRECOFIXO,PRODUSAENGRADADO,PSICOTROPICO,QTTOTPAL,QTUNIT,QTUNITCX,RUA,SOMENTETV3,STATUSSUCATA,TEMREPOS,TIPOARM,TIPOCALCST,TIPODESCARGA,TIPOESTOQUE,TIPOPROD,TIPORESTRICAOMED,TIPOTRIBUTMEDIC,UNIDADE,UNIDADEMASTER,USAALIQCREDICMSDIFER,USACLASSIFICACAO,USALICENCAIMPORTACAO,USAWMS,VLBONIFIC,VLPAUTA,VOLUME) VALUES (
  /*DTCADASTRO*/ SYSDATE,
  /*DTULTALTCOM*/ SYSDATE,
  /*DESCRICAO2*/ 'CADASTRO VIA PORTAL',
  /*CODFUNCCADASTRO*/ '".$_SESSION['login']['MATRICULA']."',
  /*CODFUNCULTALTER*/ '".$_SESSION['login']['MATRICULA']."',
  /*CODPROD*/ '".$PROD_NEW["CODPROD"]."',
  /*DV*/ '".$PROD_NEW["DV"]."',
  /*CODAUXILIAR*/ '".$PROD_NEW["CODPROD"]."',
  /*CODPRODPRINC*/ '".$PROD_NEW["CODPROD"]."',
  /*CODPRODMASTER*/ '".$PROD_NEW["CODPROD"]."',
  /*NUMORIGINAL*/ '".sanitizeOracleString($PROD_NEW["NUMORIGINAL"], 0, true)."',
  /*DESCRICAO*/ '".sanitizeOracleString($PROD_NEW["DESCRICAO"], 0, true)."',
  /*DESCRICAO1*/ '".sanitizeOracleString($PROD_NEW["DESCRICAO"], 0, true)."',
  /*CODFAB*/ '".sanitizeOracleString($PROD_NEW["CODFAB"], 0, true)."',
  /*CODMARCA*/ '".$PROD_NEW["CODMARCA"]."',
  /*CODFORNEC*/ '".$PROD_NEW["CODFORNEC"]."',
  /*NBM*/ '".$PROD_NEW["NBM"]."',
  /*CODNCMEX*/ '".$PROD_NEW["NBM"].".',
  /*MARCA*/ '".sanitizeOracleString($PROD_NEW["MARCA"], 0, true)."',
  /*PCOMINT1*/ '".moedaPHP($PROD_NEW["PCOMINT1"])."',
  /*PCOMREP1*/ '".moedaPHP($PROD_NEW["PCOMINT1"])."',
  /*PCOMEXT1*/ '".moedaPHP($PROD_NEW["PCOMINT1"])."',
  /*CODEPTO*/ '".$PROD_NEW["CODEPTO"]."',
  /*CODSEC*/ '".$PROD_NEW["CODSEC"]."',
  /*INFORMACOESTECNICAS*/ '".sanitizeOracleString($PROD_NEW["LOCACAO"], 0, true)."',
  /*REVENDA*/ '".$PROD_NEW['REVENDA']."',
  /*TIPOMERC*/ '".$PROD_NEW['TIPOMERC']."',
  /*IMPORTADO*/ '".$PROD_NEW['IMPORTADO']."',
  /*MULTIPLO*/ '".$PARAM['MULTIPLO']."',
  /*ACEITAVENDAFRACAO*/ '".$PARAM["ACEITAVENDAFRACAO"]."',
  /*ALTURAM3*/ '".$PARAM["ALTURAM3"]."',
  /*ANTIDUMPING*/ '".$PARAM["ANTIDUMPING"]."',
  /*APTO*/ '".$PARAM["APTO"]."',
  /*AUTORIZATIPO4*/ '".$PARAM["AUTORIZATIPO4"]."',
  /*CAMPANHA*/ '".$PARAM["CAMPANHA"]."',
  /*CHECARMULTIPLOVENDABNF*/ '".$PARAM["CHECARMULTIPLOVENDABNF"]."',
  /*CODNESTLETERCEIROS*/ '".$PARAM["CODNESTLETERCEIROS"]."',
  /*COMISSAOFIXA*/ '".$PARAM["COMISSAOFIXA"]."',
  /*COMPRIMENTOM3*/ '".$PARAM["COMPRIMENTOM3"]."',
  /*CONCILIAIMPORTACAO*/ '".$PARAM['CONCILIAIMPORTACAO']."',
  /*CONFAZ*/ '".$PARAM['CONFAZ']."',
  /*CONTROLAVALIDADEDOLOTE*/ '".$PARAM['CONTROLAVALIDADEDOLOTE']."',
  /*CUSTOREP*/ '".$PARAM['CUSTOREP']."',
  /*CUSTOREPTAB*/ '".$PARAM['CUSTOREPTAB']."',
  /*EMBALAGEM*/ '".$PARAM['EMBALAGEM']."',
  /*ESTOQUEPORLOTE*/ '".$PARAM['ESTOQUEPORLOTE']."',
  /*EXPORTABALANCA*/ '".$PARAM['EXPORTABALANCA']."',
  /*FRACAOSEPARACAO*/ '".$PARAM['FRACAOSEPARACAO']."',
  /*FRETEESPECIAL*/ '".$PARAM['FRETEESPECIAL']."',
  /*LARGURAM3*/ '".$PARAM['LARGURAM3']."',
  /*MEDICAMENTOHOSPITALAR*/ '".$PARAM['MEDICAMENTOHOSPITALAR']."',
  /*MODULO*/ '".$PARAM['MODULO']."',
  /*MOEDA*/ '".$PARAM['MOEDA']."',
  /*NATUREZAPRODUTO*/ '".$PARAM['NATUREZAPRODUTO']."',
  /*NUMERO*/ '".$PARAM['NUMERO']."',
  /*PASSELIVRE*/ '".$PARAM['PASSELIVRE']."',
  /*PCKROTATIVO*/ '".$PARAM['PCKROTATIVO']."',
  /*PERCALIQEXT*/ '".$PARAM['PERCALIQEXT']."',
  /*PERCALIQINT*/ '".$PARAM['PERCALIQINT']."',
  /*PERCBON*/ '".$PARAM['PERCBON']."',
  /*PERCBONOUTRAS*/ '".$PARAM['PERCBONOUTRAS']."',
  /*PERCCOMMOT*/ '".$PARAM['PERCCOMMOT']."',
  /*PERCDESC1*/ '".$PARAM['PERCDESC1']."',
  /*PERCDESC2*/ '".$PARAM['PERCDESC2']."',
  /*PERCDESC3*/ '".$PARAM['PERCDESC3']."',
  /*PERCDESC4*/ '".$PARAM['PERCDESC4']."',
  /*PERCDESC5*/ '".$PARAM['PERCDESC5']."',
  /*PERCDESC6*/ '".$PARAM['PERCDESC6']."',
  /*PERCDESC7*/ '".$PARAM['PERCDESC7']."',
  /*PERCDESC8*/ '".$PARAM['PERCDESC8']."',
  /*PERCDESC9*/ '".$PARAM['PERCDESC9']."',
  /*PERCDESC10*/ '".$PARAM['PERCDESC10']."',
  /*PERCDESPADICIONAL*/ '".$PARAM['PERCDESPADICIONAL']."',
  /*PERCFRETE*/ '".$PARAM['PERCFRETE']."',
  /*PERCFRETEFOB*/ '".$PARAM['PERCFRETEFOB']."',
  /*PERCICMRED*/ '".$PARAM['PERCICMRED']."',
  /*PERCIVA*/ '".$PARAM['PERCIVA']."',
  /*PERCIPI*/ '".$PARAM['PERCIPI']."',
  /*PERCOFINS*/ '".$PARAM['PERCOFINS']."',
  /*PERCOUTRASDESP*/ '".$PARAM['PERCOUTRASDESP']."',
  /*PERICM*/ '".$PARAM['PERICM']."',
  /*PERICMSANTECIPADO*/ '".$PARAM['PERICMSANTECIPADO']."',
  /*PERPIS*/ '".$PARAM['PERPIS']."',
  /*PESOBRUTO*/ '".$PARAM['PESOBRUTO']."',
  /*PESOLIQ*/ '".$PARAM['PESOLIQ']."',
  /*PISCOFINSRETIDO*/ '".$PARAM['PISCOFINSRETIDO']."',
  /*PRAZOEXPURGO*/ '".$PARAM['PRAZOEXPURGO']."',
  /*PRECOFIXO*/ '".$PARAM['PRECOFIXO']."',
  /*PRODUSAENGRADADO*/ '".$PARAM['PRODUSAENGRADADO']."',
  /*PSICOTROPICO*/ '".$PARAM['PSICOTROPICO']."',
  /*QTTOTPAL*/ '".$PARAM['QTTOTPAL']."',
  /*QTUNIT*/ '".$PARAM['QTUNIT']."',
  /*QTUNITCX*/ '".$PARAM['QTUNITCX']."',
  /*RUA*/ '".$PARAM['RUA']."',
  /*SOMENTETV3*/ '".$PARAM['SOMENTETV3']."',
  /*STATUSSUCATA*/ '".$PARAM['STATUSSUCATA']."',
  /*TEMREPOS*/ '".$PARAM['TEMREPOS']."',
  /*TIPOARM*/ '".$PARAM['TIPOARM']."',
  /*TIPOCALCST*/ '".$PARAM['TIPOCALCST']."',
  /*TIPODESCARGA*/ '".$PARAM['TIPODESCARGA']."',
  /*TIPOESTOQUE*/ '".$PARAM['TIPOESTOQUE']."',
  /*TIPOPROD*/ '".$PARAM['TIPOPROD']."',
  /*TIPORESTRICAOMED*/ '".$PARAM['TIPORESTRICAOMED']."',
  /*TIPOTRIBUTMEDIC*/ '".$PARAM['TIPOTRIBUTMEDIC']."',
  /*UNIDADE*/ '".$PARAM['UNIDADE']."',
  /*UNIDADEMASTER*/ '".$PARAM['UNIDADEMASTER']."',
  /*USAALIQCREDICMSDIFER*/ '".$PARAM['USAALIQCREDICMSDIFER']."',
  /*USACLASSIFICACAO*/ '".$PARAM['USACLASSIFICACAO']."',
  /*USALICENCAIMPORTACAO*/ '".$PARAM['USALICENCAIMPORTACAO']."',
  /*USAWMS*/ '".$PARAM['USAWMS']."',
  /*VLBONIFIC*/ '".$PARAM['VLBONIFIC']."',
  /*VLPAUTA*/ '".$PARAM['VLPAUTA']."',
  /*VOLUME*/ '".$PARAM['VOLUME']."')";
  // varDump2($PROD_NEW);
  // varDump2($sql); 
  // die();

  executarOracle($sql);

  $REGISTRO  = "{CODPROD_NEW:".$PROD_NEW['CODPROD']."}";
  $REGISTRO .= "{NUMORIGINAL_NEW:".$PROD_NEW['NUMORIGINAL']."}";
  $REGISTRO .= "{DESCRICAO_NEW:".$PROD_NEW['DESCRICAO']."}";
  $REGISTRO .= "{CODFAB_NEW:".$PROD_NEW["CODFAB"]."}";
  $REGISTRO .= "{CODMARCA_NEW:".$PROD_NEW['CODMARCA']."}";
  $REGISTRO .= "{CODFORNEC_NEW:".$PROD_NEW['CODFORNEC']."}";
  $REGISTRO .= "{NBM_NEW:".$PROD_NEW['NBM']."}";
  $REGISTRO .= "{INFORMACOESTECNICAS_NEW:".$PROD_NEW['LOCACAO']."}";
  $REGISTRO .= "{REVENDA_NEW:".$PROD_NEW['REVENDA']."}";
  $REGISTRO .= "{TIPOMERC_NEW:".$PROD_NEW['TIPOMERC']."}";
  geraLogAlteracao('INSERT', 'PCPRODUT', $REGISTRO);
  atualizaPROXNUMPRODUT();
  return true;

}

function updatePCPRODUT(array $PARAM, array $PROD_NEW){

  if (empty($PARAM) || $PARAM === false) {
    varDump2("ERRO ao executar updatePCPRODUT. PARAM inválido");
    return false;
  }
  // varDump2($PARAM);

  if (!isset($PROD_NEW['CODPROD']) || $PROD_NEW['CODPROD'] == "") {
    varDump2("ERRO ao executar updatePCPRODUT. CODPROD inválido");
    return false;
  }

  $PROD_OLD = buscaDadosPCPRODUT($PROD_NEW['CODPROD']);
  if ($PROD_OLD === false){
    varDump2("ERRO ao executar buscaDadosPCPRODUT. CODPROD inválido");
    return false;
  }
  // varDump2($PROD_OLD);

  $PROD_NEW["DESCRICAO"] = max($PROD_OLD["DESCRICAO"], $PROD_NEW["DESCRICAO"]);
  $PROD_NEW["MULTIPLO"] = max(1, intval($PROD_OLD["MULTIPLO"]), intval($PROD_NEW["MULTIPLO"]));

  if (($PROD_OLD["LOCACAO"] <> "9999")){
    $PROD_NEW["LOCACAO"] = $PROD_OLD["LOCACAO"];
  } 
  // varDump2($PROD_NEW);
  // die();


  $sql = "UPDATE PCPRODUT
             SET DESCRICAO2 = 'CADASTRO VIA PORTAL',
                 DTULTALTCOM = SYSDATE,
                 OBS2 = '',
                 DTEXCLUSAO = '',
                 CODFUNCULTALTER = '".$_SESSION['login']['MATRICULA']."',
                 NUMORIGINAL = '".sanitizeOracleString($PROD_NEW["NUMORIGINAL"], 0, true)."',
                 DESCRICAO = '".sanitizeOracleString($PROD_NEW["DESCRICAO"], 0, true)."',
                 DESCRICAO1 = '".sanitizeOracleString($PROD_NEW["DESCRICAO"], 0, true)."',
                 CODFAB = '".sanitizeOracleString($PROD_NEW["CODFAB"], 0, true)."',
                 INFORMACOESTECNICAS = '".sanitizeOracleString($PROD_NEW["LOCACAO"], 0, true)."',
                 MARCA = '".sanitizeOracleString($PROD_NEW["MARCA"], 0, true)."',
                 CODAUXILIAR = '".$PROD_NEW["CODPROD"]."',
                 CODPRODPRINC = '".$PROD_NEW["CODPROD"]."',
                 CODPRODMASTER = '".$PROD_NEW["CODPROD"]."',
                 CODMARCA = '".$PROD_NEW["CODMARCA"]."',
                 CODFORNEC = '".$PROD_NEW["CODFORNEC"]."',
                 NBM = '".$PROD_NEW["NBM"]."',
                 CODNCMEX = '".$PROD_NEW["NBM"].".',
                 PCOMINT1 = '".moedaPHP($PROD_NEW["PCOMINT1"])."',
                 PCOMREP1 = '".moedaPHP($PROD_NEW["PCOMINT1"])."',
                 PCOMEXT1 = '".moedaPHP($PROD_NEW["PCOMINT1"])."',
                 CODEPTO = '".$PROD_NEW["CODEPTO"]."',
                 CODSEC = '".$PROD_NEW["CODSEC"]."',
                 REVENDA = '".$PROD_NEW['REVENDA']."',
                 TIPOMERC = '".$PROD_NEW['TIPOMERC']."',
                 IMPORTADO = '".$PROD_NEW['IMPORTADO']."',
                 MULTIPLO = '".$PROD_NEW['MULTIPLO']."'
           WHERE CODPROD = {$PROD_NEW['CODPROD']} 
             AND DV = {$PROD_NEW['DV']}";
  // varDump2($sql); die();

  if(executarOracle($sql)){
    $REGISTRO  = "{CODPROD_NEW:".$PROD_NEW['CODPROD']."}";
    $REGISTRO .= "{NUMORIGINAL_NEW:".$PROD_NEW['NUMORIGINAL']."}";
    $REGISTRO .= "{DESCRICAO_NEW:".$PROD_NEW['DESCRICAO']."}";
    $REGISTRO .= "{CODFAB_NEW:".$PROD_NEW["CODFAB"]."}";
    $REGISTRO .= "{CODMARCA_NEW:".$PROD_NEW['CODMARCA']."}";
    $REGISTRO .= "{CODFORNEC_NEW:".$PROD_NEW['CODFORNEC']."}";
    $REGISTRO .= "{NBM_NEW:".$PROD_NEW['NBM']."}";
    $REGISTRO .= "{INFORMACOESTECNICAS_NEW:".$PROD_NEW['LOCACAO']."}";
    $REGISTRO .= "{REVENDA_NEW:".$PROD_NEW['REVENDA']."}";
    $REGISTRO .= "{TIPOMERC_NEW:".$PROD_NEW['TIPOMERC']."}";
    geraLogAlteracao('UPDATE', 'PCPRODUT', $REGISTRO);
    return true;
  } else {
    return false;
  }
}


function updateMassaPCPRODUT($dados){
  $sql = "UPDATE PCPRODUT
             SET DTULTALTCOM         = SYSDATE,
                 DESCRICAO2          = 'ATUALIZADO VIA PORTAL',
                 CODFUNCULTALTER     = '".$_SESSION['login']['MATRICULA']."',
                 NUMORIGINAL         = '".$dados['NUMORIGINAL']."',
                 CODAUXILIAR         = '".$dados['CODPROD']."',
                 CODPRODPRINC        = '".$dados['CODPROD']."',
                 CODPRODMASTER       = '".$dados['CODPROD']."',
                 DESCRICAO           = '".$dados['DESCRICAO']."',
                 DESCRICAO1          = '".$dados['DESCRICAO']."',
                 CODMARCA            = '".$dados['CODMARCA']."',
                 MARCA               = '".$dados['MARCA']."',
                 CODEPTO             = '".$dados['CODEPTO']."',
                 CODSEC              = '".$dados['CODSEC']."',
                 INFORMACOESTECNICAS = '".$dados['LOCACAO']."'
           WHERE CODPROD = ".$dados['CODPROD']."
             AND DV = ".$dados['DV'];
  // varDump2($sql);
  return executarOracle($sql);
}

function validaCODEPTO(int $CODEPTO){
  $sql = "SELECT   d.codepto, d.descricao AS departamento, NVL(d.ativo, 'S') as ativo
            FROM   pcdepto d
           WHERE   d.codepto = {$CODEPTO}";
  if ($ret = selectOracle($sql)) {
    // varDump2($ret); 
    // die();
    if (count($ret) == 1) {
      return reset($ret);
    } else {
      foreach ($ret as $key => $value) {
        if ($value["ATIVO"] == "S") {
          return $ret[$key];
        }
      }
      return reset($ret);
    }
  } else {
    return false;
  }
}

function validaCODSEC(int $CODSEC){
  $sql = "SELECT   s.codsec, s.descricao as secao, decode(s.dtexclusao, null, 'S', 'N') as ativo, s.codepto
            FROM   pcsecao s
           WHERE   s.codsec = {$CODSEC}";
  if ($ret = selectOracle($sql)) {
    // varDump2($ret); 
    // die();
    if (count($ret) == 1) {
      return reset($ret);
    } else {
      foreach ($ret as $key => $value) {
        if ($value["ATIVO"] == "S") {
          return $ret[$key];
        }
      }
      return reset($ret);
    }
  } else {
    return false;
  }
}


function validaSituacaoProduto(string $numoriginal, int $codmarca){

  $sql = "SELECT   p.codprod,
                   p.dv,
                   p.codmarca,
                   TRIM (p.numoriginal) AS numoriginal,
                   TRIM (p.descricao) AS descricao
            FROM   pcprodut p
           WHERE   p.dtexclusao is null
             AND   p.numoriginal = '$numoriginal'
             AND   p.codmarca = $codmarca
        ORDER BY   dtcadastro desc";
  // varDump2($sql); die();

  if ($ret = selectOracle($sql)) {
    return reset($ret);
  } else {
    return false;
  }
}

function limpaCamposPCPRODUT($CODPROD){
  $sql = "UPDATE PCPRODUT 
             SET DTULTALTCAD = SYSDATE, 
                 CODFUNCULTALTER = '".$_SESSION['login']['MATRICULA']."',
                 DTEXCLUSAO IS NULL,
                 OBS2 IS NULL
           WHERE CODPROD = ".$CODPROD;
  return executarOracle($sql);
}


function atualizaDadosPCPRODUT($debug = false, $CODPRODVALIDO = false, $DADOSNEW = array()){
  // varDump2($DADOSNEW); die();

  $sql = "SELECT ROWIDTOCHAR(ROWID) AS ROWIDCHAR, 
                 NBM, 
                 CODNCMEX, 
                 CODFAB, 
                 INFORMACOESTECNICAS,
                 PCOMINT1,
                 PCOMREP1,
                 PCOMEXT1,
                 CODFORNEC
            FROM PCPRODUT 
           WHERE CODPROD = ".$CODPRODVALIDO;
  $DADOSOLD = selectOracle($sql);
  $DADOSOLD = $DADOSOLD[0];

  if ($debug) varDump2($DADOSOLD);

  $STR_DADOSOLD  = "";
  $STR_DADOSNEW  = "";
  $criterio_update = "";

  if ($DADOSOLD['NBM'] != $DADOSNEW['NBM']) {
    $STR_DADOSOLD .= "{NBM=".$DADOSOLD['NBM']."}";
    $STR_DADOSNEW .= "{NBM=".$DADOSNEW['NBM']."}";
    $criterio_update .= "   ,NBM = '".$DADOSNEW['NBM']."'";
  }
  if ($DADOSOLD['CODNCMEX'] != $DADOSNEW['NBM'].".") {
    $STR_DADOSOLD .= "{CODNCMEX=".$DADOSOLD['CODNCMEX']."}";
    $STR_DADOSNEW .= "{CODNCMEX=".$DADOSNEW['NBM'].".}";
    $criterio_update .= "   ,CODNCMEX = '".$DADOSNEW['NBM'].".'";
  }
  if ($DADOSOLD['CODFAB'] != $DADOSNEW['CODFAB']) {
    $STR_DADOSOLD .= "{CODFAB=".$DADOSOLD['CODFAB']."}";
    $STR_DADOSNEW .= "{CODFAB=".$DADOSNEW['CODFAB']."}";
    $criterio_update .= "   ,CODFAB = '".$DADOSNEW['CODFAB']."'";
  }
  $DADOSNEW['PCOMINT1'] = str_replace(",", ".", $DADOSNEW['PCOMINT1']);
  if ($DADOSOLD['PCOMINT1'] != $DADOSNEW['PCOMINT1']) {
    $STR_DADOSOLD .= "{PCOMINT1=".$DADOSOLD['PCOMINT1']."}";
    $STR_DADOSNEW .= "{PCOMINT1=".$DADOSNEW['PCOMINT1']."}";
    $criterio_update .= "   ,PCOMINT1 = '".$DADOSNEW['PCOMINT1']."'";

    $STR_DADOSOLD .= "{PCOMREP1=".$DADOSOLD['PCOMREP1']."}";
    $STR_DADOSNEW .= "{PCOMREP1=".$DADOSNEW['PCOMINT1']."}";
    $criterio_update .= "   ,PCOMREP1 = '".$DADOSNEW['PCOMINT1']."'";

    $STR_DADOSOLD .= "{PCOMEXT1=".$DADOSOLD['PCOMEXT1']."}";
    $STR_DADOSNEW .= "{PCOMEXT1=".$DADOSNEW['PCOMINT1']."}";
    $criterio_update .= "   ,PCOMEXT1 = '".$DADOSNEW['PCOMINT1']."'";
  }
  if ($DADOSOLD['CODFORNEC'] != $DADOSNEW['CODFORNEC']) {
    $STR_DADOSOLD .= "{CODFORNEC=".$DADOSOLD['CODFORNEC']."}";
    $STR_DADOSNEW .= "{CODFORNEC=".$DADOSNEW['CODFORNEC']."}";
    $criterio_update .= "   ,CODFORNEC = '".$DADOSNEW['CODFORNEC']."'";
  }
  if ($DADOSOLD['INFORMACOESTECNICAS'] != $DADOSNEW['INFORMACOESTECNICAS']) {
    if (($DADOSOLD['INFORMACOESTECNICAS'] == '9999') || ($DADOSOLD['INFORMACOESTECNICAS'] == '')) {
      if (($DADOSNEW['INFORMACOESTECNICAS'] != '9999') || ($DADOSNEW['INFORMACOESTECNICAS'] != '')) {
        $STR_DADOSOLD .= "{INFORMACOESTECNICAS=".$DADOSOLD['INFORMACOESTECNICAS']."}";
        $STR_DADOSNEW .= "{INFORMACOESTECNICAS=".$DADOSNEW['INFORMACOESTECNICAS']."}";
        $criterio_update .= "   ,INFORMACOESTECNICAS = '".$DADOSNEW['INFORMACOESTECNICAS']."'";
      }
    }
  }

  if ($debug) varDump2($DADOSNEW);

  if ($criterio_update == "") {
    if ($debug) varDump2("OS DADOS DA PLANILHA NÃO DIFEREM DOS DADOS ATUAIS. NÃO TERÁ ALTERAÇÃO NA PCPRODUT");
    return true;

  } else {
    
    if ($debug) varDump2($criterio_update);
    $sql_update = "UPDATE PCPRODUT SET  
                      DTULTALTCAD = SYSDATE, 
                      CODFUNCULTALTER = '".$_SESSION['login']['MATRICULA']."'
                      ";
    $sql_update .= $criterio_update."   
                    WHERE CODPROD = ".$CODPRODVALIDO;
    
    $ret_update = executarOracle($sql_update);
    if ($debug) varDump2($sql_update);
    if ($debug) varDump2($ret_update);
  }

}


function geraLogAlteracao($OPERACAO, $TABELA, $REGISTRO){
  $REGISTRO = str_replace(".", " ", $REGISTRO);
  $REGISTRO = str_replace("&", " ", $REGISTRO);
  $REGISTRO = str_replace("'", " ", $REGISTRO);
  $sql = "INSERT INTO ORCLOGALTERACAO (IDLOG, USUARIO, OPERACAO, TABELA, REGISTRO) VALUES (
          (SELECT NVL(MAX(IDLOG),0)+1 FROM ORCLOGALTERACAO), 
          '".$_SESSION['login']['NOME']."', 
          '".$OPERACAO."', 
          '".$TABELA."', 
          '".$REGISTRO."'
          )";
  executarOracle($sql);
  // varDump2($sql);
}


function deletePCCODFABRICA(array $mapCODFAB){
  if (empty($mapCODFAB)) {
    return true;
  }

  // varDump2("deletePCCODFABRICA");
  $listaCODFAB = json_encode(array_keys($mapCODFAB));
  $listaCODFAB = str_replace("[", "", $listaCODFAB);
  $listaCODFAB = str_replace("]", "", $listaCODFAB);
  $listaCODFAB = str_replace(",", "','", $listaCODFAB);

  // DELETE seguro (apenas o registro específico)
  $sql = "DELETE FROM PCCODFABRICA
           WHERE CODFAB in ('".$listaCODFAB."')";
  if (executarOracle($sql) === false){
    throw new Exception('ERRO deletePCCODFABRICA: falhou ao excluir registro anterior.');
  }
  return true;
}

function validaPCCODFABRICA(array $lista_codfab, array $lista_codprod){

  if ($lista_codfab && !empty($lista_codfab)) {
    $str_codfab = "'".implode("', '", array_keys($lista_codfab))."'";
  } else {
    return false;
  }

  // INSERT direto (NOT EXISTS não é mais necessário)
  $sql = "DELETE FROM PCCODFABRICA WHERE CODFAB IN ($str_codfab)";
  // varDump2($lista_codfab);
  // varDump2($sql);
  // die();
  if (executarOracle($sql) === false){
    return false;
  }

  if ($lista_codprod && !empty($lista_codprod)) {
    $str_codprod = implode(", ", array_keys($lista_codprod));
  } else {
    return false;
  }

  // INSERT direto (NOT EXISTS não é mais necessário)
  $sql = "INSERT INTO PCCODFABRICA (
            CODPROD,
            CODFORNEC,
            CODFAB,
            TIPOFATOR,
            FATOR
        )
        SELECT
            p.codprod,
            p.codfornec,
            p.codfab,
            'M',
            1
        FROM pcprodut p
        LEFT JOIN PCCODFABRICA cf
               ON cf.codprod   = p.codprod
              AND cf.codfornec = p.codfornec
              AND cf.codfab    = p.codfab
        WHERE p.dtexclusao IS NULL
          AND cf.codprod IS NULL
          AND p.codfab IS NOT NULL
          AND p.codfornec IS NOT NULL
          AND p.codprod IN ($str_codprod)";
  // varDump2($str_codprod);
  // varDump2($sql);
  // die();
  return executarOracle($sql);
}

function buscaPCCODFABRICA($PRODUTO){
  $sql = "SELECT * FROM PCCODFABRICA 
          WHERE CODFAB = '{$PRODUTO['CODFAB']}' ";
    if (isset($PRODUTO['CODPROD']) && !empty($PRODUTO['CODPROD'])) {
      $sql .= PHP_EOL." OR  CODPROD = ".$PRODUTO['CODPROD'];
    }
    if (isset($PRODUTO['CODFORNEC']) && !empty($PRODUTO['CODFORNEC'])) {
      $sql .= PHP_EOL." OR  CODFORNEC = ".$PRODUTO['CODFORNEC'];
    }
  return reset(selectOracle($sql));
}


function validaPCPRODFILIAL(){
  if (empty($_SESSION['login']['MATRICULA'])) {
      throw new Exception('Matrícula não encontrada na sessão.');
  }

  $sql = "INSERT INTO PCPRODFILIAL
          (CODPROD,
           CODFILIAL,
           PCOMREP1,
           PCOMINT1,
           PCOMEXT1,
           CODCOMPRADOR,
           ORIGMERCTRIB,
           FORALINHA,
           CHECARMULTIPLOVENDABNF,
           ACEITAVENDAFRACAO,
           PISCOFINSRETIDO,
           REVENDA,
           MULTIPLO,
           ESTOQUEIDEAL,
           ATIVO)
           SELECT TRUNC(P.CODPROD) AS CODPROD,
                 F.CODIGO AS CODFILIAL,
                 P.PCOMREP1,
                 P.PCOMINT1,
                 P.PCOMEXT1,
                 {$_SESSION['login']['MATRICULA']} AS CODCOMPRADOR,
                 (SELECT DECODE(MAX(O.VALOR),'NULL',NULL,MAX(O.VALOR)) FROM ORCPARAMETROS O WHERE  O.PARAMETRO = 'ORIGMERCTRIB') AS ORIGMERCTRIB,
                 (SELECT MAX(O.VALOR) FROM ORCPARAMETROS O WHERE O.PARAMETRO = 'FORALINHA') AS FORALINHA,
                 (SELECT MAX(O.VALOR) FROM ORCPARAMETROS O WHERE O.PARAMETRO = 'CHECARMULTIPLOVENDABNF') AS CHECARMULTIPLOVENDABNF,
                 (SELECT MAX(O.VALOR) FROM ORCPARAMETROS O WHERE O.PARAMETRO = 'ACEITAVENDAFRACAO') AS ACEITAVENDAFRACAO,
                 (SELECT MAX(O.VALOR) FROM ORCPARAMETROS O WHERE O.PARAMETRO = 'PISCOFINSRETIDO') AS PISCOFINSRETIDO,
                 (SELECT MAX(O.VALOR) FROM ORCPARAMETROS O WHERE O.PARAMETRO = 'REVENDA') AS REVENDA,
                 (SELECT MAX(O.VALOR) FROM ORCPARAMETROS O WHERE O.PARAMETRO = 'MULTIPLO') AS MULTIPLO,
                 (SELECT MAX(O.VALOR) FROM ORCPARAMETROS O WHERE O.PARAMETRO = 'ESTOQUEIDEAL') AS ESTOQUEIDEAL,
                 (SELECT MAX(O.VALOR) FROM ORCPARAMETROS O WHERE O.PARAMETRO = 'ATIVO') AS ATIVO
            FROM PCPRODUT P, PCFILIAL F
           WHERE F.CODIGO NOT IN ('99')
             AND NOT EXISTS (SELECT 1 FROM PCPRODFILIAL PF WHERE PF.CODPROD=P.CODPROD AND PF.CODFILIAL=F.CODIGO)";
    // varDump2($sql);
    // die();
    if (executarOracle($sql) ){
      return "OK";
    } else {
      return "ERRO ao executar validaPCPRODFILIAL.";
    }
}

function validaPCEST(){
  $sql = "INSERT INTO PCEST (
            CODPROD, 
            CODFILIAL)
          SELECT P.CODPROD, 
                 F.CODIGO AS CODFILIAL
            FROM PCPRODUT P, PCFILIAL F
           WHERE F.CODIGO NOT IN ('99')
             AND P.DTEXCLUSAO IS NULL
             AND NOT EXISTS (SELECT 1 FROM PCEST E WHERE E.CODFILIAL = F.CODIGO AND E.CODPROD = P.CODPROD)";
  // varDump2($sql);
  if (executarOracle($sql) ){
    return "OK";
  } else {
    return "ERRO ao executar validaPCEST.";
  }
}


function validaPCTRIBENTRADA() {
  if (empty($_SESSION['login']['MATRICULA'])) {
      throw new Exception('Matrícula não encontrada na sessão.');
  }

  $sql = "INSERT INTO PCTRIBENTRADA (
                      NCM, 
                      CODFILIAL, 
                      UFORIGEM, 
                      TIPOFORNEC, 
                      CODFIGURA, 
                      CODTRIBPISCOFINS, 
                      CODEXCECAOPISCOFINS, 
                      CODFUNCCAD, 
                      DTCADASTRO)
          SELECT DISTINCT
                 P.CODNCMEX                AS NCM,
                 F.CODIGO                  AS CODFILIAL,
                 C.UF                      AS UFORIGEM,
                 FO.TIPOFORNEC,
                 CASE 
                      WHEN C.UF = 'AM' THEN 3
                      WHEN C.UF IN ('MG','ES','RJ','SP','SC','PR','RS') THEN 1
                      ELSE 2
                 END                       AS CODFIGURA,
                 2                         AS CODTRIBPISCOFINS,
                 1                         AS CODEXCECAOPISCOFINS,
                 {$_SESSION['login']['MATRICULA']}  AS CODFUNCCAD,
                 SYSDATE                   AS DTCADASTRO
          FROM PCPRODUT P
          JOIN PCFORNEC FO 
               ON FO.CODFORNEC = P.CODFORNEC
          JOIN PCCIDADE C 
               ON C.CODCIDADE = FO.CODCIDADE
          JOIN PCFILIAL F 
               ON F.DTEXCLUSAO IS NULL
          WHERE P.DTEXCLUSAO IS NULL
            AND FO.DTEXCLUSAO IS NULL
            AND F.CODIGO <> '99'
            AND P.CODNCMEX IS NOT NULL
            AND FO.TIPOFORNEC IS NOT NULL
            AND NOT EXISTS (
                  SELECT 1
                  FROM PCTRIBENTRADA T
                  WHERE T.NCM        = P.CODNCMEX
                    AND T.CODFILIAL  = F.CODIGO
                    AND T.UFORIGEM   = C.UF
                    AND T.TIPOFORNEC = FO.TIPOFORNEC )";
  // varDump2($sql);
  if (executarOracle($sql) ){
    return "OK";
  } else {
    return "ERRO ao executar validaPCTRIBENTRADA.";
  }
}


function validaPCTABTRIB(){
  $sql = "INSERT INTO PCTABTRIB (
                      CODPROD, 
                      CODFILIALNF, 
                      UFDESTINO, 
                      CODST, 
                      CODTRIBPISCOFINS)

            WITH produtos AS (
                SELECT /*+ MATERIALIZE */ p.codprod
                FROM pcprodut p
                WHERE p.dtexclusao IS NULL
            ),
            regioes AS (
                SELECT /*+ MATERIALIZE */
                       DISTINCT
                       NVL(r.codfilial, 1) AS codfilialnf,
                       r.uf               AS ufdestino
                FROM pcregiao r
                WHERE r.status = 'A'
                  AND r.uf IS NOT NULL
            )
            SELECT
                   p.codprod,
                   r.codfilialnf AS codfilialnf,
                   r.ufdestino   AS ufdestino,
                   CASE
                        WHEN r.ufdestino IN ('DF','GO','MG','MS','MT','PR','RJ','RS','SC','SP') THEN 3
                        WHEN r.ufdestino IN ('AC','AL','AM','AP','BA','CE','ES','MA','PA','PB','PE','PI',
                                             'RN','RO','RR','SE','TO') THEN 4
                        ELSE 2
                   END AS codst,
                   2 AS codtribpiscofins
            FROM produtos p
            CROSS JOIN regioes r
            WHERE NOT EXISTS (
                SELECT 1
                FROM pctabtrib t
                WHERE t.codprod     = p.codprod
                  AND t.codfilialnf = r.codfilialnf
                  AND t.ufdestino   = r.ufdestino )";
  // varDump2($sql);
  if (executarOracle($sql) ){
    return "OK";
  } else {
    return "ERRO ao executar validaPCTABTRIB.";
  }
}

function aplicarPreco($PRODUTO){
  $sql = "UPDATE PCTABPR
             SET DTULTALTPVENDA = SYSDATE,
                 DTULTALTPTABELA = SYSDATE,
                 CODST = 2, 
                 CODTRIBPISCOFINS = (CASE NUMREGIAO 
                                         WHEN 1 THEN 2
                                         WHEN 5 THEN 2
                                         ELSE 4 END),
                 MARGEM = (SELECT PA.VALOR 
                             FROM ORCPARAMETROS PA 
                            WHERE PA.PARAMETRO = 'MARGEM' 
                              AND PA.TIPO = 'CAD_PRODUTO'),
                 PTABELA  = ".moedaPHP($PRODUTO['PVENDA']).",
                 PTABELA1 = ".moedaPHP($PRODUTO['PVENDA']).",
                 PTABELA2 = ".moedaPHP($PRODUTO['PVENDA']).",
                 PTABELA3 = ".moedaPHP($PRODUTO['PVENDA']).",
                 PTABELA4 = ".moedaPHP($PRODUTO['PVENDA']).",
                 PTABELA5 = ".moedaPHP($PRODUTO['PVENDA']).",
                 PTABELA6 = ".moedaPHP($PRODUTO['PVENDA']).",
                 PTABELA7 = ".moedaPHP($PRODUTO['PVENDA']).",
                 PVENDA   = ".moedaPHP($PRODUTO['PVENDA']).",
                 PVENDA1  = ".moedaPHP($PRODUTO['PVENDA']).",
                 PVENDA2  = ".moedaPHP($PRODUTO['PVENDA']).",
                 PVENDA3  = ".moedaPHP($PRODUTO['PVENDA']).",
                 PVENDA4  = ".moedaPHP($PRODUTO['PVENDA']).",
                 PVENDA5  = ".moedaPHP($PRODUTO['PVENDA']).",
                 PVENDA6  = ".moedaPHP($PRODUTO['PVENDA']).",
                 PVENDA7  = ".moedaPHP($PRODUTO['PVENDA'])."
           WHERE CODPROD  = ".$PRODUTO['CODPROD'];
  // varDump2($sql);
  return executarOracle($sql);
}

function validaPCTABPR() {
  if (empty($_SESSION['login']['MATRICULA'])) {
      throw new Exception('Matrícula não encontrada na sessão.');
  }

  $sql = "INSERT INTO PCTABPR (
                      CODPROD,
                      NUMREGIAO,
                      ROTINA,
                      MATRICULA,
                      DTULTALTPTABELA,
                      DTULTALTPVENDA,
                      CODST,
                      CODTRIBPISCOFINS,
                      MARGEM,
                      PTABELA, PTABELA1, PTABELA2, PTABELA3,
                      PTABELA4, PTABELA5, PTABELA6, PTABELA7,
                      PVENDA, PVENDA1, PVENDA2, PVENDA3,
                      PVENDA4, PVENDA5, PVENDA6, PVENDA7
                  )
                  WITH PARAM AS (
              SELECT NVL(MAX(VALOR),0) AS MARGEM
              FROM ORCPARAMETROS
              WHERE PARAMETRO = 'MARGEM'
                AND TIPO = 'CAD_PRODUTO'
          )
          SELECT  
                  P.CODPROD,
                  R.NUMREGIAO,
                  'APP_PORTAL'              AS ROTINA,
                  {$_SESSION['login']['MATRICULA']} AS MATRICULA,
                  SYSDATE                   AS DTULTALTPTABELA,
                  SYSDATE                   AS DTULTALTPVENDA,
                  2                         AS CODST,
                  CASE 
                      WHEN R.NUMREGIAO IN (1,5) THEN 2
                      ELSE 4
                  END                       AS CODTRIBPISCOFINS,
                  PARAM.MARGEM,
                  0.0001 PTABELA, 0.0001 PTABELA1, 0.0001 PTABELA2, 0.0001 PTABELA3,
                  0.0001 PTABELA4, 0.0001 PTABELA5, 0.0001 PTABELA6, 0.0001 PTABELA7,
                  0.0001 PVENDA, 0.0001 PVENDA1, 0.0001 PVENDA2, 0.0001 PVENDA3,
                  0.0001 PVENDA4, 0.0001 PVENDA5, 0.0001 PVENDA6, 0.0001 PVENDA7
          FROM PCPRODUT P
          CROSS JOIN PCREGIAO R
          CROSS JOIN PARAM
          WHERE P.DTEXCLUSAO IS NULL
            AND NVL(R.STATUS, 'A') = 'A'
          AND NOT EXISTS (
                SELECT 1
                FROM PCTABPR T
                WHERE T.CODPROD   = P.CODPROD
                  AND T.NUMREGIAO = R.NUMREGIAO
          )";
  if (executarOracle($sql) ){
    return "OK";
  } else {
    return "ERRO ao executar validaPCTABPR.";
  }
}





function buscaEstados(){
  $sql = "SELECT DISTINCT UF FROM PCCIDADE";
  $tmp = selectOracle($sql);
  foreach ($tmp as $key => $value) {
    $ret[] = $value['UF'];
  }
  return $ret;
}




function buscaFiliaisAtivas(){
  $sql = "SELECT CODIGO FROM PCFILIAL WHERE CODIGO <> '99' AND DTEXCLUSAO IS NULL";
  $ret = selectOracle($sql);
  return $ret;
}

function buscaRegioesAtivas(){
  // A LISTA DE REGIOES ATIVAS
  $sql1 = "SELECT NUMREGIAO, REGIAO FROM PCREGIAO R WHERE R.STATUS = 'A' ORDER BY NUMREGIAO";
  $ret1 = selectOracle($sql1);
  return $ret1;
}

// function buscaMargem(){
//   // BUSCA A MARGEM PARAMETRIZADA
//   $sql = "SELECT VALOR AS MARGEM FROM parametros 
//            WHERE parametro = 'MARGEM' 
//              AND tipo in ('CAD_PRECO')";
//   $ret = selectMysql($sql);
//   //varDump2($retorno); die;
//   if ($ret) {
//     return $ret[0]['MARGEM'];
//   } else {
//     return floatval(28.79);
//   }
// }

function buscaDadosProduto($CODPROD, $CODORIGEM = 0){
  $sql = "SELECT PCPRODUT.CODPROD,
            '".$_SESSION['login']['MATRICULA']."' AS CODCOMPRADOR,
            '".$CODORIGEM."' AS ORIGMERCTRIB,
            DECODE(PCPRODUT.OBS, 'PV', 'S', 'N') PROIBIDAVENDA,
            DECODE(PCPRODUT.OBS2, 'FL', 'S', 'N') FORALINHA,
            NVL(PCPRODUT.CHECARMULTIPLOVENDABNF, 'N') AS CHECARMULTIPLOVENDABNF,
            NVL(PCPRODUT.ACEITAVENDAFRACAO, 'N') AS ACEITAVENDAFRACAO,
            NVL(PCPRODUT.PISCOFINSRETIDO, 'N') AS PISCOFINSRETIDO,
            PCPRODUT.REVENDA,
            PCPRODUT.MULTIPLO,
            PCPRODUT.CLASSE,
            PCPRODUT.CLASSEVENDA,
            PCPRODUT.CLASSEESTOQUE,
            PCPRODUT.PCOMREP1,
            PCPRODUT.PCOMINT1,
            PCPRODUT.PCOMEXT1,
            0 AS ESTOQUEIDEAL,
            0 AS QTMINAUTOSERV,
            0 AS QTMINIMAATACADO,
            0 AS QTMINIMAATACADOF,
            0 AS CODDISPESTRUTURA,
            NULL AS PERPIS,
            NULL AS PERCOFINS,
            'N' AS ESTOQUEPORSERIE,
            'S' AS ATIVO
       FROM PCPRODUT
      WHERE PCPRODUT.CODPROD IS NOT NULL
        AND PCPRODUT.CODPROD = ".$CODPROD;
  $ret = selectOracle($sql);
  if ($ret) {
    return $ret[0];
  } else {
    return false;
  }
}



function buscaDepto($CODEPTO){
  $sql = "SELECT CODEPTO, DESCRICAO AS DEPARTAMENTO FROM PCDEPTO WHERE CODEPTO = ".$CODEPTO;
  if($ret = selectOracle($sql)){
    return reset($ret);
  } else {
    return false;
  }
}

function buscaSecao($CODSEC){
  $sql = "SELECT CODSEC, DESCRICAO AS SECAO FROM PCSECAO WHERE CODSEC = ".$CODSEC;
  if($ret = selectOracle($sql)){
    return reset($ret);
  } else {
    return false;
  }
}

function pesquisarprodutos($dados){
  $sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
           P.DV,
           P.DESCRICAO,
           P.NUMORIGINAL,
           M.MARCA,
           P.CODFAB,
           F.CODFORNEC,
           F.FORNECEDOR,
           P.DTCADASTRO,
           P.INFORMACOESTECNICAS AS LOCACAO
      FROM PCPRODUT P, PCMARCA M, PCFORNEC F
     WHERE P.CODMARCA = M.CODMARCA
       AND P.CODFORNEC = F.CODFORNEC";

  if (empty($dados['CODPROD']) && empty($dados['NUMORIGINAL']) && empty($dados['CODFAB'])) {
    insereModal('danger', 'Ao menos um campo deve ser preenchido para realizar a pesquisa!');
  } else {
    if (isset($dados['CODPROD']) && $dados['CODPROD']) {
      if (is_numeric($dados['CODPROD'])){
        $CODPROD = intval($dados['CODPROD']);
      } else {
        if (substr(mb_strtoupper($dados['CODPROD']), 0, 1) == 'W') {
          $CODPROD = intval(substr($dados['CODPROD'], 1));
        } else {
          insereModal("warning", "O CODPROD informado é inválido");
        }
      }
      $sql .= PHP_EOL . " AND P.CODPROD = '" . $CODPROD . "'";
    }
    if (isset($dados['NUMORIGINAL']) && $dados['NUMORIGINAL']) {
      $sql .= PHP_EOL . " AND P.NUMORIGINAL = UPPER('" . mb_strtoupper(trim($dados['NUMORIGINAL']),'UTF-8') . "') ";
    }
    if (isset($dados['CODFAB']) && $dados['CODFAB']) {
      $sql .= PHP_EOL . " AND P.CODFAB = UPPER('" . mb_strtoupper(trim($dados['CODFAB']),'UTF-8') . "') ";
    }
    if (!isset($_SESSION['PRODUTOS'])) $_SESSION['PRODUTOS'] = array();
    
    // varDump2($sql);
    if($ret = selectOracle($sql)){
      foreach ($ret as $key => $value) {
        $existe = false;
        foreach ($_SESSION['PRODUTOS'] as $arrayProdutos) {
          if ($arrayProdutos['CODPROD'] == $value['CODPROD']) {
            $existe = true;
          }
        }
        if ($existe == false) {
          $_SESSION['PRODUTOS'][] = $value;
        }
      }
      varDump2( $_SESSION['PRODUTOS']);
    }
  }
}

function formataListaArquivoCadastro($ARQUIVO){
  if ($ARQUIVO) {
    foreach ($ARQUIVO as $key => $value) {
        $CODPROD = $value['CODPROD'];
        $DV = $value['DV'];

        unset($ARQUIVO[$key]['CODPROD']);
        unset($ARQUIVO[$key]['DV']);
        unset($ARQUIVO[$key]['CODWINT']);
        unset($ARQUIVO[$key]['VALIDALABEL']);
        unset($ARQUIVO[$key]['VALIDATITLE']);
        unset($ARQUIVO[$key]['STATUS']);
        unset($ARQUIVO[$key]['STATUSTITLE']);
        unset($ARQUIVO[$key]['STATUSLABEL']);
        unset($ARQUIVO[$key]['V_PCPRODFILIAL']);
        unset($ARQUIVO[$key]['V_PCCODFABRICA']);
        unset($ARQUIVO[$key]['V_PCEST']);
        unset($ARQUIVO[$key]['V_PCTRIBENTRADA']);
        unset($ARQUIVO[$key]['V_PCTABTRIB']);
        unset($ARQUIVO[$key]['V_PCTABPR']);
        
        $ARQUIVO[$key]['CODPROD'] = $CODPROD;
        $ARQUIVO[$key]['DV'] = $DV;
      }
    return $ARQUIVO;
  } else {
    exibeMensagem("nao encontrou ARQUIVO");
    return false;
  }
}

function validacaoGeral($LISTA_CODPROD){
  if ($LISTA_CODPROD == "") {
    return false;
  } else {
    $sql = "SELECT TRUNC(P.CODPROD) || '-' || P.DV AS CODPROD,
                   P.DESCRICAO,
                   P.NUMORIGINAL,
                   P.CODFAB,
                   P.MARCA,
                   P.CODNCMEX AS NCM,
                   (SELECT MAX(1) FROM PCDEPTO D WHERE D.CODEPTO = P.CODEPTO) AS V_PCDEPTO,
                   (SELECT MAX(1) FROM PCSECAO S WHERE S.CODSEC = P.CODSEC) AS V_PCSECAO,
                   (SELECT MAX(1) FROM PCCODFABRICA CF WHERE CF.CODPROD = P.CODPROD AND CF.CODFORNEC = P.CODFORNEC) AS V_PCCODFABRICA,
                   (SELECT MAX(1) FROM PCPRODFILIAL PF WHERE PF.CODPROD = P.CODPROD) AS V_PCPRODFILIAL,
                   (SELECT MAX(1) FROM PCEST E WHERE E.CODPROD = P.CODPROD) AS V_PCEST,
                   (SELECT MAX(1) FROM PCTRIBENTRADA TE WHERE TE.NCM = P.CODNCMEX) AS V_PCTRIBENTRADA,
                   (SELECT MAX(1) FROM PCTABTRIB TT WHERE TT.CODPROD = P.CODPROD) AS V_PCTABTRIB,
                   (SELECT MAX(1) FROM PCTABPR TP WHERE TP.CODPROD = P.CODPROD) AS V_PCTABPR
              FROM PCPRODUT P
             WHERE P.CODPROD IN (".$LISTA_CODPROD.")
             ORDER BY P.CODPROD ASC";
    // varDump2($sql);
    return selectOracle($sql);
  }
}

function validaCODPROD($CODPROD){
  $sql = "SELECT CODPROD FROM PCPRODUT WHERE CODPROD = ".$CODPROD;
  $ret = reset(selectOracle($sql));
  // varDump2($sql);
  // varDump2($ret);
  return $ret['CODPROD'];
}

function buscaDadosFilial($CODFILIAL){
    $sql = "SELECT F.CODIGO AS CODFILIAL,
       DECODE(F.CGC,
              NULL,
              NULL,
              REPLACE(REPLACE(REPLACE(TO_CHAR(LPAD(REPLACE(F.CGC, ' '),
                                                   14,
                                                   '0'),
                                              '00,000,000,0000,00'),
                                      ',',
                                      '.'),
                              ' '),
                      '.' ||
                      TRIM(TO_CHAR(TRUNC(MOD(LPAD(F.CGC, 14, '0'), 1000000) / 100),
                                   '0000')) || '.',
                      '/' ||
                      TRIM(TO_CHAR(TRUNC(MOD(LPAD(F.CGC, 14, '0'), 1000000) / 100),
                                   '0000')) || '-')) AS CNPJ,
           F.RAZAOSOCIAL,
           F.FANTASIA,
           F.CIDADE,
           F.UF
        FROM PCFILIAL F
        WHERE F.CODIGO = ".$CODFILIAL;
    $ret = selectOracle($sql);
    // varDump2($sql); 
    // varDump2($ret);
    // die();
    
    if($ret){
        return $ret[0];
    } else {
        return false;
    }
}

function buscaDadosFornecedor($CODFORNEC){
    $sql = "SELECT F.CODFORNEC, 
            F.FORNECEDOR, 
            F.CGC AS CNPJ, 
            F.ENDER||' - BAIRRO'||F.BAIRRO||' - CEP: '||F.CEP AS ENDERECO, 
            F.TELFAB, 
            F.CIDADE, 
            F.ESTADO AS UF,
            F.TIPOFORNEC
        FROM PCFORNEC F
        WHERE F.CODFORNEC = ".$CODFORNEC;
    $ret = selectOracle($sql);
    // varDump2($sql); 
    // varDump2($ret);
    if($ret){
        return $ret[0];
    } else {
        return false;
    }
}

function apagaItensOrfaos(){
    $sql = "DELETE FROM PCSUGESTAOCOMPRAI I WHERE I.NUMSUGESTAO NOT IN (SELECT C.NUMSUGESTAO FROM PCSUGESTAOCOMPRAC C)";
    executarOracle($sql);
}

function buscaUltimoNUMSUGESTAO(){
    apagaItensOrfaos();
    $sql = "SELECT MAX(NUMSUGESTAO) as NUMSUGESTAO FROM PCSUGESTAOCOMPRAC";
    $retorno = selectOracle($sql);
    //var_dump($retorno); die;
    if($retorno <> false){
        $numsugestao = ((int)$retorno[0]['NUMSUGESTAO']);
    }
    return $numsugestao;
}


function validaTribEntrada($CODPROD, $CODFORNEC) {
    $sql = "SELECT CONCAT(P.NBM, '.') AS NCM,
                   1 AS CODFILIAL,
                   F.ESTADO AS UFORIGEM,
                   F.TIPOFORNEC,
                   NVL((SELECT 'S'
                     FROM PCTRIBENTRADA E
                    WHERE E.NCM = CONCAT(P.NBM, '.')
                      AND E.CODFILIAL = 1
                      AND E.UFORIGEM = F.ESTADO
                      AND E.TIPOFORNEC = F.TIPOFORNEC), 'N') AS TRIBENT
          FROM PCPRODUT P, PCFORNEC F
         WHERE P.CODPROD = ".$CODPROD."
           AND F.CODFORNEC = ".$CODFORNEC;
    $result = reset(selectOracle($sql));
    
    if ($result['TRIBENT'] == 'S') {
        return 'S';
    } else {
        switch ($result['UFORIGEM']) {
            case 'AM':  $result['CODFIGURA'] = '3'; break;
            case 'AC':  $result['CODFIGURA'] = '2'; break;
            case 'RO':  $result['CODFIGURA'] = '2'; break;
            case 'RR':  $result['CODFIGURA'] = '2'; break;
            case 'AP':  $result['CODFIGURA'] = '2'; break;
            case 'PA':  $result['CODFIGURA'] = '2'; break;
            case 'TO':  $result['CODFIGURA'] = '2'; break;
            case 'MA':  $result['CODFIGURA'] = '2'; break;
            case 'PI':  $result['CODFIGURA'] = '2'; break;
            case 'RN':  $result['CODFIGURA'] = '2'; break;
            case 'CE':  $result['CODFIGURA'] = '2'; break;
            case 'PB':  $result['CODFIGURA'] = '2'; break;
            case 'BA':  $result['CODFIGURA'] = '2'; break;
            case 'PE':  $result['CODFIGURA'] = '2'; break;
            case 'AL':  $result['CODFIGURA'] = '2'; break;
            case 'SE':  $result['CODFIGURA'] = '2'; break;
            case 'GO':  $result['CODFIGURA'] = '2'; break;
            case 'MT':  $result['CODFIGURA'] = '2'; break;
            case 'MS':  $result['CODFIGURA'] = '2'; break;
            case 'DF':  $result['CODFIGURA'] = '2'; break;
            case 'MG':  $result['CODFIGURA'] = '1'; break;
            case 'ES':  $result['CODFIGURA'] = '1'; break;
            case 'RJ':  $result['CODFIGURA'] = '1'; break;
            case 'SP':  $result['CODFIGURA'] = '1'; break;
            case 'SC':  $result['CODFIGURA'] = '1'; break;
            case 'PR':  $result['CODFIGURA'] = '1'; break;
            case 'RS':  $result['CODFIGURA'] = '1'; break;
        }
        $result['CODTRIBPISCOFINS'] = '2';
        $result['CODEXCECAOPISCOFINS'] = '1';
        insereRegistroPCTRIBENTRADA($result);
        return validaTribEntrada($CODPROD, $CODFORNEC);
    }
    
}


function insereRegistroPCTRIBENTRADA($dados){
    $sql = "INSERT INTO PCTRIBENTRADA
            (NCM, CODFILIAL, UFORIGEM, TIPOFORNEC, CODFIGURA, CODTRIBPISCOFINS, CODEXCECAOPISCOFINS, CODFUNCCAD, DTCADASTRO)
            VALUES(
            '".$dados['NCM']."',
            '".$dados['CODFILIAL']."',
            '".$dados['UFORIGEM']."',
            '".$dados['TIPOFORNEC']."',
            '".$dados['CODFIGURA']."',
            '".$dados['CODTRIBPISCOFINS']."',
            '".$dados['CODEXCECAOPISCOFINS']."',
            '".$_SESSION['login']["MATRICULAWINTHOR"]."',
            SYSDATE
            )";
    return executarOracle($sql);
}

function buscaProduto($CODPROD){
    $sql = "SELECT DESCRICAO, MARCA FROM PCPRODUT WHERE CODPROD = ".$CODPROD;
    if ($produto = reset(selectOracle($sql))) {
        return $produto;
    } else {
        return "PRODUTO NÃO ENCONTRADO NO WINTHOR";
    }
}

function consultarPedidoCompras($dados){

  if ($dados['TIPOPESQUISA'] == "PCSUGESTAOCOMPRAC") {
    $sql = "SELECT 'PCSUGESTAOCOMPRAC' AS TIPOPESQUISA,
                   C.NUMSUGESTAO AS NUMPED, 
                   C.CODFORNEC, 
                   F.FORNECEDOR,
                   C.CODFILIAL, 
                   C.DATASUGESTAO AS DATA,
                   (SELECT COUNT(I.CODPROD) FROM PCSUGESTAOCOMPRAI I WHERE I.NUMSUGESTAO = C.NUMSUGESTAO) AS QTD_ITENS
              FROM PCSUGESTAOCOMPRAC C, PCFORNEC F
             WHERE C.CODFORNEC = F.CODFORNEC 
               AND TRUNC(C.DATASUGESTAO) BETWEEN TO_DATE('".$dados['DATAINI']."','DD/MM/YYYY') AND TO_DATE('".$dados['DATAFIM']."','DD/MM/YYYY')";
    if (!empty($dados['NUMPED'])) {
      $sql .= PHP_EOL." AND C.NUMSUGESTAO = ".$dados['NUMPED'];
    }
    if (!empty($dados['CODFORNEC'])) {
      $sql .= PHP_EOL." AND C.CODFORNEC = ".$dados['CODFORNEC'];
    }
    $sql .= PHP_EOL." ORDER BY C.DATASUGESTAO DESC";
  } else {
    $sql = "SELECT 'PCPEDIDO' AS TIPOPESQUISA,
                   C.NUMPED,
                   C.CODFORNEC,
                   F.FORNECEDOR,
                   C.CODFILIAL,
                   C.DTEMISSAO AS DATA,
                   (SELECT COUNT(I.CODPROD) FROM PCITEM I WHERE I.NUMPED = C.NUMPED) AS QTD_ITENS
              FROM PCPEDIDO C, PCFORNEC F
             WHERE C.CODFORNEC = F.CODFORNEC 
               AND TRUNC(C.DTEMISSAO) BETWEEN TO_DATE('".$dados['DATAINI']."','DD/MM/YYYY') AND TO_DATE('".$dados['DATAFIM']."','DD/MM/YYYY')";
    if (!empty($dados['NUMPED'])) {
      $sql .= " AND C.NUMPED = ".$dados['NUMPED'];
    }
    if (!empty($dados['CODFORNEC'])) {
      $sql .= " AND C.CODFORNEC = ".$dados['CODFORNEC'];
    }
    $sql .= " ORDER BY C.DTEMISSAO DESC";
  }
  // varDump2($sql);
  $_SESSION['LISTAPEDCOMPRA'] = selectOracle($sql);

}

function buscaPedcompraCabecalho($dados){
  if ($dados['TIPOPESQUISA'] == "PCSUGESTAOCOMPRAC") {
    $sql = "SELECT C.NUMSUGESTAO AS NUMPED, 
                   C.CODFORNEC, 
                   F.FORNECEDOR,
                   C.CODFILIAL, 
                   C.DATASUGESTAO AS DATA,
                   (SELECT COUNT(I.CODPROD) FROM PCSUGESTAOCOMPRAI I WHERE I.NUMSUGESTAO = C.NUMSUGESTAO) AS QTD_ITENS,
                   'SUGESTAO' AS TIPO
              FROM PCSUGESTAOCOMPRAC C, PCFORNEC F
             WHERE C.CODFORNEC = F.CODFORNEC 
               AND C.NUMSUGESTAO = ".$dados['NUMPED'];
  } else {
    $sql = "SELECT C.NUMPED,
                   C.CODFORNEC,
                   F.FORNECEDOR,
                   C.CODFILIAL,
                   C.DTEMISSAO AS DATA,
                   'PEDIDO' AS TIPO,
                   (SELECT COUNT(I.CODPROD) FROM PCITEM I WHERE I.NUMPED = C.NUMPED) AS QTD_ITENS
              FROM PCPEDIDO C, PCFORNEC F
             WHERE C.CODFORNEC = F.CODFORNEC 
               AND C.NUMPED = ".$dados['NUMPED'];
  }
  return reset(selectOracle($sql));
}

function buscaPedcompraItens($dados){
  if ($dados['TIPOPESQUISA'] == "PCSUGESTAOCOMPRAC") {
    $sql = "SELECT P.NUMORIGINAL,
                   I.CODPROD,
                   P.DESCRICAO,
                   M.MARCA,
                   I.QTSUGERIDA AS QTPEDIDA,
                   I.PCOMPRALIQSUGERIDO AS PCOMPRA
              FROM PCSUGESTAOCOMPRAI I, PCPRODUT P, PCMARCA M
             WHERE I.CODPROD = P.CODPROD
               AND NVL(P.CODMARCA, 1) = M.CODMARCA
               AND I.NUMSUGESTAO = ".$dados['NUMPED'];
  } else {
    $sql = "SELECT P.NUMORIGINAL,
                   I.CODPROD,
                   P.DESCRICAO,
                   M.MARCA,
                   I.QTPEDIDA,
                   I.PCOMPRA
              FROM PCITEM I, PCPRODUT P, PCMARCA M
             WHERE I.CODPROD = P.CODPROD
               AND NVL(P.CODMARCA, 1) = M.CODMARCA
               AND I.NUMPED = ".$dados['NUMPED'];
  }
  return selectOracle($sql);
}

function validaDV($CODPROD, $DV){
  $sql = "SELECT CODPROD, DV FROM PCPRODUT WHERE CODPROD = ".$CODPROD." AND DV = ".$DV;
  if ($ret = selectOracle($sql)) {
    if (empty($ret)) {
      return false;
    } else {
      return true;
    }
  } else {
    return false;
  }
}


function consultarVide($dados){
  // varDump2($dados);
  if (empty($dados['CODPECA'])) {
    insereModal("danger", "Ao menos um dos campos precisa ser preenchido.");
    return false;
  } else {
    $sql = "SELECT * FROM ORCVIDE 
             WHERE DTEXCLUSAO IS NULL
               AND CODPECA LIKE '".trim(mb_strtoupper($dados['CODPECA'],'UTF-8'))."%'
                OR VIDE LIKE '".trim(mb_strtoupper($dados['CODPECA'],'UTF-8'))."%'";
    // varDump2($sql);
    $_SESSION['LISTA'] = selectOracle($sql);
  }
}

function chaveVideExiste($dados){
  $sql = "SELECT * FROM ORCVIDE 
           WHERE DTEXCLUSAO IS NULL
             AND TRIM(CODPECA) = '".trim(mb_strtoupper($dados['CODPECA'],'UTF-8'))."'
             AND TRIM(VIDE) = '".trim(mb_strtoupper($dados['VIDE'],'UTF-8'))."'
             AND TRIM(APLICMARCA) = '".trim(mb_strtoupper($dados['APLICMARCA'],'UTF-8'))."'";
  $sql = str_replace("= ''", "is null", $sql);
  $ret = selectOracle($sql);
  // varDump2($sql);
  // varDump2($ret);
  if ($ret && !empty($ret[0])) {
    return reset($ret);
  } else {
    return false;
  }
}

function buscaDadosORCVIDE($IDVIDE){
  $sql = "SELECT * FROM ORCVIDE WHERE DTEXCLUSAO IS NULL AND IDVIDE = ".$IDVIDE;
  return reset(selectOracle($sql));
}

function salvarNovoVide($dados){
  $sql = "INSERT INTO ORCVIDE (IDVIDE, DTCADASTRO, USUARIOCADASTRO, CODPECA, VIDE, APLICMARCA, DESCRICAO, MARCA, NOMEOPCAO, IMPORTADO, PRECO, DATA) VALUES ( 
          (SELECT NVL(MAX(IDVIDE),0)+1 FROM ORCVIDE),
          SYSDATE,
          ".$_SESSION['login']['IDUSUARIO'].",
          '".trim(mb_strtoupper($dados['CODPECA'],'UTF-8'))."',
          '".trim(mb_strtoupper($dados['VIDE'],'UTF-8'))."',
          '".trim(mb_strtoupper($dados['APLICMARCA'],'UTF-8'))."',
          '".trim(mb_strtoupper($dados['DESCRICAO'],'UTF-8'))."',
          '".trim(mb_strtoupper($dados['MARCA'],'UTF-8'))."',
          '".trim(mb_strtoupper($dados['NOMEOPCAO'],'UTF-8'))."',
          '".((isset($dados['IMPORTADO']))?'S':'N')."',
          '".trim(mb_strtoupper($dados['PRECO'],'UTF-8'))."',
          '".trim(mb_strtoupper($dados['DATA'],'UTF-8'))."'
          )";
  // varDump2($sql);
  return executarOracle($sql);
}

function salvarAlteracaoVide($dados){
  $sql = "UPDATE ORCVIDE SET 
                 CODPECA = '".trim(mb_strtoupper($dados['CODPECA'],'UTF-8'))."',
                 VIDE = '".trim(mb_strtoupper($dados['VIDE'],'UTF-8'))."',
                 APLICMARCA = '".trim(mb_strtoupper($dados['APLICMARCA'],'UTF-8'))."',
                 DESCRICAO = '".trim(mb_strtoupper($dados['DESCRICAO'],'UTF-8'))."',
                 MARCA = '".trim(mb_strtoupper($dados['MARCA'],'UTF-8'))."',
                 NOMEOPCAO = '".trim(mb_strtoupper($dados['NOMEOPCAO'],'UTF-8'))."',
                 IMPORTADO = '".((isset($dados['IMPORTADO']))?$dados['IMPORTADO']:'N')."',
                 PRECO = '".trim(mb_strtoupper($dados['PRECO'],'UTF-8'))."',
                 DATA = '".trim(mb_strtoupper($dados['DATA'],'UTF-8'))."',
                 DTULTALTERACAO = SYSDATE,
                 USUARIOULTALTERACAO = ".$_SESSION['login']['IDUSUARIO']."
          WHERE IDVIDE = ".$dados['IDVIDE'];
  // varDump2($dados);
  // varDump2($sql);
  if(executarOracle($sql)){
    $_SESSION['LISTA'][$dados['key']]['CODPECA'] = trim(mb_strtoupper($dados['CODPECA'],'UTF-8'));
    $_SESSION['LISTA'][$dados['key']]['VIDE'] = trim(mb_strtoupper($dados['VIDE'],'UTF-8'));
    $_SESSION['LISTA'][$dados['key']]['APLICMARCA'] = trim(mb_strtoupper($dados['APLICMARCA'],'UTF-8'));
    $_SESSION['LISTA'][$dados['key']]['DESCRICAO'] = trim(mb_strtoupper($dados['DESCRICAO'],'UTF-8'));
    $_SESSION['LISTA'][$dados['key']]['MARCA'] = trim(mb_strtoupper($dados['MARCA'],'UTF-8'));
    $_SESSION['LISTA'][$dados['key']]['NOMEOPCAO'] = trim(mb_strtoupper($dados['NOMEOPCAO'],'UTF-8'));
    $_SESSION['LISTA'][$dados['key']]['IMPORTADO'] = ((isset($dados['IMPORTADO']))?'S':'N');
    $_SESSION['LISTA'][$dados['key']]['PRECO'] = trim(mb_strtoupper($dados['PRECO'],'UTF-8'));
    $_SESSION['LISTA'][$dados['key']]['DATA'] = trim(mb_strtoupper($dados['DATA'],'UTF-8'));
    return true;
  } else {
    return false;
  }
}


function excluirVide($dados){
  $sql = "UPDATE ORCVIDE SET";
  $sql .= PHP_EOL."DTEXCLUSAO = SYSDATE,";
  $sql .= PHP_EOL."USUARIOEXCLUSAO = '".$_SESSION['login']['NOME']."'";
  $sql .= PHP_EOL."WHERE IDVIDE = ".$dados['IDVIDE'];
  // varDump2($dados); 
  // varDump2($sql); 
  // die();
  executarOracle($sql);
  unset($_SESSION['LISTA'][$dados['key']]);

}

function buscaNumoriginalVide($NUMORIGINAL){
  $sql = "SELECT P.NUMORIGINAL
  FROM PCPRODUT P
 WHERE P.NUMORIGINAL IN
       (SELECT '".$NUMORIGINAL."' AS NUMORIGINAL
          FROM DUAL
        UNION
        SELECT V.VIDE AS NUMORIGINAL
          FROM ORCVIDE V
         WHERE V.DTEXCLUSAO IS NULL
           AND V.VIDE IS NOT NULL
           AND V.VIDE NOT LIKE 'W%'
           AND V.CODPECA = '".$NUMORIGINAL."'
           AND V.APLICMARCA NOT LIKE '%INFO%'
           AND V.APLICMARCA NOT LIKE '%OPCAO%'
        UNION
        SELECT V.CODPECA AS NUMORIGINAL
          FROM ORCVIDE V
         WHERE V.DTEXCLUSAO IS NULL
           AND V.CODPECA IS NOT NULL
           AND V.CODPECA NOT LIKE 'W%'
           AND V.VIDE = '".$NUMORIGINAL."'
           AND V.APLICMARCA NOT LIKE '%INFO%'
           AND V.APLICMARCA NOT LIKE '%OPCAO%')
    GROUP BY P.NUMORIGINAL";
   // varDump2($sql);
   // die();

  if ($ret = selectOracle($sql)){
    $lista = ""; 
    foreach ($ret as $key => $value) {
      if ($key > 0){
        $lista .= ", ";
      }
      $lista .= "'".$value['NUMORIGINAL']."'";
    }
    return $lista;
  } else {
    return false;
  }
}

function buscaSaldoGeral($lista){
  $sql = "SELECT TRUNC(P.CODPROD) AS CODPROD, 
                 TRUNC(P.DV) AS DV, 
                 TRIM(P.NUMORIGINAL) AS NUMORIGINAL, 
                 TRIM(P.DESCRICAO) AS DESCRICAO, 
                 TRIM(M.MARCA) AS MARCA, 
                 NVL(E.QTESTGER,0) AS SALDO,
                 (SELECT MAX(M1.DTMOV) FROM PCMOV M1 WHERE M1.CODPROD = P.CODPROD AND M1.CODOPER LIKE 'E%') AS DTULTCOMPRA,
                 (SELECT MAX(M1.DTMOV) FROM PCMOV M1 WHERE M1.CODPROD = P.CODPROD AND M1.CODOPER LIKE 'S%') AS DTULTVENDA
          FROM PCPRODUT P, PCMARCA M, PCEST E
         WHERE P.CODMARCA = M.CODMARCA
           AND P.CODPROD = E.CODPROD
           AND P.NUMORIGINAL IN (".$lista.")
           ORDER BY NVL(E.QTESTGER,0) DESC";
   // varDump2($sql);
  if ($ret = selectOracle($sql) ){
    return reset($ret);
  } else {
    return false;
  }
}

function buscaHistVendas($lista){
  $sql = "SELECT EXTRACT(YEAR FROM TRUNC(F.DTSAIDA, 'RR')) AS ANO, SUM(F.QT) AS QTVENDA
            FROM VIEW_VENDAS_RESUMO_FATURAMENTO F
           WHERE F.DTCANCEL IS NULL
             AND F.NUMORIGINAL IN (".$lista.")
          GROUP BY EXTRACT(YEAR FROM TRUNC(F.DTSAIDA, 'RR'))
          ORDER BY 1 ASC";
  // varDump2($sql);
  return selectOracle($sql);
}

function buscaUltEntrada($listaNumoriginal){
  $sql = "SELECT TRIM(P2.NUMORIGINAL) AS NUMORIGINAL,
             TRUNC(M2.DTMOV) AS DT_ULTIMA_ENT,
             TRIM(P2.DESCRICAO) AS DESCRICAO,
             NVL(MA.MARCA, P2.MARCA) AS MARCA,
             trim(FO.FORNECEDOR) AS FORNECEDOR,
             ROUND(M2.PUNIT, 2) AS PCOMPRA
        FROM PCMOV M2, PCPRODUT P2, PCMARCA MA, PCFORNEC FO
       WHERE M2.CODOPER LIKE 'E%'
         AND M2.CODOPER NOT LIKE 'ED'
         AND M2.CODPROD = P2.CODPROD
         AND M2.CODMARCA = MA.CODMARCA(+)
         AND M2.CODFORNEC = FO.CODFORNEC(+)
         AND M2.CODPROD IN (SELECT P1.CODPROD
                              FROM PCPRODUT P1
                             WHERE P1.NUMORIGINAL IN (".$listaNumoriginal."))
         AND M2.NUMTRANSENT =
             (SELECT MAX(M1.NUMTRANSENT) AS MAX_NUMTRANSENT
                FROM PCMOV M1
               WHERE M1.CODOPER LIKE 'E%'
                 AND M1.CODOPER NOT LIKE 'ED'
                 AND M1.CODPROD IN
                     (SELECT P.CODPROD
                        FROM PCPRODUT P
                       WHERE P.NUMORIGINAL IN (".$listaNumoriginal.")))";
  if ($ret = selectOracle($sql)) {
    return reset($ret);
  } else {
    $sql2 = "SELECT TRIM(P1.NUMORIGINAL) AS NUMORIGINAL,
               '' AS DT_ULTIMA_ENT,
               TRUNC(P1.CODPROD) AS CODPROD,
               TRIM(P1.DESCRICAO) AS DESCRICAO,
               NVL(M1.MARCA, P1.MARCA) AS MARCA,
               '' AS FORNECEDOR,
               '' AS PCOMPRA
          FROM PCPRODUT P1, PCMARCA M1
         WHERE P1.CODMARCA = M1.CODMARCA(+)
           AND P1.CODPROD IN (SELECT MAX(P2.CODPROD)
                                FROM PCPRODUT P2
                               WHERE P2.DTEXCLUSAO IS NULL
                                 AND P2.NUMORIGINAL IN (".$listaNumoriginal."))";
    return reset(selectOracle($sql2));
  }
}

function buscaSaldoDisponivel($listaNumoriginal){
    $sql = "SELECT SUM(NVL(E1.QTESTGER, 0) - NVL(E1.QTBLOQUEADA, 0) -
                   NVL(E1.QTINDENIZ, 0) - NVL(E1.QTRESERV, 0)) AS QTSALDODISPONIVEL
          FROM PCEST E1
         WHERE E1.CODFILIAL = 1
           AND E1.CODPROD IN (SELECT P2.CODPROD
                                FROM PCPRODUT P2
                               WHERE P2.DTEXCLUSAO IS NULL
                                 AND P2.NUMORIGINAL IN (".$listaNumoriginal."))";
    return reset(selectOracle($sql));
}

function buscaNomeFornecedor($CODFORNEC){
  $sql = "SELECT FORNECEDOR FROM PCFORNEC WHERE CODFORNEC = {$CODFORNEC}";
  if ($ret = selectOracle($sql)) {
    return $ret[0]['FORNECEDOR'];
  } else {
    return false;
  }
}

function validaProdutoEntrada($CODPROD){
  $sql = "SELECT TRUNC(P.CODPROD) AS CODPROD,
                 P.NUMORIGINAL,
                 P.DESCRICAO,
                 P.DTEXCLUSAO,
                 P.CODMARCA,
                 P.NBM AS NCM,
                 (SELECT N.DESCRICAO FROM PCNCM N WHERE N.CODNCM = P.NBM) AS NCM_DESCRICAO,
                 P.CODFORNEC,
                 F.FORNECEDOR,
                 F.TIPOFORNEC,
                 DECODE(F.TIPOFORNEC,
                        'D',
                        'CENTRAL DE DISTRIBUICAO',
                        'C',
                        'COMERCIO ATACADISTA',
                        'V',
                        'COMERCIO VAREJISTA',
                        'I',
                        'INDUSTRIA',
                        'O',
                        'OUTROS',
                        '',
                        'NAO INFORMADO',
                        F.TIPOFORNEC) AS DESCTIPOFORNEC,
                 F.ESTADO AS UF_FORNEC,
                 P.CODFAB,
                 (SELECT DECODE(C.FATOR, '1', 'SIM', 'NAO')
                    FROM PCCODFABRICA C
                   WHERE C.CODPROD = P.CODPROD
                     AND C.CODFORNEC = P.CODFORNEC
                     AND C.CODFAB = P.CODFAB) AS PCCODFABRICA,
                 NVL(P.CODFILIAL, 1) AS CODFILIAL,
                 (SELECT NVL(TO_CHAR(T.CODTRIBPISCOFINS), 'SEM TRIBUTACAO ENTRADA')
                    FROM PCTRIBENTRADA T, PCFILIAL FI, PCFORNEC FO
                   WHERE T.CODFILIAL = NVL(P.CODFILIAL, 1)
                     AND T.CODFILIAL = FI.CODIGO
                     AND FO.CODFORNEC = P.CODFORNEC
                     AND T.NCM = P.NBM || '.'
                     AND T.UFORIGEM = F.ESTADO
                     AND T.TIPOFORNEC = FO.TIPOFORNEC) AS TRIB_ENTRADA
            FROM PCPRODUT P, PCFORNEC F
           WHERE P.CODFORNEC = F.CODFORNEC(+)
             AND P.CODPROD = {$CODPROD}";
  $ret = selectOracle($sql);
  // varDump2($sql);
  // varDump2($ret);
  return reset($ret);
}


function consultaSugestaoCompra($dados){
  $mes4ini = explode("/", $dados["dataini4meses"]);
  $mes4ini = $mes4ini[1];
  // varDump2($mes4ini);
  
  $mes4fim = explode("/", $dados["datafim4meses"]);
  $mes4fim = $mes4fim[1];
  // varDump2($mes4fim);

  $meses = [];
  if ($mes4fim >= $mes4ini) {
    for ($i=$mes4ini; $i <= $mes4fim; $i++) { 
      $meses[] = str_pad(intval($i), 2, '0', STR_PAD_LEFT);
    }
  } else {
    for ($i=$mes4ini; $i <= 12; $i++) { 
      $meses[] = str_pad(intval($i), 2, '0', STR_PAD_LEFT);
    }
    for ($i=1; $i <= $mes4fim; $i++) { 
      $meses[] = str_pad(intval($i), 2, '0', STR_PAD_LEFT);
    }
  }
  // varDump2($meses);

  $sql = "SELECT TRIM(ESTOQUE.NUMORIGINAL) NUMORIGINAL,
               TRIM(MAX(P.DESCRICAO)) DESCRICAO,
               TRIM(MAX(CLASSE.CLASSE)) CLASSE,
               MAX(ESTOQUE.QTESTGER) SALDO,".PHP_EOL;
  $sql .=      ((in_array("01", $meses))?"MAX(MOVIMENTO.JANEIRO) JANEIRO,".PHP_EOL:"");
  $sql .=      ((in_array("02", $meses))?"MAX(MOVIMENTO.FEVEREIRO) FEVEREIRO,".PHP_EOL:"");
  $sql .=      ((in_array("03", $meses))?"MAX(MOVIMENTO.MARCO) MARCO,".PHP_EOL:"");
  $sql .=      ((in_array("04", $meses))?"MAX(MOVIMENTO.ABRIL) ABRIL,".PHP_EOL:"");
  $sql .=      ((in_array("05", $meses))?"MAX(MOVIMENTO.MAIO) MAIO,".PHP_EOL:"");
  $sql .=      ((in_array("06", $meses))?"MAX(MOVIMENTO.JUNHO) JUNHO,".PHP_EOL:"");
  $sql .=      ((in_array("07", $meses))?"MAX(MOVIMENTO.JULHO) JULHO,".PHP_EOL:"");
  $sql .=      ((in_array("08", $meses))?"MAX(MOVIMENTO.AGOSTO) AGOSTO,".PHP_EOL:"");
  $sql .=      ((in_array("09", $meses))?"MAX(MOVIMENTO.SETEMBRO) SETEMBRO,".PHP_EOL:"");
  $sql .=      ((in_array("10", $meses))?"MAX(MOVIMENTO.OUTUBRO) OUTUBRO,".PHP_EOL:"");
  $sql .=      ((in_array("11", $meses))?"MAX(MOVIMENTO.NOVEMBRO) NOVEMBRO,".PHP_EOL:"");
  $sql .=      ((in_array("12", $meses))?"MAX(MOVIMENTO.DEZEMBRO) DEZEMBRO,".PHP_EOL:"");
  $sql .= "    ROUND(MAX(MOVIMENTO12.VL_12MESES),2) VL_ANUAL,
               MAX(MOVIMENTO12.QT_12MESES) QT_ANUAL,
               TO_CHAR(MAX(DATAULTENT.DT_ENTRADA),'DD/MM/YYYY') DT_ULTIMA_ENT,
               ROUND(MAX(ENT.PRECO_COMPRA),2) PCOMPRA,
               TRIM(MAX(ENT.MARCA)) MARCA,
               TRIM(MAX(ENT.FORNECEDOR)) FORNECEDOR
          FROM PCPRODUT P,

               (SELECT P.NUMORIGINAL, SUM(E.QTESTGER) AS QTESTGER
                  FROM PCEST E, PCPRODUT P
                 WHERE P.CODPROD = E.CODPROD
                   AND E.CODFILIAL = 1
                   AND P.CODMARCA <> 1
                   ".((!empty($dados['NUMORIGINAL']))?"AND P.NUMORIGINAL LIKE '".$dados['NUMORIGINAL']."'":"")."
                 GROUP BY P.NUMORIGINAL) ESTOQUE,
               /*-----------------------------------*/

               (SELECT P.NUMORIGINAL, MAX(E.DTULTENT) AS DT_ENTRADA
                  FROM PCEST E, PCPRODUT P
                 WHERE P.CODPROD = E.CODPROD
                   AND E.CODFILIAL = 1
                   ".((!empty($dados['NUMORIGINAL']))?"AND P.NUMORIGINAL LIKE '".$dados['NUMORIGINAL']."'":"")."
                 GROUP BY P.NUMORIGINAL) DATAULTENT,
               /*-----------------------------------*/

               (SELECT PCPRODUT.CODPROD,
                       PCPRODUT.DESCRICAO,
                       PCPRODUT.NUMORIGINAL,
                       PCPRODUT.CODMARCA,
                       PCMOV.CODFORNEC,
                       (SELECT FORNECEDOR
                          FROM PCFORNEC
                         WHERE PCFORNEC.CODFORNEC = PCMOV.CODFORNEC) FORNECEDOR,
                       (SELECT MARCA
                          FROM PCMARCA
                         WHERE PCMARCA.CODMARCA = PCPRODUT.CODMARCA) MARCA,
                       PCMOV.PTABELA PRECO_COMPRA,
                       PCMOV.NUMTRANSENT
                  FROM PCMOV,
                       (SELECT P.NUMORIGINAL, MAX(M.NUMTRANSENT) MAXENT
                          FROM PCMOV M, PCPRODUT P
                         WHERE P.CODPROD = M.CODPROD
                           AND M.CODOPER = 'E'
                         GROUP BY P.NUMORIGINAL) A,
                       PCPRODUT
                 WHERE PCPRODUT.CODPROD = PCMOV.CODPROD
                   AND PCMOV.NUMTRANSENT = A.MAXENT
                   AND PCPRODUT.NUMORIGINAL = A.NUMORIGINAL
                   AND PCMOV.CODOPER = 'E') ENT,
               /*-----------------------------------*/

               (SELECT P.NUMORIGINAL, MAX(LIN.DESCRICAO) AS CLASSE
                  FROM PCPRODUT P, PCLINHAPROD LIN, PCMOV M
                 WHERE P.CODLINHAPROD = LIN.CODLINHA
                   AND P.CODPROD = M.CODPROD
                   AND M.DTMOV IN (SELECT MAX(E.DTULTENT) AS DT_ENTRADA
                                     FROM PCEST E, PCPRODUT P
                                    WHERE P.CODPROD = E.CODPROD
                                      AND E.CODFILIAL = 1
                                      ".((!empty($dados['NUMORIGINAL']))?"AND P.NUMORIGINAL LIKE '".$dados['NUMORIGINAL']."'":"")."
                                    GROUP BY P.NUMORIGINAL)
                   ".((!empty($dados['NUMORIGINAL']))?"AND P.NUMORIGINAL LIKE '".$dados['NUMORIGINAL']."'":"")."
                 GROUP BY P.NUMORIGINAL) CLASSE,
               /*-----------------------------------*/

               (SELECT P.NUMORIGINAL,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '01', (NVL(M.QT, 0)))) AS JANEIRO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '02', (NVL(M.QT, 0)))) AS FEVEREIRO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '03', (NVL(M.QT, 0)))) AS MARCO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '04', (NVL(M.QT, 0)))) AS ABRIL,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '05', (NVL(M.QT, 0)))) AS MAIO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '06', (NVL(M.QT, 0)))) AS JUNHO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '07', (NVL(M.QT, 0)))) AS JULHO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '08', (NVL(M.QT, 0)))) AS AGOSTO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '09', (NVL(M.QT, 0)))) AS SETEMBRO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '10', (NVL(M.QT, 0)))) AS OUTUBRO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '11', (NVL(M.QT, 0)))) AS NOVEMBRO,
                       SUM(DECODE(TO_CHAR(M.DTMOV, 'MM'), '12', (NVL(M.QT, 0)))) AS DEZEMBRO
                  FROM PCMOV M, PCPRODUT P
                 WHERE P.CODPROD = M.CODPROD
                   AND M.CODFILIAL = 1
                   AND M.CODOPER = 'S'
                   AND M.DTCANCEL IS NULL
                   AND M.DTMOV BETWEEN TO_DATE('".$dados['dataini4meses']."','DD/MM/YYYY') AND TO_DATE('".$dados['datafim4meses']."','DD/MM/YYYY') /*4 MESES*/
                   ".((!empty($dados['NUMORIGINAL']))?"AND P.NUMORIGINAL LIKE '".$dados['NUMORIGINAL']."'":"")."
                 GROUP BY P.NUMORIGINAL) MOVIMENTO,
               /*-----------------------------------*/

               (SELECT P.NUMORIGINAL, 
                       SUM(M.QTCONT) QT_12MESES,
                       SUM(M.QTCONT * M.PUNITCONT) VL_12MESES
                  FROM PCMOV M, PCPRODUT P
                 WHERE P.CODPROD = M.CODPROD
                   AND M.CODFILIAL = 1
                   AND M.CODOPER = 'S'
                   AND M.DTCANCEL IS NULL
                   AND M.DTMOV BETWEEN TO_DATE('".$dados['datainiAno']."','DD/MM/YYYY') AND TO_DATE('".$dados['datafimAno']."','DD/MM/YYYY') /*12 MESES*/
                   ".((!empty($dados['NUMORIGINAL']))?"AND P.NUMORIGINAL LIKE '".$dados['NUMORIGINAL']."'":"")."
                 GROUP BY P.NUMORIGINAL) MOVIMENTO12
               /*-----------------------------------*/

         WHERE ESTOQUE.NUMORIGINAL = MOVIMENTO.NUMORIGINAL
           AND P.NUMORIGINAL = ESTOQUE.NUMORIGINAL
           AND P.NUMORIGINAL = MOVIMENTO.NUMORIGINAL
           AND P.NUMORIGINAL = CLASSE.NUMORIGINAL(+)
           AND P.NUMORIGINAL = MOVIMENTO12.NUMORIGINAL(+)
           AND P.NUMORIGINAL = DATAULTENT.NUMORIGINAL(+)
           AND P.NUMORIGINAL = ENT.NUMORIGINAL(+)
           ".((!empty($dados['NUMORIGINAL']))?"AND P.NUMORIGINAL LIKE '".$dados['NUMORIGINAL']."'":"")."
         GROUP BY ESTOQUE.NUMORIGINAL
         ORDER BY ESTOQUE.NUMORIGINAL";
  $return = selectOracle($sql);
  // varDump2($sql); 
  // varDump2($return); 
  // die();
  return $return;
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
                                    WHERE   p.codprod = m.codprod AND m.codoper = 'E'
                                 GROUP BY   p.numoriginal) a, pcprodut
                 WHERE       pcprodut.codprod = pcmov.codprod
                         AND pcmov.numtransent = a.maxent
                         AND pcprodut.numoriginal = a.numoriginal
                         AND pcmov.codoper = 'E') ent,
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
       WHERE       estoque.numoriginal = movimento.numoriginal
               AND p.numoriginal = estoque.numoriginal
               AND p.numoriginal = movimento.numoriginal
               AND p.numoriginal = ent.numoriginal(+)
               AND p.numoriginal LIKE '{$dados['NUMORIGINAL']}'
    GROUP BY   estoque.numoriginal
    ORDER BY   estoque.numoriginal";
  $return = selectOracle($sql);
  // varDump2($sql); 
  // varDump2($return); 
  // die();
  return $return;
}

function buscaClasses(){
  $sql = "SELECT   l.codfilial, l.codlinha, l.descricao AS classe, count(p.codprod) as qt_produtos
            FROM   pclinhaprod l
       LEFT JOIN   pcprodut p ON l.codlinha = p.codlinhaprod
        GROUP BY   l.codfilial, l.codlinha, l.descricao
        ORDER BY   l.descricao";
  return selectOracle($sql);
}

function buscaFiliais() {
  $sql = "SELECT   codigo AS codfilial, razaosocial, fantasia
            FROM   pcfilial
           WHERE   codigo <> '99'
        ORDER BY   codfilial";
  return selectOracle($sql);
}

function classeInsert($dados){
  $LINHA = converterUTF8(trim($dados["LINHA"]));
  $sql = "INSERT INTO PCLINHAPROD(
         CODLINHA
       , DESCRICAO
       , CODFILIAL
       , PRAZOMAXIMO
       , OBS
       )
         VALUES (
         (SELECT NVL(MAX(CODLINHA), 0)+1 FROM PCLINHAPROD)
       , '".$dados["DESCRICAO"]."'
       , '".$dados["CODFILIAL"]."'
       , '".$dados["PRAZOMAXIMO"]."'
       , '".$dados["OBS"]."'
       )";
       if (!empty($LINHA)) {
          return selectOracle($sql);
       } else {
          return false;
       }
}

function pesquisarClasses($dados){
  $sql = "SELECT L.CODLINHA
               , L.DESCRICAO AS CLASSE
               , L.CODFILIAL
               , F.FANTASIA AS FILIAL
               , P.NUMORIGINAL
               , count(distinct P.CODPROD) AS QT_PROD
            FROM PCLINHAPROD L
            LEFT JOIN PCFILIAL F ON L.CODFILIAL = F.CODIGO
            INNER JOIN PCPRODUT P ON L.CODLINHA = P.CODLINHAPROD
           WHERE 1=1";
  if (!empty($dados["CODFILIAL"])) {
    $sql .= PHP_EOL." AND L.CODFILIAL = '{$dados["CODFILIAL"]}'";
  }
  if ($dados["NUMORIGINAL"] !== "") {
    $sql .= PHP_EOL." AND P.NUMORIGINAL = '".mb_strtoupper(trim($dados["NUMORIGINAL"]), 'UTF-8')."'";
  } else {
    if ($dados["CODLINHA"] !== "ALL") {
      $sql .= PHP_EOL." AND L.CODLINHA = '{$dados["CODLINHA"]}'";
    }
  }
  $sql .= PHP_EOL." group by L.CODLINHA
               , L.DESCRICAO
               , L.CODFILIAL
               , F.FANTASIA
               , P.NUMORIGINAL";

  if (  ($dados["CODFILIAL"] == "ALL") && 
        ($dados["CODLINHA"] == "ALL") && 
        ($dados["NUMORIGINAL"] == "") ) {
    // varDump2($dados);
    exibeMensagem("Nenhum filtro informado para pesquisa!");
    return false;
  } else {
    $ret = selectOracle($sql);
    // varDump2($sql);
    // varDump2($ret);
    return $ret;
  }
}

function buscaCODLINHA($classe){
  $sql = "SELECT L.CODLINHA
               , L.DESCRICAO AS CLASSE
            FROM PCLINHAPROD L
           WHERE L.DESCRICAO = '{$classe}'";
  $ret = selectOracle($sql);
  if ($ret) {
    return $ret[0]["CODLINHA"];
  } else {
    return false;
  }
}

function buscaLinhas($listaClasses){
  if (is_array($listaClasses) && !empty($listaClasses)) {
    $linhas = implode("','", $listaClasses);
    // varDump2($linhas);
    $sql = "SELECT L.CODLINHA
                 , L.DESCRICAO AS CLASSE
              FROM PCLINHAPROD L
             WHERE L.DESCRICAO in ('{$linhas}')";
    // varDump2($sql);
    return selectOracle($sql);
  }
}

function buscaUltimoCODLINHA(){
  $sql = "SELECT NVL(MAX(CODLINHA),0) as CODLINHA FROM PCLINHAPROD";
  $ret = reset(selectOracle($sql));
  return intval($ret['CODLINHA']);
}

function cadastrarLinhas(array $classesSemCadastro)
{

    $ultimoCODLINHA = buscaUltimoCODLINHA();

    // Escapa valores
    $classesSemCadastro = array_map(function ($valor) {
        return str_replace("'", "''", trim($valor));
    }, $classesSemCadastro);

    $sql = "
        INSERT ALL
    ";

    $contaValidos = 0;
    foreach ($classesSemCadastro as $classe) {
      if (mb_strlen($classe) === 3) {
        $contaValidos++;
        $ultimoCODLINHA++;
        $sql .= "
            INTO PCLINHAPROD (CODLINHA, DESCRICAO, CODFILIAL)
            VALUES (
                {$ultimoCODLINHA},
                '{$classe}',
                1
            )
        ";
      }
    }

    $sql .= "
        SELECT 1 FROM DUAL
    ";

    // varDump2($sql);
    if ($contaValidos > 0) {
      if (executarOracle($sql)) {
          return true;
      } else {
          varDump2($sql);
          throw new RuntimeException("Erro ao cadastrar Classes!");
          return false;
      }
    } else {
      return false;
    }
}


function atualizarProdutoLinha($numOriginal, $codLinhaProd){
  $listaNumOriginal = implode("','", $numOriginal);
  $sql = "UPDATE PCPRODUT SET CODLINHAPROD = {$codLinhaProd} WHERE NUMORIGINAL in ('{$listaNumOriginal}')";
  return  executarOracle($sql);
}

function precificacao_pesquisar(array $dados){
  $sql = "WITH 
      /*-----------
      ÚLTIMA ENTRADA
      ------------*/
      entrada AS (
          SELECT
              m.codprod,
              MAX(m.dtmov) AS dtultentrada
          FROM pcmov m
          WHERE m.codoper LIKE 'E%'
            AND m.codoper <> 'ED'
            AND m.dtcancel IS NULL
          GROUP BY m.codprod
      ),
      /*-----------
      ÚLTIMA SAÍDA
      ------------*/
      saida AS (
          SELECT
              m.codprod,
              MAX(m.dtmov) AS dtultsaida
          FROM pcmov m
          WHERE m.codoper LIKE 'S%'
            AND m.codoper <> 'SD'
            AND m.dtcancel IS NULL
          GROUP BY m.codprod
      )
      SELECT
          p.codprod,
          p.dv,

          TRIM(p.numoriginal) AS numoriginal,
          TRIM(p.descricao)   AS descricao,

          p.codmarca,
          TRIM(m.marca)       AS marca,

          p.codfornec,
          TRIM(f.fornecedor)  AS fornecedor,

          TRIM(p.codfab)      AS codfab,

          e.dtultentrada,
          s.dtultsaida,

          CASE
              WHEN p.dtexclusao IS NULL THEN 'N'
              ELSE 'S'
          END AS produto_excluido,

          CASE
              WHEN f.dtexclusao IS NULL THEN 'N'
              ELSE 'S'
          END AS fornecedor_excluido,

          pr.ptabela,
          pr.pvenda
      FROM pcprodut p
      LEFT JOIN pcmarca m
             ON m.codmarca = p.codmarca
      LEFT JOIN pcfornec f
             ON f.codfornec = p.codfornec
      LEFT JOIN pctabpr pr
             ON pr.codprod   = p.codprod
            AND pr.numregiao = 1
      LEFT JOIN entrada e
             ON e.codprod = p.codprod
      LEFT JOIN saida s
             ON s.codprod = p.codprod
      WHERE 1=1 ";
  if (!empty($dados["CODPROD"])) {
    if (strpos($dados["CODPROD"], "-")) {
      $CODPROD = reset(explode("-", $dados["CODPROD"]));
    } else {
      $CODPROD = (int) $dados["CODPROD"];
    }
    $sql .= PHP_EOL." AND P.CODPROD = {$CODPROD}";
  }
  if (!empty($dados["NUMORIGINAL"])) {
    $NUMORIGINAL = mb_strtoupper(trim($dados["NUMORIGINAL"]), 'UTF-8');
    $sql .= PHP_EOL." AND P.NUMORIGINAL = '{$NUMORIGINAL}'";
  }
  // varDump2($sql);

  if (empty($dados["CODPROD"]) && empty($dados["NUMORIGINAL"])) {
    insereModal("warning", "É obrigatório informar algum filtro de pesquisa!");
    return;
  } else {
    if (!isset($_SESSION['PRECIFICACAO'])) 
      $_SESSION['PRECIFICACAO'] = [];
    if ($ret = selectOracle($sql) ){
      foreach ($ret as $key => $value) {
        if (!isset($_SESSION['PRECIFICACAO'][$value["CODPROD"]])) {
          $_SESSION['PRECIFICACAO'][$value["CODPROD"]] = $value;
        }
      }
    }

  }

}

function precificacao_buscaDados($CODPROD = null){
  $sql = "WITH
          /*-----------
            ÚLTIMA ENTRADA
          ------------*/
          entrada AS (
              SELECT
                  m.codprod,
                  MAX(m.dtmov) AS dtultentrada
              FROM pcmov m
              WHERE m.codoper LIKE 'E%'
                AND m.codoper <> 'ED'
                AND m.dtcancel IS NULL
              GROUP BY m.codprod
          ),

          /*-----------
            ÚLTIMA SAÍDA
          ------------*/
          saida AS (
              SELECT
                  m.codprod,
                  MAX(m.dtmov) AS dtultsaida
              FROM pcmov m
              WHERE m.codoper LIKE 'S%'
                AND m.codoper <> 'SD'
                AND m.dtcancel IS NULL
              GROUP BY m.codprod
          )

          SELECT
              p.codprod,
              p.dv,

              TRIM(p.numoriginal) AS numoriginal,
              TRIM(p.descricao)   AS descricao,

              p.codmarca,
              TRIM(m.marca)       AS marca,

              p.codfornec,
              TRIM(f.fornecedor)  AS fornecedor,

              TRIM(p.codfab)      AS codfab,

              e.dtultentrada,
              s.dtultsaida,

              /* Saldo disponível */
              GREATEST(
                  PKG_ESTOQUE.ESTOQUE_DISPONIVEL(
                      p.codprod,
                      1,
                      'VP',
                      TRUNC(SYSDATE)
                  ),
                  0
              ) AS saldo,

              CASE
                  WHEN p.dtexclusao IS NULL THEN 'N'
                  ELSE 'S'
              END AS produto_excluido,

              pr.ptabela,
              pr.pvenda

          FROM pcprodut p

          LEFT JOIN pcmarca m
                 ON m.codmarca = p.codmarca

          LEFT JOIN pcfornec f
                 ON f.codfornec = p.codfornec

          LEFT JOIN pctabpr pr
                 ON pr.codprod   = p.codprod
                AND pr.numregiao = 1

          LEFT JOIN entrada e
                 ON e.codprod = p.codprod

          LEFT JOIN saida s
                 ON s.codprod = p.codprod
          
          WHERE p.codprod = {$CODPROD}";

  if (!$CODPROD) {
    insereModal("warning", "É obrigatório informar algum filtro de pesquisa!");
    return;
  } else {
    if ($ret = selectOracle($sql) ){
      return reset($ret);
    } else {
      insereModal("danger", "CODPROD informado é inválido: {$CODPROD}");
      return;
    }
  }
}


function geraLogAlteracaoPreco(array $dados)
{
    if (
        empty($_SESSION['login']['IDUSUARIO']) ||
        empty($dados['CODPROD']) ||
        !isset($dados['PVENDA_OLD'], $dados['PVENDA_NEW'])
    ) {
        return false;
    }

    $idUsuario  = (int) $_SESSION['login']['IDUSUARIO'];
    $codprod    = (int) $dados['CODPROD'];
    $dv         = isset($dados['DV']) ? (int) $dados['DV'] : 0;
    $pvendaOld  = (float) str_replace(",", ".", str_replace(".", "", $dados['PVENDA_OLD']));
    $pvendaNew  = (float) $dados['PVENDA_NEW'];
    $motivo     = addslashes(trim($dados['MOTIVO'] ?? ''));

    $sql = "INSERT INTO logaltprecowinthor
                (idusuario, codprod, dv, pvenda_old, pvenda_new, motivo)
            VALUES
                ($idUsuario, $codprod, $dv, $pvendaOld, $pvendaNew, '$motivo')";
    // varDump2($sql); die();
    return executarOracle($sql);
}


function precificacao_editarPreco(array $dados)
{
    if (empty($dados['CODPROD']) || empty($dados['PVENDA_NEW'])) {
        insereModal("danger", "Dados obrigatórios não informados.");
        return false;
    }

    $codprod   = (int) $dados['CODPROD'];
    $pvendaNew = (float) $dados['PVENDA_NEW'];

    $sql = "UPDATE pctabpr
           SET ptabela  = $pvendaNew,
               ptabela1 = $pvendaNew,
               ptabela2 = $pvendaNew,
               ptabela3 = $pvendaNew,
               ptabela4 = $pvendaNew,
               ptabela5 = $pvendaNew,
               ptabela6 = $pvendaNew,
               ptabela7 = $pvendaNew,
               pvenda   = $pvendaNew,
               pvenda1  = $pvendaNew,
               pvenda2  = $pvendaNew,
               pvenda3  = $pvendaNew,
               pvenda4  = $pvendaNew,
               pvenda5  = $pvendaNew,
               pvenda6  = $pvendaNew,
               pvenda7  = $pvendaNew,
               dtultaltpvenda  = SYSDATE,
               dtultaltptabela = SYSDATE
         WHERE codprod = $codprod";

    // varDump2($sql); die();
    if (!executarOracle($sql)) {
        insereModal("danger", "Erro ao atualizar o preço de venda.");
        return false;
    }

    if (!geraLogAlteracaoPreco($dados)) {
        insereModal("warning", "Preço atualizado, mas falhou ao gerar log.");
        return false;
    }
    if ($_SESSION['LISTAPRECIFICACAO']) {
      $_SESSION['LISTAPRECIFICACAO'][$codprod]["PVENDA"] = $pvendaNew;
      insereModal("success", "Preço de venda atualizado com sucesso!");
    }
    return true;
}

function precificacao_zerarPreco(array $dados)
{
    if (empty($dados['CODPROD']) || empty($dados['PVENDA_NEW'])) {
        throw new Exception("Dados obrigatórios não informados.");
    }

    $numoriginal   = sanitizeOracleString($dados['NUMORIGINAL_NEW']);
    $codprod   = (int) $dados['CODPROD'];
    $pvendaNew = (float) $dados['PVENDA_NEW'];

    $sql = "UPDATE pcprodut
           SET numoriginal = '$numoriginal'
         WHERE codprod = $codprod";

    // varDump2($sql); die();
    if (!executarOracle($sql)) {
      throw new Exception("Erro ao atualizar o numoriginal do produto W$codprod de venda.");
    }

    $sql = "UPDATE pctabpr
           SET ptabela  = $pvendaNew,
               ptabela1 = $pvendaNew,
               ptabela2 = $pvendaNew,
               ptabela3 = $pvendaNew,
               ptabela4 = $pvendaNew,
               ptabela5 = $pvendaNew,
               ptabela6 = $pvendaNew,
               ptabela7 = $pvendaNew,
               pvenda   = $pvendaNew,
               pvenda1  = $pvendaNew,
               pvenda2  = $pvendaNew,
               pvenda3  = $pvendaNew,
               pvenda4  = $pvendaNew,
               pvenda5  = $pvendaNew,
               pvenda6  = $pvendaNew,
               pvenda7  = $pvendaNew,
               dtultaltpvenda  = SYSDATE,
               dtultaltptabela = SYSDATE
         WHERE codprod = $codprod";

    // varDump2($dados);
    // varDump2($sql); 
    // die();
    if (executarOracle($sql) === false) {
      throw new Exception("Erro ao atualizar o preço de venda.");
    }

    if (geraLogAlteracaoPreco($dados) === false) {
      throw new Exception("Preço atualizado, mas falhou ao gerar log.");
    }

    if ($_SESSION['PRECIFICACAO']) {
      foreach ($_SESSION['PRECIFICACAO'] as &$value){
        if ($value['CODPROD'] == $codprod) {
          $value["PVENDA"] = $pvendaNew;
        }
      }
    }
    
    insereModal("success", "Preço de venda atualizado com sucesso!");
    return true;
}

function validaSecaoDepto(int $CODEPTO) {

  $sql = "SELECT MIN(CODSEC) AS CODSEC FROM PCSECAO WHERE CODEPTO = NVL({$CODEPTO},1)";
  // varDump2($sql); die();
  if ($ret = selectOracle($sql) ){
    return $ret[0]['CODSEC'];
  } else {
    return false;
  }
}

function buscaDadosDepsec(int $CODEPTO, int $CODSEC) {
  $sql = "SELECT S.CODSEC, S.DESCRICAO AS SECAO, D.CODEPTO, D.DESCRICAO AS DEPARTAMENTO
            FROM PCSECAO S, PCDEPTO D 
           WHERE S.CODEPTO = D.CODEPTO
             AND S.CODEPTO = {$CODEPTO}
             AND S.CODSEC = {$CODSEC}";
  return reset(selectOracle($sql));
}


