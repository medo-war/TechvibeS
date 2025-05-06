const genres = ['Rock', 'Pop', 'Jazz', 'Classique', 'Hip-Hop', 'Electro', 'Metal', 'Rap', 'RnB', 'Reggae', 'all'];

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
let recognition = SpeechRecognition ? new SpeechRecognition() : null;

if (!recognition) {
    console.error('SpeechRecognition non pris en charge. Utilisez Chrome.');
} else {
    recognition.lang = 'fr-FR';
}

document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('voice-search-btn');
    if (!button) {
        console.error('Bouton #voice-search-btn introuvable.');
        return;
    }

    button.addEventListener('click', () => {
        if (!recognition) {
            alert('Recherche vocale non prise en charge. Utilisez Chrome.');
            return;
        }

        recognition.start();
        console.log('Écoute en cours...');

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript.toLowerCase();
            console.log('Reconnu :', transcript);

            let genre = 'all';
            for (let g of genres) {
                if (transcript.includes(g.toLowerCase())) {
                    genre = g;
                    break;
                }
            }

            console.log('Genre détecté :', genre);
            window.location.href = `events.php?genre=${encodeURIComponent(genre)}&sort_date=asc`;
        };

        recognition.onerror = (event) => {
            console.error('Erreur :', event.error);
            alert('Erreur : ' + (event.error === 'not-allowed' ? 'Autorisez le microphone.' : 'Problème de reconnaissance.'));
        };
    });
});