<?php
// ================================================================
//  VoiceTranslate Pro – Claude API Proxy
//  Cramer Mühle © 2025
//
//  INSTALACJA:
//  1. Wpisz klucz API poniżej (pobierz z console.anthropic.com)
//  2. Wgraj ten plik na serwer obok voicetranslate.html
//  3. Gotowe – aplikacja automatycznie go znajdzie
// ================================================================

define('CLAUDE_API_KEY', 'sk-ant-TUTAJ-WPISZ-KLUCZ-API');
define('CLAUDE_MODEL',   'claude-sonnet-4-20250514');
define('MAX_TOKENS',     1000);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { http_response_code(405); echo json_encode(['error'=>'Method not allowed']); exit; }

$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['text']) || empty($input['targetLang'])) {
    http_response_code(400); echo json_encode(['error'=>'Missing text or targetLang']); exit;
}

$text   = substr(trim($input['text']), 0, 5000);
$lang   = trim($input['targetLang']);
$names  = ['pl'=>'Polish','en'=>'English','de'=>'German','fr'=>'French','es'=>'Spanish',
           'it'=>'Italian','uk'=>'Ukrainian','ru'=>'Russian','ro'=>'Romanian',
           'ja'=>'Japanese','zh'=>'Chinese','tr'=>'Turkish','ar'=>'Arabic'];

if (!array_key_exists($lang, $names)) { http_response_code(400); echo json_encode(['error'=>'Invalid language']); exit; }

$payload = json_encode([
    'model'      => CLAUDE_MODEL,
    'max_tokens' => MAX_TOKENS,
    'messages'   => [['role'=>'user','content'=>
        "Translate to {$names[$lang]}. Return ONLY the translation, no explanation:\n\n{$text}"
    ]]
]);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch,[
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: '.CLAUDE_API_KEY,
        'anthropic-version: 2023-06-01',
    ],
]);

$response = curl_exec($ch);
$code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);
curl_close($ch);

if ($err)       { http_response_code(500); echo json_encode(['error'=>'cURL: '.$err]); exit; }
if ($code!=200) { http_response_code(502); echo json_encode(['error'=>'Claude API error','code'=>$code]); exit; }

$data = json_decode($response, true);
$translation = trim($data['content'][0]['text'] ?? '');
if (!$translation) { http_response_code(500); echo json_encode(['error'=>'Empty response']); exit; }

echo json_encode(['translation'=>$translation,'original'=>$text,'targetLang'=>$lang]);
