<?php

/**
 * Fabrik List Template: Cards Row
 * Note the div cell container is now generated in the default template
 * in FabrikHelperHTML::bootstrapGrid();
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$rowClass = isset($this->_row->rowClass) ? $this->_row->rowClass : '';

// Catching the elements IDs in params
$title_element_id = $this->params->get('titulo');
$thumbnail_element_id = $this->params->get('thumbnail');

$regexTitle = $title_element_id . '_order';
$regexThumb = $thumbnail_element_id . '_order';

?>
<nav class="hide-mobile">
    <div class="col">
        <div class="card-container">
            <?php foreach ($this->headings as $heading => $label) :
                $d = @$this->_row->data->$heading;

                //skip empty elements but don't skip the checkbox (delete, list plugins)
                if (isset($this->showEmpty) && $this->showEmpty === false && trim(strip_tags($d)) == '' && $heading != 'fabrik_select') :
                    continue;
                endif;
                $h = $this->headingClass[$heading];
                $c = $this->cellClass[$heading];
                $hStyle = empty($h['style']) ? '' : 'style="' . $h['style'] . '"';
                $cStyle = empty($c['style']) ? '' : 'style="' . $c['style'] . '"';

                //var_dump($h['class']);

                $cStyle = preg_match("/{$regexTitle}/", $h['class']) ? 'style="font-weight: bold;"' : '';
                //var_dump($h['style']);
                if (preg_match("/{$regexThumb}/", $h['class']) && preg_match("/width/", $h['style'])) :
                    echo '<div class="col-md-6">';
                    echo '<span class="' . $c['class'] . '" ' . $cStyle . '>' . $d . '</span>';
                    echo '</div>';
                endif;
            ?>
            <?php
            //echo '</div>';
            endforeach;
            ?>
            <div class="col-md-6">
                <?php foreach ($this->headings as $heading => $label) :
                    $d = @$this->_row->data->$heading;
                    $footer = new stdClass();
                    //var_dump($d);
                    //skip empty elements but don't skip the checkbox (delete, list plugins)
                    if (isset($this->showEmpty) && $this->showEmpty === false && trim(strip_tags($d)) == '' && $heading != 'fabrik_select') :
                        continue;
                    endif;

                    $h = $this->headingClass[$heading];
                    if(preg_match("/width/", $h['style'])):
                        continue;
                    endif;
                    $c = $this->cellClass[$heading];
                    //var_dump($c['class']);
                    $cStyle = empty($c['style']) ? '' : 'style="' . $c['style'] . ' font-size: inherit; float: left;'.$h[style].'"';


                    if(preg_match("/fabrik_actions/", $c['class'])):
                        $footer->html = $d;
                        $footer->class = $c['class'];
                        $footer->cStyle = $cStyle;
                    else:
                        echo '<p><span class="' . $c['class'] . '" ' . $cStyle . '>' . $d . '</span></p>';
                    endif;
                ?>
                <?php
                //echo '</div>';
                endforeach;
                    echo $footer->html;
                ?>
            </div>
        </div>
    </div>
</nav>

<nav class="show-mobile">
    <div class="<?php echo $rowClass; ?>">
        <?php foreach ($this->headings as $heading => $label) :
            $d = @$this->_row->data->$heading;

            //skip empty elements but don't skip the checkbox (delete, list plugins)
            if (isset($this->showEmpty) && $this->showEmpty === false && trim(strip_tags($d)) == '' && $heading != 'fabrik_select') :
                continue;
            endif;
            $h = $this->headingClass[$heading];
            $c = $this->cellClass[$heading];
            $hStyle = empty($h['style']) ? '' : 'style="' . $h['style'] . '"';
            $cStyle = empty($c['style']) ? '' : 'style="' . $c['style'] . '"';

            $cStyle = preg_match("/{$regexTitle}/", $h['class']) ? 'style="font-weight: bold;"' : ''

        ?>
            <div style="margin-top: 10px;">
                <?php if (isset($this->showLabels) && $this->showLabels) :
                    echo '<span class="muted ' . $h['class'] . '" ' . $hStyle . '>' . $label . ': </span>';
                endif; ?>

                <?php echo '<span class="' . $c['class'] . '" ' . $cStyle . '>' . $d . '</span>'; ?>
            </div>
        <?php
        endforeach;
        ?>
    </div>
</nav>