<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur d'Album IA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Styles néon/vibe musical */
        :root {
            --neon-pink: #ff2a6d;
            --neon-blue: #05d9e8;
            --neon-purple: #d300c5;
            --neon-green: #00ff9d;
            --dark-bg: #0d0221;
            --darker-bg: #05010e;
        }
        
        body {
            font-family: 'Rajdhani', 'Arial Narrow', sans-serif;
            background-color: var(--dark-bg);
            color: white;
        }
        
        .album-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(5, 1, 14, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .album-popup-content {
            background: var(--darker-bg);
            padding: 20px;
            border-radius: 15px;
            width: 80%;
            max-width: 800px;
            border: 2px solid var(--neon-purple);
            box-shadow: 0 0 20px var(--neon-purple),
                        0 0 40px rgba(211, 0, 197, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .album-popup-content::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(45deg, 
                var(--neon-pink), 
                var(--neon-blue), 
                var(--neon-purple));
            z-index: -1;
            filter: blur(20px);
            opacity: 0.3;
        }

        .close-popup {
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 28px;
            color: var(--neon-blue);
            cursor: pointer;
            transition: all 0.3s;
            text-shadow: 0 0 10px var(--neon-blue);
        }

        .close-popup:hover {
            color: var(--neon-pink);
            transform: scale(1.2);
            text-shadow: 0 0 15px var(--neon-pink);
        }
        
        h2 {
            color: var(--neon-green);
            text-shadow: 0 0 10px var(--neon-green);
            font-size: 28px;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--neon-blue);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        h3 {
            color: var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
            margin-bottom: 20px;
            font-size: 22px;
        }

        .step {
            display: none;
            padding: 20px;
            animation: fadeIn 0.5s;
        }

        .step.active {
            display: block;
        }

        textarea {
            width: 100%;
            min-height: 120px;
            padding: 15px;
            border: 1px solid var(--neon-blue);
            border-radius: 8px;
            background: rgba(13, 2, 33, 0.7);
            color: white;
            margin-bottom: 20px;
            font-size: 16px;
            transition: all 0.3s;
            box-shadow: 0 0 10px rgba(5, 217, 232, 0.3);
        }
        
        textarea:focus {
            outline: none;
            border-color: var(--neon-pink);
            box-shadow: 0 0 15px var(--neon-pink);
        }

        .upload-zone {
            display: flex;
            gap: 20px;
            margin: 25px 0;
            flex-wrap: wrap;
        }

        .upload-btn {
            background: rgba(5, 217, 232, 0.1);
            border: 2px dashed var(--neon-blue);
            padding: 25px;
            text-align: center;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s;
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: var(--neon-blue);
        }
        
        .upload-btn i {
            font-size: 30px;
            color: var(--neon-blue);
            text-shadow: 0 0 10px var(--neon-blue);
        }

        .upload-btn:hover {
            background: rgba(5, 217, 232, 0.2);
            border-color: var(--neon-green);
            transform: translateY(-3px);
            box-shadow: 0 0 20px rgba(0, 255, 157, 0.3);
        }
        
        .upload-btn:hover i {
            color: var(--neon-green);
            text-shadow: 0 0 15px var(--neon-green);
        }

        .previews {
            margin: 25px 0;
            display: flex;
            gap: 25px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .step-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .generate-btn, .download-btn {
            background: linear-gradient(45deg, var(--neon-pink), var(--neon-purple));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 0 15px rgba(255, 42, 109, 0.5);
        }
        
        .download-btn {
            background: linear-gradient(45deg, var(--neon-green), var(--neon-blue));
            box-shadow: 0 0 15px rgba(0, 255, 157, 0.5);
            margin: 0 auto;
            display: block;
        }

        .generate-btn:hover, .download-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 0 25px rgba(255, 42, 109, 0.8);
        }
        
        .download-btn:hover {
            box-shadow: 0 0 25px rgba(0, 255, 157, 0.8);
        }

        #albumCanvas {
            max-width: 100%;
            border: 3px solid var(--neon-purple);
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(211, 0, 197, 0.6);
            margin: 20px auto;
            display: block;
        }

        #musicPreview {
            width: 100%;
            max-width: 300px;
            background: rgba(5, 217, 232, 0.1);
            border-radius: 10px;
            border: 1px solid var(--neon-blue);
        }
        
        #musicPreview::-webkit-media-controls-panel {
            background: rgba(5, 217, 232, 0.2);
            border-radius: 8px;
        }

        #imagePreview {
            max-height: 250px;
            max-width: 100%;
            border: 2px solid var(--neon-pink);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255, 42, 109, 0.4);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 10px var(--neon-blue); }
            50% { box-shadow: 0 0 20px var(--neon-pink); }
            100% { box-shadow: 0 0 10px var(--neon-blue); }
        }

        .nav-btn {
            background: transparent;
            color: var(--neon-blue);
            border: 2px solid var(--neon-blue);
            padding: 10px 25px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            letter-spacing: 1px;
            text-shadow: 0 0 5px var(--neon-blue);
            box-shadow: 0 0 10px rgba(5, 217, 232, 0.3);
        }
        
        .nav-btn i {
            transition: all 0.3s;
        }

        .nav-btn:hover {
            background: rgba(5, 217, 232, 0.2);
            color: var(--neon-green);
            border-color: var(--neon-green);
            text-shadow: 0 0 10px var(--neon-green);
            box-shadow: 0 0 20px rgba(0, 255, 157, 0.3);
        }
        
        .nav-btn:hover i {
            color: var(--neon-green);
        }

        .nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            box-shadow: none;
        }

        .loading-spinner {
            display: none;
            margin: 40px auto;
            border: 5px solid rgba(5, 217, 232, 0.1);
            border-top: 5px solid var(--neon-green);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            box-shadow: 0 0 20px var(--neon-green);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Effets néon supplémentaires */
        .neon-text {
            text-shadow: 0 0 5px currentColor;
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        /* Style pour les tooltips */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }
        
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--darker-bg);
            border: 1px solid var(--neon-blue);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            white-space: nowrap;
            z-index: 100;
            box-shadow: 0 0 10px rgba(5, 217, 232, 0.5);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div id="aiAlbumPopup" class="album-popup">
    <div class="album-popup-content pulse-animation">
        <span class="close-popup">&times;</span>
        <h2><i class="fas fa-robot"></i> GÉNÉRATEUR IA D'ALBUM</h2>

        <!-- Étape 1 : Description -->
        <div class="step active">
            <h3>DÉCRIVEZ VOTRE ALBUM</h3>
            <textarea id="albumDescription" placeholder="Ex: 'Pochette futuriste avec des néons pour mon single de hip-hop...'"></textarea>
        </div>

        <!-- Étape 2 : Upload -->
        <div class="step">
            <h3>PERSONNALISATION</h3>
            <div class="upload-zone">
                <label for="musicUpload" class="upload-btn" data-tooltip="Formats supportés: MP3, WAV, OGG">
                    <i class="fas fa-music"></i> IMPORTER UNE MUSIQUE
                    <input type="file" id="musicUpload" accept="audio/*" hidden>
                </label>
                <label for="imageUpload" class="upload-btn" data-tooltip="Formats supportés: JPG, PNG, WEBP">
                    <i class="fas fa-image"></i> IMPORTER UNE IMAGE
                    <input type="file" id="imageUpload" accept="image/*" hidden>
                </label>
            </div>
            <div class="previews">
                <audio id="musicPreview" controls style="display:none;"></audio>
                <img id="imagePreview" style="display:none;">
            </div>
        </div>

        <!-- Étape 3 : Génération -->
        <div class="step">
            <h3>RÉSULTAT</h3>
            <div id="aiResult">
                <div class="loading-spinner" id="loadingSpinner"></div>
                <canvas id="albumCanvas" width="1000" height="1000"></canvas>
            </div>
            <button id="downloadAlbumBtn" class="download-btn">
                <i class="fas fa-download"></i> TÉLÉCHARGER LA POCHETTE
            </button>
        </div>

        <!-- Navigation -->
        <div class="step-nav">
            <button id="prevStepBtn" class="nav-btn" disabled><i class="fas fa-arrow-left"></i> PRÉCÉDENT</button>
            <button id="nextStepBtn" class="nav-btn">SUIVANT <i class="fas fa-arrow-right"></i></button>
            <button id="generateBtn" class="generate-btn" style="display:none;">
                <i class="fas fa-magic"></i> GÉNÉRER AVEC IA
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration - Utilisation de l'API Stable Diffusion
    const STABILITY_API_KEY = "sk-hxCxx6rgLle16hJGt8eWon6tZXBpWL0y29GG06vCxQ5FV6G6"; // Obtenez-la sur platform.stability.ai
    
    // Éléments DOM
    const generateBtn = document.getElementById('generateBtn');
    const descriptionInput = document.getElementById('albumDescription');
    const canvas = document.getElementById('albumCanvas');
    const ctx = canvas.getContext('2d');
    const musicUpload = document.getElementById('musicUpload');
    const imageUpload = document.getElementById('imageUpload');
    const musicPreview = document.getElementById('musicPreview');
    const imagePreview = document.getElementById('imagePreview');
    const nextBtn = document.getElementById('nextStepBtn');
    const prevBtn = document.getElementById('prevStepBtn');
    const downloadBtn = document.getElementById('downloadAlbumBtn');
    const closePopup = document.querySelector('.close-popup');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const popupContent = document.querySelector('.album-popup-content');
    
    // Navigation entre étapes
    const steps = document.querySelectorAll('.step');
    let currentStep = 0;
    let generatedImageUrl = null;
    
    // Initialisation
    updateButtons();

    // Animation aléatoire du fond
    setInterval(() => {
        const colors = ['var(--neon-pink)', 'var(--neon-blue)', 'var(--neon-purple)', 'var(--neon-green)'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        popupContent.style.boxShadow = `0 0 20px ${randomColor}, 0 0 40px rgba(211, 0, 197, 0.3)`;
    }, 3000);

    // Fermeture du popup
    closePopup.addEventListener('click', function() {
        document.getElementById('aiAlbumPopup').style.display = 'none';
    });

    // Gestion de la navigation
    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            goToStep(currentStep + 1);
        }
    });

    prevBtn.addEventListener('click', function() {
        goToStep(currentStep - 1);
    });

    // Gestion de l'upload de musique
    musicUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.match('audio.*')) {
                alert("Veuillez sélectionner un fichier audio valide (MP3, WAV, etc.)");
                return;
            }
            
            const audioURL = URL.createObjectURL(file);
            musicPreview.src = audioURL;
            musicPreview.style.display = 'block';
            checkUploads();
        }
    });

    // Gestion de l'upload d'image
    imageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.match('image.*')) {
                alert("Veuillez sélectionner une image valide (JPG, PNG, etc.)");
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(event) {
                imagePreview.src = event.target.result;
                imagePreview.style.display = 'block';
                checkUploads();
            };
            reader.readAsDataURL(file);
        }
    });

    // Génération avec Stability AI API
    generateBtn.addEventListener('click', async function() {
        if (!descriptionInput.value.trim()) {
            alert("Veuillez décrire votre album !");
            return;
        }

        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GÉNÉRATION EN COURS...';
        loadingSpinner.style.display = 'block';
        canvas.style.display = 'none';

        try {
            const prompt = `Professional album cover: ${descriptionInput.value}, digital art, high quality, 4K, neon colors, cyberpunk style, vibrant, music theme`;
            const imageUrl = await generateWithStabilityAI(prompt);
            generatedImageUrl = imageUrl;
            
            const img = new Image();
            img.crossOrigin = "Anonymous";
            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
                
                // Ajouter le titre si musique uploadée
                if (musicUpload.files[0]) {
                    addMusicTitle(musicUpload.files[0].name.replace(/\.[^/.]+$/, ""));
                }
                
                // Ajouter un effet néon au canvas
                addNeonEffect();
                
                downloadBtn.disabled = false;
                loadingSpinner.style.display = 'none';
                canvas.style.display = 'block';
            };
            img.onerror = function() {
                throw new Error("Échec du chargement de l'image générée");
            };
            img.src = imageUrl;

        } catch (error) {
            console.error("Erreur:", error);
            alert(`Échec de génération: ${error.message}`);
            loadingSpinner.style.display = 'none';
        } finally {
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="fas fa-magic"></i> GÉNÉRER AVEC IA';
        }
    });

    // Téléchargement de l'image
    downloadBtn.addEventListener('click', function() {
        if (!canvas || canvas.width === 0) {
            alert("Aucune image à télécharger !");
            return;
        }
        
        const link = document.createElement('a');
        link.download = `pochette-album-${Date.now()}.png`;
        canvas.toBlob(function(blob) {
            link.href = URL.createObjectURL(blob);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, 'image/png');
    });

    // Fonction pour générer avec Stability AI
    async function generateWithStabilityAI(prompt) {
        // Vérification supplémentaire
        if(!prompt || prompt.length < 10) {
            throw new Error("Le texte descriptif est trop court");
        }

        const engineId = "stable-diffusion-xl-1024-v1-0";
        const apiHost = 'https://api.stability.ai';
        
        try {
            const response = await fetch(
                `${apiHost}/v1/generation/${engineId}/text-to-image`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${STABILITY_API_KEY}`
                    },
                    body: JSON.stringify({
                        text_prompts: [
                            {
                                text: prompt,
                                weight: 1
                            }
                        ],
                        cfg_scale: 7,
                        height: 1024,
                        width: 1024,
                        steps: 30,
                        samples: 1,
                    }),
                }
            );

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || "Échec de la requête API");
            }

            const responseJSON = await response.json();
            
            // L'API Stability AI retourne les images en base64
            const base64Image = responseJSON.artifacts[0].base64;
            return `data:image/png;base64,${base64Image}`;
            
        } catch (error) {
            console.error('Error:', error);
            throw new Error(`Failed to generate image: ${error.message}`);
        }
    }

    function addMusicTitle(title) {
        ctx.fillStyle = "rgba(13, 2, 33, 0.8)";
        ctx.fillRect(0, canvas.height - 70, canvas.width, 70);
        
        // Texte avec effet néon
        ctx.font = "bold 32px 'Rajdhani', sans-serif";
        ctx.textAlign = "center";
        
        // Ombre portée pour effet néon
        ctx.shadowColor = '#05d9e8';
        ctx.shadowBlur = 15;
        ctx.fillStyle = "white";
        ctx.fillText(title.toUpperCase(), canvas.width/2, canvas.height - 25);
        
        // Réinitialiser l'ombre
        ctx.shadowBlur = 0;
    }
    
    function addNeonEffect() {
        // Ajoute un effet de glow néon autour des bords
        ctx.globalCompositeOperation = 'lighter';
        ctx.shadowColor = '#ff2a6d';
        ctx.shadowBlur = 30;
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.1)';
        ctx.lineWidth = 20;
        ctx.strokeRect(0, 0, canvas.width, canvas.height);
        
        // Réinitialiser les paramètres
        ctx.globalCompositeOperation = 'source-over';
        ctx.shadowBlur = 0;
    }

    function goToStep(step) {
        steps[currentStep].classList.remove('active');
        currentStep = step;
        steps[currentStep].classList.add('active');
        updateButtons();
    }

    function validateStep(step) {
        if (step === 0 && !descriptionInput.value.trim()) {
            alert("Veuillez décrire votre album avant de continuer");
            return false;
        }
        
        // Rend l'upload optionnel pour plus de flexibilité
        return true;
    }

    function checkUploads() {
        // Rend l'upload optionnel pour plus de flexibilité
        nextBtn.disabled = false;
    }

    function updateButtons() {
        prevBtn.disabled = currentStep === 0;
        
        if (currentStep === steps.length - 1) {
            nextBtn.style.display = 'none';
            generateBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            generateBtn.style.display = 'none';
        }
        
        downloadBtn.disabled = !generatedImageUrl;
    }
});

// Activez le mode debug
console.log("Script chargé, prêt à générer");
window.debugMode = true; 
</script>
</body>
</html>