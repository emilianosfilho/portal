<?php
// Recomenda-se aumentar o limite de memória para planilhas grandes
ini_set('memory_limit', '512M');

$debug = true;
$arquivo = [];

try {
    // 1. Validação Inicial de Upload
    if (!isset($_FILES) || empty($_FILES)) {
        throw new Exception("ERRO - Nenhum arquivo enviado.");
    }

    $dir = defined('DIR_UPLOAD') ? DIR_UPLOAD : '/tmp/'; // Fallback caso a constante não esteja definida
    if (!is_dir($dir)) {
        throw new Exception("ERRO - Diretório de upload não encontrado: " . $dir);
    }

    // O PhpSpreadsheet é pesado, incluímos apenas quando necessário
    require_once(__DIR__ . '/../../vendor/autoload.php');

    foreach ($_FILES as $key => $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $erros = [
                1 => "Arquivo excede 'upload_max_filesize'.",
                2 => "Arquivo excede 'MAX_FILE_SIZE'.",
                3 => "Upload parcial.",
                4 => "Nenhum arquivo enviado.",
                6 => "Pasta temporária ausente.",
                7 => "Falha ao gravar no disco.",
                8 => "Extensão do PHP interrompeu o upload."
            ];
            throw new Exception("ERRO no arquivo [{$key}]: " . ($erros[$file['error']] ?? "Erro desconhecido."));
        }

        // 2. Validação de Extensão
        $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $permitidas = ['xls', 'xlsx'];
        
        if (!in_array($extensao, $permitidas)) {
            throw new Exception("ERRO - Extensão .{$extensao} não permitida. Use apenas .xls ou .xlsx");
        }

        // 3. Movimentação do Arquivo
        // Nota: Certifique-se de que $dados['NOMEARQUIVO'] está definido no escopo global ou via $_POST
        $nomeBase = isset($dados['NOMEARQUIVO']) ? somenteNumeros($dados['NOMEARQUIVO']) : 'upload';
        $inputFileName = $dir . date('Ymd_His_') . $nomeBase . '.' . $extensao;

        if (!move_uploaded_file($file['tmp_name'], $inputFileName)) {
            throw new Exception("ERRO - Falha ao mover arquivo para {$inputFileName}");
        }

        // 4. Processamento da Planilha com Foco em Performance
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
        $reader->setReadDataOnly(true); // PERFORMANCE: Ignora formatação/estilos
        $reader->setReadEmptyCells(false); // PERFORMANCE: Pula células vazias se possível
        
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        
        $dadosFinal = [];
        $cabecalho = [];

        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true); // PERFORMANCE: Itera apenas células com dados

            $linhaTemporaria = [];
            foreach ($cellIterator as $cell) {
                $colIndex = $cell->getColumn();
                $valor = $cell->getValue();

                // Sanitização baseada no seu functions.php
                if (is_numeric($valor)) {
                    // Mantém numérico ou formata se for moeda
                    $valor = (strpos((string)$valor, '.') !== false) ? moedaPHP($valor) : $valor;
                } else {
                    $valor = sanitizeOracleString($valor);
                }
                
                $linhaTemporaria[$colIndex] = $valor;
            }

            // Define o cabeçalho na primeira linha
            if ($rowIndex === 1) {
                $cabecalho = $linhaTemporaria;
                continue;
            }

            // Pula linhas vazias
            if (empty($linhaTemporaria)) continue;

            // Mapeia a linha usando o cabeçalho (Garante acuracidade para Oracle)
            $linhaMapeada = [];
            foreach ($cabecalho as $col => $titulo) {
                if (!empty($titulo)) {
                    $linhaMapeada[$titulo] = $linhaTemporaria[$col] ?? null;
                }
            }

            if (!empty($linhaMapeada)) {
                $dadosFinal[] = $linhaMapeada;
            }
        }

        $arquivo = array_merge($arquivo, $dadosFinal);

        // Limpeza de memória imediata
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $reader, $sheet);
    }

    if ($debug) {
        insereModal('success', "Processamento concluído com sucesso. Total de linhas: " . count($arquivo));
    }

} catch (Exception $e) {
    if ($debug) {
        insereModal('danger', $e->getMessage());
        die();
    } else {
        insereModal('danger', $e->getMessage());
    }
}