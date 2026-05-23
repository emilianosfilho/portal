<?php

$_SESSION['MARCAS_INEXISTENTES'] = [];
$_SESSION['NCM_INEXISTENTES']    = [];

$arquivo = $_SESSION['ARQUIVO'];

$map = [
    'Marcas'       => [],
    'Codfab'       => [],
    'Nbm'          => [],
    'Codfornec'    => [],
    'Codorigem'    => [],
    'Produto'      => [],
    'DeptoSecao'   => []
];

foreach ($arquivo as $key => &$row) {

    /* ================= NUMORIGINAL ================= */

    $numoriginal = sanitizeOracleString($row['NUMORIGINAL'], 20);
    if ($numoriginal === '') {
        $row['V_NUMORIGINAL'] = "CAMPO NUMORIGINAL INVÁLIDO";
    }
    $row['NUMORIGINAL'] = $numoriginal;

    /* ================= MARCA ================= */

    $marca = sanitizeOracleString($row['MARCA']);
    $row['MARCA'] = $marca;

    if ($marca === '') {
        $row['V_MARCA'] = "CAMPO MARCA INVÁLIDO";
    } else {

        if (!isset($map['Marcas'][$marca])) {
            $map['Marcas'][$marca] = validaMARCA($marca);
        }

        $dadosMarca = $map['Marcas'][$marca];

        if (!$dadosMarca) {
            $row['V_MARCA'] = "MARCA $marca NÃO CADASTRADA";
            if (!isset($_SESSION['MARCAS_INEXISTENTES'][$marca])) {
            	$_SESSION['MARCAS_INEXISTENTES'][$marca] = $marca;
            }
        } else {
            $row['CODMARCA'] = $dadosMarca['CODMARCA'];

            if ($dadosMarca['ATIVO'] === 'N') {
                $row['V_MARCA'] = "MARCA INATIVA";
            }
        }
    }

    /* ================= PRODUTO ================= */

    if ($numoriginal === '0X0001') {

        $row['STATUS'] = "NOVO";

    } else {

        $produtoKey = $numoriginal . '_' . ($row['CODMARCA'] ?? 0);

        if (!isset($map['Produto'][$produtoKey])) {
            $map['Produto'][$produtoKey] =
                validaSituacaoProduto($numoriginal, $row['CODMARCA'] ?? 0);
        }

        $produto = $map['Produto'][$produtoKey];

        if ($produto) {

            $row['STATUS']    = "EXISTENTE";
            $row['CODPROD']   = $produto['CODPROD'];
            $row['DV']        = $produto['DV'];
            $row['DESCRICAO'] = $produto['DESCRICAO'];

        } else {

            $row['STATUS'] = "NOVO";

            $descricao = sanitizeOracleString($row['DESCRICAO'], 40);
            if ($descricao === '') {
                $row['V_DESCRICAO'] = "CAMPO DESCRICAO INVÁLIDO";
            }
            $row['DESCRICAO'] = $descricao;
        }
    }

    /* ================= NBM ================= */

    $nbm = (int)$row['NBM'];
    $row['NBM'] = $nbm;

    if ($nbm > 0) {

        if (!isset($map['Nbm'][$nbm])) {
            $map['Nbm'][$nbm] = validaNCM($nbm);
        }

        if ($map['Nbm'][$nbm]) {
            $row['CODNCM'] = $map['Nbm'][$nbm]['CODNCM'];
            $row['CODNCMDESC'] = $map['Nbm'][$nbm]['CODNCMDESC'];
        } else {
            $row['V_NBM'] = "NBM $nbm NÃO CADASTRADO";
            if (!isset($_SESSION['NCM_INEXISTENTES'][$nbm])) {
            	$_SESSION['NCM_INEXISTENTES'][$nbm] = $nbm;
            }

        }
    }

    /* ================= ORIGEM CST ================= */

    $codorigem = (int)$row['CODORIGEM'];
    $row['CODORIGEM'] = $codorigem;

    if (!isset($map['Codorigem'][$codorigem])) {
        $map['Codorigem'][$codorigem] = validaCodorigem($codorigem);
    }

    if ($map['Codorigem'][$codorigem]) {
        $row['IMPORTADO'] = $map['Codorigem'][$codorigem]['IMPORTADO'];
        $row['ORIGEM'] 		= $map['Codorigem'][$codorigem]['ORIGEM'];
    } else {
        $row['V_CODORIGEM'] = "CAMPO CODORIGEM INVÁLIDO";
    }

    /* ================= FORNECEDOR ================= */

    $codfornec = (int)$row['CODFORNEC'];
    $row['CODFORNEC'] = $codfornec;

    if ($codfornec > 0) {

        if (!isset($map['Codfornec'][$codfornec])) {
            $map['Codfornec'][$codfornec] = validaCODFORNEC($codfornec);
        }

        if ($map['Codfornec'][$codfornec]) {

            $forn = $map['Codfornec'][$codfornec];

            $row['FORNECEDOR']    = $forn['FORNECEDOR'];
            $row['UFFORNEC']      = $forn['ESTADO'];
            $row['TIPOFORNEC']    = $forn['TIPOFORNEC'];
            $row['EXCLUIDOFORNEC']= $forn['EXCLUIDO'];

        } else {
            $row['V_CODFORNEC'] = "FORNECEDOR $codfornec NÃO CADASTRADO";
        }
    }

    /* ================= DEPARTAMENTO ================= */

    $coddepto = (int)($row['CODEPTO'] ?: 1);
    $row['CODEPTO'] = $coddepto;

    if (!isset($map['DeptoSecao'][$coddepto])) {

        $codsec = validaSecaoDepto($coddepto);
        $map['DeptoSecao'][$coddepto] = buscaDadosDepsec($coddepto, $codsec);
    }

    $depsec = $map['DeptoSecao'][$coddepto];

    $row['DEPARTAMENTO'] = $depsec['DEPARTAMENTO'] ?? null;
    $row['CODSEC']       = $depsec['CODSEC'] ?? null;
    $row['SECAO']        = $depsec['SECAO'] ?? null;

    /* ================= REVENDA ================= */

    $revenda = strtoupper(trim($row['REVENDA']));
    $row['REVENDA'] = (($revenda=="")?"S":$revenda);
    if ($row['REVENDA']=="S") {
        $row['TIPOMERC'] = "L"; // L: LIBERADO
    } else {
        $row['TIPOMERC'] = "MC"; // MC: MATERIAL DE CONSUMO
    }

    /* ================= LOCACAO ================= */

    $locacao = strtoupper(trim($row['LOCACAO']));
    $row['LOCACAO'] = (($locacao=="")?"9999":$locacao);

}

unset($row);

// varDump2($arquivo);

$_SESSION['ARQUIVO'] = $arquivo;