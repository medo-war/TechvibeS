<?php
session_start();

$song_data = $_SESSION['song_data'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['song_data'], $_SESSION['error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['audio_file'])) {
        $uploaded_file = $_FILES['audio_file']['tmp_name'];
        $file_type = mime_content_type($uploaded_file);
        $file_name = $_FILES['audio_file']['name'];

        $data = [
            'file' => new CURLFile($uploaded_file, $file_type, $file_name),
            'return' => 'apple_music,spotify',
            'api_token' => '6ba73550edbe294def797823a3192407',
        ];

    } elseif (isset($_POST['audio_data'])) {
        $audio_data = $_POST['audio_data'];
        $audio_data = str_replace('data:audio/webm;base64,', '', $audio_data);
        $audio_data = str_replace(' ', '+', $audio_data);
        $decoded_audio = base64_decode($audio_data);

        $temp_file = tempnam(sys_get_temp_dir(), 'audio_') . '.webm';
        file_put_contents($temp_file, $decoded_audio);

        $data = [
            'file' => new CURLFile($temp_file, 'audio/webm', 'recording.webm'),
            'return' => 'apple_music,spotify',
            'api_token' => '6ba73550edbe294def797823a3192407',
        ];
    }

    if (isset($data)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.audd.io/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (isset($temp_file)) {
            unlink($temp_file);
        }

        if ($http_code === 200) {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === 'success') {
                $_SESSION['song_data'] = $result['result'];
            } else {
                $_SESSION['error'] = "Song recognition failed: " . ($result['error']['error_message'] ?? 'Unknown error');
            }
        } else {
            $_SESSION['error'] = "API request failed with HTTP code $http_code";
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Music Recognition</title>
  <script src="https://cdn.jsdelivr.net/npm/wavesurfer.js@6.6"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --neon-pink: #ff2a6d;
      --neon-blue: #05d9e8;
      --dark-blue: #01012b;
      --darker-blue: #000022;
      --light-pink: #ff7bbf;
      --menu-width: 250px;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Arial', sans-serif;
      background-color: #000;
      color: white;
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
    }
    
    /* Side Menu */
    .side-menu {
      width: var(--menu-width);
      background: rgba(1, 1, 43, 0.9);
      padding: 20px;
      position: fixed;
      height: 100vh;
      border-right: 1px solid var(--neon-blue);
      box-shadow: 0 0 20px var(--neon-blue);
      z-index: 100;
      transform: translateX(0);
      transition: transform 0.3s ease-in-out;
    }
    
    .menu-toggle {
      display: none;
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 101;
      background: var(--dark-blue);
      border: 1px solid var(--neon-blue);
      color: var(--neon-blue);
      width: 50px;
      height: 50px;
      border-radius: 50%;
      font-size: 20px;
      cursor: pointer;
      box-shadow: 0 0 10px var(--neon-blue);
      transition: all 0.3s;
    }
    
    .menu-toggle:hover {
      background: var(--neon-blue);
      color: var(--dark-blue);
    }
    
    .logo {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid rgba(5, 217, 232, 0.3);
    }
    
    .logo h1 {
      color: var(--neon-pink);
      text-shadow: 0 0 5px var(--neon-pink);
      font-size: 24px;
      margin-bottom: 5px;
    }
    
    .logo p {
      color: var(--neon-blue);
      font-size: 12px;
    }
    
    .nav-menu {
      list-style: none;
    }
    
    .nav-menu li {
      margin-bottom: 15px;
    }
    
    .nav-menu a {
      display: flex;
      align-items: center;
      color: white;
      text-decoration: none;
      padding: 10px;
      border-radius: 5px;
      transition: all 0.3s;
    }
    
    .nav-menu a:hover, .nav-menu a.active {
      background: rgba(5, 217, 232, 0.2);
      color: var(--neon-blue);
      transform: translateX(5px);
    }
    
    .nav-menu i {
      margin-right: 10px;
      font-size: 18px;
    }
    
    /* Main Content */
    .main-content {
      flex: 1;
      padding: 40px;
      margin-left: var(--menu-width);
      transition: margin-left 0.3s ease-in-out;
    }
    
    .section {
      background: rgba(1, 1, 43, 0.7);
      border-radius: 10px;
      padding: 30px;
      margin-bottom: 30px;
      border: 1px solid var(--neon-pink);
      box-shadow: 0 0 15px rgba(255, 42, 109, 0.3);
      transform: translateY(0);
      opacity: 1;
      transition: all 0.5s ease-out;
    }
    
    .section.hidden {
      transform: translateY(20px);
      opacity: 0;
      height: 0;
      padding: 0;
      margin-bottom: 0;
      overflow: hidden;
    }
    
    h2 {
      color: var(--neon-pink);
      text-shadow: 0 0 5px var(--neon-pink);
      margin-bottom: 20px;
      font-size: 22px;
      display: flex;
      align-items: center;
    }
    
    h2 i {
      margin-right: 10px;
    }
    
    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 25px;
      font-size: 16px;
      border-radius: 30px;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      border: none;
      margin: 5px;
      position: relative;
      overflow: hidden;
    }
    
    .btn i {
      margin-right: 8px;
    }
    
    .btn-primary {
      background: linear-gradient(45deg, var(--neon-pink), var(--light-pink));
      color: white;
      box-shadow: 0 0 10px var(--neon-pink);
    }
    
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 20px var(--neon-pink);
    }
    
    .btn-secondary {
      background: linear-gradient(45deg, var(--neon-blue), #00f7ff);
      color: var(--darker-blue);
      box-shadow: 0 0 10px var(--neon-blue);
    }
    
    .btn-secondary:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 20px var(--neon-blue);
    }
    
    .btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none !important;
      box-shadow: 0 0 10px rgba(255,255,255,0.1) !important;
    }
    
    .btn-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin: 15px 0;
    }
    
    /* Forms */
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: var(--neon-blue);
    }
    
    .file-input {
      display: none;
    }
    
    .file-label {
      display: inline-block;
      padding: 12px 25px;
      background: rgba(5, 217, 232, 0.2);
      border: 1px dashed var(--neon-blue);
      border-radius: 30px;
      cursor: pointer;
      transition: all 0.3s;
      text-align: center;
    }
    
    .file-label:hover {
      background: rgba(5, 217, 232, 0.3);
      border-color: var(--light-pink);
    }
    
    /* Results */
    .result-container {
      margin-top: 30px;
      padding: 30px;
      background: rgba(1, 1, 43, 0.8);
      border-radius: 10px;
      border: 1px solid var(--neon-blue);
      box-shadow: 0 0 20px var(--neon-blue);
      animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .song-info {
      margin-bottom: 20px;
    }
    
    .song-info p {
      margin-bottom: 10px;
      font-size: 16px;
    }
    
    .song-info strong {
      color: var(--neon-pink);
    }
    
    /* Waveform */
    #waveform {
      width: 100%;
      height: 120px;
      margin: 20px 0;
      background: rgba(5, 217, 232, 0.1);
      border-radius: 10px;
      cursor: pointer;
    }
    
    /* Platform Links */
    .platform-links {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }
    
    .platform-link {
      display: flex;
      align-items: center;
      padding: 10px 20px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 30px;
      color: white;
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .platform-link:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .platform-link.spotify {
      background: #1DB954;
    }
    
    .platform-link.apple {
      background: #FC3C44;
    }
    
    .platform-logo {
      height: 20px;
      margin-right: 10px;
    }
    
    /* Recording Indicator */
    .recording-indicator {
      display: inline-block;
      width: 12px;
      height: 12px;
      background-color: var(--neon-pink);
      border-radius: 50%;
      margin-right: 10px;
      box-shadow: 0 0 10px var(--neon-pink);
      animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
      0% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.2); opacity: 0.7; }
      100% { transform: scale(1); opacity: 1; }
    }
    
    .recording-status {
      display: flex;
      align-items: center;
      margin: 15px 0;
      color: var(--neon-pink);
      font-size: 14px;
    }
    
    /* Alert */
    .alert {
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      background: var(--neon-pink);
      color: white;
      box-shadow: 0 0 15px var(--neon-pink);
      animation: slideIn 0.5s ease-out;
    }
    
    @keyframes slideIn {
      from { transform: translateX(-100px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .side-menu {
        transform: translateX(-100%);
      }
      
      .side-menu.open {
        transform: translateX(0);
      }
      
      .main-content {
        margin-left: 0;
        padding: 20px;
        padding-top: 80px;
      }
      
      .menu-toggle {
        display: block;
      }
      
      .btn-group {
        flex-direction: column;
      }
      
      .platform-links {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <!-- Mobile Menu Toggle -->
  <button class="menu-toggle" id="menuToggle">
    <i class="fas fa-bars"></i>
  </button>
  
  <!-- Side Menu -->
  <nav class="side-menu" id="sideMenu">
    <div class="logo">
      <h1>MusicID</h1>
      <p>Recognize any song</p>
    </div>
    <ul class="nav-menu">
      <li><a href="#" class="active" data-section="upload"><i class="fas fa-upload"></i> Upload</a></li>
      <li><a href="#" data-section="record"><i class="fas fa-microphone"></i> Record</a></li>
      <li><a href="#" data-section="results"><i class="fas fa-music"></i> Results</a></li>
    </ul>
  </nav>
  
  <!-- Main Content -->
  <main class="main-content">
    <?php if ($error): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <!-- Upload Section -->
    <section class="section" id="uploadSection">
      <h2><i class="fas fa-upload"></i> Upload Audio File</h2>
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <input type="file" name="audio_file" id="audioFile" class="file-input" accept="audio/*" required>
          <label for="audioFile" class="file-label">
            <i class="fas fa-file-audio"></i> Choose Audio File
          </label>
          <div id="fileName" style="margin-top: 10px; color: var(--neon-blue);"></div>
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-search"></i> Recognize Song
        </button>
      </form>
    </section>
    
    <!-- Record Section -->
    <section class="section hidden" id="recordSection">
      <h2><i class="fas fa-microphone"></i> Record Audio</h2>
      <form method="POST" id="recordForm">
        <div class="btn-group">
          <button type="button" id="recordBtn" class="btn btn-primary">
            <i class="fas fa-circle"></i> Start Recording
          </button>
          <button type="button" id="stopBtn" class="btn btn-secondary" disabled>
            <i class="fas fa-stop"></i> Stop Recording
          </button>
        </div>
        <div id="recordingStatus" class="recording-status" style="display: none;">
          <span class="recording-indicator"></span>
          <span class="recording-text">Recording in progress...</span>
        </div>
        <input type="hidden" name="audio_data" id="audioData">
        <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
          <i class="fas fa-paper-plane"></i> Send Recording
        </button>
      </form>
    </section>
    
    <!-- Results Section -->
    <section class="section hidden" id="resultsSection">
      <?php if ($song_data): ?>
        <div class="result-container">
          <h2><i class="fas fa-music"></i> Song Identified</h2>
          <div class="song-info">
            <p><strong>Title:</strong> <?= htmlspecialchars($song_data['title']) ?></p>
            <p><strong>Artist:</strong> <?= htmlspecialchars($song_data['artist']) ?></p>
            <?php if (isset($song_data['album'])): ?>
              <p><strong>Album:</strong> <?= htmlspecialchars($song_data['album']) ?></p>
            <?php endif; ?>
          </div>
          
          <div id="waveform"></div>
          <div class="btn-group">
            <button id="playBtn" class="btn btn-secondary">
              <i class="fas fa-play"></i> Play Preview
            </button>
          </div>
          
          <div class="platform-links">
            <?php if (isset($song_data['spotify']['external_urls']['spotify'])): ?>
              <a href="<?= $song_data['spotify']['external_urls']['spotify'] ?>" class="platform-link spotify" target="_blank">
                <img src="https://upload.wikimedia.org/wikipedia/commons/1/19/Spotify_logo_without_text.svg" class="platform-logo" alt="Spotify">
                Listen on Spotify
              </a>
            <?php endif; ?>
            <?php if (isset($song_data['apple_music']['url'])): ?>
              <a href="<?= $song_data['apple_music']['url'] ?>" class="platform-link apple" target="_blank">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" class="platform-logo" alt="Apple Music">
                Listen on Apple Music
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <div class="result-container">
          <h2><i class="fas fa-music"></i> No Results Yet</h2>
          <p>Upload or record a song to see results here.</p>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <script>
    // Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const sideMenu = document.getElementById('sideMenu');
    
    menuToggle.addEventListener('click', () => {
      sideMenu.classList.toggle('open');
      menuToggle.innerHTML = sideMenu.classList.contains('open') ? 
        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });
    
    // Section Navigation
    const navLinks = document.querySelectorAll('.nav-menu a');
    const sections = {
      upload: document.getElementById('uploadSection'),
      record: document.getElementById('recordSection'),
      results: document.getElementById('resultsSection')
    };
    
    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Update active link
        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        
        // Show selected section
        const sectionId = link.dataset.section;
        Object.values(sections).forEach(section => {
          section.classList.add('hidden');
        });
        sections[sectionId].classList.remove('hidden');
        
        // Close menu on mobile
        if (window.innerWidth <= 768) {
          sideMenu.classList.remove('open');
          menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        }
      });
    });
    
    // File Input Display
    const fileInput = document.getElementById('audioFile');
    const fileNameDisplay = document.getElementById('fileName');
    
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length > 0) {
        fileNameDisplay.textContent = `Selected: ${fileInput.files[0].name}`;
      } else {
        fileNameDisplay.textContent = '';
      }
    });
    
    // Audio Recording
    const recordBtn = document.getElementById('recordBtn');
    const stopBtn = document.getElementById('stopBtn');
    const submitBtn = document.getElementById('submitBtn');
    const audioDataInput = document.getElementById('audioData');
    const recordingStatus = document.getElementById('recordingStatus');
    
    let mediaRecorder;
    let audioChunks = [];
    let wavesurfer;

    recordBtn.addEventListener('click', async () => {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.start();
        audioChunks = [];

        mediaRecorder.ondataavailable = e => audioChunks.push(e.data);

        mediaRecorder.onstop = async () => {
          const blob = new Blob(audioChunks, { type: 'audio/webm' });
          const reader = new FileReader();
          reader.onloadend = () => {
            audioDataInput.value = reader.result;
            submitBtn.disabled = false;
          };
          reader.readAsDataURL(blob);
          stream.getTracks().forEach(track => track.stop());
          recordingStatus.style.display = 'none';
        };

        recordBtn.disabled = true;
        stopBtn.disabled = false;
        recordingStatus.style.display = 'flex';
      } catch (err) {
        alert('Could not start recording: ' + err.message);
      }
    });

    stopBtn.addEventListener('click', () => {
      if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        recordBtn.disabled = false;
        stopBtn.disabled = true;
      }
    });
    
    // Waveform Player
    <?php if ($song_data && !empty($song_data['spotify']['preview_url'])): ?>
      document.addEventListener('DOMContentLoaded', function() {
        wavesurfer = WaveSurfer.create({
          container: '#waveform',
          waveColor: '#05d9e8',
          progressColor: '#ff2a6d',
          cursorColor: '#ff7bbf',
          barWidth: 2,
          barRadius: 3,
          cursorWidth: 1,
          height: 120,
          barGap: 2,
          responsive: true
        });
        
        wavesurfer.load('<?= htmlspecialchars($song_data['spotify']['preview_url']) ?>');
        
        const playBtn = document.getElementById('playBtn');
        playBtn.addEventListener('click', function() {
          wavesurfer.playPause();
          playBtn.innerHTML = wavesurfer.isPlaying() ? 
            '<i class="fas fa-pause"></i> Pause' : '<i class="fas fa-play"></i> Play';
        });
        
        wavesurfer.on('finish', function() {
          playBtn.innerHTML = '<i class="fas fa-play"></i> Play';
        });
      });
    <?php endif; ?>
    
    // Auto-show results if available
    <?php if ($song_data): ?>
      document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('[data-section="results"]').click();
      });
    <?php endif; ?>
  </script>
</body>
</html>