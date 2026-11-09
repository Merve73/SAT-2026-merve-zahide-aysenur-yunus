@echo off
rem Eski calisan PHP sunucusunu kapatir, sonra siteyi baslatir.
taskkill /f /im php.exe >nul 2>&1
call "%~dp0baslat.bat"
