/**
* Ensure that Fabrik's loaded
*/
window.addEvent('fabrik.loaded', function () {

let groups_to_hide = ['#group31', '#group32', '#group33', '#group34', '#group36', '#group38', '#group51', '#group52', '#group55', '#group56', '#group57', '#group58', '#group60', '#group66', '#group67', '#group71', '#group121', '#group122', '#group123', '#group124', '#group125', '#group147', '#group175', '#group187','#group191','.fb_el_edu_solicitacoes___num_requisicao_ro', '.fb_el_edu_solicitacoes___valor_pg_ro','.fb_el_edu_solicitacoes___correcoes_ro', '.fb_el_edu_solicitacoes___justifica_cancelamento_ro','.fb_el_edu_solicitacoes___termo_justificativa_ro','.fb_el_edu_solicitacoes___matricula_ro', '.fb_el_edu_solicitacoes___declaracao_vinculo_ro','.fb_el_edu_solicitacoes___valor_total_ro','.fb_el_edu_solicitacoes___nome_rh_ro','.fb_el_edu_solicitacoes___cargo_rh_ro','.fb_el_edu_solicitacoes___quantidade_rh_ro','.fb_el_edu_solicitacoes___remuneracao_rh_ro','.fb_el_edu_solicitacoes___descricao_rh_ro','.fb_el_edu_solicitacoes___requisitos_rh_ro','.fb_el_edu_solicitacoes___edital_rh_ro','.fb_el_edu_solicitacoes___justificativa_rh_ro','.fb_el_edu_solicitacoes___data_desliga_ro','.fb_el_edu_solicitacoes___aviso_previo_ro','.fb_el_edu_solicitacoes___datainicio_rh_ro','.fb_el_edu_solicitacoes___datafinal_rh_ro','.fb_el_edu_solicitacoes___lotacao_rh_ro'];

setTimeout(() => {
    let request_type = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___tipo_solicitacao').get('value');
    let priority_level = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___prioridade').get('value');
    let initial_phase = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___etapa').get('value');
    let initial_phase_fundacao = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___status_anterior').get('value');
    let initial_status = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___status_dg').get('value');
    let tipo_bolsista = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___tipo_bolsista').get('value');
    let sem_custos = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___sem_custos').get('value');
    let request_typerh = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___tipo_rh').get('value');

    hideGroups(groups_to_hide);
    switchGroups(request_type);
    switchRH(request_typerh);
    switchPriority(priority_level);
    switchPhases(initial_phase);
    switchPhasesFundacao(initial_phase_fundacao);
    initialStatusDenied(initial_status);
    tipoDeBolsista(tipo_bolsista);
    semCustos(sem_custos)

}, 1000);

const showGroups = function (groups_to_show) {
    console.log("groups_to_show " + groups_to_show);
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const switchPriority = function (priority_level) {
    const group = ['#group31']
    switch (priority_level) {
        case "Alta":
        case "Critica":
            showGroups(group);
            break;
    }
}

const switchGroups = function (request_type) {
    let groups_to_show = [];
    switch (request_type) {
        case "Diarias":
            groups_to_show = ['#group32', '#group33', '#group34', '#group175'];
            showGroups(groups_to_show);
            break;

        case "Pedido de produtos":
            groups_to_show = ['#group51', '#group52'];
            showGroups(groups_to_show);
            break;

        case "Adiantamento":
            groups_to_show = ['#group56', '#group33', '#group34', '#group175'];
            showGroups(groups_to_show);
            break;

        case "Reembolso":
            groups_to_show = ['#group57', '#group58', '#group33', '#group175'];
            showGroups(groups_to_show);
            break;

        case "Aluguel de veiculo":
            groups_to_show = ['#group60', '#group175'];
            showGroups(groups_to_show);
            break;

        case "Servicos":
            groups_to_show = ['#group55'];
            showGroups(groups_to_show);
            break;

        case "RPA":
            groups_to_show = ['#group33', '#group66', '#group175'];
            showGroups(groups_to_show);
            break;

        case "Requisicao de bolsa":
            groups_to_show = ['#group71', '#group67', '#group175'];
            showGroups(groups_to_show);
            break;

        case "Recursos Humanos":
            groups_to_show = ['#group191'];
            showGroups(groups_to_show);
            break;

        case "Outros":
            break;
    }
}

/////////////////////

const switchRH = function (request_typerh) {
    let groups_to_show = [];
    switch (request_typerh) {
        case "Abertura de Processo Seletivo":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh_ro','.fb_el_edu_solicitacoes___cargo_rh_ro','.fb_el_edu_solicitacoes___quantidade_rh_ro','.fb_el_edu_solicitacoes___remuneracao_rh_ro','.fb_el_edu_solicitacoes___descricao_rh_ro','.fb_el_edu_solicitacoes___requisitos_rh_ro'];
            showGroups(groups_to_show);
            break;

        case "Contratacao":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh_ro','.fb_el_edu_solicitacoes___edital_rh_ro','.fb_el_edu_solicitacoes___justificativa_rh_ro'];
            showGroups(groups_to_show);
            break;

        case "Encerramento de contrato":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh_ro','.fb_el_edu_solicitacoes___nome_rh_ro','.fb_el_edu_solicitacoes___cargo_rh_ro','.fb_el_edu_solicitacoes___justificativa_rh_ro','.fb_el_edu_solicitacoes___data_desliga_ro','.fb_el_edu_solicitacoes___aviso_previo_ro'];
            showGroups(groups_to_show);
            break;

        case "Alteracoes contratuais":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh_ro','.fb_el_edu_solicitacoes___nome_rh_ro','.fb_el_edu_solicitacoes___cargo_rh_ro','.fb_el_edu_solicitacoes___descricao_rh_ro'];
            showGroups(groups_to_show);
            break;

        case "Concessao de ferias":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh_ro','.fb_el_edu_solicitacoes___nome_rh_ro','.fb_el_edu_solicitacoes___cargo_rh_ro','.fb_el_edu_solicitacoes___datainicio_rh_ro','.fb_el_edu_solicitacoes___datafinal_rh_ro'];
            showGroups(groups_to_show);
            break;

        case "Gestao de beneficios":
            groups_to_show = ['.fb_el_edu_solicitacoes___lotacao_rh_ro','.fb_el_edu_solicitacoes___nome_rh_ro','.fb_el_edu_solicitacoes___cargo_rh_ro','.fb_el_edu_solicitacoes___descricao_rh_ro','.fb_el_edu_solicitacoes___justificativa_rh_ro'];
            showGroups(groups_to_show);
            break;

        case "Outros":
            groups_to_show = ['.fb_el_edu_solicitacoes___nome_rh_ro','.fb_el_edu_solicitacoes___cargo_rh_ro','.fb_el_edu_solicitacoes___descricao_rh_ro'];
            showGroups(groups_to_show);
            break;

        case " ":
            break;
    }
}

////////////////////

const switchPhases = function (phase) {
    switch (phase) {
        case "Encaminhada para a Fundacao":            
            groups_to_show = ['#group36'];
            showGroups(groups_to_show);
            break;

        case "Devolvida ao autor para correcoes":
            groups_to_show = ['.fb_el_edu_solicitacoes___correcoes_ro'];
            showGroups(groups_to_show);
            break;

        case "Devolvida ao autor para ciencia e conclusao":
            request_type = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___tipo_solicitacao').get('value');
            if (request_type == 'Diarias'){
                groups_to_show = ['#group38','#group36','#group122','#group124'];
            } else if (request_type == 'Adiantamento') {
                groups_to_show = ['#group38','#group36','#group123','#group124'];
            } else {
                groups_to_show = ['#group38', '#group36'];
            }
            showGroups(groups_to_show);
            break;

        case "Validacao da prestacao de contas":
        case "Correcao da prestacao de contas":
        case "Concluido":
            request_type = Fabrik.getBlock('details_19').elements.get('edu_solicitacoes___tipo_solicitacao').get('value');
            if (request_type == 'Diarias'){
                groups_to_show = ['#group38','#group36','#group122','#group124', '#group187'];
            } else if (request_type == 'Adiantamento') {
                groups_to_show = ['#group38','#group36','#group123','#group124', '#group187'];
            } else {
                groups_to_show = ['#group38', '#group36'];
            }
            showGroups(groups_to_show);
            break;

        case "Cancelado":
            groups_to_show = ['.fb_el_edu_solicitacoes___justifica_cancelamento_ro'];
            showGroups(groups_to_show);
            break;
    }
}

const initialStatusDenied = function (initial_status){
    if (initial_status == 'Negado'){
        groups_to_hide = ['#group122','#group123','#group124'];
        hideGroups(groups_to_hide);
    }
}

const switchPhasesFundacao = function (phase) {
    groups_to_hide = ['#group121','#group147','.fb_el_edu_solicitacoes___num_requisicao_ro','.fb_el_edu_solicitacoes___valor_pg_ro','.fb_el_edu_solicitacoes___valortotal_pg_ro'];
    hideGroups(groups_to_hide);

    switch (phase) {
        case "Pagamento sem requisicao":
            groups_to_show = ['.fb_el_edu_solicitacoes___valor_pg_ro','.fb_el_edu_solicitacoes___valortotal_pg_ro'];
            showGroups(groups_to_show);
            break;

        case "Pagamento com requisicao":
            groups_to_show = ['#group147','.fb_el_edu_solicitacoes___valortotal_pg_ro'];
            showGroups(groups_to_show);
            break;

        case "Compras e servicos":
            groups_to_show = ['#group121','.fb_el_edu_solicitacoes___valortotal_pg_ro'];
            showGroups(groups_to_show);
            break;

        default:
            hideGroups(groups_to_hide);
            break;
    }
}

const tipoDeBolsista = function (tipo) {
    switch (tipo) {
        case 'Docente':
        case 'Discente':
        case 'Tecnico':
            groups_to_show = ['.fb_el_edu_solicitacoes___matricula_ro', '.fb_el_edu_solicitacoes___declaracao_vinculo_ro']
            showGroups(groups_to_show);
            break;

        case 'Externo':
            groups_to_show = ['.fb_el_edu_solicitacoes___termo_justificativa_ro']
            showGroups(groups_to_show);
            break;
    }
}

const semCustos= function(sem_custos) {
    if (sem_custos == 0){
        groups_to_show = ['.fb_el_edu_solicitacoes___valor_total_ro']
        showGroups(groups_to_show);
    }
}
});