function loadInterlocutors() {

    $('#confirm-btn, #sheet_saveDraft, #sheet_saveDraftExit').prop( "disabled", true );

    $("#interlocutors-message").html("<p class='text'>Chargement des interlocuteurs en cours... </p>");

    // Si un organisme est selectionné
    var organization = $("select#sheet_organization").find("option:selected");
    var organization_id = organization.val();

    if(organization_id){

        $.ajax({
            type: 'POST',
            url: path,
            data: { id: organization_id},
            success: function(interlocutors) {


                var cards = "";

                for (var item in interlocutors) {

                var id = interlocutors[item]['id'];
                var fonction = interlocutors[item]['function'];
                var fullName = interlocutors[item]['firstName'] + " " + interlocutors[item]['lastName'];
                var initiales = interlocutors[item]['firstName'].slice(0,1) + interlocutors[item]['lastName'].slice(0,1);

                var email = interlocutors[item]['email'];
                var phoneNumber = interlocutors[item]['phoneNumber'];

                // Récupération de l'ID de la fiche
                var sheet_id = $('#sheet_id').text();

                var checked = "";
                var active = "";

                var sheets = interlocutors[item]['sheets'];

                // Pour chaque interlocuteur, on vérifie s'il y la fiche correspondante dans sa liste de fiches associées dans $interlocutor->getSheets()
                if(typeof sheets !== 'undefined'){
                    for (var item2 in sheets){

                        if(sheet_id == sheets[item2]['id']){

                            checked = "checked='checked'";
                            active = "active";

                        }

                    }
                }

                if(!email){ address = "-" };
                if(!phoneNumber){ phoneNumber = "-" };

                // cards += " <div class='col-12'><div class='card'><div class='card-body'><div class='form-group'><label class='interlocutor_label'><input name='interlocutor-" + interlocutors[item]['id'] + "' type='checkbox' value='" + interlocutors[item]['id'] + "' class='interlocutor_checkbox'><span> Non sélectionné </span></label> <br>" + interlocutors[item]['function'] + "</div><div class='mt-2'><strong>" + interlocutors[item]['firstName'] + " "+ interlocutors[item]['lastName'] + "</strong></div>" + interlocutors[item]['phoneNumber'] + " <br>" + interlocutors[item]['email'] + " <br> </div></div></div>";
                cards += "<div class='col-12'>" +
                            "<div class='card my-card user interlocutor no-br m-0 " + active +  "'>" +
                                "<div class='card-body p-0'>" +

                                    "<table class='my-table'>" +
                                        "<tbody>" +
                                            "<tr>" +
                                                "<td class='text-left pl-2'>" +
                                                    "<input type='checkbox' name='interlocutor-" + id + "' value=" + id + " class='my-checkbox d-none' " + checked +">" +
                                                    "<div class='text my-secondary main'>" + fullName +  "</div>" +
                                                    "<div class='text my-secondary'>" + fonction + "</div>" +
                                                    "<div class='text my-secondary'><i class='uil uil-envelope-alt'></i>" + email + "</div>" +
                                                    "<div class='text my-secondary'><i class='uil uil-phone-alt'></i>" + phoneNumber + "</div>" +
                                                "</td>" +
                                            "</tr>" +
                                        "</tbody>" +
                                    "</table>" +
                                    
                                "</div>" +
                            "</div>" +
                        "</div>";
                }

                $("#interlocutors").html(cards);
                $("#interlocutors-message").html("<div class='alert alert-info mt-0 mb-2' role='alert'><i class='fas fa-info-circle mr-2'></i>Sélectionnez les <strong>interlocuteurs</strong> que vous souhaitez joindre à votre fiche (optionnel).</div>");

                $(".interlocutor").click(function() {
                    if($(this).hasClass('active'))
                    {
                        $(this).removeClass('active');
                        $(this).find('.my-checkbox').attr('checked', false);

                    }else{
                        $(this).addClass('active');
                        $(this).find('.my-checkbox').attr('checked', true);
                    }

                    changeUpdate();
                });

                // Data Organization Show
                var address = organization.data('address');
                var phone = organization.data('phone');
                var email = organization.data('email');
                var website = organization.data('website');

                if(!address){ address = "-" };
                if(!phone){ phone = "-" };
                if(!email){ email = "-" };

                var website_text = "<a class='violet' href='" + website + "' target='_blank'><i class='uil uil-external-link-alt'></i> Voir le site web</a>";
                if(!website){ website_text = "<i class='uil uil-external-link-alt violet'></i> -" };


                
                var organization_data = "<div class='card my-card my-3 no-br'>" +
                                            "<div class='card-body'>" +
                                                    "<div class='main'>Détails de l'organisme</div>" +
                                                    "<a href='/organization/" + organization_id + "/edit' class='violet' target='_blank'>Ouvrir dans l'Annuaire</a>" +
                                                    "<div class='organization-address small my-secondary black op-70 mt-1'>" + address + "</div>" +
                                                    "<div class='organization-phone small my-secondary black  op-70 mt-2'>" +
                                                        "<i class='uil uil-phone-alt'></i>" + phone +
                                                    "</div>" +
                                                    "<div class='organization-email small my-secondary black'>" +
                                                        "<a class='mail-link' href='mailto:" + email + "'><i class='uil uil-envelope-alt'></i>" + email + "</a>" +
                                                    "</div>" +
                                                    "<div class='organization-email small my-secondary'>" + website_text + "</div>" + 
                                                    
                                            
                                            "</div>" +
                                        "</div>";

                $("#organization-message").html(organization_data);




                $('#confirm-btn, #sheet_saveDraft, #sheet_saveDraftExit').prop( "disabled", false );

                
            },
            error: function(data) {
                /* alert("Error !") */
            }


        });


    

    // Si aucun organisme sélectionné
    }else{
        
        $("#interlocutors-message").html("<div class='alert alert-info my-0' role='alert'><i class='fas fa-info-circle mr-2'></i>Sélectionnez un <strong>organisme</strong> pour afficher les interlocuteurs.</div>");
        $("#interlocutors").html("");
        $("#organization-message").html("");

        $('#confirm-btn, #sheet_saveDraft, #sheet_saveDraftExit').prop( "disabled", false );
    }

    

}



/* Quand l'organisme change, on change également les interlocuteurs */
$("select#sheet_organization").change(function() {  


    loadInterlocutors();
});

 
