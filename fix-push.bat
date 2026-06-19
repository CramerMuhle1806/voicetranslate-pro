@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo Sprawdzam status repo...
git status
echo.
echo Sprawdzam logi...
git log --oneline 2>nul || echo "Brak commitow!"
echo.
echo Probuje wypchnac...
git push -u origin main
echo.
if %errorlevel% neq 0 (
  echo Blad! Probuje ponownie z force...
  git push -f origin main
)
echo.
echo GOTOWE!
pause
