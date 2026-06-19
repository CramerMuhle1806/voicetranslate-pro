@echo off
chcp 65001 >nul
cd /d "%~dp0"
git add Dockerfile
git commit -m "Add Dockerfile for Coolify deployment"
git push
echo.
echo GOTOWE! Dockerfile wyslany na GitHub.
pause
