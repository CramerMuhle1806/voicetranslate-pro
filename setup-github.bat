@echo off
chcp 65001 >nul
echo ========================================
echo   Podlaczanie do GitHub
echo ========================================
echo.
cd /d "%~dp0"

set /p REPO_URL="Wklej URL repozytorium z GitHub (np. https://github.com/roman/voicetranslate-pro.git): "

git remote add origin %REPO_URL%
git push -u origin main

echo.
echo ========================================
echo   GOTOWE! Kod wyslany na GitHub.
echo   Teraz mozesz podlaczyc repo do Coolify.
echo ========================================
echo.
pause
