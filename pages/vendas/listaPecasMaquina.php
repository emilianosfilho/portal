<?php

echo '<div class="form-group">
        <div id="peca">
          <table id="tb_pecaMaquina" class="table table-bordered table-striped">     
            <thead>
            <tr>
              <th>Componente</th>
              <th>Sub-Componente</th>
              <th>Cod Peça</th>
              <th>Decrição</th>
              <th>Qtd</th>
              <th></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
              <th>Componente</th>
              <th>Sub-Componente</th>
              <th>Cod Peça</th>
              <th>Decrição</th>
              <th>Qtd</th>
              <th></th>
            </tr>
            </tfoot>
            <tbody>';

  foreach ($pecas as $key => $value) {
    echo '
            <tr>
              <td>'.$value['COMPONENTE'].'</td>
              <td>'.$value['SUBCOMPONENTE'].'</td>
              <td>'.$value['CODPECA'].'</td>
              <td><div style="font-size:0px">'.$value['DESCRICAO'].'</div><input type="text" name="PECA['.$key.'][DESCRICAO]" value="'.$value['DESCRICAO'].'"  size="50"/></td>
              <td><input type="number" name="PECA['.$key.'][QUANTIDADE]" value="'.$value['QUANTIDADE'].'"  style="width: 3em;" /></td>
              <td><input type="checkbox" name="PECA['.$key.'][CHECK]" /></td>
              <input type="hidden" name="PECA['.$key.'][IDPECA]" value="'.$value['IDPECA'].'">
              <input type="hidden" name="PECA['.$key.'][CODPECA]" value="'.$value['CODPECA'].'">
            </tr>';
    
  }
  echo '   </tbody>
          </table>
        </div>';

echo '</div> ';

?>
