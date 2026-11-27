@echo off
title ProiectPBD - Pornire Server & Baza de Date
color 0A

echo ============================================================
echo   ProiectPBD - Sistem Gestiune Proiecte Europene
echo   Pornire servicii necesare (MariaDB + PHP Web Server)
echo ============================================================
echo.

:: 1. Verificare daca MariaDB/MySQL ruleaza pe portul 3307
netstat -ano | findstr ":3307" >nul
if %errorlevel% neq 0 (
    echo [1/3] Pornire serviciu MariaDB / MySQL pe portul 3307...
    start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini"
    timeout /t 3 /nobreak >nul
) else (
    echo [1/3] MariaDB / MySQL ruleaza deja pe portul 3307.
)

:: 2. Deschidere browser pe http://localhost:8000
echo [2/3] Deschidere browser la http://localhost:8000 ...
start "" "http://localhost:8000"

:: 3. Pornire server web PHP pe portul 8000
echo [3/3] Pornire server web PHP pe localhost:8000 ...
echo.
echo   Serverul este activ! Nu inchide aceasta fereastra cat timp folosesti aplicatia.
echo   Pentru oprire, apasa Ctrl+C sau inchide fereastra.
echo.
"C:\xampp\php\php.exe" -S localhost:8000 -t "%~dp0app\public" "%~dp0app\public\index.php"
