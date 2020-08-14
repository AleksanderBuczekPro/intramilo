function loadInterlocutors() {

    $('#confirm-btn, #sheet_saveDraft, #sheet_saveDraftExit').prop( "disabled", true );

    $("#interlocutors-message").html("<p class='text'>Chargement des interlocuteurs en cours... </p>");

    // Si un organisme est selectionné
    var organization_id = $("select#sheet_organization").find("option:selected").val();

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

        $('#confirm-btn, #sheet_saveDraft, #sheet_saveDraftExit').prop( "disabled", false );
    }

    

}



/* Quand l'organisme change, on change également les interlocuteurs */
$("select#sheet_organization").change(function() {  


    loadInterlocutors();
});

 
