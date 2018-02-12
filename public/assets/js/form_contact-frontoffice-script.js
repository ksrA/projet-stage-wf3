$(function () {



    /* formulaire****************************************** */
    var $lastName = $('#form_lastname');
    var $firstName = $('#form_firstname');
    var $subject = $('#form_subject');
    var $message = $('#form_message');



    // bordure rouge pour le champ Nom
    function validLastName() {

        var lastName = $lastName.val();

        if (lastName.length < 2 || lastName.length > 30) {
            $lastName.addClass('is-invalid');
        } else {
            $lastName.removeClass('is-invalid');
        }
    };
    $lastName.on('keyup', validLastName);



    // bordure rouge pour le champ Prénom
    function validFirstName() {

        var firstName = $firstName.val();

        if (firstName.length < 2 || firstName.length > 30) {
            $firstName.addClass('is-invalid');
        } else {
            $firstName.removeClass('is-invalid');
        }
    };
    $firstName.on('keyup', validFirstName);


    // bordure rouge pour le champ sujet
    function validSubject() {

        var subject = $subject.val();

        if (subject.length < 2 || subject.length > 30) {
            $subject.addClass('is-invalid');
        } else {
            $subject.removeClass('is-invalid');
        }
    };
    $subject.on('keyup', validSubject);


    // bordure rouge pour le champ message
    function validMessage() {

        var message = $message.val();

        if (message.length < 5 ) {
            $message.addClass('is-invalid');
        } else {
            $message.removeClass('is-invalid');
        }
    };
    $message.on('keyup', validMessage);
    /* ****************************************************** */



})