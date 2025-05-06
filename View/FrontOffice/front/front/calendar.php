<?php
// Récupérer les concerts à passer en paramètre
if (!isset($concerts)) {
    die("Erreur : Les concerts doivent être fournis pour afficher le calendrier.");
}

// Préparer les données des concerts pour FullCalendar (format JSON)
$eventsForCalendar = [];
$genreColors = [
    'Rock' => '#FF0055',      // Rose néon
    'Pop' => '#00F0FF',       // Cyan néon
    'Jazz' => '#A83AFB',      // Violet néon
    'Classique' => '#FFD700', // Or
    'Hip-Hop' => '#00FFAA',   // Vert néon
    'Electro' => '#FF5500',   // Orange
    'Metal' => '#FF00AA',     // Magenta
    'Rap' => '#FFAA00',       // Jaune orangé
    'RnB' => '#FF66CC',       // Rose pâle
    'Reggae' => '#00CC00'     // Vert
];

foreach ($concerts as $concert) {
    $genre = $concert['genre'] ?? 'Inconnu';
    $color = $genreColors[$genre] ?? '#FF0055'; // Couleur par défaut si genre inconnu

    $eventsForCalendar[] = [
        'title' => htmlspecialchars($concert['nom_lieux'] . ' - ' . $genre),
        'start' => $concert['date_concert'],
        'backgroundColor' => $color, // Couleur spécifique au genre
        'borderColor' => $color,
        'extendedProps' => [
            'id' => $concert['id_concert'],
            'genre' => $genre,
            'lieu' => $concert['nom_lieux'],
            'adresse' => $concert['adresse'],
            'prix' => $concert['prix_concert'],
            'places' => $concert['place_dispo'],
            'image' => !empty($concert['image']) 
                ? '/projetwebCRUD%20-%20ranim/View/BackOffice/baccii/bacci/startbootstrap-sb-admin-gh-pages/' . htmlspecialchars($concert['image'])
                : 'assets/images/default-concert.jpg'
        ]
    ];
}
$eventsJSON = json_encode($eventsForCalendar);
?>

<!-- HTML du calendrier -->
<div class="calendar-container">
    <div id="calendar"></div>
</div>

<!-- Modal pour les détails des concerts -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Détails du Concert</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="event-image-container">
                    <img id="eventImage" src="" class="img-fluid rounded mb-3" alt="Concert Image">
                    <div class="image-overlay"></div>
                </div>
                <div class="event-details">
                    <p><strong><i class="fas fa-map-marker-alt me-2"></i>Lieu :</strong> <span id="eventLieu"></span></p>
                    <p><strong><i class="fas fa-music me-2"></i>Genre :</strong> <span id="eventGenre"></span></p>
                    <p><strong><i class="fas fa-map-pin me-2"></i>Adresse :</strong> <span id="eventAdresse"></span></p>
                    <p><strong><i class="fas fa-euro-sign me-2"></i>Prix :</strong> <span id="eventPrix"></span> €</p>
                    <p><strong><i class="fas fa-ticket-alt me-2"></i>Places disponibles :</strong> <span id="eventPlaces"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a id="eventReserveLink" href="" class="btn btn-primary">Réserver</a>
            </div>
        </div>
    </div>
</div>

<!-- CSS amélioré pour un design néon et professionnel -->
<style>
    /* Conteneur du calendrier avec animation d'entrée */
    .calendar-container {
        max-width: 1100px;
        margin: 0 auto 50px auto;
        padding: 20px;
        background: linear-gradient(135deg, rgba(255, 0, 85, 0.1), rgba(0, 240, 255, 0.1));
        border-radius: 15px;
        border: 1px solid rgba(255, 0, 85, 0.3);
        box-shadow: 0 0 30px rgba(255, 0, 85, 0.4), 0 0 60px rgba(0, 240, 255, 0.2);
        animation: glowFadeIn 1.5s ease-in-out;
    }

    @keyframes glowFadeIn {
        0% {
            opacity: 0;
            transform: scale(0.95);
            box-shadow: 0 0 10px rgba(255, 0, 85, 0.1);
        }
        100% {
            opacity: 1;
            transform: scale(1);
            box-shadow: 0 0 30px rgba(255, 0, 85, 0.4), 0 0 60px rgba(0, 240, 255, 0.2);
        }
    }

    #calendar {
        background-color: var(--dark-color);
        border-radius: 10px;
        overflow: hidden;
    }

    /* En-tête du calendrier */
    .fc-header-toolbar {
        margin-bottom: 20px !important;
        padding: 10px;
        background: linear-gradient(90deg, rgba(255, 0, 85, 0.2), rgba(0, 240, 255, 0.2));
        border-bottom: 1px solid rgba(255, 0, 85, 0.4);
    }

    .fc-toolbar-title {
        color: var(--primary-color);
        font-size: 1.8rem;
        font-weight: 700;
        text-shadow: 0 0 10px var(--primary-color), 0 0 20px var(--primary-color);
        letter-spacing: 2px;
    }

    .fc-button {
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)) !important;
        border: none !important;
        color: white !important;
        text-transform: uppercase;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 8px 15px !important;
        border-radius: 25px !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 0 10px rgba(255, 0, 85, 0.5);
    }

    .fc-button:hover {
        background: linear-gradient(45deg, #E0004D, #00C8E0) !important;
        box-shadow: 0 0 20px rgba(255, 0, 85, 0.8), 0 0 30px rgba(0, 240, 255, 0.5) !important;
        transform: scale(1.05);
    }

    /* Jours du calendrier */
    .fc-daygrid-day {
        background-color: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 0, 85, 0.2);
        transition: background-color 0.3s ease;
    }

    .fc-daygrid-day:hover {
        background-color: rgba(255, 0, 85, 0.1);
    }

    .fc-daygrid-day-number {
        color: white;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .fc-daygrid-day:hover .fc-daygrid-day-number {
        color: var(--secondary-color);
        text-shadow: 0 0 5px var(--secondary-color);
    }

    .fc-day-today {
        background: linear-gradient(135deg, rgba(255, 0, 85, 0.3), rgba(0, 240, 255, 0.3)) !important;
        border: 1px solid var(--primary-color) !important;
        box-shadow: inset 0 0 10px rgba(255, 0, 85, 0.5);
    }

    .fc-day-today .fc-daygrid-day-number {
        color: white;
        font-weight: 700;
        text-shadow: 0 0 10px var(--primary-color);
    }

    /* Événements */
    .fc-event {
        position: relative;
        border: none;
        color: white;
        font-size: 0.9rem;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .fc-event::before {
        content: '🎵';
        position: absolute;
        left: 5px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
        opacity: 0.7;
    }

    .fc-event .fc-event-main {
        padding-left: 20px;
    }

    .fc-event:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    }

    /* Tooltip personnalisé */
    .fc-event:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.8rem;
        white-space: nowrap;
        z-index: 1000;
        box-shadow: 0 0 10px rgba(255, 0, 85, 0.5);
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    /* Style du modal */
    .modal-content {
        background: linear-gradient(135deg, var(--dark-color), rgba(255, 0, 85, 0.1));
        color: white;
        border: 1px solid rgba(255, 0, 85, 0.5);
        border-radius: 15px;
        box-shadow: 0 0 30px rgba(255, 0, 85, 0.4);
        animation: modalFadeIn 0.5s ease;
    }

    @keyframes modalFadeIn {
        0% {
            opacity: 0;
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-header {
        border-bottom: 1px solid rgba(255, 0, 85, 0.3);
        background: rgba(255, 0, 85, 0.1);
    }

    .modal-title {
        color: var(--primary-color);
        text-shadow: 0 0 10px var(--primary-color);
        font-weight: 700;
        letter-spacing: 1px;
    }

    .modal-body {
        padding: 20px;
    }

    .event-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .event-image-container img {
        transition: transform 0.3s ease;
    }

    .event-image-container:hover img {
        transform: scale(1.05);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 0, 85, 0.2), rgba(0, 240, 255, 0.2));
        opacity: 0.3;
        transition: opacity 0.3s ease;
    }

    .event-image-container:hover .image-overlay {
        opacity: 0.5;
    }

    .event-details p {
        margin: 10px 0;
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .event-details i {
        color: var(--secondary-color);
        text-shadow: 0 0 5px var(--secondary-color);
    }

    .modal-footer {
        border-top: 1px solid rgba(255, 0, 85, 0.3);
        background: rgba(255, 0, 85, 0.1);
    }

    .modal .btn-secondary {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 0, 85, 0.3);
        color: white;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .modal .btn-secondary:hover {
        background-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 10px rgba(255, 0, 85, 0.5);
    }

    .modal .btn-primary {
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        border: none;
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(255, 0, 85, 0.5);
    }

    .modal .btn-primary:hover {
        background: linear-gradient(45deg, #E0004D, #00C8E0);
        box-shadow: 0 0 25px rgba(255, 0, 85, 0.8);
        transform: scale(1.05);
    }
</style>

<!-- JavaScript pour initialiser FullCalendar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: <?php echo $eventsJSON; ?>,
        eventDidMount: function(info) {
            // Ajouter un tooltip avec les détails de l'événement
            var tooltipContent = `${info.event.extendedProps.lieu} (${info.event.extendedProps.genre}) - ${info.event.extendedProps.prix}€`;
            info.el.setAttribute('data-tooltip', tooltipContent);
        },
        eventClick: function(info) {
            document.getElementById('eventModalLabel').innerText = info.event.title;
            document.getElementById('eventImage').src = info.event.extendedProps.image;
            document.getElementById('eventLieu').innerText = info.event.extendedProps.lieu;
            document.getElementById('eventGenre').innerText = info.event.extendedProps.genre;
            document.getElementById('eventAdresse').innerText = info.event.extendedProps.adresse;
            document.getElementById('eventPrix').innerText = info.event.extendedProps.prix;
            document.getElementById('eventPlaces').innerText = info.event.extendedProps.places;
            document.getElementById('eventReserveLink').href = 'reservation.php?id=' + info.event.extendedProps.id;

            var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            eventModal.show();
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        }
    });
    calendar.render();
});
</script>