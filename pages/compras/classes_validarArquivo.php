<?php

if (empty($_SESSION['ARQUIVO']) || !is_array($_SESSION['ARQUIVO'])) {
    throw new RuntimeException('Sessão ARQUIVO inválida ou vazia.');
}

$classesArquivo     = [];
$classesInvalidas   = [];
$classesComCadastro = [];
$_SESSION['classesSemCadastro'] = [];

/**
 * 1) Normaliza dados e coleta classes únicas
 */
foreach ($_SESSION['ARQUIVO'] as $key => &$item) {
    foreach ($item as $chave => $valor) {
        if ($chave === '' && $valor === '') {
            unset($item[$chave]);
        }
    }

    $classe       = mb_strtoupper(trim((string)($item['CLASSE'] ?? '')), 'UTF-8');
    $numOriginal  = mb_strtoupper(trim((string)($item['NUMORIGINAL'] ?? '')), 'UTF-8');

    if ($classe === '' || $numOriginal === '') {
        // code...
    }

    $item['CLASSE']       = $classe;
    $item['NUMORIGINAL']  = $numOriginal;

    // Validação mínima
    if ($classe === '' || $numOriginal === '') {
        $item['STATUS'] = [
            'id'    => '0',
            'img'   => 'fa-circle-xmark',
            'color' => 'text-danger',
            'msg'   => 'DADOS INCOMPLETOS'
        ];
        continue;
    }

    $classesArquivo[$classe] = true;

    if (strlen($classe) !== 3) {
        $classesInvalidas[$classe] = true;
    }
}
unset($item);

/**
 * 2) Busca classes cadastradas
 */
if ($classesArquivo) {
    $linhasCadastradas = buscaLinhas(array_keys($classesArquivo));

    foreach ($linhasCadastradas as $linha) {
        if (!empty($linha['CLASSE'])) {
            $classesComCadastro[$linha['CLASSE']] = $linha;
        }
    }
}

/**
 * 3) Define status final
 */
foreach ($_SESSION['ARQUIVO'] as &$item) {

    $classe = $item['CLASSE'] ?? '';
    $classeValida = strlen($classe) === 3;
    $temCadastro  = isset($classesComCadastro[$classe]);

    $item['STATUS'] = match (true) {
        !$classeValida => [
            'id'    => '0',
            'img'   => 'fa-circle-xmark',
            'color' => 'text-danger',
            'msg'   => 'CLASSE INVÁLIDA'
        ],
        $temCadastro => [
            'id'       => '2',
            'img'      => 'fa-circle-check',
            'color'    => 'text-success',
            'msg'      => 'CLASSE VÁLIDA'
        ],
        default => [
            'id'    => '1',
            'img'   => 'fa-triangle-exclamation',
            'color' => 'text-warning',
            'msg'   => 'CLASSE SEM CADASTRO'
        ],
    };

    if ($item['STATUS']['id'] === '1') {
        $_SESSION['classesSemCadastro'][$classe] = true;
    }

    if ($item['STATUS']['id'] === '2') {
        $item['CODLINHA'] = $classesComCadastro[$classe]['CODLINHA'];
    }

}
unset($item);

// varDump2($_SESSION['ARQUIVO']);