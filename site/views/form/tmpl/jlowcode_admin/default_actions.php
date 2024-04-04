<?php

/**
 * Jlowcode_admin Form Template - Actions
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
$params = json_decode($form->params);
$multiButtonsSave = (bool) $params->copy_button || (bool) $params->apply_button || (bool) $params->submit_button || explode('_', $form->formid)[2] ? true : false;
$multiButtonsOthers = (bool) $params->reset_button || (bool) $params->delete_button ? true : false;
?>

<div class="fabrikActions form-actions">
	<div class="row-fluid footer-btn">
		<div>
			<div class="btn-group btn-group-save">
				<ul class="ul-btn-actions" style="width: 170px;">
					<li>
						<button type="submit" class="btn-save-back button btn-group-actions btn salvar" name="Submit" id="fabrikSubmit_<?php echo $form->id; ?>_A">
							Salvar e Voltar
							<?php if ($multiButtonsSave) : ?>	
								<i class="fa-icon-down fa fa-angle-down fa-lg" aria-hidden="true"></i>
							<?php endif; ?>
						</button>

						<ul>
							<?php if (explode('_', $form->formid)[2]) : ?>
								<button type="submit" class="btn-save-copy button btn-group-actions btn" name="Copy">
									Salvar e copiar
								</button>
							<?php endif; ?>
							
							<?php if ((bool) $params->apply_button) : ?>
								<li>
									<button type="submit" class="btn-save-new button btn-group-actions btn salvar " <?php echo $prams->apply_button_class ?> name="SubmitAndNew" id="fabrikSubmit_<?php echo $form->id; ?>_B">
										<?php echo $params->apply_button_label ?>
									</button>
								</li>
							<?php endif; ?>
							
							<?php if ((bool) $params->submit_button) : ?>
								<li>
									<button type="submit" class="btn-save-details button btn-group-actions btn salvar " <?php echo $prams->submit_button_class ?> name="SubmitAndDetails" id="fabrikSubmit_<?php echo $form->id; ?>_C">
										<?php echo $params->submit_button_label ?>
									</button>
								</li>
							<?php endif; ?>

							<?php if ((bool) $params->copy_button) : ?>
								<li>
									<button type="submit" class="btn-save-only button btn-group-actions btn salvar " <?php echo $prams->copy_button_class ?> name="apply">
										<?php echo $params->copy_button_label ?>
									</button>
								</li>
							<?php endif; ?>
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
				<ul class="ul-btn-actions" style="width: 170px;">
					<?php if ((bool) $params->goback_button) : ?>
						<li>
							<button type="button" class="btn button btn-cancel-back btn-group-actions" onclick="javascript:window.location.href='/index.php?option=com_fabrik&view=list&listid=<?php echo $form->id; ?>'" name="Goback">
								<?php echo $params->goback_button_label ?>
								<?php if ($multiButtonsOthers) : ?>	
									<i class="fa-icon-down fa fa-angle-down fa-lg" aria-hidden="true"></i>
								<?php endif; ?>
							</button>
							<ul>
								<?php if ((bool) $params->reset_button) : ?>
									<li>
										<button type="reset" class="btn-reset button btn-group-actions btn" name="reset">
											<?php echo $params->reset_button_label ?>
										</button>
									</li>
								<?php endif; ?>

								<?php if ((bool) $params->delete_button) : ?>
									<li>
										<button type="submit" class="btn-delete button btn-group-actions btn" name="delete">
											<?php echo $params->delete_button_label ?>
										</button>
									</li>
								<?php endif; ?>
							</ul>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>

	</div>
</div>