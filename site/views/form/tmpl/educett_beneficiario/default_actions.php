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
defined('_JEXEC') or die('Restricted access');

$form = $this->form;
?>
<div class="fabrikActions form-actions">
	<div class="row-fluid footer-btn">
		<div class="text-left <?php echo FabrikHelperHTML::getGridSpan(6); ?>">
			<div class="btn-group ">
				<ul class="ul-btn-actions">
					<li><button type="submit" class="btn-save-back button btn-group-actions btn salvar" name="Submit" id="fabrikSubmit_<?php echo $form->id; ?>">
							Salvar e Voltar</button>
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
					<li><button type="button" class="btn button btn-cancel-back btn-group-actions" onclick="javascript:window.location.href='/index.php?option=com_fabrik&view=list&listid=<?php echo $form->id; ?>'" name="Goback">
							Cancelar<i class="fa-icon-down fa fa-angle-down fa-lg" aria-hidden="true"></i></button>
						<ul>
							<li><button type="reset" class="btn-reset button btn-group-actions btn" name="reset">Limpar</button></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>