@echo off
chcp 65001 >nul
cd /d "%~dp0"

echo === Stan lokalny ===
git status
echo.

echo === Historia commitow ===
git log --oneline 2>nul
if %errorlevel% neq 0 (
  echo Brak commitow - tworze...
  git add -A
  git commit -m "Initial commit: VoiceTranslate Pro v2.0"
)

echo.
echo === Pushuje na GitHub ===
git remote -v
git push -u origin main --force

echo.
echo === Weryfikacja ===
git log --oneline origin/main 2>nul || echo "Sprawdz powyzej czy push zakonczony sukcesem"
pause
