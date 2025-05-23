# Transcribe and Translate System

This repository contains two PHP scripts:

- `transcribe.php` – Upload an audio file and use the OpenAI API to transcribe it to English.
- `text_to_voice.php` – Enter English text and output audio in a chosen language (Chinese, Korean, Japanese, French or Spanish). The script uses OpenAI to translate the text and generate speech audio.

## Requirements

- PHP with cURL support (install with `apt install php php-curl` on Ubuntu).
- An OpenAI API key (set the environment variable `OPENAI_API_KEY` for the web server).

## Usage

Place both PHP files in your web directory (e.g., `/var/www/html`). Access them from a browser:

1. `transcribe.php` – upload an audio file (MP3, WAV, etc.). The server will send it to OpenAI for transcription and display the English text.
2. `text_to_voice.php` – type text in English and choose a target language. The script will translate the text using OpenAI, generate speech in the target language, and return an audio file.

Each script contains minimal error handling and assumes the server has outbound internet access to reach the OpenAI API.
