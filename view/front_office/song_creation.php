<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/Config.php';

// Handle AJAX request for executing YuE music generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_track') {
    // Get the form data
    $genre = isset($_POST['genre']) ? $_POST['genre'] : '';
    $mood = isset($_POST['mood']) ? $_POST['mood'] : '';
    $lyrics = isset($_POST['lyrics']) ? $_POST['lyrics'] : '';
    
    // Create temporary files for genre and lyrics
    $tempGenreFile = sys_get_temp_dir() . '/genre_' . uniqid() . '.txt';
    $tempLyricsFile = sys_get_temp_dir() . '/lyrics_' . uniqid() . '.txt';
    
    // Write genre and mood to genre file
    file_put_contents($tempGenreFile, $genre . ', ' . $mood);
    
    // Write lyrics to lyrics file
    file_put_contents($tempLyricsFile, $lyrics);
    
    // Prepare the command
    $command = 'conda activate yue && ';
    $command .= 'cd C:\\Users\\Ahmed\\YuE-exllamav2 && ';
    $command .= 'python src/yue/infer.py --stage1_use_exl2 --stage2_use_exl2 --stage2_cache_size 32768 ';
    $command .= '--stage1_model C:\\Users\\Ahmed\\YuE-exllamav2\\YuE-s1-7B-anneal-en-cot-exl2 ';
    $command .= '--stage2_model C:\\Users\\Ahmed\\YuE-exllamav2\\YuE-s2-1B-general-exl2 ';
    $command .= '--genre_txt ' . escapeshellarg($tempGenreFile) . ' ';
    $command .= '--lyrics_txt ' . escapeshellarg($tempLyricsFile) . ' ';
    $command .= '--output_dir C:\\Users\\Ahmed\\YuE-exllamav2\\output ';
    $command .= '--keep_intermediate';
    
    // Execute command
    $output = [];
    $return_var = 0;
    
    // Log the command for debugging
    error_log('Executing YuE command: ' . $command);
    
    // Execute the command
    exec($command, $output, $return_var);
    
    // Get the latest generated file from output directory
    $outputDir = 'C:\\Users\\Ahmed\\YuE-exllamav2\\output';
    $latestFile = null;
    $latestTime = 0;
    
    if (is_dir($outputDir)) {
        $files = scandir($outputDir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'wav') {
                $filePath = $outputDir . '\\' . $file;
                $fileTime = filemtime($filePath);
                if ($fileTime > $latestTime) {
                    $latestTime = $fileTime;
                    $latestFile = $file;
                }
            }
        }
    }
    
    // Clean up temporary files
    @unlink($tempGenreFile);
    @unlink($tempLyricsFile);
    
    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => ($return_var === 0),
        'output' => $output,
        'command_status' => $return_var,
        'generated_file' => $latestFile ? $outputDir . '\\' . $latestFile : null
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEON AI Music Player</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-liberty-market.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --bg-color: #0a0a12;
            --card-color: #12121a;
            --text-primary: #ffffff;
            --text-secondary: #b8b8ff;
            --accent-pink: #ff2a6d;
            --accent-blue: #05d9e8;
            --vinyl-color: #1a1a2e;
            --vinyl-label: #0d0221;
            --neon-glow: 0 0 10px rgba(5, 217, 232, 0.8), 
                          0 0 20px rgba(255, 42, 109, 0.6);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            background: radial-gradient(circle at center, #1a1a2e 0%, #0a0a12 100%);
        }
        
        /* Particles container styling */
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        
        /* Container for centering the music player */
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 100px 20px;
            box-sizing: border-box;
        }

        .music-player {
            width: 90%;
            max-width: 800px;
            background-color: var(--card-color);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--neon-glow);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            border: 1px solid rgba(255, 42, 109, 0.3);
        }

        .player-header {
            grid-column: 1 / -1;
            text-align: center;
            margin-bottom: 10px;
        }

        .player-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.8rem;
            background: linear-gradient(45deg, var(--accent-pink), var(--accent-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 5px;
            text-shadow: 0 0 5px rgba(255, 42, 109, 0.5),
                         0 0 10px rgba(5, 217, 232, 0.5);
            letter-spacing: 2px;
        }

        .player-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-shadow: 0 0 5px rgba(184, 184, 255, 0.3);
        }

        /* Vinyl Player Section */
        .vinyl-container {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 350px;
        }

        .vinyl {
            width: 300px;
            height: 300px;
            background: radial-gradient(
                circle,
                var(--vinyl-label) 0%,
                var(--vinyl-label) 30%,
                var(--vinyl-color) 30%,
                var(--vinyl-color) 100%
            );
            border-radius: 50%;
            position: relative;
            box-shadow: 0 0 20px rgba(255, 42, 109, 0.5),
                        inset 0 0 20px rgba(5, 217, 232, 0.3);
            animation: spin 5s linear infinite;
            animation-play-state: paused;
            transition: transform 0.3s;
            border: 2px solid var(--accent-blue);
        }

        .vinyl.active {
            animation-play-state: running;
            box-shadow: 0 0 30px rgba(255, 42, 109, 0.7),
                        inset 0 0 30px rgba(5, 217, 232, 0.5);
        }

        .vinyl::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, var(--accent-pink), var(--accent-blue));
            border-radius: 50%;
            border: 3px solid var(--vinyl-color);
            z-index: 2;
            box-shadow: 0 0 10px rgba(255, 42, 109, 0.7);
        }

        .vinyl::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            background-color: var(--vinyl-color);
            border-radius: 50%;
            border: 2px solid var(--accent-blue);
            box-shadow: inset 0 0 10px rgba(255, 42, 109, 0.5);
        }

        .vinyl-hole {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 15px;
            height: 15px;
            background-color: var(--vinyl-color);
            border-radius: 50%;
            z-index: 3;
            border: 1px solid var(--accent-pink);
        }

        .tonearm {
            position: absolute;
            top: 0;
            right: 30px;
            width: 120px;
            height: 120px;
            transform-origin: right top;
            transform: rotate(-30deg);
            transition: transform 1s ease-in-out;
            z-index: 1;
        }

        .tonearm.active {
            transform: rotate(10deg);
        }

        .tonearm::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 8px;
            background: linear-gradient(90deg, var(--accent-pink), var(--accent-blue));
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(255, 42, 109, 0.7);
        }

        .tonearm::after {
            content: '';
            position: absolute;
            top: 8px;
            right: -10px;
            width: 20px;
            height: 20px;
            background-color: var(--accent-blue);
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(5, 217, 232, 0.8);
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Song Info Section */
        .song-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .now-playing {
            margin-bottom: 30px;
        }

        .now-playing h2 {
            font-size: 1.2rem;
            color: var(--accent-blue);
            margin-bottom: 10px;
            text-shadow: 0 0 5px rgba(5, 217, 232, 0.5);
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
        }

        .current-song {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
            background: linear-gradient(45deg, var(--accent-pink), var(--accent-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 5px rgba(255, 42, 109, 0.3);
        }

        .current-artist {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 20px;
            text-shadow: 0 0 3px rgba(184, 184, 255, 0.3);
        }

        .song-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .control-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--accent-blue);
            color: var(--text-primary);
            font-size: 1.2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 0 5px rgba(5, 217, 232, 0.3);
        }

        .control-btn:hover {
            background-color: rgba(255, 42, 109, 0.2);
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(255, 42, 109, 0.5);
            border-color: var(--accent-pink);
        }

        .control-btn.play {
            width: 70px;
            height: 70px;
            background: linear-gradient(45deg, var(--accent-pink), var(--accent-blue));
            font-size: 1.5rem;
            box-shadow: 0 0 15px rgba(255, 42, 109, 0.7);
        }

        .control-btn.play:hover {
            transform: scale(1.08);
            box-shadow: 0 0 20px rgba(255, 42, 109, 0.9),
                        0 0 30px rgba(5, 217, 232, 0.5);
        }

        .progress-container {
            margin-bottom: 30px;
        }

        .progress-bar {
            height: 4px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 2px;
            margin-bottom: 5px;
            overflow: hidden;
            box-shadow: inset 0 0 3px rgba(255, 42, 109, 0.3);
        }

        .progress {
            height: 100%;
            width: 30%;
            background: linear-gradient(90deg, var(--accent-pink), var(--accent-blue));
            border-radius: 2px;
            transition: width 0.1s linear;
            box-shadow: 0 0 5px rgba(255, 42, 109, 0.7);
        }

        .time-info {
            display: flex;
            justify-content: space-between;
            color: var(--text-secondary);
            font-size: 0.8rem;
        }

        /* Song Generator Form */
        .song-generator {
            grid-column: 1 / -1;
            background-color: rgba(5, 217, 232, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 1px solid rgba(255, 42, 109, 0.2);
            box-shadow: inset 0 0 10px rgba(5, 217, 232, 0.1);
        }

        .song-generator h3 {
            margin-bottom: 15px;
            font-size: 1.2rem;
            color: var(--accent-blue);
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-secondary);
            text-shadow: 0 0 3px rgba(184, 184, 255, 0.2);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            background-color: rgba(5, 217, 232, 0.1);
            border: 1px solid var(--accent-blue);
            border-radius: 5px;
            color: var(--text-primary);
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-pink);
            box-shadow: 0 0 8px rgba(255, 42, 109, 0.5);
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .generate-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, var(--accent-pink), var(--accent-blue));
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            text-shadow: 0 0 5px rgba(0,0,0,0.5);
            box-shadow: 0 0 10px rgba(255, 42, 109, 0.5);
        }

        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 42, 109, 0.7),
                         0 5px 30px rgba(5, 217, 232, 0.5);
        }

        /* Playlist */
        .playlist {
            grid-column: 1 / -1;
            margin-top: 20px;
        }

        .playlist h3 {
            margin-bottom: 15px;
            font-size: 1.2rem;
            color: var(--accent-blue);
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
        }

        .playlist-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background-color: rgba(255, 42, 109, 0.05);
            border-radius: 5px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 2px solid transparent;
        }

        .playlist-item:hover {
            background-color: rgba(255, 42, 109, 0.1);
            transform: translateX(5px);
        }

        .playlist-item.active {
            background-color: rgba(255, 42, 109, 0.15);
            border-left: 3px solid var(--accent-blue);
            box-shadow: inset 5px 0 10px rgba(5, 217, 232, 0.2);
        }

        .playlist-info {
            flex: 1;
        }

        .playlist-title {
            font-weight: 600;
            margin-bottom: 3px;
            color: var(--text-primary);
        }

        .playlist-artist {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .playlist-duration {
            color: var(--accent-blue);
            font-size: 0.9rem;
            font-family: 'Orbitron', sans-serif;
        }

        /* Loading Animation */
        .loading {
            display: none;
            text-align: center;
            padding: 15px;
            color: var(--accent-blue);
            font-style: italic;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
        }

        .loading.active {
            display: block;
            animation: pulse 1.5s infinite, neonGlow 2s infinite alternate;
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        @keyframes neonGlow {
            from { text-shadow: 0 0 5px var(--accent-blue); }
            to { text-shadow: 0 0 10px var(--accent-pink), 
                             0 0 20px var(--accent-blue); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .music-player {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .vinyl-container {
                height: 250px;
                margin-bottom: 20px;
            }

            .vinyl {
                width: 200px;
                height: 200px;
            }

            .player-header h1 {
                font-size: 2rem;
            }
        }

        /* Vinyl record grooves */
        .vinyl-grooves {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: repeating-radial-gradient(
                circle,
                transparent,
                transparent 3px,
                rgba(255, 42, 109, 0.1) 3px,
                rgba(255, 42, 109, 0.1) 4px
            );
        }
    </style>
</head>
<body>
    <!-- ***** Preloader Start ***** -->
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- ***** Preloader End ***** -->
    
    <!-- Particles.js Container -->
    <div id="particles-js"></div>
    
    <!-- Include the navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Center container for the music player -->
    <div class="center-container">
        <div class="music-player">
        <div class="player-header">
            <h1>NEON AI MUSIC</h1>
            <p>Generate cyberpunk beats with AI</p>
        </div>

        <!-- Vinyl Player -->
        <div class="vinyl-container">
            <div class="tonearm"></div>
            <div class="vinyl">
                <div class="vinyl-grooves"></div>
                <div class="vinyl-hole"></div>
            </div>
        </div>

        <!-- Song Info -->
        <div class="song-info">
            <div class="now-playing">
                <h2>NOW PLAYING</h2>
                <div class="current-song">Neon Dreams</div>
                <div class="current-artist">Synthwave AI</div>
            </div>

            <div class="song-controls">
                <button class="control-btn">⏮</button>
                <button class="control-btn play">▶</button>
                <button class="control-btn">⏭</button>
            </div>

            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress"></div>
                </div>
                <div class="time-info">
                    <span>1:23</span>
                    <span>3:45</span>
                </div>
            </div>
        </div>

        <!-- Song Generator -->
        <div class="song-generator">
            <h3>Generate Your Own Track</h3>
            <p>Create custom AI-generated music by selecting a genre, mood, and adding your own lyrics.</p>
            <div class="generator-form">
                <div class="form-group">
                    <label for="genre-select">Genre</label>
                    <select id="genre-select" class="form-control">
                        <option value="synthwave">Synthwave</option>
                        <option value="cyberpunk">Cyberpunk</option>
                        <option value="vaporwave">Vaporwave</option>
                        <option value="darksynth">Darksynth</option>
                        <option value="retrowave">Retrowave</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mood-select">Mood</label>
                    <select id="mood-select" class="form-control">
                        <option value="energetic">Energetic</option>
                        <option value="melancholic">Melancholic</option>
                        <option value="dreamy">Dreamy</option>
                        <option value="intense">Intense</option>
                        <option value="relaxed">Relaxed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="lyrics-input">Lyrics/Theme (optional)</label>
                    <textarea id="lyrics-input" class="form-control" rows="3" placeholder="Enter some lyrics or theme ideas..."></textarea>
                </div>
                <button id="generate-btn" class="btn btn-generate">Generate Track</button>
                <div id="generation-status" class="mt-2"></div>
            </div>
        </div>

        <!-- Playlist -->
        <div class="playlist">
            <h3>YuE OUTPUT TRACKS</h3>
            <div id="playlist-container">
                <?php
                // First, copy music files from YuE output to web-accessible directory
                $outputDir = 'C:\\Users\\Ahmed\\YuE-exllamav2\\output';
                $webDir = $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/uploads/music';
                $tracks = [];
                
                // Ensure web directory exists
                if (!is_dir($webDir)) {
                    mkdir($webDir, 0755, true);
                }
                
                // Copy files from output directory to web directory
                if (is_dir($outputDir)) {
                    $files = scandir($outputDir);
                    $activeSet = false;
                    
                    foreach ($files as $file) {
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'wav' || 
                            pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                            
                            $sourceFilePath = $outputDir . '\\' . $file;
                            $destFilePath = $webDir . '/' . $file;
                            $webFilePath = '/livethemusic/uploads/music/' . $file;
                            $fileName = pathinfo($file, PATHINFO_FILENAME);
                            
                            // Copy file if it doesn't exist or is newer
                            if (!file_exists($destFilePath) || filemtime($sourceFilePath) > filemtime($destFilePath)) {
                                copy($sourceFilePath, $destFilePath);
                            }
                            
                            // Generate artist and title from filename
                            $parts = explode('_', $fileName);
                            $genre = isset($parts[0]) ? ucfirst($parts[0]) : 'YuE';
                            $title = isset($parts[1]) ? ucfirst($parts[1]) : 'AI Track ' . rand(100, 999);
                            
                            // Get file creation time
                            $creationTime = filemtime($sourceFilePath);
                            
                            $tracks[] = [
                                'file' => $file,
                                'path' => $webFilePath, // Use web path instead of file path
                                'title' => $title,
                                'artist' => $genre . ' AI',
                                'time' => $creationTime
                            ];
                        }
                    }
                    
                    // Sort tracks by creation time (newest first)
                    usort($tracks, function($a, $b) {
                        return $b['time'] - $a['time'];
                    });
                    
                    // Display tracks
                    foreach ($tracks as $index => $track) {
                        $activeClass = ($index === 0 && !$activeSet) ? 'active' : '';
                        if ($index === 0 && !$activeSet) $activeSet = true;
                        
                        echo '<div class="playlist-item ' . $activeClass . '" data-file="' . htmlspecialchars($track['path']) . '">';
                        echo '    <div class="playlist-info">';
                        echo '        <div class="playlist-title">' . htmlspecialchars($track['title']) . '</div>';
                        echo '        <div class="playlist-artist">' . htmlspecialchars($track['artist']) . '</div>';
                        echo '    </div>';
                        echo '    <div class="playlist-duration">3:30</div>';
                        echo '</div>';
                    }
                }
                
                // If no tracks found, show placeholder
                if (empty($tracks)) {
                    echo '<div class="playlist-item active">';
                    echo '    <div class="playlist-info">';
                    echo '        <div class="playlist-title">No Tracks Found</div>';
                    echo '        <div class="playlist-artist">YuE AI</div>';
                    echo '    </div>';
                    echo '    <div class="playlist-duration">0:00</div>';
                    echo '</div>';
                }
                ?>
                <!-- Audio element for playback -->
                <audio id="audio-player" style="display: none;"></audio>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const vinyl = document.querySelector('.vinyl');
            const tonearm = document.querySelector('.tonearm');
            const playBtn = document.querySelector('.control-btn.play');
            const playlistContainer = document.getElementById('playlist-container');
            const currentSong = document.querySelector('.current-song');
            const currentArtist = document.querySelector('.current-artist');
            const progress = document.querySelector('.progress');
            const timeCurrent = document.querySelector('.time-info span:first-child');
            const generateBtn = document.getElementById('generate-btn');
            
            // Get audio player element
            const audioPlayer = document.getElementById('audio-player');
            
            // Player state
            let isPlaying = false;
            let currentTime = 0; 
            let duration = 210; // Default 3:30 in seconds
            let progressInterval;
            
            // Initialize with the first track if available
            const initializePlayer = () => {
                const activeItem = document.querySelector('.playlist-item.active');
                if (activeItem) {
                    const title = activeItem.querySelector('.playlist-title').textContent;
                    const artist = activeItem.querySelector('.playlist-artist').textContent;
                    const audioFile = activeItem.getAttribute('data-file');
                    
                    currentSong.textContent = title;
                    currentArtist.textContent = artist;
                    
                    if (audioFile) {
                        audioPlayer.src = audioFile;
                        audioPlayer.load();
                    }
                }
            };
            
            // Call initialization
            initializePlayer();
            
            // Play/Pause functionality
            playBtn.addEventListener('click', function() {
                if (!audioPlayer.src) {
                    showNotification('No track selected', 'error');
                    return;
                }
                
                isPlaying = !isPlaying;
                
                if (isPlaying) {
                    this.textContent = '⏸';
                    vinyl.classList.add('active');
                    tonearm.classList.add('active');
                    audioPlayer.play();
                    startProgress();
                    
                    // Add pulsing glow effect
                    vinyl.style.boxShadow = '0 0 30px rgba(255, 42, 109, 0.7), ' +
                                           'inset 0 0 30px rgba(5, 217, 232, 0.5), ' +
                                           '0 0 40px rgba(255, 42, 109, 0.5)';
                } else {
                    this.textContent = '▶';
                    vinyl.classList.remove('active');
                    tonearm.classList.remove('active');
                    audioPlayer.pause();
                    clearInterval(progressInterval);
                    
                    // Reset glow effect
                    vinyl.style.boxShadow = '0 0 20px rgba(255, 42, 109, 0.5), ' +
                                          'inset 0 0 20px rgba(5, 217, 232, 0.3)';
                }
            });
            
            // Update audio player when duration is loaded
            audioPlayer.addEventListener('loadedmetadata', function() {
                duration = audioPlayer.duration;
                const minutes = Math.floor(duration / 60);
                const seconds = Math.floor(duration % 60);
                document.querySelector('.time-info span:last-child').textContent = 
                    `${minutes}:${seconds.toString().padStart(2, '0')}`;
            });
            
            // Handle audio ending
            audioPlayer.addEventListener('ended', function() {
                playNextSong();
            });
            
            // Update progress bar
            function startProgress() {
                clearInterval(progressInterval);
                progressInterval = setInterval(function() {
                    if (audioPlayer.paused) return;
                    
                    currentTime = audioPlayer.currentTime;
                    updateProgress();
                }, 1000);
            }
            
            function updateProgress() {
                const progressPercent = (currentTime / duration) * 100;
                progress.style.width = `${progressPercent}%`;
                
                // Update time display
                const minutes = Math.floor(currentTime / 60);
                const seconds = Math.floor(currentTime % 60);
                timeCurrent.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
            
            function playNextSong() {
                const items = document.querySelectorAll('.playlist-item');
                let currentIndex = -1;
                
                // Find currently playing song
                items.forEach((item, index) => {
                    if (item.classList.contains('active')) {
                        currentIndex = index;
                    }
                });
                
                // Determine next song (loop if at end)
                const nextIndex = (currentIndex + 1) % items.length;
                
                // Remove active class from all items
                items.forEach(item => item.classList.remove('active'));
                
                // Add active class to next item
                items[nextIndex].classList.add('active');
                
                // Update player with new song info
                const title = items[nextIndex].querySelector('.playlist-title').textContent;
                const artist = items[nextIndex].querySelector('.playlist-artist').textContent;
                const audioFile = items[nextIndex].getAttribute('data-file');
                
                currentSong.textContent = title;
                currentArtist.textContent = artist;
                
                // Update audio source
                if (audioFile) {
                    audioPlayer.src = audioFile;
                    audioPlayer.load();
                    
                    // If player was playing, continue playing
                    if (isPlaying) {
                        audioPlayer.play();
                    }
                }
                
                // Reset current time
                currentTime = 0;
                updateProgress();
            }
            
            // Add click handlers for prev/next buttons
            document.querySelector('.control-btn:first-child').addEventListener('click', playPrevSong);
            document.querySelector('.control-btn:last-child').addEventListener('click', playNextSong);
            
            function playPrevSong() {
                const items = document.querySelectorAll('.playlist-item');
                let currentIndex = -1;
                
                // Find currently playing song
                items.forEach((item, index) => {
                    if (item.classList.contains('active')) {
                        currentIndex = index;
                    }
                });
                
                // Determine prev song (loop if at start)
                const prevIndex = (currentIndex - 1 + items.length) % items.length;
                
                // Remove active class from all items
                items.forEach(item => item.classList.remove('active'));
                
                // Add active class to prev item
                items[prevIndex].classList.add('active');
                
                // Update player with new song info
                const title = items[prevIndex].querySelector('.playlist-title').textContent;
                const artist = items[prevIndex].querySelector('.playlist-artist').textContent;
                const audioFile = items[prevIndex].getAttribute('data-file');
                
                currentSong.textContent = title;
                currentArtist.textContent = artist;
                
                // Update audio source
                if (audioFile) {
                    audioPlayer.src = audioFile;
                    audioPlayer.load();
                    
                    // If player was playing, continue playing
                    if (isPlaying) {
                        audioPlayer.play();
                    }
                }
                
                // Reset current time
                currentTime = 0;
                updateProgress();
            }
            
            // Initialize progress
            updateProgress();
            
            // Initialize click handlers for existing playlist items
            document.querySelectorAll('.playlist-item').forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    document.querySelectorAll('.playlist-item').forEach(i => i.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Update current song info
                    const title = this.querySelector('.playlist-title').textContent;
                    const artist = this.querySelector('.playlist-artist').textContent;
                    const audioFile = this.getAttribute('data-file');
                    
                    currentSong.textContent = title;
                    currentArtist.textContent = artist;
                    
                    // Update audio source
                    if (audioFile) {
                        audioPlayer.src = audioFile;
                        audioPlayer.load();
                        
                        // Auto-play when selecting a new track
                        playBtn.textContent = '⏸';
                        vinyl.classList.add('active');
                        tonearm.classList.add('active');
                        audioPlayer.play();
                        isPlaying = true;
                        startProgress();
                        
                        // Add pulsing glow effect
                        vinyl.style.boxShadow = '0 0 30px rgba(255, 42, 109, 0.7), ' +
                                             'inset 0 0 30px rgba(5, 217, 232, 0.5), ' +
                                             '0 0 40px rgba(255, 42, 109, 0.5)';
                    } else {
                        showNotification('No audio file available', 'error');
                    }
                });
            });
            
            // Song generation
            generateBtn.addEventListener('click', function() {
                const genre = document.getElementById('genre-select').value;
                const mood = document.getElementById('mood-select').value;
                const lyrics = document.getElementById('lyrics-input').value;
                
                if (!genre || !mood) {
                    showNotification('Please select a genre and mood', 'error');
                    return;
                }
                
                generateSong(genre, mood, lyrics);
            });
            
            function generateSong(genre, mood, lyrics) {
                // Show loading state
                showNotification('Generating your track with YuE AI...', 'info');
                document.getElementById('generation-status').textContent = 'Generating track...';
                document.getElementById('generation-status').classList.add('generating');
                
                // Sample data for neon/cyberpunk theme
                const genreArtists = {
                    synthwave: ["Neon Dreams", "Midnight Cruiser", "Retro Wave", "Synthwave AI"],
                    cyberpunk: ["Cyberpunk Generator", "Neon AI", "Dystopian Beats", "Megacity One"],
                    vaporwave: ["Outrun AI", "Sunset Drive", "Palm Shadows", "VHS Dreams"],
                    darksynth: ["Shadow Synth", "Midnight AI", "Dark Matter", "Obsidian Waves"],
                    retrowave: ["Neon Techno", "Pulse Generator", "Circuit Breaker", "Digital Rave"]
                };
                
                // Call the PHP endpoint to generate music
                const formData = new FormData();
                formData.append('action', 'generate_track');
                formData.append('genre', genre);
                formData.append('mood', mood);
                formData.append('lyrics', lyrics);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Generation response:', data);
                    document.getElementById('generation-status').textContent = 'Track generated!';
                    document.getElementById('generation-status').classList.remove('generating');
                    
                    if (data.success) {
                        showNotification('Track generated successfully!', 'success');
                        
                        // Generate random data for the song
                        const artistName = genreArtists[genre.toLowerCase()] ? 
                            genreArtists[genre.toLowerCase()][Math.floor(Math.random() * genreArtists[genre.toLowerCase()].length)] : 
                            "AI Music Generator";
                        
                        const songTitle = lyrics.split(' ')
                            .filter(word => word.length > 3)
                            .sort(() => 0.5 - Math.random())
                            .slice(0, 2)
                            .join(' ');
                        
                        const duration = Math.floor(Math.random() * 120) + 120; // 2-4 minutes in seconds
                        
                        // Create a new playlist item with the generated audio file
                        const newSong = {
                            title: songTitle || `${genre} ${mood}`,
                            artist: artistName,
                            duration: duration,
                            audioSrc: data.generated_file || 'https://example.com/placeholder-audio.mp3'
                        };
                        
                        // Add the new song to the playlist
                        addSongToPlaylist(newSong);
                    } else {
                        showNotification('Failed to generate track. Check console for details.', 'error');
                        console.error('Generation failed:', data.output);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('generation-status').textContent = 'Generation failed';
                    document.getElementById('generation-status').classList.remove('generating');
                    showNotification('Error generating track: ' + error.message, 'error');
                });
                
                // Create a placeholder while generating
                const artistName = genreArtists[genre.toLowerCase()] ? 
                    genreArtists[genre.toLowerCase()][Math.floor(Math.random() * genreArtists[genre.toLowerCase()].length)] : 
                    "AI Music Generator";
                
                const songTitle = lyrics.split(' ')
                    .filter(word => word.length > 3)
                    .sort(() => 0.5 - Math.random())
                    .slice(0, 2)
                    .join(' ');
                
                const duration = Math.floor(Math.random() * 120) + 120; // 2-4 minutes in seconds
                
                // Create a new playlist item
                const newSong = {
                    title: songTitle || `${genre} ${mood}`,
                    artist: artistName,
                    duration: duration,
                    audioSrc: 'https://example.com/placeholder-audio.mp3' // Placeholder until real file is ready
                };
                
                // Add the song to the playlist
                addSongToPlaylist(newSong);
            }
            
            function addSongToPlaylist(song) {
                // Format duration for display
                const minutes = Math.floor(song.duration / 60);
                const seconds = Math.floor(song.duration % 60);
                const durationText = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                // Create playlist item
                const playlistItem = document.createElement('div');
                playlistItem.className = 'playlist-item';
                playlistItem.innerHTML = `
                    <div class="playlist-info">
                        <div class="playlist-title">${song.title}</div>
                        <div class="playlist-artist">${song.artist}</div>
                    </div>
                    <div class="playlist-duration">${durationText}</div>
                `;
                
                // Add click handler to play song
                playlistItem.addEventListener('click', function() {
                    // Remove active class from all items
                    document.querySelectorAll('.playlist-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Update current song info
                    currentSong.textContent = song.title;
                    currentArtist.textContent = song.artist;
                    
                    // Update audio source if available
                    if (song.audioSrc) {
                        audioPlayer.src = song.audioSrc;
                        audioPlayer.load();
                        
                        // Auto-play when selecting a new track
                        playBtn.textContent = '⏸';
                        vinyl.classList.add('active');
                        tonearm.classList.add('active');
                        audioPlayer.play();
                        isPlaying = true;
                        startProgress();
                        
                        // Add pulsing glow effect
                        vinyl.style.boxShadow = '0 0 30px rgba(255, 42, 109, 0.7), ' +
                                               'inset 0 0 30px rgba(5, 217, 232, 0.5), ' +
                                               '0 0 40px rgba(255, 42, 109, 0.5)';
                    }
                    
                    // Update duration display
                    document.querySelector('.time-info span:last-child').textContent = durationText;
                });
                
                // Add to top of playlist
                playlistContainer.insertBefore(playlistItem, playlistContainer.firstChild);
            }
            
            // Show notification
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = 'notification';
                notification.textContent = message;
                notification.style.position = 'fixed';
                notification.style.bottom = '20px';
                notification.style.left = '50%';
                notification.style.transform = 'translateX(-50%)';
                notification.style.backgroundColor = type === 'error' ? 'var(--accent-pink)' : 
                                                 type === 'info' ? '#3498db' : 'var(--accent-blue)';
                notification.style.color = 'white';
                notification.style.padding = '12px 24px';
                notification.style.borderRadius = '5px';
                notification.style.boxShadow = '0 3px 15px rgba(255, 42, 109, 0.5)';
                notification.style.zIndex = '1000';
                notification.style.animation = 'fadeIn 0.3s, fadeOut 0.3s 2.7s';
                notification.style.fontFamily = "'Orbitron', sans-serif";
                notification.style.letterSpacing = '1px';
                notification.style.textAlign = 'center';
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
            
            // Style for notification animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateX(-50%) translateY(20px); }
                    to { opacity: 1; transform: translateX(-50%) translateY(0); }
                }
                @keyframes fadeOut {
                    from { opacity: 1; transform: translateX(-50%) translateY(0); }
                    to { opacity: 0; transform: translateX(-50%) translateY(20px); }
                }
                .generating {
                    color: var(--accent-blue);
                    animation: pulse 1.5s infinite;
                }
                @keyframes pulse {
                    0% { opacity: 0.6; }
                    50% { opacity: 1; }
                    100% { opacity: 0.6; }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    </div> <!-- End of center-container -->
    
    <!-- Scripts for navbar and site functionality -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <script>
        // Particles.js Configuration and Initialization
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS('particles-js', {
                "particles": {
                    "number": {
                        "value": 160,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": "#ffffff"
                    },
                    "shape": {
                        "type": "star",
                        "stroke": {
                            "width": 0,
                            "color": "#000000"
                        },
                        "polygon": {
                            "nb_sides": 5
                        }
                    },
                    "opacity": {
                        "value": 0.7,
                        "random": true,
                        "anim": {
                            "enable": true,
                            "speed": 1,
                            "opacity_min": 0.1,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 4,
                        "random": true,
                        "anim": {
                            "enable": true,
                            "speed": 2,
                            "size_min": 0.5,
                            "sync": false
                        }
                    },
                    "line_linked": {
                        "enable": false
                    },
                    "move": {
                        "enable": true,
                        "speed": 1.5,
                        "direction": "bottom",
                        "random": true,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false,
                        "attract": {
                            "enable": false,
                            "rotateX": 600,
                            "rotateY": 1200
                        }
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "repulse"
                        },
                        "onclick": {
                            "enable": true,
                            "mode": "push"
                        },
                        "resize": true
                    },
                    "modes": {
                        "repulse": {
                            "distance": 100,
                            "duration": 0.4
                        },
                        "push": {
                            "particles_nb": 4
                        }
                    }
                },
                "retina_detect": true
            });
        });
    </script>
</body>
</html>