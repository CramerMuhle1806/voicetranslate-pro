@echo off
chcp 65001 >nul
cd /d "%~dp0"

echo === Inicjalizacja git ===
git init
git branch -M main
git config user.name "Roman"
git config user.email "magirus.serwis@gmail.com"

echo.
echo === Dodawanie plikow ===
git add -A
git status

echo.
echo === Tworzenie commita ===
git commit -m "Initial commit: VoiceTranslate Pro v2.0"

echo.
echo === Ustawianie zdalnego repo ===
git remote remove origin 2>nul
git remote add origin https://github.com/CramerMuhle1806/voicetranslate-pro.git

echo.
echo === Wysylanie na GitHub ===
git push -u origin main --force

echo.
echo === GOTOWE! ===
pause
