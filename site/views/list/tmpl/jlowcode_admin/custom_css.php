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

.intro-container {
  position: relative;
  text-align: right;
}

.text-intro-content {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  max-height: 8.5em;
  text-align: left
}

.intro-container fa {
  background-color: transparent;
  border: none;
  color: blue;
  cursor: pointer;
  padding: 0;
  font-size: 16px;
  margin-top: 5px;
}

.intro-container fa:focus {
  outline: none;
}

.fabrik_action .dropdown-menu {
    transform: translate(0px, 30px) !important;
}

.btn:hover {
	color: #C2F6F9 !important;
}

.fabrikFilterContainer a {
    text-decoration: underline;
}

.fabrik___heading .fabrik_ordercell a, .fabrik___heading .fabrik_ordercell span {
    max-width: 90%; 
    overflow: hidden; 
    text-overflow: ellipsis; 
    white-space: nowrap;
}

textarea, input, select {
    background-color: #fff !important;
	border-radius: 12px !important;
    border: 2px solid #eee !important;
}

.modal-content {
    border-radius: 20px !important;
    box-shadow: 0px 6px 6px 0px #3c3939;
}

.filterContent .span12 {
	width: 91.48936170212765%;
}

.filterContent .span11 {
	width: 82.97872340425532%;
}

.filterContent .span10 {
	width: 74.46808510638297%;
}

.filterContent .span9 {
	width: 65.95744680851064%;
}

.filterContent .span8 {
	width: 57.44680851063829%;
}

.filterContent .span7 {
	width: 48.93617021276595%;
}

.filterContent .span6 {
	width: 40.42553191489362%;
}

.filterContent .span5 {
	width: 31.914893617021278%;
}

.filterContent .span4 {
	width: 23.404255319148934%;
}

.filterContent .span3 {
	width: 14.893617021276595%;
}

.filterContent .span2 {
	width: 6.382978723404255%;
}

.filterContent .span1 {
	width: 6.382978723404255%;
}

.fabrik_actions img {
    max-width: inherit;
}

thead .fabrik___heading a,
.fabrik___heading select {
    color: #011627 !important;
}

.g-menu-item span {
	color: #032B43 !important;
    font-weight: 400 !important;
}

.g-menu-item span:hover {
	font-weight: 700 !important;
}

.fabrikgrid_checkbox {
    display: flex !important;
    align-items: center !important;
}

.fabrikgrid_checkbox input {
    margin-left: 0px !important;
    margin-top: 0px !important;
}

.fabrikgrid_checkbox label {
    margin-bottom: 0px !important;
}

input.fabrik_filter {
    padding-left: 40px !important;
}

.dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus, .dropdown-submenu:hover > a, .dropdown-submenu:focus > a {
    background: inherit !important;
    font-weight: 500;
}

.dropdown-toggle .fabrikImg {
    max-width: inherit;
}

.btn.dropdown-toggle-no-caret::after {
    border: none !important;
}

.pagination .active {
    font-weight: 600;
}

.pagination > li {
    padding: 0px 7px;
    border-bottom: 2px solid #eee;
    border-top: 2px solid #eee;
}


.list-footer {
    display: flex;
    align-items: center;
    justify-content: space-between !important;
}

.list-footer .limit {
    display: flex;
    align-items: center;
}

.fabrik_filter, .fabrikinput {
    border: 2px solid #eee !important;
    height: 50px !important;
    background-color: #fff !important;
}

form .fabrikButtonsContainer .nav {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: end;
    width: 100%;
}

.fabrikButtonsContainer ul {
    margin-bottom: 30px !important;
}

.fabrikButtonsContainer ul li {
    padding: 0px 30px;
}

.addbutton, .tagSearched, .addRecord, .process_mapper-modal-footer button {
    border-radius: 12px;
    padding: 16px 32px;
    color: #fff !important;
    background-color: #003EA1;
    width: 188px !important;
}

.addbutton, .tagSearched, .addRecord {
    height: 52px !important;
    display: flex;
    justify-content: center;
    align-items: center;
}

.addbutton .fa, .tagSearched .fa, .addRecord .fa { 
    margin-right: 5px !important;
}

.header_buttons {
    display: flex;
    align-items: center;
}

.header {
    display: flex;
    width: 100%;
    justify-content: space-between;
}

main .platform-content {
    background-color: #fff;
    border-radius: 12px;
    padding: 50px !important;
}

thead .fabrik___heading {
    background-color: #FBFBFB !important; 
    border-bottom: 2px solid #eee !important;
}

tfoot .fabrik___heading {
    background-color: #fff !important;
}

.table > :not(caption) > * > * {
    border-bottom: none !important;
}


.table > :not(:last-child) > :last-child > * {
    border-bottom-color: unset !important;
}

.table {
    border-color: snow !important;
}


form a, table a {
    color: #011627 !important;
}

.dropdown-menu > li > a {
	color: #011627 !important;
}

.dropdown-menu > li > a:hover {
	font-weight: 700;
	background: none !important;
    cursor: pointer;
}


#listform_$c {
	margin-top: 25px !important;
}

.g-container{
    width: 90rem !important;
}

h1, 
.nav {
    margin: 10px 0px !important;
}


.fabrikForm {
    font-size: 1rem;
}

.active>.page-link, .page-link.active{
    z-index: 1 !important;
    background-color: #eceff3;
}

.table-aditional.in {
    display: block;
}

#eventsContainer ul {
    list-style: none;
}

li.page-item a {
    padding: 0.1rem  0.6rem;
}

i.fas.fa-exclamation-triangle.fa-sm{
    padding: 0rem 0.3rem;
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
    display:inline-flex;
}

.btn-primary:hover, .btn-primary:active, .btn-primary:focus {
    background: #bbb !important;
}

.btn.calendarbutton {
    background: #eee;
    border-radius: 0px 5px 5px 0px !important;
    margin: 2px 0px 0px 0px;
    font-size: 10px;
    border: 0px;
    color: #000;
    padding: 0rem 1.7rem;
    height: 47px;
    display: flex;
    align-items: center;
}

.fabrik_filter_submit {
    margin-top: 10px !important;
}

.fabrikFilterContainer {
    background-color: #FBFBFB;
	padding: 10px;
}

.filterContentNotEmpty {
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
.btn.fabrik_edit
.btn.fabrik_report{
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

.page-header{
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

@media (max-width: 800px){
    main .platform-content {
        padding: 5px !important;
    }

    .g-content {
        padding: 10px 15px !important;
    }

    .input-append input {
        width: 200px !important;
    }

    .fabrikButtonsContainer {
        padding: 0px !important;
    }

    .filterContent {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }

    .fabrikButtonsContainer ul li {
        margin-bottom: 10px;
        padding: 0px 5px;
    }

    .header {
        flex-direction: column;
        padding-bottom: 20px;
    }

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

    /*Joyce*/
    .pagination ul {
        display: flex !important;
        flex-wrap: wrap !important;
    }
    
    /*Joyce*/
    .pagination-list li {
        margin: 0px !important;
    }
   
    /*Joyce*/
    #searchall_34_com_fabrik_34.fabrik_filter.search-query.input-medium,
    #searchall_93_com_fabrik_93.fabrik_filter.search-query.input-medium,
    #searchall_27_com_fabrik_27.fabrik_filter.search-query.input-medium,
    #searchall_91_com_fabrik_91.fabrik_filter.search-query.input-medium,
    #searchall_95_com_fabrik_95.fabrik_filter.search-query.input-medium,
    #searchall_97_com_fabrik_97.fabrik_filter.search-query.input-medium {
        width:100%
    }

    /*Joyce*/
    .filterContent.filterContentNotEmpty .fabrikFilterContainer .row-fluid .span4 {
        width: 100% !important;
    }
}

@media (max-width: 500px) {
    .listContent.fabrikDataContainer {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        max-width: 100%;
        border-collapse: collapse;
    }
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



/*Joyce*/
.filterContent.filterContentNotEmpty .fabrikFilterContainer .row-fluid .span4 {
    width: 31.5%;
}

/*Joyce*/
.btn.php-1.listplugin.btn-default{
    background: #e3ecf1 !important;
    padding: 0.2rem 0.4rem;
    border-radius: 5px !important;
    margin-left: 5px !important;
    border: 1px solid #BAD0DC;
    color: #0E534A;
}

/*Joyce*/
.btn.link-1.listplugin.btn-default{
    background: #e3ecf1 !important;
    padding: 0.2rem 0.4rem;
    border-radius: 5px !important;
    margin-left: 5px !important;
    border: 1px solid #BAD0DC;
    color: #07102a;
}


/*WORKFLOW*/
.btn-outline-primary{
    border: 1px solid #003EA1 !important;
}


/* Modo Exibição*/
.box {
    position: relative;
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: ##ffffff;
    border: 1px solid #e3e3e3;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
}

.radio-group {
    display: flex;
    overflow: hidden;
}

.radio-group input[type="radio"] {
    display: none;
}
    
.radio-group img {
    padding: 0.3rem 0.3rem;
    cursor: pointer !important;
    transition: background-color 0.3s, color 0.3s;
}

.radio-group input[type="radio"]:checked + label {
    background-color: #eee;
    color: #0d3e9d;
}
    
/* Estilo do Modal */
/* Estilo do Modal */
/* Estilo do Modal */
#loadingModal {
    display: none; /* Esconde o modal inicialmente */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Fundo semitransparente */
    justify-content: center;
    align-items: center;
    text-align: center;
}

/* Ícone de Carregamento (Spinner) */
#loadingModal .spinner {
    border: 4px solid #f3f3f3; /* Cor de fundo do spinner */
    border-top: 4px solid #3498db; /* Cor da borda do spinner */
    border-radius: 50%;
    width: 200px;
    height: 200px;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

/* Animação do Spinner */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}


/* Estilo básico para o contêiner de registros */
/* Estilo básico para o contêiner de registros */
#registros-container {
    padding: 20px;
    font-family: Arial, sans-serif;
}

/* Estilo para os itens da árvore */
.tree-item {
    margin: 0em 1em;
    padding: 0;
    cursor: pointer;
    position: relative;
    margin-top: -15px;
    margin-bottom: -15px;
}

/* Estilo para o símbolo de seta */
.tree-arrow {
    cursor: pointer; /* Muda o cursor ao passar sobre a seta */
    margin-right: 5px;
    display: inline-block;
    transition: transform 0.2s; /* Animação de rotação */
}

/* Estilo para o texto do item */
.tree-text {
    margin-left: 5px;
}


.no-children {
    color: #666;
    font-style: italic;
    margin-left: 20px; /* Ajuste conforme necessário */
    margin-top: -15px;
}

.tree-item.open > .tree-arrow {
    transform: rotate(90deg); /* Rotate the arrow to indicate expansion */
}

.action-menu {
    position: absolute;
    background-color: white;
    border: 1px solid #ccc;
    list-style: none;
    padding: 5px;
    margin: 0;
    display: none;
    z-index: 1000;
}

.action-menu li {
    padding: 5px;
    cursor: pointer;
}

.action-menu li:hover {
    background-color: #eee;
}

.dropdown.fabrik_action{
    display: inline;
}

/* Esconde o dropdown por padrão */
.tree-item .dropdown.fabrik_action {
    /*visibility: hidden;
    opacity: 0;
    transition: visibility 0s, opacity 0.3s linear;*/
}

/* Aplica o hover apenas ao .dropdown que é filho direto do .tree-item que está sendo hoverado */
.tree-item:hover > .actions > .dropdown.fabrik_action {
    visibility: visible;
    opacity: 1;
}



.button.btn.btn-default.btn-mini.dropdown-toggle.dropdown-toggle-no-caret{
    margin: 0;
}

/* END - Your CSS styling ends here */
EOT;