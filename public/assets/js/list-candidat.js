$(function(){

    //Lorsque l'on select un élément du formulaire, une requete ajax se lance
    //Celle ci envoie l'id de réunion (correspondant a la réunion selectionnée du formulaire)
    //a la page generate-tab
    $('#form_locality').change(function(){
        var locality = ($(this).val());
        $.ajax({
            method: "GET",
            url: "http://127.0.0.1:8000/panel-admin/select-list/create-reunion",
            data: { "locality" : locality },
        }).done(function(response) {
            $('#list-and-form-container').html(response);
        });
    });
});