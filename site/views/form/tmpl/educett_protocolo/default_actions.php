<?php

/**
 * Bootstrap Form Template - Actions
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access

$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('etapa,status_fun')->from('edu_solicitacoes')->where('id = ' . $db->quote($this->data['edu_solicitacoes___id_raw']));
$db->setQuery($query, 1, 0);
$result = $db->loadAssoc();

if ($result['etapa'] == 'Concluido' && $result['status_fun'] == 'Concluido') {
	$link = '/protocolo/concluidas';
} else {
	$link = '/protocolo';
}

defined('_JEXEC') or die('Restricted access');

$form = $this->form;
?>
<div class="fabrikActions form-actions">
	<div class="row-fluid footer-btn">
		<div class="text-left <?php echo FabrikHelperHTML::getGridSpan(6); ?>">
			<div class="btn-group ">
				<ul class="ul-btn-actions">
					<li><button type="submit" class="btn-save-back button btn-group-actions btn salvar" name="Submit" id="fabrikSubmit_<?php echo $form->id; ?>">
							Salvar e Voltar <i class="fa-icon-down fa fa-angle-down fa-lg" aria-hidden="true"></i></button>

						<ul>
							<li><button type="submit" class="btn-save-new button btn-group-actions btn salvar" name="SubmitAndNew" id="fabrikSubmit_<?php echo $form->id; ?>">
									Salvar e Novo</button></li>
							<li><button type="submit" class="btn-save-details button btn-group-actions btn salvar" name="SubmitAndDetails" id="fabrikSubmit_<?php echo $form->id; ?>">
									Salvar e Ver</button></li>
							<li><button type="submit" class="btn-save-only button btn-group-actions btn salvar" name="apply">Salvar</button></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
		<?php 
			echo $form->copyButton;
		?>
								
		<div class="text-right <?php echo FabrikHelperHTML::getGridSpan(6); ?>">
			<div class="btn-group">
				<ul class="ul-btn-actions">
					<li><button type="button" class="btn button btn-cancel-back btn-group-actions" onclick="javascript:window.location.href='<?php echo $link; ?>'" name="Goback">
							Cancelar<i class="fa-icon-down fa fa-angle-down fa-lg" aria-hidden="true"></i></button>
						<ul>
							<li><button type="reset" class="btn-reset button btn-group-actions btn" name="reset">Limpar</button></li>

							<?php if (explode('_', $form->formid)[2]) : ?>
								<li><button type="submit" class="btn-delete button btn-group-actions btn" name="delete">Excluir</button></li>
							<?php endif; ?>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>