<?php  
// $debug = true;

$CONTA_ERROS = 0;
$PARAM = buscaParametros();

if ($PARAM === false) {
    exibeMensagem("Não foi possivel carregar os dados de PARAMETRIZAÇÃO");
    return;
} 

$arquivo = $_SESSION['ARQUIVO'] ?? [];

if (is_array($arquivo) && !empty($arquivo)) {
    
    $lista_codprod    = [];
    $lista_codfab     = [];
    $mapa_processados = [];

    // 1. Iteramos por referência (&$produto) para atualizar os dados 'in-place'.
    // Isso garante que $_SESSION['RESULTADO'] terá EXATAMENTE a mesma sequência.
    foreach ($arquivo as $key => &$produto) {
        
        // Chave hash para identificar duplicatas instantaneamente O(1)
        $hash = $produto['NUMORIGINAL'] . '|' . $produto['CODMARCA'] . '|' . $produto['CODFAB'];

        // Se este produto exato já foi processado neste laço (duplicata no array)
        if (isset($mapa_processados[$hash])) {
            $produto['CODPROD']    = $mapa_processados[$hash]['CODPROD'];
            $produto['DV']         = $mapa_processados[$hash]['DV'];
            $produto['CODWINT']    = $mapa_processados[$hash]['CODWINT'];
            $produto['V_PCPRODUT'] = $mapa_processados[$hash]['V_PCPRODUT'];
            continue; // Pula a inserção/update no DB e vai para o próximo
        }

        // =========================================================
        // BLOCO PRODUTO NOVO
        if ($produto['STATUS'] == 'NOVO') {
            $CODPRODVALIDO = buscaPROXNUMPRODUT();
            
            if ($CODPRODVALIDO) {
                $produto['CODPROD'] = $CODPRODVALIDO;
                $produto['DV']      = buscaDigitoVerificador($CODPRODVALIDO);
                $produto['CODWINT'] = $produto['CODPROD'] . "-" . $produto['DV'];

                if (insertPCPRODUT($PARAM, $produto)) {
                    $produto['V_PCPRODUT'] = "OK";
                    $lista_codprod[$produto['CODPROD']] = true;
                    $lista_codfab[$produto['CODFAB']]   = true;
                } else {
                    $produto['V_PCPRODUT'] = "ERRO ao inserir um novo produto.";
                    $produto['CODPROD']    = false;
                    $produto['DV']         = false;
                    $produto['CODWINT']    = false;
                    $CONTA_ERROS++;
                }
            } else {
                $produto['V_PCPRODUT'] = "ERRO ao executar insertPCPRODUT: ERRO ao determinar o buscaPROXNUMPRODUT.";
                $produto['CODPROD']    = false;
                $produto['DV']         = false;
                $produto['CODWINT']    = false;
                $CONTA_ERROS++;
            }
        } 
        // =========================================================
        // BLOCO PRODUTO EXISTENTE
        else {
            $produto["CODPROD"] = (int) $produto["CODPROD"];
            
            if (updatePCPRODUT($PARAM, $produto)) {
                $produto['V_PCPRODUT'] = "OK";
                $lista_codprod[$produto['CODPROD']] = true;
                $lista_codfab[$produto['CODFAB']]   = true;
            } else {
                $produto['V_PCPRODUT'] = "ERRO ao atualizar os dados do produto. " . json_encode($produto);
                $CONTA_ERROS++;
            }
        }

        // Armazena no mapa para atualizar automaticamente caso o mesmo produto reapareça no arquivo
        $mapa_processados[$hash] = $produto;
    }
    
    // Boa prática de segurança/memória em PHP: quebrar a referência do último loop
    unset($produto); 

    // =========================================================
    // CORREÇÃO: Validação das Tabelas Auxiliares
    // Como essas validações não dependem de um único produto (são do processamento global),
    // o correto é colocar os retornos em um array isolado na sessão, e não em uma variável "$produto" local perdida.
    $_SESSION['VALIDACOES_AUXILIARES'] = [
        'V_PCPRODFILIAL'  => validaPCPRODFILIAL(),
        'V_PCEST'         => validaPCEST(),
        'V_PCTRIBENTRADA' => validaPCTRIBENTRADA(),
        'V_PCTABTRIB'     => validaPCTABTRIB(),
        'V_PCTABPR'       => validaPCTABPR(),
        'V_PCCODFABRICA'  => validaPCCODFABRICA($lista_codfab, $lista_codprod)
    ];

}

// Aqui está a MÁGICA: $arquivo agora contém os mesmos índices, na mesma ordem e estrutura,
// mas com os dados (CODPROD, Status, V_PCPRODUT) injetados pelas validações acima.
$_SESSION['RESULTADO'] = $arquivo;
// Caso o processo dependa de consultar a variável primária depois, é bom refleti-la:
$_SESSION['ARQUIVO'] = $arquivo;
?>