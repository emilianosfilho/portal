<main>
  <div class="container">
    <?php  
    require_once "pages/relatorios/function.php";
    
    // varDump2($dados);

    if (isset($dados['gerarExcel'])) {

      switch ($dados['IDRELATORIO']) {

        case '5':
          $dados['titulo'] = "ANALISE DE ENTRADA";
          $dados['lista'] = buscaAnaliseEntrada($dados);        
          break;
        
        default:
          if (function_exists($dados['NAMEFUNCTION'])) {
            switch ($dados['NAMEFUNCTION']) {
              case 'buscaProdutosCotados':
                $dados['lista'] = buscaProdutosCotados($dados);
                break;
              case 'buscaAnaliseEntrada':
                $dados['lista'] = buscaAnaliseEntrada($dados);
                break;
              case 'buscaProdutosSemEstoque':
                $dados['lista'] = buscaProdutosSemEstoque($dados);
                break;
              case 'listaCadastrosDuplicados':
                $dados['lista'] = listaCadastrosDuplicados($dados);
                break;
              case 'buscaProdutosAppCliente':
                $dados['lista'] = buscaProdutosAppCliente($dados);
                break;
              case 'buscaProdutosEquipamento':
                $dados['lista'] = buscaProdutosEquipamento($dados);
                break;
              case 'buscaProdutosBloqueados':
                $dados['lista'] = buscaProdutosBloqueados($dados);
                break;


              case 'buscaProdutosComEstoqueAnaliseCompras':
                $dados['lista'] = buscaProdutosCotados($dados);
                if ($compras = consultaSugestaoCompraArquivo($dados) ){
                  foreach ($dados['lista'] as $key => $value) {
                    foreach ($compras as $keyCompras => $valueCompras) {
                      if ($value['CODPECA'] == $valueCompras['NUMORIGINAL']) {
                        foreach ($valueCompras as $keyVC => $valueVC) {
                          $dados['lista'][$key][$keyVC] = $valueVC;
                        }
                      }
                    }
                    if (!isset($dados['lista'][0]['NUMORIGINAL'])) {
                      $dados['lista'][0]['NUMORIGINAL'] = '';
                      $dados['lista'][0]['SALDO'] = '';
                      $dados['lista'][0]['2022'] = '';
                      $dados['lista'][0]['2023'] = '';
                      $dados['lista'][0]['2024'] = '';
                      $dados['lista'][0]['2025'] = '';
                      $dados['lista'][0]['TODOS'] = '';
                      $dados['lista'][0]['DT_ULTIMA_ENT'] = '';
                      $dados['lista'][0]['PCOMPRA'] = '';
                      $dados['lista'][0]['FORNECEDOR'] = '';
                    }
                  }
                }
                break;
            }
          } else {
            insereModal('warning', $dados['NAMEFUNCTION'] . " função não encontrada  ");
          }
          break;
      }
      // varDump2($dados); die();
      if (isset($_SESSION['RELATORIO'])) {
        unset($_SESSION['RELATORIO']);
      }
      if ($dados['lista'] && !empty($dados['lista'])) {
        $_SESSION['RELATORIO'] = $dados;
        abreNova("pages/relatorios/exportExcel.php", $dados);
        redireciona("index.php?op=90");
      } else {
        insereModal("info", "Nenhum registro encontrado para a consulta");
      }
    }
    $REL['dados']   = buscaDadosReltarorio($dados['IDRELATORIO']);
    $REL['filtros'] = buscaFiltrosReltarorio($dados['IDRELATORIO']);
    // varDump2($REL);
    ?>
    <h2>
      <i class="fa-solid fa-file"></i> Emitir relatórios
    </h2>

    <div class="card">
      <form role="form" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="92">
        <input type="hidden" name="IDRELATORIO" value="<?=$dados['IDRELATORIO']?>">
        <input type="hidden" name="TIPO" value="<?=$dados['TIPO']?>">
        <input type="hidden" name="DESCRICAO" value="<?=$dados['DESCRICAO']?>">
        <input type="hidden" name="NAMEFUNCTION" value="<?=$dados['NAMEFUNCTION']?>">
        
        <div class="card-header">
          <h5><?=$dados['DESCRICAO']?></h5>
        </div>

        <div class="card-body">
            
          <div class="row">
            <?php if ($REL['filtros']): ?>
              <?php foreach ($REL['filtros'] as $keyFiltro => $valueFiltro): ?>
              
                <div class="col">
                  <label><?=$valueFiltro['CAMPO']?></label>
                  <?php 
                  switch ($valueFiltro['TIPO']) {
                    case 'NUMBER':
                      echo '<input type="text" class="form-control" name="'.$valueFiltro['CAMPO'].'" value="'.((isset($dados[$valueFiltro['CAMPO']]))?$dados[$valueFiltro['CAMPO']]:'').'">';
                      break;
                    case 'TEXT':
                      echo '<input type="text" class="form-control" name="'.$valueFiltro['CAMPO'].'">';
                      break;
                    case 'EMAIL':
                      echo '<input type="email" class="form-control" name="'.$valueFiltro['CAMPO'].'">';
                      break;
                    case 'SELECT':
                      $options = selectOracle($valueFiltro['FUNCAO']);
                      echo PHP_EOL.'<select class="form-select" id="select2" name="'.$valueFiltro['CAMPO'].'" required>';
                      echo PHP_EOL.'<option></option>';
                      if ($options) {
                        foreach ($options as $keyoption => $valueoption) {
                          echo PHP_EOL.'<option value="'.$valueoption['CODIGO'].'" '.(($valueoption['CODIGO']==$dados[$valueFiltro['CAMPO']])?' selected':'').'>'.$valueoption['CODIGO'].'- '.trim($valueoption['DESCRICAO']).'</option>';
                        }
                      }
                      echo PHP_EOL.'</select>';
                      break;
                    case 'DATE':
                      echo '<div class="input-group mb-3">
                              <input type="text" name="'.$valueFiltro['CAMPO'].'" class="form-control datepicker" placeholder="Recipients username" aria-label="Recipients username" aria-describedby="basic-addon2" value="'.((array_key_exists($valueFiltro['CAMPO'], $dados))?$dados[$valueFiltro['CAMPO']]:@date('d/m/Y')).'" required>
                              <span class="input-group-text" id="basic-addon2"><i class="fa-solid fa-calendar-days"></i></span>
                            </div>';
                      break;
                  }
                  ?>
                </div>

              <?php endforeach ?>
            <?php endif ?>
          </div>
        </div>

        <div class="card-footer text-end">
          <button type="submit" name="gerarExcel" class="btn btn-primary">Emitir</button>
        </div>

      </form>
    </div>

  </div>
</main>

