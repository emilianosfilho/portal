<?php
// $debug = true;

if (!isset($_SESSION['ARQUIVO']) || !$_SESSION['ARQUIVO'] || !is_array($_SESSION['ARQUIVO'])) {
    throw new Exception("Error SESSION ARQUIVO inválido", 1);
}

$arquivo = $_SESSION['ARQUIVO'];
if ($debug) varDump2($arquivo);

$map_campos = [
    "CODPECA",
    "APLICMARCA",
    "VIDE",
    "DESCRICAO",
    "MARCA",
    "IMPORTADO",
    "PRECO",
    "DATA",
    "NOMEOPCAO"
];

$campos = array_keys($arquivo[0]);
foreach ($campos as &$value) {
    $value = trim(mb_strtoupper($value, 'UTF-8'));
}
unset($value);

if ($campos <> $map_campos) {
    varDump2($campo);
    varDump2($map_campos);
    throw new Exception("Error Campos do arquivo são deferentes do arquivo modelo.", 1);
}

foreach ($arquivo as $key => &$row) {

    $row['CODPECA'] = sanitizeOracleString($row['CODPECA'], 0, true);
    if ($row['CODPECA'] !== "") {
        if (strlen($row['CODPECA']) < 3) {
            $row['V_CODPECA'] = "CAMPO CODPECA INVÁLIDO";
        }
    } else {
        unset($arquivo[$key]);
        return;
    }

    $row['APLICMARCA'] = sanitizeOracleString($row['APLICMARCA'], 0, true);
    if ($row['APLICMARCA'] === '' || strlen($row['APLICMARCA']) < 3) {
        $row['V_APLICMARCA'] = "CAMPO APLICMARCA INVÁLIDO";
    }

    $row['VIDE'] = sanitizeOracleString($row['VIDE'], 0, true);

    $row['DESCRICAO'] = sanitizeOracleString($row['DESCRICAO'], 0, true);
    if ($row['DESCRICAO'] === '' || strlen($row['DESCRICAO']) < 3) {
        $row['V_DESCRICAO'] = "CAMPO DESCRICAO INVÁLIDO";
    }

    $row['MARCA'] = sanitizeOracleString($row['MARCA'], 0, true);

    $row['IMPORTADO'] = sanitizeOracleString($row['IMPORTADO'], 0, true);

    $row['PRECO'] = sanitizeOracleString($row['PRECO'], 0, true);

    $row['DATA'] = sanitizeOracleString($row['DATA'], 0, true);

    $row['NOMEOPCAO'] = sanitizeOracleString($row['NOMEOPCAO'], 0, true);
    if ($row['VIDE'] === "") {
        $row['NOMEOPCAO'] = "NPR";
    } else {
        if ($row['NOMEOPCAO'] === "") {
            $row['NOMEOPCAO'] = "VIDE";
        }
    }

    $row['IDVIDE'] = vide_validaExiste($row);

}
unset($row);
// varDump2($arquivo);
$_SESSION['ARQUIVO'] = $arquivo;