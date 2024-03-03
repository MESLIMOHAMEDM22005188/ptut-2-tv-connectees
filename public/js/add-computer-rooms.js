jQuery(document).ready(function($) {
    $('#addRemoveComputerRoomsForm').submit(function(e) {
        e.preventDefault();

        var actionToDo = $(this).find('input[type="submit"]:focus').val();

        // Capture de la salle sélectionnée
        var roomName = actionToDo === 'Marquer' ? $('#addComputerRoom').val() : $('#removeComputerRoom').val();

        // Préparation des données à envoyer
        var formData = {
            action: 'manage_computer_rooms_ajax',
            nonce: monPluginAjax.nonce,
            roomName: roomName,
            isComputer: (actionToDo === 'Marquer') ? 1 : 0
        };

        // Requête AJAX
        $.ajax({
            url: monPluginAjax.ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                // Afficher le message de succès ou d'erreur
                if(response.success) {
                    alert(response.data.message + ' : ' + roomName);
                    // Recharger la page après 1 seconde (1000 millisecondes)
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                } else {
                    alert("Erreur: " + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                alert("Erreur AJAX : " + error);
            }
        });
    });
});
