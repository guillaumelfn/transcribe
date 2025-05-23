<?php
// Translate English text to a chosen language and return spoken audio via OpenAI

$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
    die('OPENAI_API_KEY environment variable not set');
}

// languages allowed
$languages = ['Chinese', 'Korean', 'Japanese', 'French', 'Spanish'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = trim($_POST['text'] ?? '');
    $language = $_POST['language'] ?? '';
    if (!$text || !in_array($language, $languages)) {
        die('Invalid input');
    }

    // Step 1: translate text using chat completion
    $messages = [
        ['role' => 'system', 'content' => 'Translate the user text to ' . $language],
        ['role' => 'user', 'content' => $text],
    ];
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'max_tokens' => 1000,
        ]),
    ]);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Request Error: ' . curl_error($ch));
    }
    curl_close($ch);
    $data = json_decode($response, true);
    $translated = $data['choices'][0]['message']['content'] ?? '';
    if (!$translated) {
        die('Translation failed');
    }

    // Step 2: generate speech from translated text
    $speechCh = curl_init('https://api.openai.com/v1/audio/speech');
    $speechData = [
        'model' => 'tts-1',
        'input' => $translated,
        'voice' => 'alloy',
        'response_format' => 'mp3'
    ];
    curl_setopt_array($speechCh, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($speechData),
    ]);
    $speechResponse = curl_exec($speechCh);
    if (curl_errno($speechCh)) {
        die('Speech request error: ' . curl_error($speechCh));
    }
    $status = curl_getinfo($speechCh, CURLINFO_HTTP_CODE);
    curl_close($speechCh);

    if ($status === 200) {
        header('Content-Type: audio/mpeg');
        echo $speechResponse;
        exit;
    } else {
        die('Failed to generate speech');
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Text to Voice</title>
</head>
<body>
<h1>Enter English Text to Speak</h1>
<form method="post">
    <textarea name="text" rows="5" cols="60" required></textarea><br>
    <label>Language:
        <select name="language">
            <option>Chinese</option>
            <option>Korean</option>
            <option>Japanese</option>
            <option>French</option>
            <option>Spanish</option>
        </select>
    </label>
    <button type="submit">Speak</button>
</form>
</body>
</html>
