================================================================
  VoiceTranslate Pro – Cramer Mühle
  Instrukcja instalacji dla administratora IT
  Wersja 2.0 | 2025  (nowość: rejestracja użytkowników)
================================================================

ZAWARTOŚĆ PAKIETU
-----------------
  voicetranslate.html  → Główna aplikacja (czat + rejestracja)
  face2face.html       → Tryb face-to-face (tłumaczenie simultaniczne)
  translate.php        → Proxy serwera dla Claude AI API
  auth.php             → Zarządzanie użytkownikami (rejestracja/logowanie)
  manifest.json        → Konfiguracja PWA (instalacja na telefonie)
  README.txt           → Ta instrukcja

  [TWORZONY AUTOMATYCZNIE przy pierwszym uruchomieniu:]
  users.json           → Baza danych użytkowników (tworzona przez auth.php)

================================================================
  JAK DZIAŁA REJESTRACJA
================================================================

Każdy pracownik tworzy sobie konto samodzielnie przy pierwszym
uruchomieniu aplikacji:

  1. Otworzyć aplikację → wybrać zakładkę "Registrieren"
  2. Wpisać pełne imię i nazwisko (np. "Max Müller")
  3. Wybrać nazwę użytkownika (np. "max.mueller")
  4. Ustawić hasło (minimum 4 znaki)
  5. Wybrać swój język (Deutsch, Polnisch, Englisch...)
  6. Kliknąć "Konto erstellen" → gotowe, od razu zalogowany

  Po rejestracji WSZYSCY pracownicy widzą się automatycznie
  jako kontakty — nie trzeba nikogo ręcznie dodawać!

  Hasła są przechowywane ZASZYFROWANE (PHP password_hash).
  Nawet administrator nie może odczytać haseł.

================================================================
  KROK 1: POBRANIE KLUCZA API CLAUDE
================================================================

1. Przejdź na stronę: https://console.anthropic.com/
2. Utwórz konto lub zaloguj się
3. Kliknij "API Keys" → "Create Key"
4. Skopiuj klucz (zaczyna się od "sk-ant-...")
5. WAŻNE: Klucz widoczny tylko raz — zapisz go od razu!

Koszt: ok. $0,003 za jedno tłumaczenie (bardzo tanio)
Darmowe środki testowe dostępne przy pierwszym koncie.

================================================================
  KROK 2: KONFIGURACJA
================================================================

Otwórz plik: translate.php

Znajdź tę linijkę (linia 12):
  define('CLAUDE_API_KEY', 'sk-ant-TUTAJ-WPISZ-KLUCZ-API');

Zastąp placeholder prawdziwym kluczem:
  define('CLAUDE_API_KEY', 'sk-ant-xxxxxxxxxxxxxxxxxxxx');

Zapisz plik.

================================================================
  KROK 3: WGRANIE PLIKÓW NA SERWER
================================================================

Utwórz na serwerze nowy folder:
  /voicetranslate/

Wgraj WSZYSTKIE 6 plików do tego folderu:
  ✓ voicetranslate.html
  ✓ face2face.html
  ✓ translate.php
  ✓ auth.php             ← nowość w wersji 2.0
  ✓ manifest.json
  ✓ README.txt

Metoda wgrywania: klient FTP (np. FileZilla) lub menedżer
plików w cPanel.

================================================================
  KROK 4: USTAWIENIE UPRAWNIEŃ DO ZAPISU  ← WAŻNE (NOWOŚĆ)
================================================================

Plik auth.php automatycznie tworzy users.json przy pierwszym
uruchomieniu. Aby to zadziałało, folder /voicetranslate/ musi
być ZAPISYWALNY przez serwer WWW.

  METODA A — menedżer plików cPanel:
    1. Kliknij prawym przyciskiem na folder /voicetranslate/
    2. Wybierz "Zmień uprawnienia" → ustaw 755 (lub 775)
    3. Zapisz

  METODA B — FTP (FileZilla):
    1. Prawy klik na folder → "Atrybuty pliku"
    2. Wpisz wartość numeryczną: 755
    3. OK

  METODA C — SSH (linia poleceń):
    chmod 755 /sciezka/do/voicetranslate/
    touch /sciezka/do/voicetranslate/users.json
    chmod 664 /sciezka/do/voicetranslate/users.json

  SPRAWDZENIE czy działa:
    → Otwórz aplikację w przeglądarce
    → Zakładka "Registrieren" → utwórz konto testowe
    → Jeśli sukces: plik users.json został utworzony ✓
    → Jeśli błąd "Verbindungsfehler": sprawdź uprawnienia

================================================================
  KROK 5: SPRAWDZENIE HTTPS (OBOWIĄZKOWE!)
================================================================

Aplikacja MUSI działać przez HTTPS:
  ✓ https://cramermuehle.de/voicetranslate/voicetranslate.html
  ✗ http://... (NIE zadziała — przeglądarka zablokuje mikrofon)

Jeśli brak certyfikatu SSL:
  → Let's Encrypt: https://letsencrypt.org/ (bezpłatny)
  → Zazwyczaj dostępny w pakiecie hostingowym

================================================================
  KROK 6: TESTOWANIE
================================================================

1. Otwórz w przeglądarce Chrome:
   https://cramermuehle.de/voicetranslate/voicetranslate.html

2. Test rejestracji:
   → Zakładka "Registrieren" → wpisz dane testowe
   → Kliknij "Konto erstellen"
   → Sukces: przekierowanie do listy kontaktów

3. Test logowania:
   → Wyloguj się → zakładka "Anmelden"
   → Wpisz nazwę użytkownika i hasło → "Anmelden"
   → Sukces: lista kontaktów

4. Test kontaktów:
   → Zarejestruj drugiego użytkownika (inny telefon lub przeglądarka)
   → Obaj użytkownicy widoczni automatycznie na liście kontaktów

5. Test tłumaczenia:
   → Wybierz kontakt → wpisz wiadomość tekstową → wyślij
   → Tłumaczenie powinno pojawić się po 2-3 sekundach

6. Test trybu face-to-face:
   → Baner "Face-to-Face Modus" → dotknij
   → Wybierz języki → "Gespräch starten"
   → Przytrzymaj przycisk mikrofonu → mów → puść

================================================================
  BEZPIECZEŃSTWO DANYCH
================================================================

  Plik users.json zawiera:
    ✓ Imię i nazwisko, nazwa użytkownika, język
    ✓ HASH hasła (nie samo hasło!)
    ✗ Brak haseł w postaci jawnej
    ✗ Brak adresu e-mail (nie jest wymagany)

  Zalecenie: regularne tworzenie kopii zapasowych users.json

  Aby zablokować dostęp do users.json z zewnątrz,
  utwórz plik /voicetranslate/.htaccess z treścią:

    <Files "users.json">
      Order allow,deny
      Deny from all
    </Files>

  Dla serwerów nginx dodaj do konfiguracji:
    location ~ /users\.json { deny all; }

================================================================
  INSTALACJA NA TELEFONIE (PWA)
================================================================

Android (Chrome):
  1. Otwórz stronę aplikacji w Chrome
  2. Menu (⋮) → "Dodaj do ekranu głównego"
  3. Aplikacja pojawia się jak normalna apka na telefonie

iPhone (Safari):
  1. Otwórz stronę aplikacji w Safari
  2. Ikona udostępniania (□↑) → "Do ekranu głównego"
  3. Aplikacja pojawia się na ekranie startowym

================================================================
  DOSTĘPNE JĘZYKI
================================================================

🇩🇪 Niemiecki   🇵🇱 Polski      🇬🇧 Angielski
🇷🇴 Rumuński    🇷🇺 Rosyjski    🇺🇦 Ukraiński
🇫🇷 Francuski   🇪🇸 Hiszpański  🇮🇹 Włoski
🇯🇵 Japoński    🇨🇳 Chiński     🇸🇦 Arabski
🇹🇷 Turecki

================================================================
  WYMAGANIA TECHNICZNE
================================================================

Serwer:
  ✓ PHP 7.4 lub nowszy (z rozszerzeniem cURL)
  ✓ Certyfikat HTTPS/SSL
  ✓ Dostęp do internetu z serwera (do api.anthropic.com)
  ✓ Prawa zapisu w folderze /voicetranslate/ (dla users.json)

Urządzenia użytkowników:
  ✓ Android: przeglądarka Chrome (zalecana)
  ✓ iPhone: przeglądarka Safari
  ✓ PC: Chrome lub Edge

Uprawnienia mikrofonu:
  → Przy pierwszym uruchomieniu przeglądarka pyta o dostęp
  → Należy kliknąć "Zezwól"

================================================================
  ROZWIĄZYWANIE PROBLEMÓW
================================================================

PROBLEM: "Verbindungsfehler — Server prüfen" przy rejestracji
  → Sprawdź czy auth.php jest w tym samym folderze co voicetranslate.html
  → Sprawdź uprawnienia zapisu folderu (patrz Krok 4)
  → Sprawdź dziennik błędów PHP (cPanel → Logi)

PROBLEM: Tłumaczenie nie działa
  → Sprawdź klucz API w pliku translate.php
  → Serwer nie ma dostępu do api.anthropic.com
  → Włącz rozszerzenie cURL w PHP

PROBLEM: Mikrofon nie działa
  → Aplikacja nie działa przez HTTPS
  → Uprawnienia mikrofonu nie zostały udzielone w przeglądarce

PROBLEM: Plik users.json nie jest tworzony
  → Ustaw prawa zapisu folderu na 755 (patrz Krok 4)
  → Utwórz plik ręcznie:
      touch users.json && chmod 664 users.json

================================================================
  WSPARCIE TECHNICZNE
================================================================

  Dokumentacja API:  https://docs.anthropic.com/
  Wsparcie Anthropic: https://support.anthropic.com/
  Dokumentacja PHP:  https://www.php.net/

================================================================
  CRAMER MÜHLE © 2025 | VoiceTranslate Pro v2.0
================================================================
