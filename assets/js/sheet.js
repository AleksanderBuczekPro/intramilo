$('#add-header').click(function(){

    // Je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val(); // + transforme la chaine de caractère en nombre

    // Je récupère le prototype des entrées
    const tmpl = $('#sheet_headers').data('prototype').replace(/__name__/g, index); // g = "pour chaque"

    // J'injecte ce code au sein de la div
    $('#sheet_headers').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // Je gère le bouton supprimer
    handleDeleteButtons();

});

function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;

        $(target).remove();

    });

}

function updateCounter(){
    const count = +$('#sheet_headers div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();

