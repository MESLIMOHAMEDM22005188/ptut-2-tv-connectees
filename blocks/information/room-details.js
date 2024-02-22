// Fonction pour récupérer les détails de la salle et les afficher
function fetchRoomDetails(roomName, event) {
    // Empêche l'événement de se propager au parent
    if (event) {
        event.stopPropagation();
    }
    jQuery.ajax({
        url: 'https://projettv.alwaysdata.net/secretary/computer-rooms/',
        method: 'GET',
        data: { roomName: roomName }, // Envoyer le nom de la salle comme paramètre
        success: function(response) {
            // Une fois les détails récupérés avec succès, les afficher à l'utilisateur
            displayRoomDetails(response);
        },
        error: function(xhr, status, error) {
            // En cas d'erreur lors de la récupération des détails, afficher un message d'erreur à l'utilisateur
            console.error(error);
        }
    });
}

// Fonction pour afficher les détails de la salle
function displayRoomDetails(details) {
    jQuery('#roomDetails').html('<p>' + details.name + '</p><p>' + '</p><p>' + details.location + '</p>');
    //console.log(details);
}
