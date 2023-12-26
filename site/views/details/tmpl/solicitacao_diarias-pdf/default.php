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
	$user = JFactory::getUser();
	
	// Consulta dos dados para preenchimento do relatório
		$idrelatorio = $this->data['edu_viagens___id_raw'];
		$idbeneficiario = $this->data['edu_viagens___beneficiario_raw'];
		$idsolicitacao = $this->data['edu_viagens___idsolicitacao_raw'];

		$query = "SELECT DATE_FORMAT(a.created_date, '%Y-%m-%d') as datacriacao, DATE_FORMAT(a.data_assina, '%d/%m/%Y %H:%i:%s') as dataassina, DATE_FORMAT(a.data_assina_cordena, '%d/%m/%Y %H:%i:%s') as datacordena, a.assinado, a.assinado_cordena,
						 b.rede, b.centro_custos, b.unid_ensino_dg, b.datahora_ida as dataida, b.datahora_retorno as dataretorno, b.cidade_origem, b.cidade_destino, b.relatorio_viagem, b.locomocao, b.observacoes,
						 c.nome, c.cpf,
						 d.nome as escola,
						 e.centro_custo, e.titulo_completo,
						 f.cidade, f.estado
				  FROM edu_viagens a
				  INNER JOIN edu_solicitacoes b
				  INNER JOIN edu_beneficiarios c
				  INNER JOIN edu_escolas d
				  INNER JOIN edu_ccustos e
				  INNER JOIN dataset_cidades f
				  WHERE a.id = $idrelatorio
					AND b.id = $idsolicitacao
					AND c.id = $idbeneficiario
					AND d.grupos = b.unid_ensino_dg
					AND e.id = b.centro_custos
					AND f.id = b.cidade_origem";

		$db->setQuery($query);
		$dados = $db->loadAssoc();

		$cidadedestino = $dados['cidade_destino'];
		$query = "SELECT cidade, estado
				  FROM dataset_cidades
				  WHERE id = $cidadedestino";
		$db->setQuery($query);
		$destino = $db->loadAssoc();

	// Data atual por extenso
		date_default_timezone_set('America/Sao_Paulo');
		$dateTime = new DateTime($dados['datacriacao']);
		$formatter = new IntlDateFormatter('pt_BR',IntlDateFormatter::FULL,IntlDateFormatter::NONE,'America/Sao_Paulo',IntlDateFormatter::GREGORIAN,"dd 'de' MMMM 'de' YYYY");
		$datahoje = $formatter->format($dateTime);

	// Selo(s) de assinatura(s)
		if ($dados['assinado'] == "Sim") {
			$selo = "<div class='col-md-11 footer' style='page-break-inside: avoid;'>
						<div class='col-md-10'style='padding-left:150px;'>Documento assinado eletronicamente por " . $dados['nome'] . ", em " . $dados['dataassina'] . ", conforme horário oficial de Brasília, com fundamento no § 3º do art. 4º do <a target='_blank' href='https://www.in.gov.br/en/web/dou/-/decreto-n-10.543-de-13-de-novembro-de-2020-288224831'>Decreto nº 10.543, de 13 de novembro de 2020.</a>
						</div>
						<div class='col-md-2'style='padding-top:-65px;'><img src='/images/celo_certificado.png' width='50%'/>
						</div>
					</div>";
		} else {
			$selo = "<p style='font-weight: bold; color: red; font-size: 15px; text-align: center; padding-top: 20px;'>Esse documento não foi assinado. Portanto, não possui validade.</p><br><br>";
		}

		$num = (int)$user->id;

		if($dados['assinado_cordena'] == "Sim" && ($num == 356 || (in_array("72", $user->groups) || in_array("73", $user->groups) || in_array("105", $user->groups)))) {
			$idcoordenador = $this->data['edu_viagens___coordenador_raw'];
			
			$query = "SELECT name as coordenador
				  FROM joomla_users
				  WHERE id = $idcoordenador";

			$db->setQuery($query);
			$assinatura = $db->loadAssoc();

			$coordenador = "<div class='col-md-11 footer' style='page-break-inside: avoid;'>
								<div class='col-md-10'style='padding-left:150px;'>Documento assinado eletronicamente por " . $assinatura['coordenador'] . ", em " . $dados['datacordena'] . ", conforme horário oficial de Brasília, com fundamento no § 3º do art. 4º do <a target='_blank' href='https://www.in.gov.br/en/web/dou/-/decreto-n-10.543-de-13-de-novembro-de-2020-288224831'>Decreto nº 10.543, de 13 de novembro de 2020.</a>
								</div>
								<div class='col-md-2'style='padding-top:-65px;'><img src='/images/celo_certificado.png' width='50%'/>
								</div>
							</div>";
		} else {
			$coordenador = "";
		}

	// Logo da Fundação
		if($dados['rede'] == "EFG") {
			$logo = "<div class='col-md-4' style='float: right; padding-top: -70px; width: 31%; padding-right: -60px;'>
						<img src='/images/Logo_funape.png'>
					</div>";
		} else {
			$logo = "<div class='col-md-4' style='float: right; padding-top: -60px; width: 20%; padding-right: -60px;'>
						<img src='/images/Logo_rtve.png'>
					</div>";
		}
?>

<style>
	.text-center {
		text-align: center;
	}

	thead {
		display: table-header-group;
		vertical-align: middle;
		border-color: inherit;
		border-bottom: thin solid;
		text-align: center;
	}

	tbody {
		display: table-row-group;
		vertical-align: middle;
		border-color: inherit;
		text-align: center;
	}

	tr {
		display: table-row;
		vertical-align: inherit;
		border-color: inherit;
	}

	table td {
		border-bottom: thin solid;
	}

	td {
		padding: 4px;
	}

	.container {
		justify-content: flex-start;
	}

	.conteudo {
		margin: 10px auto;
		padding-bottom: 0px;
		margin-bottom: 0px;
	}

	.tabela {
		margin: 9px auto;
		border-collapse: collapse;
		width: 100%!important;
		border: 1px solid #000000;
		margin-bottom: -1px;
	}

	.tabela th {
		padding-left: 9px;
		font-size: 12px;
		border: 1px solid #000000;
		background: #D3D3D3;
		text-align: center; 
		padding-top: 5px;
		padding-bottom: 5px;
	}

	.tabela td {
		text-align: left;
		font-size: 12px;
		border: 1px solid #000000;
		border-left-style: none;
		border-right-style: none;
		padding-top: 5px;
		padding-bottom: 5px;
		padding-left: 10px;
	}

	.tabela1 {
		margin: 9px auto;
		border-collapse: collapse;
		width: 100%!important;
		border: 1px solid #000000;
		margin-bottom: -1px;
		margin-top: -1px;
	}

	.tabela1 th {
		padding-left: 9px;
		text-align: left;
		font-size: 12px;
		border: 1px solid #000000;
		background: #D3D3D3;
		width: 150px;
	}

	.tabela1 td {
		text-align: left;
		font-size: 12px;
		border: 1px solid #000000;
		border-left-style: none;
		border-right-style: none;
		padding-left: 10px;
		padding-right: -270px;
	}

	.tabela2 {
		margin: 9px auto;
		border-collapse: collapse;
		width: 100%!important;
		border: 1px solid #000000;
		margin-bottom: -1px;
		margin-top: 10px;
	}

	.tabela2 th {
		padding-left: 9px;
		text-align: left;
		font-size: 12px;
		border: 1px solid #000000;
		background: #D3D3D3;
		text-align: center; 
		padding-top: 5px;
		padding-bottom: 5px;
	}

	#assinatura {
		text-align: center;
		margin-top: 4%;
		font-size: 11px;
	}

	.col-md-2 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 16%;
		flex: 0 0 16%;
		max-width: 16%
	}

	.col-md-4 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 33%;
		flex: 0 0 33%;
		max-width: 33%
	}

	.col-md-8 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 66.666667%;
		flex: 0 0 66.666667%;
		max-width: 66.666667%
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

	.footer {
		margin: 0px auto;
	}

</style>

<?php
	$form = $this->form;
	$model = $this->getModel();
?>

<div id="folha-A4">

	<div class="col-md-11">
		<div class="col-md-8">
			<h4 class="text-center" style="padding-top: 16px;"> Centro de Educação, Trabalho e Tecnologia - CETT </h4>
		</div>
		<?= $logo; ?>
	</div>

	<div class="col-md-12 conteudo">
		<div class="text-center">
			<h5 style="padding-top: 30px; padding-bottom: 20px;"> RELATÓRIO DE ATIVIDADE </h5>
		</div>

		<table class="tabela1" >
			<tbody>
				<tr>
					<th > NOME </th>
					<td> <?= $dados['nome']; ?> </td>
					<td> </td>
				</tr>
			</tbody>
		</table>

		<table class="tabela1" style="border-top: 0px !important; border-bottom: 0px !important;">
			<tbody>
				<tr>
					<th > PERÍODO</th>
					<td> <?php echo date('d/m/Y', strtotime($dados['dataida'])); ?> </td>
					<th style="width: 30px; text-align: center; padding-left: 0px !important;"> A </th>
					<td> <?php echo date('d/m/Y', strtotime($dados['dataretorno'])); ?> </td>
				</tr>
			</tbody>
		</table>

		<table class="tabela1" >
			<tbody>
				<tr>
					<th > MEIO DE LOCOMOÇÃO</th>
					<td> <?= $dados['locomocao']; ?> </td>
					<td> </td>
				</tr>
				<tr>
					<th > CPF </th>
					<td> <?= $dados['cpf']; ?> </td>
					<td> </td>
				</tr>
				<tr>
					<th > UNIDADE DE ENSINO </th>
					<td> <?= $dados['escola']; ?> </td>
					<td> </td>
				</tr>
				<tr>
					<th > CENTRO DE CUSTO </th>
					<td> <?= $dados['centro_custo']; ?> - <?= $dados['titulo_completo']; ?> </td>
					<td> </td>
				</tr>
				
			</tbody>
		</table>

		<table class="tabela2" >
			<tbody>
				<tr>
					<th> TRAJETO PERCORRIDO </th>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important; border-bottom: 0px !important;">
			<tbody>
				<tr>
					<th style=" width: 150px"> Dias </th>
					<th> Localidade </th>
				</tr>
				<?php 
					$start = new DateTime($dados['dataida']);
					$end = new DateTime($dados['dataretorno']);

					$intervalo = $start->diff($end);
					$dias = $intervalo->d;
					
					if ($dias == 0 || $dias == 1) {
                        echo '<tr>';
                        echo '<td>'.$start->format('d/m/Y').'</td>';
						echo '<td>'.$dados['cidade'].' - '.$dados['estado'].'</td>';
                        echo '</tr>';

                        echo '<tr>';
                        echo '<td>'.$start->format('d/m/Y').'</td>';
						echo '<td>'.$destino['cidade'].' - '.$destino['estado'].'</td>';
                        echo '</tr>';
                    } else {
                        $periodArr = new DatePeriod($start , new DateInterval('P1D') , $end);
                        $i = 0;

                        foreach($periodArr as $period) {
                            echo '<tr>';
                            echo '<td>'.$period->format('d/m/Y').'</td>';
                            if ($i == 0) {
                                echo '<td>'.$dados['cidade'].' - '.$dados['estado'].'</td>';
                            } else {
                                echo '<td>'.$destino['cidade'].' - '.$destino['estado'].'</td>';
                            }
                            echo '</tr>';
                            $i++;
                        }
                    }
				?>
				<tr>
					<?php
						echo '<td>'.$end->format('d/m/Y').'</td>';
						echo '<td>'.$dados['cidade'].' - '.$dados['estado'].'</td>';
					?>
				</tr>
			</tbody>
		</table>

		<table class="tabela">
			<tbody>
				<tr>
					<th> RESULTADOS ALCANÇADOS </th>
				</tr>
				<tr>
					<td>
						<?= $dados['relatorio_viagem']; ?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="tabela">
			<tbody>
				<tr>
					<th> OBSERVAÇÕES </th>
				</tr>
				<tr>
					<td>
						<?= $dados['observacoes']; ?>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="text-center">
			<h5 style="padding-top: 20px; padding-bottom: 20px;"> Goiânia, <?= $datahoje; ?>. </h5>
		</div>

		<div id="assinatura">			
			<div class='col-md-10 footer' style='display:flex; text-align: center; padding-left: 10px;'>
				<div class="col-md-4 text-center" style='page-break-inside: avoid;'>
					______________________________________________
					<b> Assinatura do favorecido </b>
				</div>
				<div class="col-md-4 text-center" style="float: right; padding-right: -40px; page-break-inside: avoid;">
					______________________________________________
					<b> Assinatura do coordenador </b>
				</div>
			</div>
		</div>
		
		<br>
		<br>
		<div style='page-break-inside: avoid;'>
			<hr>
			<?= $selo; ?>
			<br>
			<?= $coordenador; ?>
			<hr>
		</div>
	</div>	
</div>