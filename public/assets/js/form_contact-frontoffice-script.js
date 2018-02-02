$(function(){



    
    /* formulaire****************************************** */
    var $selectLocality = $('#inscription_add_locality');
    var $lastName = $('#inscription_add_lastname');
    var $firstName = $('#inscription_add_firstname');
    

    
    // bordur rouge pour le selecteur de chat
    function validSelectLocality() {
        
        var selectLocality = $selectLocality.val();
        
        if (selectLocality == '') {
            $selectLocality.addClass('is-invalid');
        } else {
            $selectLocality.removeClass('is-invalid');
        }
    };
    $selectLocality.on('click', validSelectLocality);



    // bordure rouge pour lastName
    function validLastName() {
        
        var lastName = $lastName.val();
        
        if (lastName.length < 2 || lastName.length > 30) {
            $lastName.addClass('is-invalid');
        } else {
            $lastName.removeClass('is-invalid');
        }
    };
    $lastName.on('keyup', validLastName);



    // bordure rouge pour firstName
    function validFirstName() {

        var firstName = $firstName.val();

        if (firstName.length < 2 || firstName.length > 30) {
            $firstName.addClass('is-invalid');
        } else {
            $firstName.removeClass('is-invalid');
        }
    };
    $firstName.on('keyup', validFirstName);
    /* ****************************************************** */



})  