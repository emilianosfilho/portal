<main>
  <div class="container">
    <?php  
      require_once "pages/relatorios/function.php";
      require_once "pages/relatorios/controller.php";
      
      $idRelatorio = isset($dados['IDRELATORIO']) ? (int)$dados['IDRELATORIO'] : 0;

      // 1. Inicialização correta (Valor padrão/default)
      $relatorio = [
          'IDRELATORIO'    => 0,
          'IDUSUARIOALTER' => 0,
          'TIPO'           => 'EXCEL',
          'DESCRICAO'      => '',
          'COMANDO_SQL'    => '',
          'DTULTALTER'     => '',
          'DTEXCLUSAO'     => ''
      ];

      if ($idRelatorio > 0) {
          $resultadoBusca = buscaDadosORCRELATORIOS($idRelatorio);
          
          // 2. VALIDAÇÃO ESSENCIAL: O PHP 8 quebra se você acessar ['COMANDO_SQL'] em algo que não seja array
          if (is_array($resultadoBusca)) {
              $relatorio = $resultadoBusca; // Sobrescreve o default com os dados do banco
              // varDump2($relatorio);

              // 3. Verifica se a chave existe antes de extrair
              $sql = isset($relatorio['COMANDO_SQL']) ? $relatorio['COMANDO_SQL'] : '';
              $filtrosScript = extrairFiltrosScript($sql);
              // varDump2($filtrosScript);

              $filtrosRelatorio = buscaFiltrosReltarorio($idRelatorio);
              // varDump2($filtrosRelatorio);
              
              $mapFiltros = [];
              $filtrosRelatorioArr = is_array($filtrosRelatorio) ? $filtrosRelatorio : [];
              foreach ($filtrosRelatorioArr as $value) {
                $campo = mb_strtoupper($value['CAMPO'], 'UTF-8');
                if (!in_array($campo, $mapFiltros)) {
                  $mapFiltros[] = $campo;
                }  
              }
              // varDump2($mapFiltros);

              // array_diff retorna os valores em $array1 que não estão presentes em $array2
              $resultado = array_diff($filtrosScript, $mapFiltros);
              // varDump2($resultado);
              if(!empty($resultado)){
                insereAlerta("warning", "Foram identificados ".count($resultado)." filtros no seu script que não foram adicionados da seção de filtros.");
              }           

          } else {
              // Se cair aqui, a função buscaDadosORCRELATORIOS retornou false ou uma string de erro
              insereModal("danger", "Aviso: Relatório ID $idRelatorio não retornou dados válidos do Oracle.");
          }
      }
      // varDump2($relatorio);

      ?>
    
    <h2>
      <i class="fa-solid fa-file-code"></i> <?= ($idRelatorio > 0) ? 'Editar' : 'Novo' ?> Relatório
    </h2>

    <div class="card shadow-sm mt-4">
      <form role="form" action="index.php" method="POST">
        <input type="hidden" name="op" value="93">
        <input type="hidden" name="nav" value="relatorios">
        <input type="hidden" name="IDRELATORIO" value="<?= $idRelatorio ?>">

        <div class="card-body">
          <div class="row">
            <div class="col-8">
              <div class="col-12">
                <label class="form-label fw-bold">Comando SQL</label>
                <textarea class="form-control font-monospace text-sm" name="COMANDO_SQL" rows="20" maxlength="4000" required placeholder="SELECT ... FROM ..."><?= $relatorio['COMANDO_SQL'] ?></textarea>
                <div class="form-text mb-2">
                  <i class="fa-solid fa-circle-info"></i> Utilize as chaves <b>{NOME_DO_CAMPO}</b> para vincular com os filtros cadastrados. <br>
                  Exemplo: <p class="plain-text fw-bold">SELECT * FROM PCMOV WHERE DTMOV >= {DATAINI} AND CODPROD = {CODPROD}</p>
                </div>
              </div>
            </div>
            <div class="col-4">
              <div class="col mb-3">
                <label class="form-label fw-bold">Descrição do Relatório</label>
                <input type="text" class="form-control" name="DESCRICAO" required value="<?= isset($relatorio['DESCRICAO']) ? $relatorio['DESCRICAO'] : '' ?>">
              </div>
              <div class="col mb-3">
                <label class="form-label fw-bold">Tipo</label>
                <select name="TIPO" class="form-select select2">
                  <option value="EXCEL" <?=(($dados['TIPO']=='EXCEL')?'selected':'')?> >EXCEL</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label fw-bold">Filtros</label>
                <div class="float-end">
                  <a href="index.php?op=93&nav=relatorios&IDRELATORIO=<?=$idRelatorio?>&acao=adicionarFiltro" >
                    <i class="fa-solid fa-plus"></i> Adicionar
                  </a>
                </div>
                <div class="table-responvive">
                  <table class="table">
                    <tr class="bg-secondary text-light">
                      <th>Campo</th>
                      <th>Tipo</th>
                      <th>Função</th>
                      <th><i class="fa-solid fa-bolt"></i></th>
                    </tr>
                    <?php if(is_array($filtrosRelatorio)): ?>
                      <?php foreach ($filtrosRelatorio as $key => $value): ?>
                        <tr>
                          <td><?=$value['CAMPO']?></td>
                          <td><?=$value['TIPO']?></td>
                          <td><?=$value['FUNCAO']?></td>
                          <td>
                            <div class="btn-group">
                              <a class="btn btn-primary btn-xs py-1" href="index.php?op=93&nav=relatorios&IDRELATORIO=<?=$idRelatorio?>&IDRELATORIOFILTRO=<?=$value['IDRELATORIOFILTRO']?>&acao=editarFiltro" title="Editar Filtro">
                                <i class="fa-solid fa-edit"></i>
                              </a>
                              <a class="btn btn-danger btn-xs py-1" href="index.php?op=93&nav=relatorios&IDRELATORIO=<?=$idRelatorio?>&IDRELATORIOFILTRO=<?=$value['IDRELATORIOFILTRO']?>&acao=excluirFiltro" title="Excluir Filtro">
                                <i class="fa-solid fa-trash"></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach ?>
                    <?php endif ?>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer text-end bg-light">
          <div class="d-flex justify-content-evenly">
            <a href="index.php?op=90&nav=relatorios" class="btn btn-secondary"><i class="fa-solid fa-times"></i> Cancelar</a>
            <?php if($relatorio['ATIVO'] == "S"): ?>
              <button type="submit" name="acao" value="inativarRelatorio" class="btn btn-danger"><i class="fa-solid fa-ban"></i> Inativar</button>
            <?php else: ?>
              <button type="submit" name="acao" value="ativarRelatorio" class="btn btn-success"><i class="fa-solid fa-check"></i> Ativar</button>
            <?php endif ?>

            <button type="submit" name="acao" value="emitirRelatorio" class="btn btn-primary"><i class="fa-solid fa-file-excel"></i> Emitir</button>
            <button type="submit" name="acao" value="salvarRelatorioDinamico" class="btn btn-success"><i class="fa-solid fa-save"></i> Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</main>