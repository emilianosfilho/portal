<br>
<br>
<br>
<br>
<?php

  
  //inicializa variaveis
  $existeDisponibilidade = 0;
  $_SESSION['listaProduto'] = array();

  foreach ($listaPeca as $key => $value) {

    // varDump2($value); die();
    
    $DESCRICAO  = strtoupper(trim($value['DESCRICAO']));
    $QUANTIDADE = intval($value['QUANTIDADE']);
    $CODPECA    = strtoupper($value['CODPECA']);
    $CODPECA    = str_replace(" ", "", $CODPECA);

    if ($QUANTIDADE > 0) {
      
      if($debug>0) varDump2("*************************************");

      if($debug>0) varDump2($CODPECA);

      // // BUSCA O PRODUTO COM BASE NO CODPEÇA DIGITADO
      $produto = buscaItemWinthor($CODPECA);
      if($debug>0) varDump2($produto);

      if ($produto != false) {
         include_once("blocoProduto.php");
         insereProduto($CODPECA, $produto, $DESCRICAO, $QUANTIDADE);
      }

      $vide = buscaItemVide($CODPECA);
      if($debug>0) varDump2($vide);
      if ($vide != false) {
        include_once("blocoVide.php");
      }

      if (($produto == false) && ($vide == false)) {
        $npr = buscaItemNpr($CODPECA);
        if($debug>0) varDump2($npr);
        if ($npr != false) {
           include_once("blocoNpr.php");
        }
      }


      if (($produto == false) && ($vide == false) && ($npr == false)) {
        if($debug>0) varDump2("NADA");
         include_once("blocoNada.php");
      }

    }
  }

  if($debug>0) varDump2($_SESSION['listaProduto']);

  foreach ($_SESSION['listaProduto'] as $key2 => $value2) {
    if (validaCodprodExisteOrcamento($value2) === false){
      salvarItemOrcamento($value2); 
    }
  }

  // die();



if($debug==0) fechaAba();