/**
* Ensure that Fabrik's loaded
*/
// #group58 = Solicitações - Reembolso (Grupo repetível)
// #group122 = Solicitações - Prestação de contas (Diárias)
// #group123 = Solicitações - Prestação de contas (Adiantamento)
// #group124 = Solicitações - Prestação de contas (Grupo repetível)
// #group125 = Solicitações - Devolução 

let aux = "";
let groups_to_hide = ['#group31', '#group32', '#group33', '#group34', '#group36', '#group38', '#group51', '#group52', '#group55', '#group56', '#group57', '#group58', '#group60', '#group66', '#group67', '#group71','#group121','#group122','#group123','#group124','#group125','#group147','#group187','#group191','.fb_el_edu_solicitacoes___num_requisicao','.fb_el_edu_solicitacoes___valor_pg','.fb_el_edu_solicitacoes___correcoes','.fb_el_edu_solicitacoes___correcoes_fun','.fb_el_edu_solicitacoes___justifica_cancelamento'];

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const showGroups = function (groups_to_show) {
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const switchPriority = function (aux) {
    const group = ['#group31']
    switch (aux) {
        case "Alta":
            showGroups(group);
            break;
        case "Critica":
            showGroups(group);
            break;
        case "Baixa":
            hideGroups(group);
            break;
        case "Normal":
            hideGroups(group);
            break;
    }
}

const switchGroups = function (aux, groups_to_hide) {
    groups_to_hide = ['#group32', '#group33', '#group34', '#group38', '#group51', '#group52', '#group55', '#group56', '#group57', '#group58', '#group60', '#group66', '#group67', '#group71','#group122','#group123','#group124','#group125','#group191'];
    let groups_to_show = [];
    switch (aux) {
        case "Diarias":
            groups_to_show = ['#group32', '#group33', '#group34'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            
            initial_phase = jQuery("#edu_solicitacoes___etapa").val();
            if (initial_phase == ''){
                initial_phase = jQuery("#edu_solicitacoes___etapa")[0].innerText;
            }
            switchPhases(initial_phase);
            break;

        case "Pedido de produtos":
            groups_to_show = ['#group51', '#group52'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Adiantamento":
            groups_to_show = ['#group56', '#group33', '#group34'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            
            initial_phase = jQuery("#edu_solicitacoes___etapa").val();
            if (initial_phase == ''){
                initial_phase = jQuery("#edu_solicitacoes___etapa")[0].innerText;
            }
            switchPhases(initial_phase);
            break;

        case "Reembolso":
            groups_to_show = ['#group57', '#group58', '#group33'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Aluguel de veiculo":
            groups_to_show = ['#group60'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Servicos":
            groups_to_show = ['#group55'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "RPA":
            groups_to_show = ['#group33', '#group66'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Requisicao de bolsa":
            groups_to_show = ['#group71', '#group67'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;
        
        case "Recursos Humanos":
            groups_to_show = ['#group191'];
            showGroups(groups_to_show);
    
            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Outros":
            hideGroups(groups_to_hide);
            break;
    }
}

//////////////

const switchRH = function (aux, groups_to_hide) {
    groups_to_hide = ['.fb_el_edu_solicitacoes___nome_rh','.fb_el_edu_solicitacoes___cargo_rh','.fb_el_edu_solicitacoes___quantidade_rh','.fb_el_edu_solicitacoes___remuneracao_rh','.fb_el_edu_solicitacoes___descricao_rh','.fb_el_edu_solicitacoes___requisitos_rh','.fb_el_edu_solicitacoes___edital_rh','.fb_el_edu_solicitacoes___justificativa_rh','.fb_el_edu_solicitacoes___data_desliga','.fb_el_edu_solicitacoes___aviso_previo','.fb_el_edu_solicitacoes___datainicio_rh','.fb_el_edu_solicitacoes___datafinal_rh','.fb_el_edu_solicitacoes___lotacao_rh'];
    let groups_to_show = [];
    switch (aux) {
        case "Abertura de Processo Seletivo":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh','.fb_el_edu_solicitacoes___cargo_rh','.fb_el_edu_solicitacoes___quantidade_rh','.fb_el_edu_solicitacoes___remuneracao_rh','.fb_el_edu_solicitacoes___descricao_rh','.fb_el_edu_solicitacoes___requisitos_rh'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Contratacao":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh','.fb_el_edu_solicitacoes___edital_rh','.fb_el_edu_solicitacoes___justificativa_rh'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Encerramento de contrato":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh','.fb_el_edu_solicitacoes___nome_rh','.fb_el_edu_solicitacoes___cargo_rh','.fb_el_edu_solicitacoes___justificativa_rh','.fb_el_edu_solicitacoes___data_desliga','.fb_el_edu_solicitacoes___aviso_previo'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Alteracoes contratuais":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh','.fb_el_edu_solicitacoes___nome_rh','.fb_el_edu_solicitacoes___cargo_rh','.fb_el_edu_solicitacoes___descricao_rh'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Concessao de ferias":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh','.fb_el_edu_solicitacoes___nome_rh','.fb_el_edu_solicitacoes___cargo_rh','.fb_el_edu_solicitacoes___datainicio_rh','.fb_el_edu_solicitacoes___datafinal_rh'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;
        case "Gestao de beneficios":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh','.fb_el_edu_solicitacoes___nome_rh','.fb_el_edu_solicitacoes___cargo_rh','.fb_el_edu_solicitacoes___descricao_rh','.fb_el_edu_solicitacoes___justificativa_rh'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;
        case "Outros":
            groups_to_show = ['.fb_el_edu_solicitacoes___nome_rh','.fb_el_edu_solicitacoes___cargo_rh','.fb_el_edu_solicitacoes___descricao_rh'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;
        
        case " ":
            hideGroups(groups_to_hide);
            break;
    }
}

/////////////

const switchPhases = function (phase) {
    initial_type = jQuery("#edu_solicitacoes___tipo_solicitacao").val();
    switch (phase) {
        case "Encaminhada para a Fundacao":
        case "Encaminhada para a Fundação":
            initial_phase_fundacao = jQuery("#edu_solicitacoes___status_fun").val();
            switchPhasesFundacao(initial_phase_fundacao);
            
            groups_to_show = ['#group36'];
            showGroups(groups_to_show);

            groups_to_hide = ['#group38','.fb_el_edu_solicitacoes___correcoes','.fb_el_edu_solicitacoes___justifica_cancelamento'];
            hideGroups(groups_to_hide);
            break;

        case "Validacao da prestacao de contas":
        case "Validação da prestação de contas":
        case "Correcao da prestacao de contas":
        case "Correção da prestação de contas":
        case "Concluido":
        case "Concluído":
            if (initial_type == 'Diarias'){
                groups_to_show = ['#group38','#group36','#group122','#group124','#group187'];
            } else if (initial_type == 'Adiantamento') {
                groups_to_show = ['#group38','#group36','#group123','#group124','#group187'];
            } else {
                groups_to_show = ['#group38', '#group36'];
            }
        
            showGroups(groups_to_show);
            if (jQuery("#edu_solicitacoes___situacao_pre")[0].innerText == 'Devolução' && initial_type == 'Adiantamento'){
                groups_to_show = ['#group125'];
                showGroups(groups_to_show);
            }
            groups_to_hide = ['.fb_el_edu_solicitacoes___correcoes','.fb_el_edu_solicitacoes___justifica_cancelamento'];
            hideGroups(groups_to_hide);
            break;

        case "Devolvida ao autor para ciência e conclusão":
        case "Devolvida ao autor para ciencia e conclusao":
            groups_to_show = ['#group36'];
            showGroups(groups_to_show);

            groups_to_hide = ['.fb_el_edu_solicitacoes___correcoes','.fb_el_edu_solicitacoes___justifica_cancelamento'];
            hideGroups(groups_to_hide);

            if (jQuery("#edu_solicitacoes___situacao_pre")[0].innerText == 'Devolução' && initial_type == 'Adiantamento'){
                groups_to_show = ['#group125'];
                showGroups(groups_to_show);
            }
            break;

        case "Devolvida ao autor para correções":        
        case "Devolvida ao autor para correcoes":
            groups_to_show = ['.fb_el_edu_solicitacoes___correcoes'];
            showGroups(groups_to_show);
            
            groups_to_hide = ['.fb_el_edu_solicitacoes___justifica_cancelamento'];
            hideGroups(groups_to_hide);
            break;

        case "Cancelado":
            groups_to_show = ['.fb_el_edu_solicitacoes___justifica_cancelamento'];
            showGroups(groups_to_show);
            groups_to_hide = ['#group36', '#group38','#group121','#group122','#group124','#group123','#group125','#group147','#group187','.fb_el_edu_solicitacoes___correcoes'];
            hideGroups(groups_to_hide);
            break;
       
        default:
            groups_to_hide = ['#group36', '#group38','#group121','#group122','#group124','#group123','#group125','#group147','#group187','.fb_el_edu_solicitacoes___correcoes','.fb_el_edu_solicitacoes___justifica_cancelamento'];
            hideGroups(groups_to_hide);
    }
    prestacaoContasEtapaeStatus();
}

const switchPhasesFundacao = function (phase) {
    groups_to_hide = ['#group121','#group147','.fb_el_edu_solicitacoes___num_requisicao','.fb_el_edu_solicitacoes___valor_pg','.fb_el_edu_solicitacoes___correcoes_fun','.fb_el_edu_solicitacoes___valortotal_pg'];
    phase = phase.replace('<!-- ','');
    phase = phase.replace(' -->','');
    switch (phase) {
        case '':
        case " ":
        case "Recebido":
            hideGroups(groups_to_hide);
            break;

        case "Devolvido ao setor de projetos":
            hideGroups(groups_to_hide);
            groups_to_show = ['.fb_el_edu_solicitacoes___correcoes_fun'];
            showGroups(groups_to_show);
            break;

        case "Pagamento sem requisicao":
            hideGroups(groups_to_hide);
            groups_to_show = ['.fb_el_edu_solicitacoes___valor_pg','.fb_el_edu_solicitacoes___valortotal_pg'];
            showGroups(groups_to_show);
            break;

        case "Pagamento em analise":
        case "Pagamento com requisicao":
        case "Gestao de pessoas":
            hideGroups(groups_to_hide);
            groups_to_show = ['#group147','.fb_el_edu_solicitacoes___valortotal_pg'];
            showGroups(groups_to_show);
            break;

        case "Compras e servicos":
        case "Em cotacao pelo fornecedor":
        case "Em execucao pelo fornecedor":
        case "Encaminhado a Gestao de Contratos":
        case "Encaminhado ao Setor de Licitacoes":
            hideGroups(groups_to_hide);
            groups_to_show = ['#group121','.fb_el_edu_solicitacoes___valortotal_pg'];
            showGroups(groups_to_show);
            break;

        case "Concluido":
            break;

        default:
            hideGroups(groups_to_hide);
            break;
    }
}

const switchStatus = function (status) {
    switch (status) {
        case 'Negado':
            jQuery(status).parent().css("background-color", "#A41623");
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            prestacaoContasEtapaeStatus();
            break;

        case 'Em análise':
        case 'Em analise':
            jQuery(status).parent().css("background-color", "#ED7D3A");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            break;

        case 'Aprovado':
            jQuery(status).parent().css("background-color", "#136F63");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            break;
    }
}

const switchStatusValue = function (status) {
    initial_status = jQuery("#edu_solicitacoes___status_dg input[type='radio']:checked")
    switch (initial_status[0].value) {
        case 'Negado':
            jQuery(initial_status).parent().css("background-color", "#A41623");
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            prestacaoContasEtapaeStatus();
            break;

        case 'Em análise':
        case 'Em analise':
            jQuery(initial_status).parent().css("background-color", "#ED7D3A");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            break;

        case 'Aprovado':
            jQuery(initial_status).parent().css("background-color", "#136F63");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            break;
    }
}

const switchStatusChange = function (status) {
    switch (status.value) {
        case 'Negado':
            jQuery(status).parent().css("background-color", "#A41623");
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            prestacaoContasEtapaeStatus();
            break;

        case 'Em análise':
        case 'Em analise':
            jQuery(status).parent().css("background-color", "#ED7D3A");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            break;

        case 'Aprovado':
            jQuery(status).parent().css("background-color", "#136F63");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            prestacaoContasEtapaeStatus();
            break;
    }
}

//////////

const switchApproved = function (approved) {
    if (approved.value == undefined){
        approved.value = approved[0].value;
    }
    switch (approved.value) {
        case 'Nao':
            jQuery(approved).parent().css("background-color", "#A41623");
            //jQuery('.fabrikgrid_sim').removeAttr('style');
            jQuery("*[for^='edu_solicitacoes___contas_aprovada_input_1']").removeAttr('style');
            break;

        case 'Sim':
            jQuery(approved).parent().css("background-color", "#136F63");
            //jQuery('.fabrikgrid_nao').removeAttr('style');
            jQuery("*[for^='edu_solicitacoes___contas_aprovada_input_0']").removeAttr('style');
            break;
    }
}

//////////

const switchExpense = function (expense) {
    if (expense.value == undefined){
        expense.value = expense[0].value;
    }
    switch (expense.value) {
        case '0':
            jQuery(expense).parent().css("background-color", "#A41623");
            jQuery("*[for^='edu_solicitacoes___sem_custos_input_1']").removeAttr('style');
            break;

        case '1':
            jQuery(expense).parent().css("background-color", "#136F63");
            jQuery("*[for^='edu_solicitacoes___sem_custos_input_0']").removeAttr('style');
            break;
    }
}

/////////

const prestacaoContasEtapaeStatus = function (){
    initial_phase = jQuery("#edu_solicitacoes___etapa").val();
    if (initial_phase == ''){
        initial_phase = jQuery("#edu_solicitacoes___etapa")[0].innerText;
    } 

    initial_status = jQuery("#edu_solicitacoes___status_dg input[type='radio']:checked").val();
    if (initial_status == ''){
        initial_status = jQuery("#edu_solicitacoes___status_dg")[0].innerText;
    } 
    
    if (initial_phase == 'Concluído' || initial_phase == 'Concluido'){
        if (initial_status == 'Negado'){
            groups_to_hide = ['#group122','#group123','#group124','#group187'];
            hideGroups(groups_to_hide);
        } else if (initial_status == 'Aprovado'){
            switchPhases(initial_phase);
        }
    }
}

const switchAtendida = function (status) {
    if (status.value == undefined){
        status.value = status[0].value;
    }
    switch (status.value) {
        case 'Nao':
            jQuery(status).parent().css("background-color", "#A41623");
            //jQuery('.fabrikgrid_parcialmente').removeAttr('style');
            jQuery("*[for^='edu_solicitacoes___atendida_input_1']").removeAttr('style');

            //jQuery('.fabrikgrid_sim').removeAttr('style');
            jQuery("*[for^='edu_solicitacoes___atendida_input_2']").removeAttr('style');

            jQuery('.alert-pedido-atendido').remove();
            break;

        case 'Parcialmente':
            jQuery(status).parent().css("background-color", "#ED7D3A");
            //jQuery('.fabrikgrid_nao').removeAttr('style');
            jQuery("*[for^='edu_solicitacoes___atendida_input_0']").removeAttr('style');

            //jQuery('.fabrikgrid_sim').removeAttr('style');
            jQuery("*[for^='edu_solicitacoes___atendida_input_2']").removeAttr('style');

            jQuery('.alert-pedido-atendido').remove();
            break;

        case 'Sim':
            jQuery(status).parent().css("background-color", "#136F63");
            //jQuery('.fabrikgrid_parcialmente').removeAttr('style');
            jQuery("*[for^='edu_solicitacoes___atendida_input_1']").removeAttr('style');

            //jQuery('.fabrikgrid_nao').removeAttr('style');
            jQuery("*[for^='edu_solicitacoes___atendida_input_0']").removeAttr('style');

            if (jQuery("#edu_solicitacoes___tipo_solicitacao").val() == 'Pedido de produtos'){
                jQuery('.fb_el_edu_solicitacoes___atendida').append('<div class="alert-pedido-atendido alert alert-success" role="alert"> Por favor, atualize o seu estoque com os itens recebidos!</div>');
            }
            break;
    }
}

const tipoDeBolsista = function (tipo) {
    switch (tipo) {
        case 'Docente':
        case 'Discente':
        case 'Tecnico':
            groups_to_hide = ['.fb_el_edu_solicitacoes___termo_justificativa']
            groups_to_show= ['.fb_el_edu_solicitacoes___matricula', '.fb_el_edu_solicitacoes___declaracao_vinculo']
            hideGroups(groups_to_hide);
            showGroups(groups_to_show);
            break;

        case 'Externo':
            groups_to_show = ['.fb_el_edu_solicitacoes___termo_justificativa']
            groups_to_hide= ['.fb_el_edu_solicitacoes___matricula', '.fb_el_edu_solicitacoes___declaracao_vinculo']
            hideGroups(groups_to_hide);
            showGroups(groups_to_show);
            break;
    }
}

setTimeout(() => {
    hideGroups(groups_to_hide);
    let initial_type = jQuery("#edu_solicitacoes___tipo_solicitacao").val();
    let initial_priority = jQuery("#edu_solicitacoes___prioridade").val();
    let initial_phase = jQuery("#edu_solicitacoes___etapa").val();
    let initial_status = jQuery("#edu_solicitacoes___status_dg input[type='radio']:checked").val();
    let initial_atendida = jQuery("#edu_solicitacoes___atendida input[type='radio']:checked");
    let initial_approved = jQuery("#edu_solicitacoes___contas_aprovada input[type='radio']:checked");
    let initial_phase_fundacao = jQuery("#edu_solicitacoes___status_anterior")[0].innerHTML;
    let initial_typerh = jQuery("#edu_solicitacoes___tipo_rh").val();

    switchPriority(initial_priority);

    if (initial_phase == ''){
        initial_phase = jQuery("#edu_solicitacoes___etapa")[0].innerText;
    } 

    if (initial_status == undefined){
        initial_status = jQuery("#edu_solicitacoes___status_dg")[0].innerText;
        switchStatus(initial_status);
    } else {
        switchStatusValue(initial_status);
    }

    switchPhasesFundacao(initial_phase_fundacao);
    
    if (initial_atendida[0] != undefined){
        switchAtendida(initial_atendida);
    } 

    if (initial_approved[0] != undefined){
        switchApproved(initial_approved);
    } 

    let bolsista_inicial = jQuery("#edu_solicitacoes___tipo_bolsista").val();
    tipoDeBolsista(bolsista_inicial);

    switchGroups(initial_type, groups_to_hide);
    switchRH(initial_typerh, groups_to_hide);
    switchPhases(initial_phase);
    
    if (initial_phase == 'Devolvida ao autor para ciencia e conclusao' || jQuery("#edu_solicitacoes___etapa")[0].innerText == 'Devolvida ao autor para ciência e conclusão' || initial_phase == 'Concluido'  || jQuery("#edu_solicitacoes___etapa")[0].innerText == 'Concluído'){
            if (initial_type == 'Diarias'){
                groups_to_show = ['#group38','#group36','#group122','#group124'];
            } else if (initial_type == 'Adiantamento') {
                groups_to_show = ['#group38','#group36','#group123','#group124'];
            } else {
                groups_to_show = ['#group38', '#group36'];
            }
        showGroups(groups_to_show);
    }

    prestacaoContasEtapaeStatus();

}, 100);

jQuery('#edu_solicitacoes___tipo_solicitacao').on('change', function () {
    switchGroups(this.value, groups_to_hide);
});

jQuery('#edu_solicitacoes___tipo_rh').on('change', function () {
    switchRH(this.value, groups_to_hide);
});

jQuery('#edu_solicitacoes___prioridade').on('change', function () {
    switchPriority(this.value);
});

jQuery('#edu_solicitacoes___etapa').on('change', function () {
    switchPhases(this.value);
});

jQuery('#edu_solicitacoes___status_fun').on('change', function () {
    switchPhasesFundacao(this.value, groups_to_hide);
});

jQuery("*[id^='edu_solicitacoes___atendida_input_']").on('click', function () {
    switchAtendida(this);
});

jQuery("*[id^='edu_solicitacoes___status_dg_input_']").on('click', function () {
    switchStatusChange(this);
});

jQuery("*[id^='edu_solicitacoes___contas_aprovada_input_']").on('click', function () {
    switchApproved(this);
});

jQuery('#edu_solicitacoes___tipo_bolsista').on('change', function () {
    tipoDeBolsista(this.value);
});

jQuery("*[id^='edu_solicitacoes___sem_custos_input_']").on('click', function () {
    switchExpense(this);
    group = ['.fb_el_edu_solicitacoes___valor_total']
    if (this.value == 1){
        hideGroups(group);
    } else {
        showGroups(group);
    }
});