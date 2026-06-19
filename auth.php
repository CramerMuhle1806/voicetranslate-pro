<?php
// ================================================================
//  VoiceTranslate Pro – Auth Server
//  Cramer Mühle © 2025
//
//  Obsługuje: rejestrację, logowanie, listę użytkowników
//  Dane zapisuje w: users.json (ten sam folder)
// ================================================================

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { http_response_code(405); echo json_encode(['error'=>'Method not allowed']); exit; }

define('USERS_FILE', __DIR__ . '/users.json');

// ── Load/save users ──────────────────────────────────────────────
function loadUsers() {
    if (!file_exists(USERS_FILE)) return [];
    $raw = file_get_contents(USERS_FILE);
    return json_decode($raw, true) ?: [];
}
function saveUsers($users) {
    file_put_contents(USERS_FILE, json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// ── Input ────────────────────────────────────────────────────────
$input = json_decode(file_get_contents('php://input'), true);
$action = trim($input['action'] ?? '');

// ── REGISTER ─────────────────────────────────────────────────────
if ($action === 'register') {
    $name     = trim($input['name']     ?? '');
    $username = strtolower(trim($input['username'] ?? ''));
    $password = trim($input['password'] ?? '');
    $lang     = trim($input['lang']     ?? 'de');

    // Validation
    if (strlen($name) < 2)           { echo json_encode(['error'=>'Name muss mindestens 2 Zeichen haben']); exit; }
    if (strlen($username) < 3)       { echo json_encode(['error'=>'Benutzername muss mindestens 3 Zeichen haben']); exit; }
    if (!preg_match('/^[a-z0-9._]+$/', $username)) { echo json_encode(['error'=>'Benutzername: nur Buchstaben, Zahlen, Punkt, Unterstrich']); exit; }
    if (strlen($password) < 4)       { echo json_encode(['error'=>'Passwort muss mindestens 4 Zeichen haben']); exit; }

    $valid_langs = ['de','pl','en','ro','ru','uk','fr','es','it','ja','zh','ar','tr'];
    if (!in_array($lang, $valid_langs)) $lang = 'de';

    $users = loadUsers();

    // Check duplicate username
    foreach ($users as $u) {
        if ($u['username'] === $username) {
            echo json_encode(['error'=>'Benutzername bereits vergeben']); exit;
        }
    }

    // Create user
    $initials = '';
    $parts = explode(' ', $name);
    foreach ($parts as $p) { if ($p) $initials .= strtoupper($p[0]); }
    $initials = substr($initials, 0, 2) ?: strtoupper(substr($name, 0, 2));

    $user = [
        'id'        => 'u' . time() . rand(100,999),
        'name'      => $name,
        'username'  => $username,
        'password'  => password_hash($password, PASSWORD_DEFAULT),
        'lang'      => $lang,
        'initials'  => $initials,
        'status'    => 'online',
        'created'   => date('Y-m-d H:i:s'),
    ];

    $users[] = $user;
    saveUsers($users);

    // Return safe user (no password)
    $safe = $user; unset($safe['password']);
    echo json_encode(['ok'=>true, 'user'=>$safe]);
    exit;
}

// ── LOGIN ─────────────────────────────────────────────────────────
if ($action === 'login') {
    $username = strtolower(trim($input['username'] ?? ''));
    $password = trim($input['password'] ?? '');

    if (!$username || !$password) { echo json_encode(['error'=>'Benutzername und Passwort erforderlich']); exit; }

    $users = loadUsers();
    foreach ($users as $u) {
        if ($u['username'] === $username && password_verify($password, $u['password'])) {
            $safe = $u; unset($safe['password']);
            echo json_encode(['ok'=>true, 'user'=>$safe]);
            exit;
        }
    }
    echo json_encode(['error'=>'Falscher Benutzername oder Passwort']);
    exit;
}

// ── LIST USERS (for contacts) ─────────────────────────────────────
if ($action === 'list') {
    $exclude = trim($input['exclude'] ?? '');
    $users = loadUsers();
    $list = [];
    foreach ($users as $u) {
        if ($u['id'] === $exclude) continue;
        $list[] = [
            'id'       => $u['id'],
            'name'     => $u['name'],
            'username' => $u['username'],
            'initials' => $u['initials'],
            'lang'     => $u['lang'],
            'status'   => 'online',
        ];
    }
    echo json_encode(['ok'=>true, 'users'=>$list]);
    exit;
}

// ── UPDATE LANG ───────────────────────────────────────────────────
if ($action === 'update_lang') {
    $id   = trim($input['id']   ?? '');
    $lang = trim($input['lang'] ?? '');
    $valid_langs = ['de','pl','en','ro','ru','uk','fr','es','it','ja','zh','ar','tr'];
    if (!$id || !in_array($lang, $valid_langs)) { echo json_encode(['error'=>'Invalid']); exit; }

    $users = loadUsers();
    foreach ($users as &$u) {
        if ($u['id'] === $id) { $u['lang'] = $lang; break; }
    }
    saveUsers($users);
    echo json_encode(['ok'=>true]);
    exit;
}

echo json_encode(['error'=>'Unknown action']);
