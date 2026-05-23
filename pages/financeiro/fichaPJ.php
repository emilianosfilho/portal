	<div class="pricing-header p-3 pb-md-4 mx-auto text-center">
		<h1 class="display-4 fw-normal"><i class="fa-solid fa-user-edit"></i> Cadastro de Cliente VEMAP</h1>
	</div>

	<?php 
		require_once("pages/financeiro/controller.php"); 
	?>

	<div class="card w-100">
		<div class="card-header bg-primary">
			<h5 class="card-title">Cadastro Empresarial - Pessoa Jurídica.</h5>
		</div>
		<div class="card-body">
			<form class="row" method="post" enctype="multipart/form-data" action="ficha.php">
				<input type="hidden" name="TIPOFJ" value="J">

				<h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Dados Empresariais</h3>          

				<div class="col-2 form-group">
					<label class="form-label">CNPJ<span class="text-danger"><b>*</b></span></label>
					<input onkeypress="mascara(this, cnpj)" maxlength="18" name="CNPJ" id="CNPJ" class="form-control" placeholder="__.___.___/____-__" type="text" value="<?=$dados['CNPJ']?>" autocomplete="off" required>
					<small class="text-muted">formato: "99.999.999/9999-99"</small>
				</div> 
						 
				<div class="col-8 form-group">
					<label class="form-label">Nome Empresarial <span class="text-danger"><b>*</b></span></label>
					<input type="text" class="form-control" name="RAZAOSOCIAL" id="RAZAOSOCIAL" value="<?=$dados['RAZAOSOCIAL']?>" autocomplete="off" placeholder="RAZÃO SOCIAL">
				</div>   

				<div class="col-2 form-group">
					<label class="form-label">Insc. Estadual</label>
					<input type="text" class="form-control" name="INSCESTADUAL" id="INSCESTADUAL" value="<?=$dados['INSCESTADUAL']?>" autocomplete="off" maxlength="20" placeholder="Incrição Estadual">
				</div>         

				<div class="col-6 form-group">
					<label class="form-label">Logradouro (Avenida / Rua / Beco) <span class="text-danger"><b>*</b></span></label>
					<input type="text" class="form-control" name="ENDERECO" id="ENDERECO" value="<?=$dados['ENDERECO']?>" autocomplete="off" placeholder="LOGRADOURO">
				</div>

				<div class="col-2 form-group">
					<label class="form-label">Número (casa ou apto) <span class="text-danger"><b>*</b></span></label>
					<input onkeypress="mascara(this, numero)" maxlength="6" name="NUMERO" id="NUMERO" class="form-control" placeholder="999999" type="text" value="<?=$dados['NUMERO']?>" autocomplete="off">
					<small class="text-muted">formato: "99999"</small>
				</div> 
			
				<div class="col-4 form-group">
					<label class="form-label">Complemento</label>
					<input type="text" class="form-control" name="COMPLEMENTO" id="COMPLEMENTO" value="<?=$dados['COMPLEMENTO']?>" autocomplete="off" placeholder="COMPLEMENTO">
				</div>

				<div class="col-2 form-group">
					<label class="form-label">CEP <span class="text-danger"><b>*</b></span></label>
					<input onkeypress="mascara(this, cep)" maxlength="9" name="CEP" id="CEP" class="form-control" placeholder="_____-___" type="text" value="<?=$dados['CEP']?>" autocomplete="off">
					<small class="text-muted">formato: "99999-999"</small>
				</div>

				<div class="col-4 form-group">
					<label class="form-label mt-2 mb-0">Bairro <span class="text-danger"><b>*</b></span></label>
					<input type="text" class="form-control" name="BAIRRO" id="BAIRRO" value="<?=$dados['BAIRRO']?>" autocomplete="off">
				</div>

				<div class="col-1 form-group">
					<label class="form-label px-0 mt-2 mb-0">Estado <span class="text-danger"><b>*</b></span></label>
					<select name="UF" id="UF_DADOSEMPRESARIAIS" class="form-select select2" aria-label="" required></select>
				</div>

				<div class="col-5 form-group">
					<label class="form-label px-0 mt-2 mb-0">Município <span class="text-danger"><b>*</b></span></label>
					<select name="MUNICIPIO" id="MUNICIPIO_DADOSEMPRESARIAIS" class="form-select select2" aria-label="" required></select>
				</div>

				<h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Contato Administrativo</h3>

				<div class="col-6 form-group">
					<label class="form-label mt-2 mb-0">NOME <span class="text-danger"><b>*</b></span></label>
					<input type="text" class="form-control" name="CONTATONOME" id="CONTATONOME" value="<?=$dados['CONTATONOME']?>" autocomplete="off" placeholder="NOME COMPLETO">
				</div>

				<div class="col-6 form-group">
					<label class="form-label mt-2 mb-0">EMAIL <span class="text-danger"><b>*</b></span></label>
					<input type="mail" class="form-control" name="EMAIL" id="EMAIL" value="<?=$dados['EMAIL']?>" autocomplete="off" placeholder="exemplo@seudominio.com">
				</div>

				<div class="col-3 form-group">
					<label class="form-label mt-2 mb-0">TELEFONE CELULAR <span class="text-danger"><b>*</b></span></label>
					<input onkeypress="mascara(this, celular)" maxlength="15" name="TELCELULAR" id="TELCELULAR" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['TELCELULAR']?>" autocomplete="off">
					<small class="text-muted">formato: "(99) 99999-9999"</small>
				</div>          

				<div class="col-3 form-group">
					<label class="form-label mt-2 mb-0">TELEFONE FIXO</label>
					<input onkeypress="mascara(this, telefone)" maxlength="14" name="TELFIXO" id="TELFIXO" class="form-control" placeholder="(__) ____-____" type="text" value="<?=$dados['TELFIXO']?>" autocomplete="off">
					<small class="text-muted">formato: "(99) 9999-9999"</small>
				</div>            

				<div class="col-6 form-group">
					<label class="form-label mt-2 mb-0">OBSERVAÇÕES</label>
					<input type="text" class="form-control px-1" name="OBSERVACOES" id="OBSERVACOES" value="<?=$dados['OBSERVACOES']?>"  autocomplete="off" maxlength="50" placeholder="OBSERVACOES">
				</div>

				<h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Contato Financeiro</h3>

				<div class="col-6 form-group">
					<label class="form-label mt-2 mb-0">NOME <span class="text-danger"><b>*</b></span></label>
					<input type="text" class="form-control" name="FINANCEIRONOME" id="FINANCEIRONOME" value="<?=$dados['FINANCEIRONOME']?>" autocomplete="off" placeholder="NOME COMPLETO">
				</div>

				<div class="col-6 form-group">
					<label class="form-label mt-2 mb-0">EMAIL <span class="text-danger"><b>*</b></span></label>
					<input type="mail" class="form-control" name="FINANCEIROEMAIL" id="FINANCEIROEMAIL" value="<?=$dados['FINANCEIROEMAIL']?>" autocomplete="off" placeholder="exemplo@seudominio.com">
				</div>

				<div class="col-3 form-group">
					<label class="form-label mt-2 mb-0">TELEFONE CELULAR <span class="text-danger"><b>*</b></span></label>
					<input onkeypress="mascara(this, celular)" maxlength="15" name="TELCELULARFINANCEIRO" id="TELCELULARFINANCEIRO" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['TELCELULARFINANCEIRO']?>" autocomplete="off">
					<small class="text-muted">formato: "(99) 99999-9999"</small>
				</div>            

				<div class="col-3 form-group">
					<label class="form-label mt-2 mb-0">TELEFONE FIXO</label>
					<input onkeypress="mascara(this, telefone)" maxlength="14" name="TELFINANCEIRO" id="TELFINANCEIRO" class="form-control" placeholder="(__) ____-____" type="text" value="<?=$dados['TELFINANCEIRO']?>" autocomplete="off">
					<small class="text-muted">formato: "(99) 9999-9999"</small>
				</div>  

				<div class="col-6 form-group">
					<label class="form-label mt-2 mb-0">OBSERVAÇÕES</label>
					<input type="text" class="form-control px-1" name="FINANCEIROOBS" id="FINANCEIROOBS" value="<?=$dados['FINANCEIROOBS']?>"  autocomplete="off" maxlength="50" placeholder="OBSERVACOES">
				</div>

				<h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Compradores</h3>

				<label class="form-label mt-2 mb-0"><b>COMPRADOR 1 <span class="text-danger"><b>*</b></span></b></label>
				<div class="col-8 form-group">
					<label class="form-label mt-2 mb-0">NOME <span class="text-danger"><b>*</b></span></label>
					<input type="text" class="form-control" name="COMPRADOR1NOME" id="COMPRADOR1NOME" value="<?=$dados['COMPRADOR1NOME']?>"  autocomplete="off">
				</div>   

				<div class="col-2 form-group">
					<label class="form-label mt-2 mb-0">CPF <span class="text-danger"><b>*</b></span></label>
					<input onkeypress="mascara(this, cpf)" maxlength="14" name="COMPRADOR1CPF" id="COMPRADOR1CPF" class="form-control" placeholder="___.___.___-__" type="text" value="<?=$dados['COMPRADOR1CPF']?>" autocomplete="off">
					<small class="text-muted">formato: "999.999.999-99"</small>
				</div>    

				<div class="col-2 form-group">
					<label class="form-label mt-2 mb-0">TEL CELULAR <span class="text-danger"><b>*</b></span></label>
					<input onkeypress="mascara(this, celular)" maxlength="15" name="COMPRADOR1CELULAR" id="COMPRADOR1CELULAR" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['COMPRADOR1CELULAR']?>" autocomplete="off">
					<small class="text-muted">formato: "(99) 99999-9999"</small>
				</div>                     

				<label class="form-label mt-2 mb-0"><b>COMPRADOR 2</b></label>
				<div class="col-8 form-group">
					<label class="form-label mt-2 mb-0">NOME</label>
					<input type="text" class="form-control" name="COMPRADOR2NOME" id="COMPRADOR2NOME" value="<?=$dados['COMPRADOR2NOME']?>"  autocomplete="off">
				</div>

				<div class="col-2 form-group">
					<label class="form-label mt-2 mb-0">CPF</label>
					<input onkeypress="mascara(this, cpf)" maxlength="14" name="COMPRADOR2CPF" id="COMPRADOR2CPF" class="form-control" placeholder="___.___.___-__" type="text" value="<?=$dados['COMPRADOR2CPF']?>" autocomplete="off">
					<small class="text-muted">formato: "999.999.999-99"</small>
				</div> 

				<div class="col-2 form-group">
					<label class="form-label mt-2 mb-0">TEL CELULAR</label>
					<input onkeypress="mascara(this, celular)" maxlength="15" name="COMPRADOR2CELULAR" id="COMPRADOR2CELULAR" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['COMPRADOR2CELULAR']?>" autocomplete="off">
					<small class="text-muted">formato: "(99) 99999-9999"</small>
				</div>                    

				<label class="form-label mt-2 mb-0"><b>COMPRADOR 3</b></label>
				<div class="col-8 form-group">
					<label class="form-label mt-2 mb-0">NOME</label>
					<input type="text" class="form-control" name="COMPRADOR3NOME" id="COMPRADOR3NOME" value="<?=$dados['COMPRADOR3NOME']?>"  autocomplete="off">
				</div>

				<div class="col-2 form-group">
					<label class="form-label mt-2 mb-0">CPF</label>
					<input onkeypress="mascara(this, cpf)" maxlength="14" name="COMPRADOR3CPF" id="COMPRADOR3CPF" class="form-control" placeholder="___.___.___-__" type="text" value="<?=$dados['COMPRADOR3CPF']?>" autocomplete="off">
					<small class="text-muted">formato: "999.999.999-99"</small>
				</div> 

				<div class="col-2 form-group">
					<label class="form-label mt-2 mb-0">TEL CELULAR</label>
					<input onkeypress="mascara(this, celular)" maxlength="15" name="COMPRADOR3CELULAR" id="COMPRADOR3CELULAR" class="form-control" placeholder="(__) _____-____" type="text" value="<?=$dados['COMPRADOR3CELULAR']?>" autocomplete="off">
					<small class="text-muted">formato: "(99) 99999-9999"</small>
				</div>    

				<h3 class="mt-3"><i class="fa-solid fa-circle-info"></i> Comprovantes</h3>

				<div class="col-12 form-group">
					<label class="form-label mt-2 mb-0">Comprovante de Endereço <span class="text-danger"><b>*</b></span></label>
					<input class="form-control" type="file" name="COMPENDERECO1" id="COMPENDERECO1" value="<?=$_FILES["COMPENDERECO1"]["name"]?>" accept=".pdf" required>
					<small class="text-muted">Apenas contas de Água, Energia elétrica ou de Linha telefônica</small>
				</div> 


				<div class="col-12 mt-3 border-top">
					<a href="ficha.php" class="btn btn-secondary m-2 float-md-start">
						<i class="fa-solid fa-ban"></i> Cancelar
					</a>
					<button type="submit" name="acao" value="validarFichaPJ" class="btn btn-success m-2 float-md-end">
						<i class="fa-solid fa-save"></i> Salvar Ficha
					</button>
				</div>

			</form>
		</div>
	</div>

<script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>    
<script type="text/javascript">
	$(document).ready(function () {


		$("#CNPJ").blur(function () {
			var CNPJ = $("#CNPJ").val();
			console.log("CNPJ: "+CNPJ);
			$.ajax({ 
				type: 'GET', 
				data: { 
					acao: 'buscaFicha',
					CNPJ: CNPJ
				}, 
				url: 'pages/financeiro/ajax.php', 
				dataType: 'json', 
				success: function (response) { 
					console.log(response.data);
					$.each(response.data, function (key, value) {
						console.log("================================");
						console.log("key: "+key);
						console.log("value: "+value);
						if (key == "UF") {
							var elementKey = "UF_DADOSEMPRESARIAIS";
						} else {
							if (key == "MUNICIPIO") {
								var elementKey = "UF_DADOSEMPRESARIAIS";
							} else {
								var elementKey = key;
							}
						}
						var campo = document.getElementById(elementKey);
						if(campo) {
							var type = campo.type;
							console.log("type: "+type);
							if(type == "text") {
								campo.value = value;
							}
						}
					}) 
				}
			});
		});

		$.ajax({ 
			type: 'GET', 
			data: { 
				acao: 'buscaTodosEstados'
			}, 
			url: 'pages/financeiro/ajax.php', 
			dataType: 'json', 
			success: function (response) { 
				console.log(response);
				if (response !== null) { 
					var selectboxMun = $('#MUNICIPIO_DADOSEMPRESARIAIS'); 
					$("#MUNICIPIO_DADOSEMPRESARIAIS").empty() 
					$('<option>').val("0").text("Selecione o Município").appendTo(selectboxMun); 

					var selectboxEst = $('#UF_DADOSEMPRESARIAIS'); 
					$("#UF_DADOSEMPRESARIAIS").empty() 
					$('<option>').val("0").text("Selecione o Estado").appendTo(selectboxEst); 
					$.each(response.data, function (i, d) { 
						$('<option>').val(d.UF).text(d.UF).appendTo(selectboxEst); 
					}) 
				}
			}
		});


		$('#UF_DADOSEMPRESARIAIS').change(function() {
			var UF = $('#UF_DADOSEMPRESARIAIS').val(); //Pegando o id do estado
			$.ajax({ 
				type: 'GET', 
				data: { 
					acao: 'buscarMunicipiosUF', 
					UF: UF 
				}, 
				url: 'pages/financeiro/ajax.php', 
				dataType: 'json', 
				success: function (response) { 
					console.log(response);
					if (response !== null) { 
						var selectbox = $('#MUNICIPIO_DADOSEMPRESARIAIS'); 
						$("#MUNICIPIO_DADOSEMPRESARIAIS").empty() 
						$('<option>').val("0").text("Selecione o Município").appendTo(selectbox); 
						$.each(response.data, function (i, d) { 
							$('<option>').val(d.MUNICIPIO).text(d.MUNICIPIO).appendTo(selectbox); 
						}) 
					}
				}
			});
		});  

		var dadosUF = "<?php echo $dados['UF']?>";
		var dadosMUNICIPIO = "<?php echo $dados['MUNICIPIO']?>";
		if (dadosUF !== "") {
			$.ajax({ 
				type: 'GET', 
				data: { 
					acao: 'buscarMunicipiosUF', 
					UF: dadosUF 
				}, 
				url: 'pages/financeiro/ajax.php', 
				dataType: 'json', 
				success: function (response) { 
					console.log(response);
					if (response !== null) { 
						var selectbox = $('#MUNICIPIO_DADOSEMPRESARIAIS'); 
						$("#MUNICIPIO_DADOSEMPRESARIAIS").empty() 
						$('<option>').val("0").text("Selecione o Município").appendTo(selectbox); 
						$.each(response.data, function (i, d) { 
							$('<option>').val(d.MUNICIPIO).text(d.MUNICIPIO).appendTo(selectbox); 
						})

						$("#UF_DADOSEMPRESARIAIS option").filter(function() {
							return this.text == dadosUF; 
						}).attr('selected', true);

						$("#MUNICIPIO_DADOSEMPRESARIAIS option").filter(function() {
							return this.text == dadosMUNICIPIO; 
						}).attr('selected', true);

					}
				}
			});
		}  

	});
</script>