// /**
// * Ensure that Fabrik's loaded
// */

const cor = function () {
    let background = document.querySelectorAll(".edu_aplicativos___cor");
    background.forEach((element) => {
        color = element.innerHTML;
        element.parentElement.parentElement.parentElement.setStyle('background', element.innerHTML);
        element.classList.add('fabrikHide');
    })
}

const link = function () {
    jQuery("a[title^='link_aplicativo']").each(function (index) {
        let link = jQuery(this).attr('href');
        let btn = jQuery(".fabrik_view.fabrik__rowlink");
        if (btn[index].firstChild) {
            btn[index].firstChild.remove();
        }
        btn[index].href = link;
        btn[index].target = "_blank";
        btn[index].innerHTML = "Acessar";
        btn[index].title = "Acessar";
    });
}


// Callback function to execute when mutations are observed
const callback = (mutationList, observer) => {
    console.log(mutationList);
    var btn_acessar = document.querySelectorAll(".btn.fabrik_view.fabrik__rowlink.btn-default")[0].title;
    
    if (btn_acessar != 'Acessar'){ 
        cor();
        link();
    }
};

const targetNode = document.getElementById('list_26_com_fabrik_26');
const config = {attributes: true, childList: true, subtree: true };
const observer = new MutationObserver(callback);
observer.observe(targetNode, config);

cor();
link();