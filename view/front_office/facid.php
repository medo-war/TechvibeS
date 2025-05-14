<?php
// Face++ API credentials
define('API_KEY', 'PUMCJCLjjKUak0j_-O7noKqzY3TB4-AB');
define('API_SECRET', 'DQxi3F1lj4eBlDtkVwFCTBM2o_yaF5TQ');
define('BASE_URL', 'https://api-us.faceplusplus.com/facepp/v3/');

// Path to the stored image to compare against
$storedImagePath = 'C:/Users/Ahmed/Pictures/Camera Roll/WIN_20250511_22_18_08_Pro.jpg';

function compareFaces($image1, $image2) {
    $url = BASE_URL . 'compare';
    
    try {
        $postFields = [
            'api_key' => API_KEY,
            'api_secret' => API_SECRET,
            'image_file1' => new CURLFile($image1),
            'image_file2' => new CURLFile($image2)
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (isset($result['error_message'])) {
            return ['error' => $result['error_message']];
        } else {
            return [
                'confidence' => $result['confidence'] ?? 0,
                'thresholds' => $result['thresholds'] ?? [],
                'same' => ($result['confidence'] ?? 0) > ($result['thresholds']['1e-5'] ?? 70)
            ];
        }
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['captured_image'])) {
    $tempFile = $_FILES['captured_image']['tmp_name'];
    $result = compareFaces($tempFile, $storedImagePath);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Comparison</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        #camera {
            width: 100%;
            max-width: 500px;
            background-color: #f0f0f0;
            margin-bottom: 20px;
        }
        #canvas {
            display: none;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        #results {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Face Comparison</h1>
    
    <div id="camera">
        <video id="video" width="100%" autoplay></video>
    </div>
    <canvas id="canvas"></canvas>
    
    <button id="capture">Capture Photo</button>
    <button id="compare" disabled>Compare Faces</button>
    
    <div id="results" style="display: none;">
        <h2>Comparison Results</h2>
        <p id="confidence"></p>
        <p id="thresholds"></p>
        <p id="samePerson"></p>
    </div>
    
    <form id="uploadForm" enctype="multipart/form-data" style="display: none;">
        <input type="file" id="imageInput" name="captured_image" accept="image/*">
    </form>
    
    <script>
        // Access camera
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('capture');
        const compareBtn = document.getElementById('compare');
        const resultsDiv = document.getElementById('results');
        const uploadForm = document.getElementById('uploadForm');
        const imageInput = document.getElementById('imageInput');
        
        let capturedImage = null;
        
        // Start camera
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
                alert("Could not access camera. Please check permissions.");
            });
        
        // Capture photo
        captureBtn.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Convert canvas to blob
            canvas.toBlob(blob => {
                capturedImage = blob;
                compareBtn.disabled = false;
                alert("Photo captured! Click 'Compare Faces' to analyze.");
            }, 'image/jpeg', 0.95);
        });
        
        // Compare faces
        compareBtn.addEventListener('click', () => {
            if (!capturedImage) return;
            
            // Create FormData and append the image
            const formData = new FormData();
            const file = new File([capturedImage], 'captured.jpg', { type: 'image/jpeg' });
            formData.append('captured_image', file);
            
            // Show loading state
            compareBtn.disabled = true;
            compareBtn.textContent = 'Processing...';
            
            // Send to server for comparison
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Display results
                resultsDiv.style.display = 'block';
                document.getElementById('confidence').textContent = 
                    `Confidence Score: ${data.confidence?.toFixed(2) || 'N/A'}`;
                document.getElementById('thresholds').textContent = 
                    `Thresholds: ${JSON.stringify(data.thresholds || {})}`;
                document.getElementById('samePerson').textContent = 
                    `Same Person: ${data.same ? 'Yes' : 'No'}`;
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred during comparison.");
            })
            .finally(() => {
                compareBtn.disabled = false;
                compareBtn.textContent = 'Compare Faces';
            });
        });
    </script>
</body>
</html>