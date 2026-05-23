<br><br><br>
<?php  

$debug = true;
$debug = false;

if($debug) varDump2($_POST);

$IDORCAMENTO = $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'];
if($debug) varDump2($IDORCAMENTO);

//VALIDA SE O IDORCAMENTO ORIGEM É VALIDO
if ($IDORCAMENTO == "") {
  exibeMensagem("NÃO FOI POSSÍVEL DUPLICAR O ORÇAMENTO! \\r\\r"."IDORCAMENTO Não identificado ");

} else {

  if($debug) varDump2("IDORCAMENTO de origem identificado com sucesso! ".$IDORCAMENTO);

  //VALIDA SE O NOVO CODCLI É VÁLIDO
  if ($_POST['AjaxNomeCliente'] == 'CLIENTE NÃO ENCONTRADO') {
    exibeMensagem("NÃO FOI POSSÍVEL DUPLICAR O ORÇAMENTO! \\r\\r".$_POST['AjaxCodcli']." - ".$_POST['AjaxNomeCliente']." ");
    exit();
  } else {
    if($debug) varDump2("Novo CODCLI é válido! ".$_POST['AjaxCodcli']);
  }

  //VALIDA SE O NOVO CODUSUR É VÁLIDO
  if ($_POST['AjaxVendedorNome'] == 'VENDEDOR NÃO ENCONTRADO') {
    exibeMensagem("NÃO FOI POSSÍVEL DUPLICAR O ORÇAMENTO! \\r\\r".$_POST['AjaxCodusur']);
    exit();
  } else {
    if($debug) varDump2("Novo CODUSUR é válido! ".$_POST['AjaxCodusur']." - ".$_POST['AjaxVendedorNome']);
  }


  //BUSCA DADOS DO CABECALHO DO ORCAMENTO DE ORIGEM
  $CAB_ORIGEM = buscaDadosCabecalho($IDORCAMENTO);
  if($debug) varDump2($CAB_ORIGEM);



  //INSERE O NOVO CABECALHO ORCAMENTO
  $sql2 = "INSERT INTO ORCORCAMENTOC (IDORCAMENTO, DATA, STATUS, ORIGEM, IDORCAMENTOORIGINAL, IDUSUARIO, CODCLI, CODUSUR, CODCOB, CODPLPAG, ATENDIMENTO, CONTATO, TELCELULAR, TELFIXO, FRETEDESPACHO, VALORFRETE, MAQUINA, OBSERVACAO, OBSERVACAO2, CLIENTEBALCAO) 
  VALUES 
  (
    (SELECT NVL(MAX(IDORCAMENTO),0)+1 FROM ORCORCAMENTOC),
    SYSDATE,
    'ORCAMENTO',
    'DUPLICAR ORCAMENTO (ORC ORIGINAL ".$IDORCAMENTO.")',
    '".$IDORCAMENTO."',
    '".$_SESSION['login']['IDUSUARIO']."', 
    '".TRIM($_POST['AjaxCodcli'])."',
    '".TRIM($_POST['AjaxCodusur'])."',
    '".$CAB_ORIGEM['CODCOB']."',
    '".$CAB_ORIGEM['CODPLPAG']."',
    '".$CAB_ORIGEM['ATENDIMENTO']."',
    '".$CAB_ORIGEM['CONTATO']."',
    '".$CAB_ORIGEM['TELCELULAR']."',
    '".$CAB_ORIGEM['TELFIXO']."',
    '".$CAB_ORIGEM['FRETEDESPACHO']."',
    '".$CAB_ORIGEM['VALORFRETE']."',
    '".$CAB_ORIGEM['MAQUINA']."',
    '".$CAB_ORIGEM['OBSERVACAO']."',
    '".$CAB_ORIGEM['OBSERVACAO2']."',
    '".$CAB_ORIGEM['CLIENTEBALCAO']."'
  )";
  if($debug)  varDump2($sql2);

  if ($ret = executarOracle($sql2)) {
    if($debug)  varDump2("Novo cabeçalho criado com sucesso");
  } else {
    if($debug)  varDump2($ret);
    die("Erro ao criar o novo cabecalho");
  }

  //BUSCA IDORCAMENTO GERADO
  $sql3 = "SELECT MAX(IDORCAMENTO) as NOVO_IDORCAMENTO 
           FROM ORCORCAMENTOC
           WHERE CODCLI = ".$_POST['AjaxCodcli']."
             AND CODUSUR = ".$_POST['AjaxCodusur'];
  if($debug) varDump2($sql3);
  $ret3 = selectOracle($sql3);
  $NOVO_IDORCAMENTO = $ret3[0]['NOVO_IDORCAMENTO'];
  if($debug)   varDump2($NOVO_IDORCAMENTO);
  // $ret2 = executarOracle($sql2);


  //BUSCA DADOS DOS ITENS DE ORIGEM
  $sql = "SELECT * FROM ORCORCAMENTOI I 
          WHERE I.IDORCAMENTO = ".$IDORCAMENTO." 
          AND STATUS = 'A'
          ORDER BY I.IDORCAMENTOI ASC";
  $ITEM_ORIGEM = selectOracle($sql);
  if($debug)   varDump2($ITEM_ORIGEM);


  //PERCORRE OS ITENS DO ORCAMENTO DE ORIGEM E ATUALIZA ESTOQUE E PREÇO
  $ITEM_DESTINO = array();
  foreach ($ITEM_ORIGEM as $key2 => $PRODUTO_ORIGEM) {
    $ret3 = false;
    if ($PRODUTO_ORIGEM['CODPROD'] <> "") {
      
      if (empty($CAB_ORIGEM['CODFILIALNF']))    $CAB_ORIGEM['CODFILIALNF'] = 1;
      if (empty($CAB_ORIGEM['NUMPR']))          $CAB_ORIGEM['NUMPR'] = 1;
      if (empty($CAB_ORIGEM['PERCDESC']))       $CAB_ORIGEM['PERCDESC'] = 0;
      if (empty($CAB_ORIGEM['NUMREGIAO']))      $CAB_ORIGEM['NUMREGIAO'] = 1;
      
      $PRODUTO_ATUALIZADO = buscaDadosCodprod($PRODUTO_ORIGEM['CODPROD'], $CAB_ORIGEM['CODFILIALNF'], $CAB_ORIGEM['NUMPR'], $CAB_ORIGEM['PERCDESC'], $CAB_ORIGEM['NUMREGIAO']);
      if($debug) varDump2($PRODUTO_ATUALIZADO);
    
   
      $SALDO_ATUAL = intval($PRODUTO_ATUALIZADO['SALDO_ATUAL']);
      if($debug) varDump2($SALDO_ATUAL);


      //VALIDA A DISPONIBILIDADE DO PRODUTO ATUAL
      if ($PRODUTO_ORIGEM['DISPONIBILIDADE'] !== "SOB CONSULTA") {
        $DISPONIBILIDADE = $PRODUTO_ORIGEM['DISPONIBILIDADE'];
        $MELHOR_PRECO = floatval($PRODUTO_ORIGEM['PVENDA']);
      } else {
        if($SALDO_ATUAL < 0){
          $DISPONIBILIDADE = 'SOB CONSULTA';
        } else { 
          if(intval($PRODUTO_ORIGEM['QTPEDIDA']) < $SALDO_ATUAL){
            $DISPONIBILIDADE = 'IMEDIATA';
          } else {
            $DISPONIBILIDADE = 'IMEDIATA ('.$SALDO_ATUAL.')';
          }
        }
        //VALIDA O MELHOR PRECO DO PRODUTO ATUAL
        if (floatval($PRODUTO_ORIGEM['PVENDA']) > floatval($PRODUTO_ATUALIZADO['PVENDA_ATUAL'])) {
          $MELHOR_PRECO = floatval($PRODUTO_ORIGEM['PVENDA']);
        } else {
          $MELHOR_PRECO = floatval($PRODUTO_ATUALIZADO['PVENDA_ATUAL']);
        }
        if($debug) varDump2($MELHOR_PRECO);
      }
      if($debug) varDump2($DISPONIBILIDADE);

    } else {

      // CASO O ITEM NÃO POSSUA CODPROD VÁIDO
      $DISPONIBILIDADE = $PRODUTO_ORIGEM['DISPONIBILIDADE'];
      $MELHOR_PRECO = floatval($PRODUTO_ORIGEM['PVENDA']);
      $SALDO_ATUAL      = 0;

    }

    $valuePeca["ORIGEM"]          = $PRODUTO_ORIGEM["VALIDACAO"];
    $valuePeca["CODPECA"]         = $PRODUTO_ORIGEM["CODPECA"];
    $valuePeca["NUMORIGINAL"]     = $PRODUTO_ORIGEM["NUMORIGINAL"];
    $valuePeca["CODVIDE"]         = $PRODUTO_ORIGEM["CODVIDE"];
    $valuePeca["CODPROD"]         = $PRODUTO_ORIGEM["CODPROD"];
    $valuePeca["DV"]              = $PRODUTO_ORIGEM["DV"];
    $valuePeca["DESCRICAO"]       = $PRODUTO_ORIGEM["DESCRICAO"];
    $valuePeca["MARCA"]           = $PRODUTO_ORIGEM["MARCA"];
    $valuePeca["QTPEDIDA"]        = $PRODUTO_ORIGEM["QTPEDIDA"];
    $valuePeca["ICMS"]            = $PRODUTO_ORIGEM["ICMS"];
    $valuePeca["PROCEDENCIA"]     = $PRODUTO_ORIGEM["PROCEDENCIA"];
    $valuePeca["OBSERVACAO"]      = $PRODUTO_ORIGEM["OBSERVACAO"];
    $valuePeca["LOCACAO"]         = $PRODUTO_ORIGEM["LOCACAO"];
    $valuePeca["SALDO"]           = $SALDO_ATUAL;
    $valuePeca["DISPONIBILIDADE"] = $DISPONIBILIDADE;
    $valuePeca["PTABELA"]         = $MELHOR_PRECO;
    $valuePeca["PVENDA"]          = $MELHOR_PRECO;
    $valuePeca["PVENDAMIN"]       = $MELHOR_PRECO;

    insereItemOrcamento($NOVO_IDORCAMENTO, $valuePeca);

  }

  unset($_SESSION['ORCAMENTO']);

  if(!$debug) redireciona("index.php?op=62&acao=consultaOrcamento&IDORCAMENTO=".$NOVO_IDORCAMENTO);
}
?>