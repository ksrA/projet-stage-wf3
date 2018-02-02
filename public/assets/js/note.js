$(function(){

    //Lorsque l'on select un élément du formulaire, une requete ajax se lance
    //Celle ci envoie l'id de réunion en GET(correspondant a la réunion selectionnée du formulaire)
    //a la page generate-tab
    $('#form_select').change(function(){
        var id = ($(this).val());
        $.ajax({
            method: "GET",
            url: "http://127.0.0.1:8000/panel-admin/note/generate-tab",
            data: { "id" : id },
        }).done(function(response) {

            // Affichage du tableau contenant les informations sur les étudiants ainsi que le champs input
            // de saisit des notes
            $('#tab-and-form-container').html(response);

            // Lorsqu'on clic pour soumettre les notes
            // Elles sont envoyé en POST par ajax a la page save-note
            // Qui va s'occuper de mettre a jour la bdd avec les infos envoyées
            $("#send-note").click(function(e) {
                console.log($('#note-formu').serialize());
                e.preventDefault();

                $.ajax({
                    method: "POST",
                    url: "http://127.0.0.1:8000/panel-admin/note/save-note",
                    data: $('#note-formu').serialize(),
                }).done(function(response){
                    $('#note-saved').text('Notes enregistrées en bdd').fadeIn(1000);
                    $('#note-saved').fadeOut(4000);
                });
            });

            //Si on entre une note minimale, alors une requete ajax (contenant la note entrée et l'id de réunion)
            // questionne la bdd a travers
            //la page search-candidat-by-note-ref
            //Un tableau html est retourné avec les infos des candidats ayant la note minimale requise
            $("#send-note-ref").click(function(e) {
                    console.log($('#note-ref-formu').serialize());
                    e.preventDefault();
                    $.ajax({
                        method: "POST",
                        url: "http://127.0.0.1:8000/panel-admin/note/search-candidat-by-note-ref",
                        data: {
                            noteRef : $('#note-ref').val(),
                            id : id,
                        }
                    }).done(function(response){
                        $('#note-checked').html(response).fadeIn(1000);
                    });
            });
        });
    });
});