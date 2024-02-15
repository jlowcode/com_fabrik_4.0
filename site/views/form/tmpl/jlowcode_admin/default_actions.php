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
		<div>
			<div class="btn-group btn-group-save">
				<ul class="ul-btn-actions">
					<li><button type="submit" class="btn-save-back button btn-group-actions btn salvar" name="Submit" id="fabrikSubmit_<?php echo $form->id; ?>_A">
							Salvar e Voltar <i class="fa-icon-down fa fa-angle-down fa-lg" aria-hidden="true"></i></button>

						<ul>
							<?php if (explode('_', $form->formid)[2]) : ?>
								<li><button type="submit" class="btn-save-copy button btn-group-actions btn" name="Copy">
									Salvar e Copiar</button></li>
							<?php endif; ?>
							<li><button type="submit" class="btn-save-new button btn-group-actions btn salvar" name="SubmitAndNew" id="fabrikSubmit_<?php echo $form->id; ?>_B">
									Salvar e Novo</button></li>
							<li><button type="submit" class="btn-save-details button btn-group-actions btn salvar" name="SubmitAndDetails" id="fabrikSubmit_<?php echo $form->id; ?>_C">
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
		<?php if ( $form->prevButton || $form->nextButton ): ?>
			<div class="offset1">
				<div class="btn-group">
					<?php echo $form->prevButton . ' ' . $form->nextButton; ?>
				</div>
			</div>
			<div>

		<?php else: ?>
			<div>

		<?php endif; ?>
		<div class="btn-group">
				<ul class="ul-btn-actions">
					<li><button type="button" class="btn button btn-cancel-back btn-group-actions" onclick="javascript:window.location.href='/index.php?option=com_fabrik&view=list&listid=<?php echo $form->id; ?>'" name="Goback">
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