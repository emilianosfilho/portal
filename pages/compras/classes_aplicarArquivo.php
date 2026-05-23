<?php

if (empty($_SESSION['ARQUIVO']) || !is_array($_SESSION['ARQUIVO'])) {
    throw new RuntimeException('Sessão ARQUIVO inválida ou vazia.');
}

$mapNovasLinha  = [];
$updateClasses = [];

/**
 * 1) Cadastra novas classes e monta mapa CLASSE => LINHA
 */
if (!empty($_SESSION['classesSemCadastro']) && is_array($_SESSION['classesSemCadastro'])) {

    $classesNovas = array_keys($_SESSION['classesSemCadastro']);

    cadastrarLinhas($classesNovas);

    foreach (buscaLinhas($classesNovas) as $linha) {
        if (!empty($linha['CLASSE'])) {
            $mapNovasLinha[$linha['CLASSE']] = $linha;
        }
    }
}

/**
 * 2) Processa o arquivo e consolida updates por CLASSE
 */
foreach ($_SESSION['ARQUIVO'] as &$row) {

    $classe      = $row['CLASSE']      ?? '';
    $numOriginal = $row['NUMORIGINAL'] ?? '';
    $statusId    = $row['STATUS']['id'] ?? null;

    if ($classe === '' || $numOriginal === '') {
        continue;
    }

    // STATUS 1 = recém cadastrada
    if ($statusId === "1") {

        if (empty($mapNovasLinha[$classe]['CODLINHA'])) {
            throw new RuntimeException("ERRO: CODLINHA não identificada para a classe {$classe}");
        }

        $row['STATUS'] = [
            'id'    => "2",
            'img'   => "fa-circle-check",
            'color' => "text-success",
            'msg'   => "CLASSE VÁLIDA"
        ];

        $row['CODLINHA'] = $mapNovasLinha[$classe]['CODLINHA'];
    }

    // STATUS 2 = já válida
    if ($row['STATUS']['id'] === "2") {

        if (!isset($updateClasses[$classe])) {
            $updateClasses[$classe] = [
                'CLASSE'       => $classe,
                'CODLINHA'     => $row['CODLINHA'],
                'NUMORIGINAL'  => []
            ];
        }

        $updateClasses[$classe]['NUMORIGINAL'][$numOriginal] = true;
    }
}
unset($row);

/**
 * 3) Atualiza produtos por classe
 */
$contadorErro = 0;

foreach ($updateClasses as $item) {

    if (
        $item['CLASSE'] === '' ||
        $item['CODLINHA'] === '' ||
        empty($item['NUMORIGINAL'])
    ) {
        continue;
    }

    if (!atualizarProdutoLinha(array_keys($item['NUMORIGINAL']), $item['CODLINHA'])) {
        $contadorErro++;
    }
}

/**
 * 4) Feedback final
 */
if ($contadorErro > 0) {
    insereModal("danger", "ERRO ao processar {$contadorErro} registros!");
} else {
    exibeMensagem("SUCESSO! Arquivo de Classes processado com sucesso!");
    redireciona("index.php?op=126&aba=classes&acao=clear");
}
