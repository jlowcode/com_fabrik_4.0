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
		font-size: 12px;
		border: 1px solid #000000;
		background: #E1EBF7;
		text-align: center; 
		padding-top: 2px;
		padding-bottom: 5px;
	}

	.tabela td {
		text-align: left;
		font-size: 12px;
		border: 1px solid #000000;
		padding-top: 5px;
		padding-bottom: 5px;
		line-height: normal;
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
		background: #C6D9F1;
		text-align: left; 
		padding-top: 2px;
		padding-bottom: 5px;
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

	.col-md-11 {
		-webkit-box-flex: 0;
		-ms-flex:
			0 0 91%;
		flex: 0 0 91%;
		max-width: 91%;
	}

	.break {
		page-break-before: always;
	}

</style>

<div id="folha-A4">
	<div class="col-md-12 conteudo">

		<?php //Bolsistas internos ?>

		<?php

			$ano = date('Y');
			$mes = date('m');

			$datainicio = $ano.'-'.($mes - 1).'-11';
			$datafinal = $ano.'-'.$mes.'-11';

			$db = JFactory::getDbo();

			$query = "SELECT a.name, a.cpf, DATE_FORMAT(b.data_inicio, '%d/%m/%Y') as datainicio, DATE_FORMAT(b.data_termino, '%d/%m/%Y') as datatermino, b.valor_mensal
			FROM edu_bolsistas a
			INNER JOIN edu_aditamentos b
			WHERE a.rede = 'COTEC'
				AND a.id = b.bolsista
				AND a.vinculacao <> 'Externo'
				AND b.acao = 'Renovacao'
				AND (b.created_date BETWEEN '".$datainicio."' AND '".$datafinal."')
			ORDER BY a.name ASC";
			
			$db->setQuery($query);
			$renovacoes = $db->loadObjectList();

			if (!empty($renovacoes)) {
				foreach($renovacoes as $renovacao) {
					$grupo[] = [$renovacao->name, $renovacao->cpf, $renovacao->datainicio, $renovacao->datatermino, $renovacao->valor_mensal];
				}

				echo '<div>
					<h5><li> Solicito prorrogação de bolsa(s) do(s) seguinte(s) bolsista(s) interno(s): </li></h5>
				</div>';

				echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
				<table class="tabela">
					<tbody>
						<tr>
							<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
							<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
						</tr>';

				for ($x = 0; $x < count($grupo); $x++) {
					echo '<tr>';
					echo '<td style = "padding-left: 10px;">'.$grupo[$x][0].'</td>';

					if (!empty($grupo[$x][1])) {
						$cpf = explode(".", $grupo[$x][1]);
						$cpfim = explode("-", $cpf[2]);
						$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
						echo '<td style = "text-align:center;">'.$cpf.'</td>';
					} else {
						echo '<td style = "text-align:center;"></td>';
					}

					echo '<td style = "text-align:center;">'.$grupo[$x][2].' a '.$grupo[$x][3].'</td>';
					echo '<td style = "text-align:center;">R$ '.number_format($grupo[$x][4], 2, ',', '.').'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}
		?>

		<?php

			$query = "SELECT b.id, b.bolsista, a.name, a.cpf, DATE_FORMAT(b.data_inicio, '%d/%m/%Y') as datainicio, DATE_FORMAT(b.data_termino, '%d/%m/%Y') as datatermino, b.valor_mensal
			FROM edu_bolsistas a
			INNER JOIN edu_aditamentos b
			WHERE a.rede = 'COTEC'
				AND a.id = b.bolsista
				AND a.vinculacao <> 'Externo'
				AND (b.acao = 'Alteracao do valor' OR b.acao = 'Alteracao da carga horaria e valor')
				AND (b.created_date BETWEEN '".$datainicio."' AND '".$datafinal."')
			ORDER BY a.name ASC";
			
			$db->setQuery($query);
			$alteracoes = $db->loadObjectList();

			if (!empty($alteracoes)) {
				foreach($alteracoes as $alteracao) {
					$grupo3[] = [$alteracao->id, $alteracao->bolsista, $alteracao->name, $alteracao->cpf, $alteracao->datainicio, $alteracao->datatermino, $alteracao->valor_mensal];
				}

				for ($x = 0; $x < count($grupo3); $x++) {
					$idadita = $grupo3[$x][0];
					$idbolsista = $grupo3[$x][1];
					$query = "SELECT MAX(id)
					FROM edu_aditamentos
					WHERE id < ".$idadita."
						AND bolsista = ".$idbolsista;
			
					$db->setQuery($query);
					$lastid = $db->loadResult();

					$query = "SELECT valor_mensal
					FROM edu_aditamentos
					WHERE id = ".$lastid;
			
					$db->setQuery($query);
					$lastvalor = $db->loadResult();

					if ($grupo3[$x][6] > $lastvalor) {
						$aumento[] = $grupo3[$x];
					} else {
						$reducao[] = $grupo3[$x];
					}
				}

				if (!empty($aumento)) {
					echo '<div>
						<h5><li> Solicito o aumento do valor de bolsa(s) do(s) seguinte(s) bolsista(s) interno(s), devido ao acréscimo de suas atribuições no projeto: </li></h5>
					</div>';

					echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
					<table class="tabela">
						<tbody>
							<tr>
								<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
								<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
								<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
								<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
							</tr>';

					for ($x = 0; $x < count($aumento); $x++) {
						echo '<tr>';
						echo '<td style = "padding-left: 10px;">'.$aumento[$x][2].'</td>';

						if (!empty($aumento[$x][3])) {
							$cpf = explode(".", $aumento[$x][3]);
							$cpfim = explode("-", $cpf[2]);
							$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
							echo '<td style = "text-align:center;">'.$cpf.'</td>';
						} else {
							echo '<td style = "text-align:center;"></td>';
						}

						echo '<td style = "text-align:center;">'.$aumento[$x][4].' a '.$aumento[$x][5].'</td>';
						echo '<td style = "text-align:center;">R$ '.number_format($aumento[$x][6], 2, ',', '.').'</td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
					echo '</div>';
				}

				if (!empty($reducao)) {
					echo '<div>
						<h5><li> Solicito redução de bolsa(s) do(s) seguinte(s) bolsista(s) interno(s), devido à diminuição de suas atribuições no projeto: </li></h5>
					</div>';

					echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
					<table class="tabela">
						<tbody>
							<tr>
								<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
								<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
								<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
								<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
							</tr>';

					for ($x = 0; $x < count($reducao); $x++) {
						echo '<tr>';
						echo '<td style = "padding-left: 10px;">'.$reducao[$x][2].'</td>';

						if (!empty($reducao[$x][3])) {
							$cpf = explode(".", $reducao[$x][3]);
							$cpfim = explode("-", $cpf[2]);
							$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
							echo '<td style = "text-align:center;">'.$cpf.'</td>';
						} else {
							echo '<td style = "text-align:center;"></td>';
						}

						echo '<td style = "text-align:center;">'.$reducao[$x][4].' a '.$reducao[$x][5].'</td>';
						echo '<td style = "text-align:center;">R$ '.number_format($reducao[$x][6], 2, ',', '.').'</td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
					echo '</div>';
				}
			}
		?>

		<?php

			$query = "SELECT a.name, a.cpf, DATE_FORMAT(b.data_inicio, '%d/%m/%Y') as datainicio, DATE_FORMAT(b.data_termino, '%d/%m/%Y') as datatermino, b.valor_mensal
			FROM edu_bolsistas a
			INNER JOIN edu_aditamentos b
			WHERE a.rede = 'COTEC'
				AND a.id = b.bolsista
				AND a.vinculacao <> 'Externo'
				AND b.acao = '-'
				AND (b.created_date BETWEEN '".$datainicio."' AND '".$datafinal."')
			ORDER BY a.name ASC";
			
			$db->setQuery($query);
			$inclusoes = $db->loadObjectList();

			if (!empty($inclusoes)) {
				foreach($inclusoes as $inclusao) {
					$grupo1[] = [$inclusao->name, $inclusao->cpf, $inclusao->datainicio, $inclusao->datatermino, $inclusao->valor_mensal];
				}

				echo '<div>
					<h5><li> Solicito inclusão de bolsa(s) do(s) seguinte(s) bolsista(s) interno(s): </li></h5>
				</div>';

				echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
				<table class="tabela">
					<tbody>
						<tr>
							<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
							<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
						</tr>';

				for ($x = 0; $x < count($grupo1); $x++) {
					echo '<tr>';
					echo '<td style = "padding-left: 10px;">'.$grupo1[$x][0].'</td>';

					if (!empty($grupo1[$x][1])) {
						$cpf = explode(".", $grupo1[$x][1]);
						$cpfim = explode("-", $cpf[2]);
						$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
						echo '<td style = "text-align:center;">'.$cpf.'</td>';
					} else {
						echo '<td style = "text-align:center;"></td>';
					}

					echo '<td style = "text-align:center;">'.$grupo1[$x][2].' a '.$grupo1[$x][3].'</td>';
					echo '<td style = "text-align:center;">R$ '.number_format($grupo1[$x][4], 2, ',', '.').'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}
		?>

		<?php

			$query = "SELECT a.name, a.cpf, DATE_FORMAT(a.data_bolsa, '%d/%m/%Y') as datainicio, DATE_FORMAT(a.databolsa_termino, '%d/%m/%Y') as datatermino, a.valor_mensal
			FROM edu_bolsistas a
			INNER JOIN edu_aditamentos b
			WHERE a.rede = 'COTEC'
				AND a.id = b.bolsista
				AND a.vinculacao <> 'Externo'
				AND b.acao = 'Encerramento'
				AND (b.created_date BETWEEN '".$datainicio."' AND '".$datafinal."')
			ORDER BY a.name ASC";
			
			$db->setQuery($query);
			$exclusoes = $db->loadObjectList();

			if (!empty($exclusoes)) {
				foreach($exclusoes as $exclusao) {
					$grupo2[] = [$exclusao->name, $exclusao->cpf, $exclusao->datainicio, $exclusao->datatermino, $exclusao->valor_mensal];
				}

				echo '<div>
					<h5><li> Solicito exclusão de bolsa(s) do(s) seguinte(s) bolsista(s) interno(s): </li></h5>
				</div>';

				echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
				<table class="tabela">
					<tbody>
						<tr>
							<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
							<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
						</tr>';

				for ($x = 0; $x < count($grupo2); $x++) {
					echo '<tr>';
					echo '<td style = "padding-left: 10px;">'.$grupo2[$x][0].'</td>';

					if (!empty($grupo2[$x][1])) {
						$cpf = explode(".", $grupo2[$x][1]);
						$cpfim = explode("-", $cpf[2]);
						$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
						echo '<td style = "text-align:center;">'.$cpf.'</td>';
					} else {
						echo '<td style = "text-align:center;"></td>';
					}

					echo '<td style = "text-align:center;">'.$grupo2[$x][2].' a '.$grupo2[$x][3].'</td>';
					echo '<td style = "text-align:center;">R$ '.number_format($grupo2[$x][4], 2, ',', '.').'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}
		?>

		<?php //Bolsistas externos ?>

		<?php

			$ano = date('Y');
			$mes = date('m');

			$datainicio = $ano.'-'.($mes - 1).'-11';
			$datafinal = $ano.'-'.$mes.'-11';

			$db = JFactory::getDbo();

			$query = "SELECT a.name, a.cpf, DATE_FORMAT(b.data_inicio, '%d/%m/%Y') as datainicio, DATE_FORMAT(b.data_termino, '%d/%m/%Y') as datatermino, b.valor_mensal
			FROM edu_bolsistas a
			INNER JOIN edu_aditamentos b
			WHERE a.rede = 'COTEC'
				AND a.id = b.bolsista
				AND a.vinculacao = 'Externo'
				AND b.acao = 'Renovacao'
				AND (b.created_date BETWEEN '".$datainicio."' AND '".$datafinal."')
			ORDER BY a.name ASC";
			
			$db->setQuery($query);
			$renovacoes = $db->loadObjectList();

			if (!empty($renovacoes)) {
				foreach($renovacoes as $renovacao) {
					$exgrupo[] = [$renovacao->name, $renovacao->cpf, $renovacao->datainicio, $renovacao->datatermino, $renovacao->valor_mensal];
				}

				echo '<div>
					<h5><li> Solicito prorrogação de bolsa(s) do(s) seguinte(s) bolsista(s) externo(s): </li></h5>
				</div>';

				echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
				<table class="tabela">
					<tbody>
						<tr>
							<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
							<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
						</tr>';

				for ($x = 0; $x < count($exgrupo); $x++) {
					echo '<tr>';
					echo '<td style = "padding-left: 10px;">'.$exgrupo[$x][0].'</td>';

					if (!empty($exgrupo[$x][1])) {
						$cpf = explode(".", $exgrupo[$x][1]);
						$cpfim = explode("-", $cpf[2]);
						$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
						echo '<td style = "text-align:center;">'.$cpf.'</td>';
					} else {
						echo '<td style = "text-align:center;"></td>';
					}

					echo '<td style = "text-align:center;">'.$exgrupo[$x][2].' a '.$exgrupo[$x][3].'</td>';
					echo '<td style = "text-align:center;">R$ '.number_format($exgrupo[$x][4], 2, ',', '.').'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}
		?>

		<?php

			$query = "SELECT b.id, b.bolsista, a.name, a.cpf, DATE_FORMAT(b.data_inicio, '%d/%m/%Y') as datainicio, DATE_FORMAT(b.data_termino, '%d/%m/%Y') as datatermino, b.valor_mensal
			FROM edu_bolsistas a
			INNER JOIN edu_aditamentos b
			WHERE a.rede = 'COTEC'
				AND a.id = b.bolsista
				AND a.vinculacao = 'Externo'
				AND (b.acao = 'Alteracao do valor' OR b.acao = 'Alteracao da carga horaria e valor')
				AND (b.created_date BETWEEN '".$datainicio."' AND '".$datafinal."')
			ORDER BY a.name ASC";
			
			$db->setQuery($query);
			$alteracoes = $db->loadObjectList();

			if (!empty($alteracoes)) {
				foreach($alteracoes as $alteracao) {
					$exgrupo3[] = [$alteracao->id, $alteracao->bolsista, $alteracao->name, $alteracao->cpf, $alteracao->datainicio, $alteracao->datatermino, $alteracao->valor_mensal];
				}

				for ($x = 0; $x < count($exgrupo3); $x++) {
					$idadita = $exgrupo3[$x][0];
					$idbolsista = $exgrupo3[$x][1];
					$query = "SELECT MAX(id)
					FROM edu_aditamentos
					WHERE id < ".$idadita."
						AND bolsista = ".$idbolsista;
			
					$db->setQuery($query);
					$lastid = $db->loadResult();

					$query = "SELECT valor_mensal
					FROM edu_aditamentos
					WHERE id = ".$lastid;
			
					$db->setQuery($query);
					$lastvalor = $db->loadResult();

					if ($exgrupo3[$x][6] > $lastvalor) {
						$exaumento[] = $exgrupo3[$x];
					} else {
						$exreducao[] = $exgrupo3[$x];
					}
				}

				if (!empty($exaumento)) {
					echo '<div>
						<h5><li> Solicito o aumento do valor de bolsa(s) do(s) seguinte(s) bolsista(s) externo(s), devido ao acréscimo de suas atribuições no projeto: </li></h5>
					</div>';

					echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
					<table class="tabela">
						<tbody>
							<tr>
								<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
								<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
								<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
								<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
							</tr>';

					for ($x = 0; $x < count($exaumento); $x++) {
						echo '<tr>';
						echo '<td style = "padding-left: 10px;">'.$exaumento[$x][2].'</td>';

						if (!empty($exaumento[$x][3])) {
							$cpf = explode(".", $exaumento[$x][3]);
							$cpfim = explode("-", $cpf[2]);
							$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
							echo '<td style = "text-align:center;">'.$cpf.'</td>';
						} else {
							echo '<td style = "text-align:center;"></td>';
						}

						echo '<td style = "text-align:center;">'.$exaumento[$x][4].' a '.$exaumento[$x][5].'</td>';
						echo '<td style = "text-align:center;">R$ '.number_format($exaumento[$x][6], 2, ',', '.').'</td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
					echo '</div>';
				}

				if (!empty($exreducao)) {
					echo '<div>
						<h5><li> Solicito redução de bolsa(s) do(s) seguinte(s) bolsista(s) externo(s), devido à diminuição de suas atribuições no projeto: </li></h5>
					</div>';

					echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
					<table class="tabela">
						<tbody>
							<tr>
								<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
								<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
								<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
								<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
							</tr>';

					for ($x = 0; $x < count($exreducao); $x++) {
						echo '<tr>';
						echo '<td style = "padding-left: 10px;">'.$exreducao[$x][2].'</td>';

						if (!empty($exreducao[$x][3])) {
							$cpf = explode(".", $exreducao[$x][3]);
							$cpfim = explode("-", $cpf[2]);
							$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
							echo '<td style = "text-align:center;">'.$cpf.'</td>';
						} else {
							echo '<td style = "text-align:center;"></td>';
						}

						echo '<td style = "text-align:center;">'.$exreducao[$x][4].' a '.$exreducao[$x][5].'</td>';
						echo '<td style = "text-align:center;">R$ '.number_format($exreducao[$x][6], 2, ',', '.').'</td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
					echo '</div>';
				}
			}
		?>

		<?php

			$query = "SELECT a.name, a.cpf, DATE_FORMAT(b.data_inicio, '%d/%m/%Y') as datainicio, DATE_FORMAT(b.data_termino, '%d/%m/%Y') as datatermino, b.valor_mensal
			FROM edu_bolsistas a
			INNER JOIN edu_aditamentos b
			WHERE a.rede = 'COTEC'
				AND a.id = b.bolsista
				AND a.vinculacao = 'Externo'
				AND b.acao = '-'
				AND (b.created_date BETWEEN '".$datainicio."' AND '".$datafinal."')
			ORDER BY a.name ASC";
			
			$db->setQuery($query);
			$inclusoes = $db->loadObjectList();

			if (!empty($inclusoes)) {
				foreach($inclusoes as $inclusao) {
					$exgrupo1[] = [$inclusao->name, $inclusao->cpf, $inclusao->datainicio, $inclusao->datatermino, $inclusao->valor_mensal];
				}

				echo '<div>
					<h5><li> Solicito inclusão de bolsa(s) do(s) seguinte(s) bolsista(s) externo(s): </li></h5>
				</div>';

				echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
				<table class="tabela">
					<tbody>
						<tr>
							<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
							<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
						</tr>';

				for ($x = 0; $x < count($exgrupo1); $x++) {
					echo '<tr>';
					echo '<td style = "padding-left: 10px;">'.$exgrupo1[$x][0].'</td>';

					if (!empty($exgrupo1[$x][1])) {
						$cpf = explode(".", $exgrupo1[$x][1]);
						$cpfim = explode("-", $cpf[2]);
						$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
						echo '<td style = "text-align:center;">'.$cpf.'</td>';
					} else {
						echo '<td style = "text-align:center;"></td>';
					}

					echo '<td style = "text-align:center;">'.$exgrupo1[$x][2].' a '.$exgrupo1[$x][3].'</td>';
					echo '<td style = "text-align:center;">R$ '.number_format($exgrupo1[$x][4], 2, ',', '.').'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}
		?>

		<?php

			$query = "SELECT a.name, a.cpf, DATE_FORMAT(a.data_bolsa, '%d/%m/%Y') as datainicio, DATE_FORMAT(a.databolsa_termino, '%d/%m/%Y') as datatermino, a.valor_mensal
			FROM edu_bolsistas a
			INNER JOIN edu_aditamentos b
			WHERE a.rede = 'COTEC'
				AND a.id = b.bolsista
				AND a.vinculacao = 'Externo'
				AND b.acao = 'Encerramento'
				AND (b.created_date BETWEEN '".$datainicio."' AND '".$datafinal."')
			ORDER BY a.name ASC";
			
			$db->setQuery($query);
			$exclusoes = $db->loadObjectList();

			if (!empty($exclusoes)) {
				foreach($exclusoes as $exclusao) {
					$exgrupo2[] = [$exclusao->name, $exclusao->cpf, $exclusao->datainicio, $exclusao->datatermino, $exclusao->valor_mensal];
				}

				echo '<div>
					<h5><li> Solicito exclusão de bolsa(s) do(s) seguinte(s) bolsista(s) externo(s): </li></h5>
				</div>';

				echo '<div class="col-md-8" style="margin: auto; padding-top: 10px; padding-bottom: 20px;">
				<table class="tabela">
					<tbody>
						<tr>
							<th style="width: 26%; background: #cdd0d1 !important;"> BOLSISTA </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> CPF </th>
							<th style="width: 20%; background: #cdd0d1 !important;"> PERÍODO </th>
							<th style="width: 17%; background: #cdd0d1 !important;"> VALOR </th>
						</tr>';

				for ($x = 0; $x < count($exgrupo2); $x++) {
					echo '<tr>';
					echo '<td style = "padding-left: 10px;">'.$exgrupo2[$x][0].'</td>';

					if (!empty($exgrupo2[$x][1])) {
						$cpf = explode(".", $exgrupo2[$x][1]);
						$cpfim = explode("-", $cpf[2]);
						$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
						echo '<td style = "text-align:center;">'.$cpf.'</td>';
					} else {
						echo '<td style = "text-align:center;"></td>';
					}

					echo '<td style = "text-align:center;">'.$exgrupo2[$x][2].' a '.$exgrupo2[$x][3].'</td>';
					echo '<td style = "text-align:center;">R$ '.number_format($exgrupo2[$x][4], 2, ',', '.').'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}
		?>

		<div>
			<h5 class="break"> III QUADRO DE PESSOAL </h5>
		</div>

		<table class="tabela2" >
			<tbody>
				<tr>
					<th> III.a. Participantes (da UFG ou de outras IES) de forma voluntária (Lei nº 8.958/94 e 10.973/2004) </th>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important; border-bottom: 0px !important;">
			<tbody>
				<tr>
					<th style="width: 23%; border-bottom: 0px !important;"> </th>
					<th style="width: 16%; border-bottom: 0px !important;"> </th>
					<th style="width: 16%; border-bottom: 0px !important;"> </th>
					<th style="width: 45%; padding-top: 1px !important;"> Dados </th>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important;">
			<tbody>
				<tr>
					<th style="width: 23%; border-top: 0px !important; padding-top: 0px !important; padding-bottom: 25px !important; padding-left: 9px; text-align: left !important;"> Nome </th>
					<th style="width: 16%; border-top: 0px !important; padding-top: 0px !important; padding-bottom: 25px !important;"> Registro Funcional ou Matrícula </th>
					<th style="width: 16%; border-top: 0px !important; padding-top: 0px !important; padding-bottom: 25px !important;"> Instituição de vinculação </th>
					<th style="width: 16.5%;"> Vinculação (Docente, Tec. Adm., Discente) </th>
					<th style="width: 14.5%;"> Período/ Duração/ Mês </th>
					<th style="width: 14%;"> Carga Horária Anual </th>
				</tr>
				<tr>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
				</tr>
			</tbody>
		</table>

		<div>
			<h5 style="padding-top: 30px;"> Obs: abaixo de cada quadro, justificar o valor das bolsas indicando os seus referenciais. </h5>
		</div>
		
		<table class="tabela2" >
			<tbody>
				<tr>
					<th> III.b. Participantes com recebimentos de bolsa (da UFG ou de outras IFES) (Lei nº 8.958/1994 e 10.973/2004) </th>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important; border-bottom: 0px !important;">
			<tbody>
				<tr>
					<th style="width: 23%; border-bottom: 0px !important;"> </th>
					<th style="width: 9%; border-bottom: 0px !important;"> </th>
					<th style="width: 9%; border-bottom: 0px !important;"> </th>
					<th style="width: 59%; padding-top: 1px !important;"> Dados </th>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important;">
			<tbody>
				<tr>
					<th style="width: 23%; border-top: 0px !important; padding-top: 0px !important; padding-bottom: 25px !important;"> Nome </th>
					<th style="width: 9%; border-top: 0px !important; padding-top: 0px !important; padding-bottom: 25px !important;"> Registro Funcional ou Matrícula </th>
					<th style="width: 9%; border-top: 0px !important; padding-top: 0px !important; padding-bottom: 25px !important;"> Instituição de vinculação </th>
					<th style="width: 10%;"> Modalidade (*) </th>
					<th style="width: 11%;"> Vinculação (Docente, Tec. Adm., Discente) </th>
					<th style="width: 10%;"> Período/ Duração/ Mês </th>
					<th style="width: 7%;"> Carga Horária Mensal </th>
					<th style="width: 10%;"> Valor Mensal </th>
					<th style="width: 11%;"> Valor total </th>
				</tr>

				<?php
				$db = JFactory::getDbo();

				// Consultar ids dos bolsistas vinculados as instituições e pagos pela RTVE
				$query = "SELECT id FROM edu_bolsistas WHERE rede = 'COTEC' AND vinculacao <> 'Externo' ORDER BY edu_bolsistas.name ASC";
				$db->setQuery($query);
				$ids = $db->loadObjectList();

				$total = 0;

				if (!empty($ids)) {
					foreach($ids as $id) {
						foreach($id as $key => $value) {
							// Consultar informações dos bolsistas para preencher o quadro
							$query = "SELECT b.name,b.matricula,b.instituicao,b.modalidade,b.vinculacao FROM edu_bolsistas b WHERE id = ".$value;
							$db->setQuery($query);
							$dados = $db->loadAssoc();

							// Consultar sigla da instituição
							$query = "SELECT sigla FROM edu_instituicoes WHERE id = ".$dados["instituicao"];
							$db->setQuery($query);
							$instituicao = $db->loadResult();

							// Mudar o valor da string
							if ($dados["vinculacao"] == 'Tecnico') {$vinculacao = 'Técnico Administrativo';} else {$vinculacao = $dados["vinculacao"];}

							// Consultar os ids dos aditamentos da bolsa concedida ao bolsita
							$query = "SELECT id FROM edu_aditamentos WHERE bolsista = ".$value." AND acao <> 'Encerramento' ORDER BY edu_aditamentos.id ASC";
							$db->setQuery($query);
							$idsadita = $db->loadObjectList();

							foreach($idsadita as $id) {
								foreach($id as $key => $value) {
									// Consultar informações dos adiantamentos
									$query = "SELECT DATE_FORMAT(data_inicio, '%d/%m/%Y') as datainicio,DATE_FORMAT(data_termino, '%d/%m/%Y') as datatermino,parcelas,carga_horaria,valor_mensal,valor_total FROM edu_aditamentos WHERE id = ".$value;
									$db->setQuery($query);
									$dadosadita = $db->loadAssoc();

									echo '<tr>';
									echo '<td style = "padding-left: 10px;">'.$dados["name"].'</td>';
									echo '<td> </td>';
									echo '<td style = "text-align:center;">'.$instituicao.'</td>';
									echo '<td style = "text-align:center;">'.$dados["modalidade"].'</td>';
									echo '<td style = "text-align:center;">'.$vinculacao.'</td>';
									echo '<td style = "text-align:center;">'.$dadosadita["datainicio"].' a '.$dadosadita["datatermino"].' ('.$dadosadita["parcelas"].')</td>';
									echo '<td style = "text-align:center;">'.$dadosadita["carga_horaria"].'</td>';
									echo '<td style = "text-align:center;">R$ '.number_format($dadosadita["valor_mensal"], 2, ',', '.').'</td>';
									echo '<td style = "text-align:center;">R$ '.number_format($dadosadita["valor_total"], 2, ',', '.').'</td>';
									echo '</tr>';

									$total += $dadosadita["valor_total"];
								}
							}
						}
					}
				}
				?>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important;">
			<tbody>
				<tr>
					<td style="width: 79%;"> </td>
					<td style="width: 10%; text-align:center;"><b>TOTAL</b></td>
					<td style="width: 11%; text-align:center;">R$ <?php echo number_format($total, 2, ',', '.') ?></td>
				</tr>
			</tbody>
		</table>
		
		<div>
			<h5> (*) Refere-se à modalidade definida nos termos da RESOLUÇÃO-CONSUNI Nº 03/2017. </h5>
			<h5 style="margin-top: -10px; padding-bottom: 30px;"> (**) Custeio de bolsa condicionado à arrecadação do projeto. </h5>
		</div>

		<table class="tabela2" >
			<tbody>
				<tr>
					<th> III.c. Outros Participantes (Pesquisador Externo/Convidado) forma de Bolsa </th>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important; border-bottom: 0px !important;">
			<tbody>
				<tr>
					<th style="width: 23%; border-bottom: 0px !important;"> </th>
					<th style="width: 10%; border-bottom: 0px !important;"> </th>
					<th style="width: 67%; padding-top: 1px !important;"> Dados </th>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important;">
			<tbody>
				<tr>
					<th style="width: 23%; border-top: 0px !important; padding-top: 0px !important; padding-bottom: 25px !important;"> Nome </th>
					<th style="width: 10%; border-top: 0px !important; padding-top: 0px !important; padding-bottom: 25px !important;"> CPF </th>
					<th style="width: 15%;"> Modalidade (*) </th>
					<th style="width: 17%;"> Período/ Duração/ Mês </th>
					<th style="width: 14%;"> Carga Horária Mensal </th>
					<th style="width: 10%;"> Valor Mensal </th>
					<th style="width: 11%;"> Valor total </th>
				</tr>
				
				<?php
				$db = JFactory::getDbo();

				// Consultar ids dos bolsistas vinculados as instituições e pagos pela RTVE
				$query = "SELECT id FROM edu_bolsistas WHERE rede = 'COTEC' AND vinculacao = 'Externo' ORDER BY edu_bolsistas.name ASC";
				$db->setQuery($query);
				$ids = $db->loadObjectList();

				$total = 0;

				if (!empty($ids)) {
					foreach($ids as $id) {
						foreach($id as $key => $value) {
							// Consultar informações dos bolsistas para preencher o quadro
							$query = "SELECT b.name,b.cpf,b.modalidade FROM edu_bolsistas b WHERE id = ".$value;
							$db->setQuery($query);
							$dados = $db->loadAssoc();
							
							// Consultar os ids dos aditamentos da bolsa concedida ao bolsita
							$query = "SELECT id FROM edu_aditamentos WHERE bolsista = ".$value." AND acao <> 'Encerramento' ORDER BY edu_aditamentos.id ASC";
							$db->setQuery($query);
							$idsadita = $db->loadObjectList();

							foreach($idsadita as $id) {
								foreach($id as $key => $value) {
									// Consultar informações dos adiantamentos
									$query = "SELECT DATE_FORMAT(data_inicio, '%d/%m/%Y') as datainicio,DATE_FORMAT(data_termino, '%d/%m/%Y') as datatermino,parcelas,carga_horaria,valor_mensal,valor_total FROM edu_aditamentos WHERE id = ".$value;
									$db->setQuery($query);
									$dadosadita = $db->loadAssoc();

									if (!empty($dados["cpf"])) {
										$cpf = explode(".", $dados["cpf"]);
										$cpfim = explode("-", $cpf[2]);
										$cpf = '***.'.$cpf[1].'.'.$cpfim[0].'-**';
									} else {
										$cpf = '';
									}

									echo '<tr>';
									echo '<td style = "padding-left: 10px;">'.$dados["name"].'</td>';
									echo '<td style = "text-align:center;">'.$cpf.'</td>';
									echo '<td style = "text-align:center;">'.$dados["modalidade"].'</td>';
									echo '<td style = "text-align:center;">'.$dadosadita["datainicio"].' a '.$dadosadita["datatermino"].' ('.$dadosadita["parcelas"].')</td>';
									echo '<td style = "text-align:center;">'.$dadosadita["carga_horaria"].'</td>';
									echo '<td style = "text-align:center;">R$ '.number_format($dadosadita["valor_mensal"], 2, ',', '.').'</td>';
									echo '<td style = "text-align:center;">R$ '.number_format($dadosadita["valor_total"], 2, ',', '.').'</td>';
									echo '</tr>';
									$total += $dadosadita["valor_total"];
								}
							}
						}
					}
				}
				?>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important;">
			<tbody>
				<tr>
					<td style="width: 79%;"> </td>
					<td style="width: 10%; text-align:center;"><b>TOTAL</b></td>
					<td style="width: 11%; text-align:center;">R$ <?php echo number_format($total, 2, ',', '.') ?></td>
				</tr>
			</tbody>
		</table>

		<div>
			<h5> (*) Refere-se à modalidade definida nos termos da RESOLUÇÃO-CONSUNI Nº 03/2017. </h5>
			<h5 style="margin-top: -10px; padding-bottom: 30px;"> (**) Custeio de bolsa condicionado à arrecadação do projeto. </h5>
		</div>

		<table class="tabela2" >
			<tbody>
				<tr>
					<th> III.d. Outros Participantes – Regime de CLT </th>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important;">
			<tbody>
				<tr>
					<th style="width: 21%;"> Nome </th>
					<th style="width: 13%;"> Cargo </th>
					<th style="width: 12%;"> Carga Horária Semanal </th>
					<th style="width: 10%;"> a. Período/ Duração </th>
					<th style="width: 12%;"> b. Salário base mensal </th>
					<th style="width: 11%;"> c. Encargos - mensal (*) </th>
					<th style="width: 11%;"> d. Benefícios - mensal (**) </th>
					<th style="width: 10%;"> Valor Total<br>(a * (b+c+d)) </th>
				</tr>
				<tr>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
					<td style = "padding-left: 10px;"> N/A. </td>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important;">
			<tbody>
				<tr>
					<td style="width: 79%;"> </td>
					<td style="width: 11%; text-align:center;"><b>TOTAL</b></td>
					<td style="width: 10%; text-align:center;"> </td>
				</tr>
			</tbody>
		</table>

		<table class="tabela" style="margin-top: -1px; border-top: 0px !important;">
			<tbody>
				<tr>
					<td style="width: 100%; padding-left: 10px;"><b>Indicação dos Benefícios não obrigatórios e gratificação de função (se houver) com os respectivos valores:</b></td>
				</tr>
			</tbody>
		</table>

		<div>
			<h5> (*) Valor estimado dos encargos (INSS, PIS, FGTS, reserva rescisória proporcional) + benefícios obrigatórios. </h5>
			<h5 style="margin-top: -10px;"> (**) Benefícios não obrigatórios (indicar se houver) + gratificação de função (indicar se houver) </h5>
		</div>

	</div>	
</div>