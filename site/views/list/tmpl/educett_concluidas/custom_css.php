<?php
/**
 * Fabrik List Template: Default Custom CSS
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

/**
* If you need to make small adjustments or additions to the CSS for a Fabrik
* list template, you can create a custom_css.php file, which will be loaded after
* the main template_css.php for the template.
*
* This file will be invoked as a PHP file, so the list ID
* can be used in order to narrow the scope of any style changes.  You do
* this by prepending #listform_$c to any selectors you use.  This will become
* (say) #listform_12, owhich will be the HTML ID of your list on the page.
*
* See examples below, which you should remove if you copy this file.
*
* Don't edit anything outside of the BEGIN and END comments.
*
* For more on custom CSS, see the Wiki at:
*
* http://www.fabrikar.com/forums/index.php?wiki/form-and-details-templates/#the-custom-css-file
*
* NOTE - for backward compatibility with Fabrik 2.1, and in case you
* just prefer a simpler CSS file, without the added PHP parsing that
* allows you to be be more specific in your selectors, we will also include
* a custom.css we find in the same location as this file.
*
*/

header('Content-type: text/css');
$c = $_REQUEST['c'];

echo <<<EOT
/* BEGIN - Your CSS styling starts here */

#listform_$c {
	margin-top: 25px !important;
}

.section-horizontal-paddings {
    padding-left: 4% !important;
    padding-right: 4% !important;
}

.g-container{
    width: 90rem !important;
}

h1, 
.nav {
    margin: 10px 0px !important;
}



#g-footer {
    padding: 0px;
}

#conteudo{
	webkit-box-flex: 0;
    -moz-box-flex: 0;
    box-flex: 0;
    -webkit-flex: 0 90%;
    -moz-flex: 0 90%;
    -ms-flex: 0 90%;
    margin: auto;
    flex: 0 90%;
    width: 90%;
}


[class*="span"] {
    margin-right: 1.7%;
    margin-left: 0px !important ;
}

.chzn-container-single .chzn-single {
    height: 30px !important;
    line-height: 26px !important;
}
.chzn-container-single .chzn-single div b{
    top: 2px !important; 
}

.date-between{
    border: 0px !important;
    border-radius: 5px 0px 0px 5px !important;
    height: 30px !important;
    line-height: 26px !important;
}
.input-append{
    margin-bottom: 10px ;
    display:inline-flex;
}



.btn.calendarbutton{
    background: #fff;
    border-radius: 0px 5px 5px 0px !important;
    margin: 2px 0px 0px 0px;
    font-size: 10px;
    border: 0px;
    color: #000;
    padding: 0rem 1.7rem;
    height: 32px;
}

.filterContentNotEmpty {
	background: #E3ECF1;
	padding: 10px;
	color: #000;
    font-weight: 500;
    border-radius: 5px;
}

.btn.fabrik_view{
    color: #054267;
}
.btn.fabrik_edit{
    color: #0E534A;
}
.btn.delete{
    color: #A41623;
}


.btn.btn-default.delete,
.btn.fabrik_view,
.btn.fabrik_edit{
    background: #e3ecf1 !important;
    padding: 0.2rem 0.4rem;
    border-radius: 5px !important;
    margin-left: 5px !important;
    border: 1px solid #BAD0DC;
}

#edu_solicitacoes___created_date_19_com_fabrik_19_filter_range_0_.0,
#edu_solicitacoes___created_date_19_com_fabrik_19_filter_range_1_.0{
    border: none;
    border-radius: 0px 0px 0px 0px !important;
}


/* Importante, verificar ids*/

.fabrik_element.fabrik_list_29_group_72{
    max-width:15%;
    width:15% !important;
}

.inputbox.fabrik_filter.input-medium {
    border: none;
}


[class^="icon-"], [class*=" icon-"]{
    margin:0px !important;
    margin-top: 4px !important;

}

.pagination{
    text-align:right !important;
}

.heading.fabrik_ordercell.fabrik_actions,
.heading.fabrik_ordercell.fabrik_select,
.fabrik_select.fabrik_element,
.fabrik_actions.fabrik_element{
    text-align: right;
}
.fabrikFilterContainer .row-fluid .span12,
.fabrik___heading,
.fabrik_row,
.fabrikorder,
.clearFilters{
	color: #000;
}

.fabrikFilterContainer{
    color: #E3ECF1
}

.tagSearched,
.addbutton.addRecord{
	display:none;
}


.page-header{
	border-bottom: 1px solid #508AA8;
    color: #032B43;
}

.fabrik_filter.search-query.input-medium{
	border: 1px solid #508AA8;
}


.pagination ul > li {
    display: table-cell !important;
}

.fabrikFilterContainer > .row-fluid:first-child{
    display: flex;
}

.row-fluid .span3{
    margin: 0px 10px;
}

@media (max-width: 600px){
    .fabrikDateListFilterRange,
    .fabrikDateListFilterRange .input-append {
        width: 100%;
    }

    .fabrikDateListFilterRange .inputbox  {
        width: 80% !important;
    }
    
   
    .fabrik_groupdata table caption {
        font-size: 1.3em;
    }
    .fabrik_groupdata table, thead, tbody, th, td, tr { 
		display: block; 
	}
	
	/* Hide table headers (but not display: none;, for accessibility) */
	.fabrik___heading th { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
	
	.fabrik_groupdata tr { 
        border: 1px solid #ccc; 
        margin: 10px auto;
        border-bottom: 1px solid #07102a;
    }
	
	.fabrik_groupdata td { 
		/* Behave  like a "row" */
		border: none;
		border-bottom: 1px solid #eee; 
		position: relative;
		padding-left: 50%; 
	}
	
	.fabrik_groupdata td:before { 
		/* Now like a table header */
		position: initial;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%; 
		padding-right: 10px; 
		white-space: nowrap;
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-transform: uppercase;
        font-size: .8em;
        text-align: left;

	}
    .listContent.fabrikDataContainer{
        margin: 20px auto;
    }
       
    .fabrik_row td {
        width: 100% !important;
    }

    td:before {
        content: attr(data-content) !important;
    }

    .fabrik_groupdata.edu_beneficiarios{
        width: 45% !important;
    }

    .pagination ul {
        display: flex !important;
        flex-wrap: wrap !important;
    }
    
    .pagination-list li {
        margin: 0px !important;
    }

}

.dropdown-menu > li > a {
    white-space: normal !important;
}

.autocomplete-trigger {
    border: 1px solid rgba(0, 0, 0, 0.1) !important; 
    height: 28px;
}

.single_field {
    border: 1px solid rgba(0, 0, 0, 0.1) !important; 
    height: 28px;
}

.search-icon-button {
    top: 5px !important;
}

/* END - Your CSS styling ends here */
EOT;