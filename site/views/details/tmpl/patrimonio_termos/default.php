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

$db = JFactory::getDbo();

$idTermo 	= $this->data['edu_termos___id_raw'];
$idPessoa	= $this->data['edu_termos___name_raw'];
$idPatr = $this->data['edu_termos___nome_patri_raw'];

$data = $this->data['edu_termos___data_assina_raw'];
$data = date("d/m/Y H:i:s", strtotime($data));

$query = "SELECT DATE_FORMAT(`data_assina`, '%d/%m/%Y') as data_assinatura, assinado, 
IF (edu_patrimonios.nmr_patri <> '' , edu_patrimonios.nmr_patri, edu_patrimonios.patri_sedi ) as nr_patrimonio,
conservacao, edu_escolas.nome as unidade_ensino, edu_beneficiarios.rg, edu_beneficiarios.cpf
FROM edu_termos

INNER JOIN edu_patrimonios
INNER JOIN edu_beneficiarios
INNER JOIN edu_escolas

WHERE edu_termos.id = $idTermo
AND edu_beneficiarios.id = $idPessoa
AND edu_patrimonios.id = $idPatr 
AND unidade_ensino = edu_escolas.grupos";

$db->setQuery($query);

$result = $db->loadAssoc();

/*
$result['data_assinatura']
$result['assinado']
$result['nr_patrimonio']
$result['conservacao']
$result['unidade_ensino']
$result['rg']
$result['cpf']
*/

if ($result['assinado'] == "Sim"){
	$selo = "<div class='col-md-11 footer' style='display:inline-flex;'>
	<div class='col-md-10'style='padding-left:150px;padding-right:20px;'>Documento assinado eletronicamente por {edu_termos___name}, em " . $data . ", conforme horário oficial de Brasília, com fundamento no § 3º do art. 4º do <a target='_blank' href='https://www.in.gov.br/en/web/dou/-/decreto-n-10.543-de-13-de-novembro-de-2020-288224831'>Decreto nº 10.543, de 13 de novembro de 2020.</a></div>
	<div class='col-md-2' style='padding:1px;'><img src='/images/celo_certificado.png' width='80%'/></div></div>";
} else {
	$selo = "<p style='font-weight: bold; color: red; font-size: 15px; text-align: center; padding-top: 20px;'>Esse documento não foi assinado. Portanto, não possui validade.</p><br><br>";
}
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
		padding-left: 280px;
		padding-top: 0px;

	}

	.logo {
		padding-top: 0px;
		padding-left: 40px;
	}

	.conteudo {
		text-align: justify;
		margin: 10px auto;
		padding-bottom: 0px;
		margin-bottom: 0px;
	}

	.tabela {
		max-width:5vw;
        margin: 40px auto;
        border-collapse:collapse;
        
    }

    .tabela th {
		text-align: center;
		font-size:12px;
        border: 1px solid #000000;
        background: #D3D3D3;
    }

    .tabela td {
		text-align: center;
		font-size:12px;
        border: 1px solid #000000;
        
    }

	#assinatura {
        
        text-align: center;
        margin-top: 8%;
        
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

<div class="col-md-11 conteudo">

	<h4 class="text-center">Termo de Responsabilidade Patrimonial n°<?= $result['nr_patrimonio'] ?></h4>
	<h4 style="text-align: right; padding-top: 10px; padding-bottom: 20px;">Goiânia, <?=$result['data_assinatura'];?> </h4>

<p>Eu, {edu_termos___name}, portador(a) da carteira de identidade n.º <?= $result['rg'] ?>, e CPF <?= $result['cpf'] ?>, colaborador do CETT/UFG ou de uma das Unidades de Ensino sob sua gestão, declaro ter recebido os bens patrimoniais supracitados abaixo, pertencentes ao patrimônio do CETT/UFG, sob sua guarda ou sob guarda das fundações de apoio aos projetos, no estado de conservação indicado, pelos quais assumo total responsabilidade, comprometendo-me a informar de imediato quaisquer alterações e/ou irregularidades ocorridas, bem como zelar pela guarda, conservação e o bom uso.</p>

<table class="tabela">
        <thead>
            <tr>
                <th> Descrição do bem  </th>
                <th> Nº do patrimônio </th>
                <th> Destinação do bem </th>
                <th> Estado de conservação </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td> {edu_termos___nome_patri} </td>
                <td> <?= $result['nr_patrimonio'] ?> </td>
                <td> <?= $result['unidade_ensino'] ?> </td>
                <td> <?= $result['conservacao'] ?> </td>
            </tr>
        </tbody>
    </table>

    <div id="assinatura">
        _______________________________________
        <br>
        <b>NOME:</b> {edu_termos___name}
        <br>
        <b>CPF:</b> <?=$result['cpf']?>
        <br>
        (Assinatura do responsável pela guarda do bem)

    </div>
</div>

<hr>
<?= $selo; ?>
<br>
<h5 class="text-center" style="position: absolute; bottom: 0; width: 100%; height: 100px; text-align: center; line-height: 100px;">Av. Esperança, s/n - Chácaras de Recreio Samambaia, Goiânia - GO, 74690-900<h5>
	</div>