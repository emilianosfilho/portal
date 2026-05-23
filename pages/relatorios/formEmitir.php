<main>
  <div class="container">
    <?php  
      require_once "pages/relatorios/function.php";
      require_once "pages/relatorios/controller.php";
      if (!isset($dados['IDRELATORIO']) || empty($dados['IDRELATORIO'])) {
        exibeMensagem("ERRO. IDRELATORIO inválido.");
        redireciona("index.php?op=90&nav=relatorios");
      }

      $idRelatorio =  (int)$dados['IDRELATORIO'];
      $relatorio['CAB'] = buscaDadosORCRELATORIOS($idRelatorio);
      $relatorio['FILTROS'] = buscaFiltrosReltarorio($idRelatorio);
      // varDump2($relatorio);

    ?>
    <h2>
      <i class="fa-solid fa-file-excel"></i> Emitir relatório
    </h2>

    <div class="card">
      <form role="form" action="pages/relatorios/exportExcel.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="IDRELATORIO" value="<?=$dados['IDRELATORIO']?>">
        <input type="hidden" name="TIPO" value="<?=$relatorio['CAB']['TIPO']?>">
        <input type="hidden" name="DESCRICAO" value="<?=$relatorio['CAB']['DESCRICAO']?>">
        
        <div class="card-header">
          <h5><?=$relatorio['CAB']['DESCRICAO']?></h5>
        </div>

        <div class="card-body">
            
          <div class="row">
            <?php if ($relatorio['FILTROS']): ?>
              <?php foreach ($relatorio['FILTROS'] as $keyFiltro => $valueFiltro): ?>
              
                <div class="col">
                  <label><?=$valueFiltro['CAMPO']?></label>
                  <?php 
                  switch ($valueFiltro['TIPO']) {
                    case 'NUMBER':
                      echo '<input type="number" class="form-control" name="filtro['.$valueFiltro['CAMPO'].']" value="'.((isset($dados[$valueFiltro['CAMPO']]))?$dados[$valueFiltro['CAMPO']]:'').'" required>';
                      break;
                    case 'TEXT':
                      echo '<input type="text" class="form-control" name="filtro['.$valueFiltro['CAMPO'].']" required>';
                      break;

                    case 'EMAIL':
                      echo '<input type="email" class="form-control" name="filtro['.$valueFiltro['CAMPO'].']" required>';
                      break;
                    case 'SELECT':
                      $options = selectOracle($valueFiltro['FUNCAO']);
                      echo PHP_EOL.'<select class="form-select" id="select2" name="filtro['.$valueFiltro['CAMPO'].']" required>';
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
                              <input type="text" name="filtro['.$valueFiltro['CAMPO'].']" class="form-control datepicker" placeholder="Recipients username" aria-label="Recipients username" aria-describedby="basic-addon2" value="'.eval($valueFiltro['FUNCAO']).'" required>
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

