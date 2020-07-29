/* Init */


// $('#sheet_attachments input').addClass('old');
$('#sheet_attachments .custom-file-input').addClass('old');


// Sheet subtitle not required (à améliorer)
$('#sheet_subtitle').prop('required',false);

autosize($('#sheet_title'));


$('input#sheet_pic').on('change',function(){

    $(this,'#sheet_pic').next().html('<i class="fas fa-check-circle success mr-3"></i> Image chargée');
    
    $('#default_pic').prop('checked',false);

});

/* VALIDATION */
/* Modal de confirmation, inspection des champs vides */
$('#confirm-btn').click(function(){

    /* Init */
    $('#form-notifications').empty();
    $('#sheet_save').prop( "disabled", false );

    var notifications = '';


    /* Title empty */
    if(!$('#sheet_title').val()) {

        $('#alert-valid').addClass('d-none');
        $('#form-notifications').append('<div class="alert alert-warning mt-2" role="alert"><i class="fas fa-exclamation-triangle mr-2"></i>Fiche sans titre</div>');

    }

    /* Header empty */
    if($('#sheet_headers .header_title input').length > 0) {

        $('#sheet_headers .header_title input').each(function() {
           
            if(!$(this).val()) {
                
                $('#alert-valid').addClass('d-none');
                notifications += '<div class="alert alert-warning mt-2" role="alert"><i class="fas fa-exclamation-triangle mr-2"></i>Entête sans titre</div>';
                return false;
            }
            
        });

    }

    /* Header empty */
    if($('#sheet_headers .my-section .title input').length > 0) {

        $('#sheet_headers .my-section .title input').each(function() {
           
            if(!$(this).val()) {
                
                $('#alert-valid').addClass('d-none');
                notifications += '<div class="alert alert-warning mt-2" role="alert"><i class="fas fa-exclamation-triangle mr-2"></i>Titre secondaire manquant (entête)</div>';
                return false;
            }
            
        });

    }

    if($('#sheet_headers .my-section .content textarea').length > 0) {

        $('#sheet_headers .my-section .content textarea').each(function() {
           
            if(!$(this).val()) {
                
                $('#alert-valid').addClass('d-none');
                notifications += '<div class="alert alert-warning mt-2" role="alert"><i class="fas fa-exclamation-triangle mr-2"></i>Information manquante (entête)</div>';
                return false;
            }
            
        });

    }

    /* Section empty */
    if($('#sheet_paragraphs .paragraph_title input').length > 0) {

        $('#sheet_paragraphs .paragraph_title input').each(function() {
           
            if(!$(this).val()) {
                
                $('#alert-valid').addClass('d-none');
                notifications += '<div class="alert alert-warning mt-2" role="alert"><i class="fas fa-exclamation-triangle mr-2"></i>Section sans titre</div>';
                return false;

            }
            
        });

    }

    /* Attachment empty */
    if($('#sheet_attachments .title input:not(.old)').length > 0) {

        $('#sheet_attachments .title input:not(.old)').each(function() {
           
            if(!$(this).val()) {
                
                $('#alert-valid').addClass('d-none');
                $('#sheet_save').attr('disabled', 'disabled');
                notifications += '<div class="alert alert-danger mt-2" role="alert"><i class="fas fa-minus-circle mr-2"></i>Titre de pièce jointe manquant <a class="float-right" data-dismiss="modal" aria-label="Close"><strong>Corriger</strong></a></div>';
                return false;
            }
            
        });

    }

    if($('#sheet_attachments .custom-file input:not(.old)').length > 0) {

        $('#sheet_attachments .custom-file input:not(.old)').each(function() {
           
            if(!$(this).val()) {
                
                $('#alert-valid').addClass('d-none');
                $('#sheet_save').attr('disabled', 'disabled');
                notifications += '<div class="alert alert-danger mt-2" role="alert"><i class="fas fa-minus-circle mr-2"></i>Fichier de pièce jointe manquant <a class="float-right" data-dismiss="modal" aria-label="Close"><strong>Corriger</strong></a></div>';
                return false;
            }
            
        });

    }

    /* Organization empty */
    console.log($('#sheet_organization option:selected').val());
    if(!$('#sheet_organization option:selected').val()) {

        console.log('ok ici');

        $('#alert-valid').addClass('d-none');
        $('#sheet_save').attr('disabled', 'disabled');
        notifications += '<div class="alert alert-danger mt-2" role="alert"><i class="fas fa-minus-circle mr-2"></i>Aucun organisme sélectionné<a class="float-right" data-dismiss="modal" aria-label="Close"><strong>Corriger</strong></a></div>';
    }


    // Pas de notifications
    if(!notifications){

        notifications = '<div class="alert alert-success mt-2" role="alert" id="alert-valid"><i class="fas fa-check-circle mr-2"></i>Tout semble correct</div>';

    }

    $('#form-notifications').append(notifications);


});


/* Bouton de validation */
$('#sheet_save').click(function(){

    $('.modal-content.validation').addClass('d-none');
    $('.modal-content.loading').removeClass('d-none');

});

autosize($('#sheet_headers div.section.content textarea'));




/* BROUILLON */
/* Modal de confirmation, inspection des champs vides */
$('#sheet_saveDraft, #sheet_saveDraftExit').click(function(){

    /* Init */
    $('.loading.ok').removeClass('d-none');
    $('.loading.invalid').addClass('d-none');



    /* Attachment empty */
    if($('#sheet_attachments .title input:not(.old)').length > 0) {

        $('#sheet_attachments .title input:not(.old)').each(function() {
         
            if(!$(this).val()) {
                
                $('.loading.ok').addClass('d-none');
                $('.loading.invalid').removeClass('d-none');
                return false;
            }
            
        });

    }

    if($('#sheet_attachments .custom-file input:not(.old)').length > 0) {

        $('#sheet_attachments .custom-file input:not(.old)').each(function() {
           
            if(!$(this).val()) {
                
                $('.loading.ok').addClass('d-none');
                $('.loading.invalid').removeClass('d-none');
                return false;
            }
            
        });

    }

});


$('#sheet_pic').next().html('<i class="fas fa-upload mr-3"></i> Changer d\'image de fond');

$('input.custom-file-input').on('change',function(){

    $(this,'#sheet_pic').next().html('<i class="fas fa-check-circle success mr-3"></i> Image chargée');

});

