jQuery(document).ready(function($) {
    // Attacher l'événement click de manière plus sécurisée et maintenable
    $(document).on('click', '#view-room-details', function() {
        var roomName = $(this).data('room-name');
        fetchRoomDetails(roomName);
    });

    function toggleRoomDetails() {
        $('#roomDetails').toggle(); // Basculer l'affichage des détails de la salle
    }

    function fetchRoomDetails(roomName) {
        console.log('URL AJAX:', roomDetailsAjax.ajaxurl);
        jQuery.ajax({
            url: roomDetailsAjax.ajaxurl, // Utilisez l'URL localisée fournie par wp_localize_script
            method: 'POST',
            dataType: 'json', // S'assurer que la réponse est traitée comme du JSON
            data: {
                action: 'get_room_details', // Le nom de l'action enregistrée dans WordPress
                roomName: roomName // Le nom de la salle à récupérer
            },
            success: function(response) {
                // Vérifier la réponse avant d'afficher les détails
                if (response && !response.error) {
                    displayRoomDetails(response);
                } else {
                    console.error('Erreur lors de la récupération des détails de la salle.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX : ', error);
            }
        });
    }

    function displayRoomDetails(details) {
        var detailsHtml = '<p>' + details.name + ' : </p>' +
            '<p>Capacité : ' + details.capacity + '</p>' +
            '<p>Équipement : ' + (details.equipment || 'Non spécifié') + '</p>' +
            '<p>Câbles : ' + (details.cables || 'Non spécifié') + '</p>';

        $('#roomDetails').html(detailsHtml);

        // Appeler toggleRoomDetails pour basculer l'affichage des détails de la salle
        toggleRoomDetails();
    }
});
