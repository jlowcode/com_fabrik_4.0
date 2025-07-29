<?php

/**
 * Bootstrap Details Template
 *
 * @package  Joomla
 * @subpackage  Fabrik
 * @copyright  Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license  GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since  3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<style>
	.text-justify {
		text-align: justify;
	}

	.text-left {
		text-align: left;
	}

	.text-right {
		text-align: right;
	}

	.text-center {
		text-align: center;
	}

	.col-md-2 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 16%;
		flex: 0 0 16%;
		max-width: 16%
	}

	.col-md-3 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 25%;
		flex: 0 0 25%;
		max-width: 25%
	}

	.col-md-4 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 33%;
		flex: 0 0 33%;
		max-width: 33%
	}

	.col-md-6 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 50%;
		flex: 0 0 50%;
		max-width: 50%
	}

	.col-md-8 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 66.666667%;
		flex: 0 0 66.666667%;
		max-width: 66.666667%
	}

	.col-md-9 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 75%;
		flex: 0 0 75%;
		max-width: 75%
	}

	.col-md-10 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 83%;
		flex: 0 0 83%;
		max-width: 83%
	}

	.col-md-11 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 91%;
		flex: 0 0 91%;
		max-width: 91%;
	}

	.col-md-12 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 793px;
		flex: 0 0 793px;
		max-width: 793px;
	}

	.title {
		padding-left: 290px;
		padding-top: -50px;

	}

	.logo {
		padding-top: -30px;
		padding-left: 50px;
	}

	.conteudo {
		text-align: left;
		margin: 10px auto;
		padding-bottom: 0px;
		margin-bottom: 0px;
	}

	.footer {
		margin: 0px auto;
	}
</style>


<?php
$form = $this->form;
$model = $this->getModel();

?>
<div id="folha-A4">

<div class="col-md-12" style="display:flex">
	<div class="col-md-11" style="display:flex">
		<div class="title col-md-8">
			<h3 class="text-center">Centro de Educação, Trabalho e Tecnologia – CETT</h3>
		</div>
		<div class="logo col-md-4"><img src="/images/logo-ufg-oficios.png" width="100%"></div>
	</div>
</div>

<div class="col-md-11 conteudo" style="display:flex">
	<table style="width:100%">
		<tr>
			<th>
				<h4 class="text-left"><?php echo '{edu_oficios___titulo_oficio_expedido}'; ?></h4>
			</th>
			<th>
				<h4 class="text-right">Goiânia, <?php echo '{edu_oficios___data_assinatura}'; ?></h4>
			</th>
		</tr>
	</table>
</div>

<div class="col-md-11 conteudo">
	<p><?php echo '{edu_oficios___conteudo_oficio_expedido}'; ?></p>
</div>

<?php

echo '<hr>';
echo '<div class="col-md-11 footer" style="display:inline-flex;">
	<div class="col-md-10"style="padding-left:150px;padding-right:20px;">Documento assinado eletronicamente por {edu_oficios___diretor}, em {edu_oficios___data_assinatura}, conforme horário oficial de Brasília, com fundamento no § 3º do art. 4º do <a target="_blank" href="https://www.in.gov.br/en/web/dou/-/decreto-n-10.543-de-13-de-novembro-de-2020-288224831">Decreto nº 10.543, de 13 de novembro de 2020.</a></div>
	<div class="col-md-2" style="padding:1px;"><img src="/images/certificado-digital-oficios.png" width="80%"/></div>
	</div>';
echo '	<h5 class="text-center" style="padding-top: -60px;">Av. Esperança, s/n - Chácaras de Recreio Samambaia, Goiânia - GO, 74690-900<h5>
	</div>';
