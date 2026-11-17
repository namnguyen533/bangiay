@echo off
setlocal
cd /d "%~dp0"

set "PHP=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe"
if not exist "%PHP%" set "PHP=php"

start "" "http://127.0.0.1:8000/"
"%PHP%" -S 127.0.0.1:8000 -t "%~dp0"

pause
