<?php
// Simple audio transcription script using OpenAI API

$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
    die('OPENAI_API_KEY environment variable not set');
}

if (!empty($_FILES['audio']['tmp_name'])) {
    $file = $_FILES['audio']['tmp_name'];
    $ch = curl_init('https://api.openai.com/v1/audio/transcriptions');
    $postFields = [
        'file' => new CURLFile($file, mime_content_type($file), $_FILES['audio']['name']),
        'model' => 'whisper-1',
    ];
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_POSTFIELDS => $postFields,
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Request Error: ' . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if (!empty($data['text'])) {
        echo "<h2>Transcription:</h2><pre>" . htmlspecialchars($data['text']) . "</pre>";
    } else {
        echo 'Error in transcription';
    }
    echo '<p><a href="' . $_SERVER['PHP_SELF'] . '">Back</a></p>';
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transcribe Audio</title>
</head>
<body>
<h1>Upload Audio to Transcribe</h1>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="audio" accept="audio/*" required>
    <button type="submit">Transcribe</button>
</form>
</body>
</html>
