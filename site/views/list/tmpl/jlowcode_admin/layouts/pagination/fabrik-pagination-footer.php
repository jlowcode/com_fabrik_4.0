<?php
/**
 * Layout: List Pagination Footer
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2015 fabrikar.com - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.3.3
 */

$d = $displayData;

if ($d->showNav) :
?>
<div class="list-footer d-flex" <?php echo empty($d->links) ? 'style="float: right"' : '' ?>>
	<?php echo $d->links; ?>
	<div class="limit d-flex">
			<div>
				<label for="<?php echo $d->listName;?>">
					<p>
						<?php echo $d->label; ?>
					</p>
				</label>
			</div>
			<?php echo $d->list; ?>
			<div>
				<p>
					<?php echo $d->pagesCounter; ?>
				</p>
			</div>
	</div>
	<input type="hidden" name="limitstart<?php echo $d->id; ?>" id="limitstart<?php echo $d->id; ?>" value="<?php echo $d->value; ?>" />
</div>
	<?php
else :
	if ($d->showTotal) : ?>
		<div class="list-footer">
			<div>
				<p>
					<?php echo $d->pagesCounter; ?>
				</p>
			</div>
		</div>
		<?php
	endif;
endif;
