@echo off
chcp 65001 >nul
echo ========================================
echo   VoiceTranslate Pro - Inicjalizacja Git
echo ========================================
echo.

cd /d "%~dp0"

REM Inicjalizacja repo
git init
git branch -M main
git config user.name "Roman"
git config user.email "magirus.serwis@gmail.com"

REM Dodaj wszystkie pliki (pomijając users.json przez .gitignore)
git add .
git commit -m "Initial commit: VoiceTranslate Pro v2.0"

echo.
echo ========================================
echo   GOTOWE! Repo zainicjalizowane.
echo.
echo   NASTEPNY KROK:
echo   1. Idz na https://github.com/new
echo   2. Nazwa repo: voicetranslate-pro
echo   3. Prywatne lub publiczne - Twoj wybor
echo   4. NIE zaznaczaj "Add README" (mamy juz pliki)
echo   5. Kliknij "Create repository"
echo   6. Skopiuj dwie komendy z sekcji "...or push an existing repository"
echo      i wklej je tutaj (albo odpal setup-github.bat)
echo ========================================
echo.
pause
