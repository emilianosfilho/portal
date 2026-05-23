<div class="modal fade" id="modalNovasMarcas" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header text-center bg-danger text-white">
			<h4 class="modal-title w-100 font-weight-bold">
				<i class="fa-solid fa-file-circle-plus"></i> Novas Marcas
			</h4>
			</div>
				<div class="modal-body mx-3">
	        <table class="table table-bordered table-striped table-hover" style="width: 100%">
	          <thead>
	            <tr>
	              <th>MARCA</th>
	            </tr>
	          </thead>
	          <tbody>
	            <?php
	            // varDump2($_SESSION['INEXISTENTES']['MARCA']);
	            foreach ($_SESSION['INEXISTENTES']['MARCA'] as $key => $value) {
	              echo '<tr>';
	              echo '  <td>'.$value.'</td>';
	              echo '</tr>';
	            }
	            ?>
	          </tbody>
	        </table>
				</div>

				<div class="modal-footer">
      		<form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
	          <input type="hidden" name="op" value="<?=$dados['op']?>">
	          <input type="hidden" name="acao" value="insereNovasMarcas">
	          <button type="submit" title="Adicionar Pecas" class="btn btn-danger float-end">
	            <i class="fa fa-plus-square"></i> Adicionar Marcas(s)
	          </button>
	    		</form>
				</div>
		</div>
	</div>
</div>