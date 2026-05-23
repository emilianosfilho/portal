
<div class="modal fade" id="modalFaturado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header text-center bg-primary">
        <h4 class="modal-title w-100 font-weight-bold">
          Acompanha Orçamento Faturado NR <?=$PEDIDO[0]['ORCAMENTOID']?>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>

        <div class="modal-body mx-3">
          <div class="md-form mb-5">
            
            <?php  
            $PEDIDO = buscaDadosCabRca($_SESSION['ORCAMENTO']['CAB']['NUMPEDRCA']);
            // varDump2($PEDIDO);
            $ITEM = buscaDadosItem($_SESSION['ORCAMENTO']['CAB']['NUMPEDRCA']);
            // varDump2($ITEM);
            ?>

            <table id="tb_clean2" class="table table-bordered table-striped table-hover cell-border" style="width:100%">
              <thead>
                <tr>
                  <th>DATA FATURAMENTO</th>
                  <th>STATUS</th>
                  <th>NR PEDIDO</th>
                  <th>POSIÇÃO</th>
                  <th>VENDEDOR</th>
                  <th>CLIENTE</th>
                  <th>RETORNO</th>
                </tr>
              </thead>
              <tbody>
                <?php  
                  foreach ($PEDIDO as $key => $value) {
                    echo '<tr>';
                    echo '  <td>'.$value['DTABERTURAPEDPALM'].'</td>';
                    echo '  <td>'.$value['IMPORTADO'].'</td>';
                    echo '  <td>'.$value['NUMPED'].'</td>';
                    echo '  <td>'.$value['POSICAO'].'</td>';
                    echo '  <td>'.$value['RCA'].'</td>';
                    echo '  <td>'.$value['CLIENTE'].'</td>';
                    echo '  <td>'.$value['RETORNO'].'</td>';
                    echo '</tr>';
                  }
                ?>
              </tbody>
            </table>

            <table id="tb_clean2" class="table table-bordered table-striped table-hover cell-border" style="width:100%">
              <thead>
                <tr>
                  <th>NUMSEQ</th>
                  <th>STATUS</th>
                  <th>NUMORIGINAL</th>
                  <th>CODPROD</th>
                  <th>DESCRIÇÃO</th>
                  <th>MARCA</th>
                  <th>QT PEDIDA</th>
                  <th>QT FATURADA</th>
                  <th>PREÇO UN</th>
                  <th>RETORNO</th>
                </tr>
              </thead>
              <tbody>
                <?php  
                  foreach ($ITEM as $key => $value) {
                    echo '<tr>';
                    echo '  <td>'.$value['NUMSEQ'].'</td>';
                    echo '  <td>'.$value['STATUS'].'</td>';
                    echo '  <td>'.$value['NUMORIGINAL'].'</td>';
                    echo '  <td>'.$value['CODPROD'].'</td>';
                    echo '  <td>'.$value['DESCRICAO'].'</td>';
                    echo '  <td>'.$value['MARCA'].'</td>';
                    echo '  <td>'.$value['QT'].'</td>';
                    echo '  <td>'.$value['QT_FATURADA'].'</td>';
                    echo '  <td>'.$value['PVENDA'].'</td>';
                    echo '  <td>'.$value['OBSERVACAO_PC'].'</td>';
                    echo '</tr>';
                  }
                ?>
              </tbody>
            </table>


          </div>
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button type="submit" class="btn btn-primary" class="close" data-dismiss="modal">Fechar</button>
        </div>
    </div>
  </div>
</div>