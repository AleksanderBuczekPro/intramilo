function scroll_to(div){
    $('html, body').animate({
        scrollTop: $(div).offset().top
    },1000);
}

function formatSizeUnits(bytes)
{
    if (bytes >= 1073741824)
    {
        bytes = (bytes / 1073741824).toFixed(2) + ' Go';
    }
    else if (bytes >= 1048576)
    {
        bytes = (bytes / 1048576).toFixed(1) + ' Mo';
    }
    else if (bytes >= 1024)
    {
        bytes = Math.ceil(bytes / 1024) + ' Ko';
    }
    else if (bytes > 1)
    {
        bytes = Math.ceil(bytes) + ' o';
    }
    else if (bytes == 1)
    {
        bytes = bytes + ' o';
    }
    else
    {
        bytes = '0 o';
    }

    return bytes;
}


// Fonction permettant de charger l'input de chargement d'une pièce jointe
$('#add-attachment').click(function(){

    changeUpdate();

    // Je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-attachment-counter').val(); // + transforme la chaine de caractère en nombre

    console.log("INDEX => " + index);

    // Je récupère le prototype des entrées
    const tmpl = $('#sheet_attachments').data('prototype').replace(/__a__/g, index); // g = "pour chaque"

    console.log("Nombre de pièces jointes = " + index);

    // J'injecte ce code au sein de la div
    $('#sheet_attachments').append(tmpl);

    // Placeholder File Upload Input
    $('#sheet_attachments_' + index + '_genericFile_file').next().html('<i class="fas fa-upload mr-3"></i> Aucun fichier selectionné');

    $('#sheet_attachments_' + index + '_genericFile_file').prop('required',true);

    $('input.custom-file-input:last-child').addClass('ok');

    $('input.custom-file-input').on('change',function(){

        var name = (this.files[0].name).split('.', 1);
        var size = formatSizeUnits(this.files[0].size);
        var mime = this.files[0].type;

        console.log("--------------------------");
        console.log("Name => " + name);
        console.log("Size => " + size);
        console.log("Mime => " + mime);

        changeUpdate();

        

        console.log(this.files[0].type);

        var type = "?";
        var color = "black";

        if (mime == "image/png"){

            type = "png";
            color = "rose";

        }

        if (mime == "image/jpeg"){

            type = "jpeg";
            color = "violet";

        }

        if (mime == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || mime == "application/msword" || mime == "application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document"){

            type = "w";
            color = "bleu";

        }

        if (mime == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || mime == "application/vnd.ms-excel"){

            type = "x";
            color = "vert";

        }

        if (mime == "application/pdf"){

            type = "pdf";
            color = "rouge";

        }


        let fileName = $(this).val().split('\\').pop(); 

        console.log("INDEX => " + "sheet_attachments_" + index + "_title");

        /* $('#sheet_attachments_' + index + '_title').val(name);*/
        $(this, '#sheet_attachments_' + index + ' .details').html(size);

        $(this, '#sheet_attachments_' + index + '_genericFile_file').next().html('<i class="fas fa-check-circle success mr-3"></i>' + name);

        /* $('#sheet_attachments_' + index + ' .type').html(type);
        $('#sheet_attachments_' + index + ' .type').addClass(color); */



    });

    $('#widgets-attachment-counter').val(index + 1);

    // Je gère le bouton supprimer
    handleDeleteButtons();

    scroll_to('#anchor_attachments');
    
});


$('#add-paragraph').click(function(){

    changeUpdate();

    // Je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-paragraph-counter').val(); // + transforme la chaine de caractère en nombre

    // Je récupère le prototype des entrées
    const tmpl = $('#sheet_paragraphs').data('prototype').replace(/__p__/g, index); // g = "pour chaque"

    
    console.log(index);

    // J'injecte ce code au sein de la div
    $('#sheet_paragraphs').append(tmpl);

    var editor = CKEDITOR.instances["sheet_paragraphs_" + index + "_content"];

    editor.on('change', function() { 
            changeUpdate();

    });

    $('#widgets-paragraph-counter').val(index + 1);

    // Je gère le bouton supprimer
    handleDeleteButtons();

    scroll_to('#block_paragraph_sheet_paragraphs_' + index);


});





$("#sheet_paragraphs").on('click', '.paragraph-move', function () {
    
    var id = $(this).data('id'); // sheet_paragraphs_[i]

    // Counter
    var counter = parseInt(id.split("_")[2]);

    // Current title
    var current_title = $("#" + id + "_title");

    // CKE Content sheet_paragraphs_[i]_content
    var current_editor = CKEDITOR.instances["sheet_paragraphs_" + counter + "_content"];

    // Si on descend le block
    if($(this).hasClass("down")){

        // Next title
        counter = counter + 1;
        while($("#" + id.split("_")[0] + "_" + id.split("_")[1] + "_" +  counter + '_title').length === 0){
            
            counter = counter + 1;

        }
        
    // Up
    }else{

        // Next title
        counter = counter - 1;
        while($("#" + id.split("_")[0] + "_" + id.split("_")[1] + "_" +  counter + '_title').length === 0){
            
            counter = counter - 1;

        }

    }
        
        var next_title = $("#" + id.split("_")[0] + "_" + id.split("_")[1] + "_" +  counter + '_title');

        // var next_cke_id = id.split("_")[0] + "_" + id.split("_")[1] + "_" +  counter + '_content';
        var next_editor = CKEDITOR.instances["sheet_paragraphs_" + counter + "_content"];

        // Relacement
        var temp = next_title.val();

        next_title.val(current_title.val());
        current_title.val(temp);


        var temp_content = next_editor.getData();

        next_editor.setData(current_editor.getData());
        current_editor.setData(temp_content);



});


$("#sheet_headers").on('click', '.section-move', function () {
    
    var id = $(this).data('id'); // sheet_headers_[i]

    console.log(id);

    // Counter
    var header_id = parseInt(id.split("_")[2]);
    var counter = parseInt(id.split("_")[4]);

    // Current title
    var current_title = $("#" + id + "_title");

    // Current title
    var current_content = $("#" + id + "_content");

    console.log(header_id);

    console.log(current_title.val());
    console.log(current_content.val());

    console.log("#" + id.split("_")[0] + "_" + id.split("_")[1] + "_" + header_id + "_" + id.split("_")[3] + "_" + counter + '_title');

    // Si on descend le block
    if($(this).hasClass("down")){

        // Next title
        counter = counter + 1;
        while($("#" + id.split("_")[0] + "_" + id.split("_")[1] + "_" + header_id + "_" + id.split("_")[3] + "_" + counter + '_title').length === 0){
            
            counter = counter + 1;

        }
        
    // Up
    }else{

        // Next title
        counter = counter - 1;
        while($("#" + id.split("_")[0] + "_" + id.split("_")[1] + "_" + header_id + "_" + id.split("_")[3] + "_" + counter + '_title').length === 0){
            
            counter = counter - 1;

        }

    }

    var next_title = $("#" + id.split("_")[0] + "_" + id.split("_")[1] + "_" + header_id + "_" + id.split("_")[3] + "_" + counter + '_title');
    var next_content = $("#" + id.split("_")[0] + "_" + id.split("_")[1] + "_" + header_id + "_" + id.split("_")[3] + "_" + counter + '_content');

     // Replacement
    var temp = next_title.val();

    next_title.val(current_title.val());
    current_title.val(temp);


    var temp_content = next_content.val();

    next_content.val(current_content.val());
    current_content.val(temp_content);


   


});


/* Introduction */
$('#add-introduction').click(function(){

    // On affiche l'introduction
    $('#block_introduction').removeClass("d-none");
    $('#delete-introduction').removeClass("d-none");


    // On affiche plus le bouton "Ajouter une introduction"
    $(this).prop('disabled', true);

    scroll_to('#anchor_introduction');

    changeUpdate();


});


$('#delete-introduction-confirm').click(function(){


    // On vide l'éditeur de l'introduction
    CKEDITOR.instances.sheet_introduction.setData('');


    // On n'affiche plus l'introduction
    $('#block_introduction').addClass("d-none");
    $('#delete-introduction').addClass("d-none");

    // On affiche de nouveau le bouton "Ajouter une introduction"
    //$('#add-introduction').removeClass("d-none");
    $('#add-introduction').prop('disabled', false);

    changeUpdate();


});




$('#add-header').click(function(){

    changeUpdate();

    // Je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val(); // + transforme la chaine de caractère en nombre

    // Je récupère le prototype des entrées
    const tmpl = $('#sheet_headers').data('prototype').replace(/__h__/g, index); // g = "pour chaque"

    // J'injecte ce code au sein de la div
    $('#sheet_headers').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // Je gère le bouton supprimer
    handleDeleteButtons();

    scroll_to('#anchor_sheet_headers_' + index);

});


function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;

        $(target).remove();

    });

}

function changeUpdate(){

    if($('#change-circle').hasClass('success')){

        $('#change-circle').removeClass('success');
        $('#change-circle').addClass('danger');

        $('#change-circle').html('<i class="fas fa-exclamation-circle"></i>');
        // $('#change-text').html('Modifications non sauvegardées');

    }

}

function updateCounter(){
    const count = +$('#sheet_headers div.form-group.header').length;
    const count_p = +$('#sheet_paragraphs div.form-group.my-paragraph').length;
    const count_a = +$('#sheet_attachments div.file-card').length;

    /* Attachments */
    $('#widgets-attachment-counter').val(count_a);

    /* Paragraphs */
    $('#widgets-paragraph-counter').val(count_p);

    for (i = 0; i < count_p; i++) {

        CKEDITOR.instances["sheet_paragraphs_" + i + "_content"].on('change', function() { 
            changeUpdate();
        });
    } 
    


    /* Headers */
    $('#widgets-counter').val(count);


    /* Sections */
    $('#sheet_headers div.form-group.header').each(function( index ) {

        var count_section = +$('#sheet_headers_'+ index +'_sections div.form-group.my-section').length;

        $('#widgets-section-counter-sheet_headers_'+ index +'_sections').val(count_section);

    });




}

$(document).on('click', '.add-section', function(event){

    changeUpdate();

    // Je récupère l'id du header pour savoir dans lequel faut ajouter une ligne
    var id = $(this).attr('id'); // sheet_headers_0_sections
    // id = id.split("_")[2]; // 0

    console.log('#widgets-section-counter-'+ id);

    var header_id = '#' + $(this).attr('id');

    // Je récupère le numéro des futurs champs que je vais créer
    var counter = $('#widgets-section-counter-'+ id).val();

    console.log('COUNTER SECTION => ' + counter);

    const index = +counter; // + transforme la chaine de caractère en nombre

    // Je récupère le prototype des entrées
    const tmpl = $(header_id).data('prototype').replace(/__s__/g, index); // g = "pour chaque"

    // J'injecte ce code au sein de la div
    $(header_id).append(tmpl);

    $('#widgets-section-counter-' + id).val(index + 1);

    autosize($(header_id + ' div.section.content textarea'));

    // Je gère le bouton supprimer
    handleDeleteButtons();
   
    
    
});


$('form[name="organization"]').submit(function(e) {

    e.preventDefault();
    var path = "{{ path('organization_create_from_sheet') }}";
    var formSerialize = $(this).serialize();

    $.post(path, formSerialize, function(response) {
        //your callback here
        alert(response);
    }, 'JSON');

});


$('#sheet_pic').next().html('<i class="fas fa-upload mr-3"></i> Changer d\'image de fond');

// Quand le document a fini de se charger, on charge les interlocuteurs
$(document).ready(function() {

    loadInterlocutors();

    updateCounter();
    handleDeleteButtons();

    /* initializeAttachment(); */

    $('#sheet_attachments_' + index + '_genericFile_file').next().html('<i class="fas fa-upload mr-3"></i> Aucun fichier selectionné');




});

    // Changes
    $('#sheet_subtitle , #sheet_title').keyup(function() {
        
        changeUpdate();

    });

    $('#sheet_title').keyup(function() {

        $('#change-text').html($(this).val());

    });


    $("input, textarea").keyup(function(){

        changeUpdate();

    });

    $('input.custom-file-input, select').on('change',function(){
        changeUpdate();
    });

    CKEDITOR.instances["sheet_introduction"].on('change', function() { 
        changeUpdate();
    });


    $('#delete-attachment, #delete-header, #delete-paragraph, button.delete-section, button.section-move, button.paragraph-move').on('click',function(){
        changeUpdate();
    });



