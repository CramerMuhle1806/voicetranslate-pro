@echo off
chcp 65001 >nul
cd /d "%~dp0"

echo === Usuwam uszkodzony folder .git ===
rmdir /s /q ".git"
echo Usunieto!

echo.
echo === Inicjalizacja git od nowa ===
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
echo === Laczenie z GitHub ===
git remote add origin https://github.com/CramerMuhle1806/voicetranslate-pro.git
git push -u origin main --force

echo.
echo === GOTOWE! ===
pause
