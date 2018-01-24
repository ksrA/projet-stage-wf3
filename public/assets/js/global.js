$(function(){

	var choixChat = $('#choice');
	var raisonAdoption = $('#reason');
	var errors = false;

	// Soumission du formulaire
	$('#requestCat').on('submit', function(e){
		e.preventDefault(); // On empêche l'envoi du formulaire

		// On vérifie la longueur de la valeur sélectionnée dans le select
		// Les classes .has-error et .has-success proviennent de bootstrap et doivent être appliqué sur la classe parente .form-group
		if(choixChat.val().length == 0){
			choixChat.addClass('is-invalid');
			errors = true;
		}
		else {
			choixChat.addClass('is-valid');
		}

		// On vérifie la longueur du textearea (minimum 15 caractères)
		if(raisonAdoption.val().length < 15){
			raisonAdoption.addClass('is-invalid');
			errors = true;
		}
		else {
			raisonAdoption.addClass('is-valid');
		}

		if(errors === false){
			$(this).replaceWith('<div class="alert alert-success" role="alert">Votre demande a bien été envoyée ! Nous vous répondrons dans les meilleurs délais.</div>');
		}
	});


	// On retire les classes .has-success ou .has-error dès que les champs changent
	choixChat.on('change', function(e){
		$(this).removeClass('is-valid is-invalid');
		errors = false;
	});

	raisonAdoption.on('keyup', function(e){
		$(this).removeClass('is-valid is-invalid');
		errors = false;
	});

});