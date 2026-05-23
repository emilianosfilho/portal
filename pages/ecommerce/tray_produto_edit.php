<main>
  <div class="container">
		<?php 

			require_once("pages/ecommerce/tray_controller.php");

		// $debug = true;

		if (isset($dados['product_id']) && !empty($dados['product_id'])) {
			$prod = tray_produto_buscaID($dados['product_id']);
			$_SESSION['PROD_TRAY'] = $prod[0]["Product"];
			if($debug) varDump2($_SESSION['PROD_TRAY']);
		}
		?>

		<form id="form_produto_edit" role="form" class="STYLE-NAME" method="POST" action="">
			<input type="hidden" name="op" value="172">
			<?php if (isset($_SESSION['PROD_TRAY']["id"])): ?>
				<input type="hidden" name="product_id" id="product_id" value="<?=$_SESSION['PROD_TRAY']["id"]?>">
				<input type="hidden" name="reference" id="reference" value="<?=$_SESSION['PROD_TRAY']['reference']?>">
			<?php endif ?>

	    <div class="row py-2">
	      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
	        <h1 class="h2"><i class="fa-solid fa-edit"></i> Editar Produto Tray #<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['id']:'')?></h1>
					<div>
						
						<div class="btn-group">

							<a class="btn btn-outline-secondary px-2" target="_blanck" href="index.php?op=172&acao=tray_modalConfirmarCancelamento&product_id=<?=$_SESSION['PROD_TRAY']["id"]?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Sair sem salvar">
							  <i class="fa-solid fa-undo"></i>
							</a>

							<a class="btn btn-outline-secondary px-2" target="_blanck" href="<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']["url"]["https"]:'')?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Visualizar produto na Loja">
							  <i class="fa-solid fa-link"></i>
							</a>

							<a class="btn btn-outline-danger px-2" target="_blanck" href="index.php?op=172&acao=tray_confirmaExcluirProduto&product_id=<?=$_SESSION['PROD_TRAY']["id"]?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Excluir produto">
							  <i class="fa-solid fa-trash"></i>
							</a>					
							
						</div>

					</div>
	      </div>
	    </div>

			<div class="card">
			  <div class="card-body row">
					<div class="col-3">
						<h4>Apresentação</h4>
						<p>Insira os dados básicos do produto. Crie uma boa descrição falando de suas funcionalidades.</p>
					</div>
					<div class="col-9 row">
						<div class="col-4">
							<div class="mb-3">
							  <label for="name" class="form-label">Nome do produto</label>
							  <input type="text" class="form-control" id="name" name="name" placeholder="" value="<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['name']:'')?>" autocomplete="off"  required>
							</div>
						</div>
						<div class="col-3">
							<div class="mb-3">
							  <label for="reference" class="form-label">Núm. Original</label>
							  <input type="text" class="form-control" id="reference" name="reference" placeholder="" value="<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['reference']:'')?>" autocomplete="off"  required>
							</div>
						</div>
						<div class="col-3">
							<div class="mb-3">
							  <label for="category_id" class="form-label">Categoria</label>
								<select class="form-select" name="category_id" id="category_id">
									<option value="" <?= (empty($_SESSION['PROD_TRAY']['category_id'])?'selected':'') ?> ></option>
									<?php if ($categories = tray_categoriaListar()): ?>
										<?php foreach ($categories as $key => $value): ?>
											<option value="<?=$value['id']?>" <?= (($_SESSION['PROD_TRAY']['category_id'] == $value['id'])?'selected':'') ?> ><?=$value['name']?></option>
										<?php endforeach ?>
									<?php endif ?>
								</select>
							</div>
						</div>
						<div class="col-2">
							<label for="available" class="form-label">Status do produto</label>
							<select class="form-select" name="available" id="available">
								<option value="1" <?= (($_SESSION['PROD_TRAY']["available"] == "1")?'selected':'') ?> >Ativo</option>
								<option value="0" <?= (($_SESSION['PROD_TRAY']["available"] == "0")?'selected':'') ?> >Inativo</option>
							</select>
						</div>
					</div>
			  </div>
			</div>

			<div class="card">
			  <div class="card-body row">
					<div class="col-3">
						<h4>Descrição</h4>
						<p>Insira os dados básicos do produto. Crie uma boa descrição falando de suas funcionalidades.</p>
					</div>
					<div class="col-9 row">
						<div class="mb-3">
						  <label for="exampleFormControlTextarea1" class="form-label">Descrição</label>
						  <textarea class="form-control" id="description" name="description" rows="10"><?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['description']:'')?></textarea>
						</div>
					</div>
			  </div>
			</div>

			<div class="card">
			  <div class="card-body row">
					<div class="col-3">
						<h4>Logística.</h4>
						<p>Informe a quantidade disponível para venda, peso e as dimensões da embalagem do produto para ser usado no cálculo do frete.</p>
					</div>
					<div class="col-9 row">
						<div class="col">
							<div class="mb-3">
							  <label for="price" class="form-label">Preço<br/>Mínimo</label>
							  <input type="text" class="form-control" id="price" name="price" placeholder="" value="<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['price']:'')?>" autocomplete="off" required>
							</div>
						</div>
						<div class="col">
							<div class="mb-3">
							  <label for="weight" class="form-label">Peso<br>[gramas]</label>
							  <input type="number" class="form-control" id="weight" name="weight" placeholder="" value="<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['weight']:'')?>" autocomplete="off"  required>
							</div>
						</div>
						<div class="col">
							<div class="mb-3">
							  <label for="height" class="form-label">Altura<br>[cm]</label>
							  <input type="number" class="form-control" id="height" name="height" placeholder="" value="<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['height']:'')?>" autocomplete="off"  required>
							</div>
						</div>
						<div class="col">
							<div class="mb-3">
							  <label for="width" class="form-label">Largura<br>[cm]</label>
							  <input type="number" class="form-control" id="width" name="width" placeholder="" value="<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['width']:'')?>" autocomplete="off"  required>
							</div>
						</div>
						<div class="col">
							<div class="mb-3">
							  <label for="length" class="form-label">Comprimento<br>[cm]</label>
							  <input type="number" class="form-control" id="length" name="length" placeholder="" value="<?=(($_SESSION['PROD_TRAY'])?$_SESSION['PROD_TRAY']['length']:'')?>" autocomplete="off"  required>
							</div>
						</div>
					</div>
			  </div>
			</div>
		</form>

		<div class="card">
		  <div class="card-body row">
				<div class="col-3">
					<h4 class="d-flex">
						<form id="form" action="index.php">
							<input type="hidden" name="op" value="172">
							<input type="hidden" name="product_id" value="<?=$_SESSION['PROD_TRAY']["id"]?>">
							<input type="hidden" name="reference" value="<?=$_SESSION['PROD_TRAY']["reference"]?>">
							<input type="hidden" name="acao" value="tray_atualizarVariacoes">
							<button class="btn btn-sm btn-outline-secondary py-1 px-2"><i class="fa-solid fa-retweet"></i></button>
						</form>
						Variações
					</h4>
					<p>A função de variações permite que seu cliente escolha uma opção de marca na página do produto.</p>
				</div>
				<div class="col-9 row">
					<?php if (isset($_SESSION['PROD_TRAY']["Variant"]) && !empty($_SESSION['PROD_TRAY']["Variant"])): ?>
					<table class="table table-hover text-lg">
					  <thead>
					    <tr>
					      <th scope="col">Marca</th>
					      <th scope="col">Cod. Winthor</th>
					      <th scope="col">Estoque</th>
					      <th scope="col">Preço Venda</th>
					    </tr>
					  </thead>
					  <tbody>
			  		<?php 
			  			$saldoTotal = 0; 
			  			$menorPreco = 0; 
			  			foreach ($_SESSION['PROD_TRAY']["Variant"] as $keyVar2 => $valueVar2){
								$variacao = tray_variacao_buscaID($valueVar2["id"]);
								if ($variacao){
					  			echo '<tr>';
					  			echo '	<th scope="row">'.$variacao["Variant"]["Sku"][0]["value"].'</th>';
					  			echo '	<td>'.$variacao["Variant"]["reference"],'</th>';
					  			echo '	<td>'.$variacao["Variant"]["stock"].'</td>';
					  			echo '	<td>R$ '.moeda($variacao["Variant"]["price"]).'</td>';
					  			echo '</tr>';
					  			$saldoTotal += $variacao["Variant"]["stock"];
					  			if (moeda($variacao["Variant"]["price"]) > 0) {
					  				if ($menorPreco == 0) {
					  					$menorPreco = moeda($variacao["Variant"]["price"]);
					  				} else {
					  					if (moeda($variacao["Variant"]["price"]) < $menorPreco) {
					  						$menorPreco = moeda($variacao["Variant"]["price"]);
					  					}
					  				}
					  			}
								}
			  			}
			  			echo '<input type="hidden" name="menorPreco" id="menorPreco" value="'.$menorPreco.'">';
			  		?>
					  </tbody>
					</table>
					<?php endif ?>

				</div>
		  </div>
		</div>

		<div class="card">
		  <div class="card-body row">
				<div class="col-3">
					<h4 class="d-flex">
						<form action="index.php">
							<input type="hidden" name="op" value="172">
							<input type="hidden" name="product_id" value="<?=$_SESSION['PROD_TRAY']["id"]?>">
							<input type="hidden" name="saldoTotal" value="<?=$saldoTotal?>">
							<input type="hidden" name="acao" value="tray_resetarImagensProduto">
							<button class="btn btn-sm btn-outline-secondary py-1 px-2"><i class="fa-solid fa-retweet"></i></button>
						</form>
						Imagens & vídeo
					</h4>
					<p>Vídeos do Youtube ou Vimeo, basta copiar o endereço do vídeo como no exemplo do campo.</p>
					<p>Aceito imagens na extensão JPG, JPEG e PNG no tamanho de 350 Kb e 2000 pixels.</p>
				</div>
				<div class="col-9 row">
			  	<?php if (!empty($_SESSION['PROD_TRAY']["ProductImage"])): ?>
						<div class="card-group">
				  		<?php foreach ($_SESSION['PROD_TRAY']["ProductImage"] as $key => $images): ?>
				  			<div class="col-2">
								  <div class="card">
								  	<center>
								    	<img src="<?=$images["http"]?>" class="card-img-top" style="height: 70px; width: 70px;" alt="...">
								  	</center>
								    <div class="card-footer">
								    	<div class="row">
								      	<a href="?op=172&product_id=<?=$dados['product_id']?>&acao=tray_modalDeletarImagem&posicao=<?=($key+1)?>" class="btn btn-xs btn-outline-danger m-0 p-0"><i class="fas fa-trash"></i></a>
								    	</div>
								    </div>
								  </div>
							  </div>
			  			<?php endforeach ?>
						</div>
			  	<?php endif ?>

				</div>
		  </div>
		</div>

		<div class="card">
		  <div class="card-body row">
				<div class="col-12">
					<button type="submit" name="acao" value="tray_atualizarProduto" class="btn btn-success float-end" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Salvar edição">
						<i class="fa-solid fa-save"></i> Salvar Alterações
					</button>
				</div>
		  </div>
		</div>

  </div>
</main>