@ECHO OFF
chcp 65001
color 0a

ECHO.
ECHO.Запускаем Веб-сервер ...
cd C:
cd %~dp0nginx
start nginx.exe

ECHO.
ECHO.Проверяем версию ОС ...
IF "%PROCESSOR_ARCHITECTURE%"=="x86" (set bit=x86) ELSE (set bit=x64)
ECHO.%bit%
ECHO.
ECHO.Запускаем Информационный сервер ...
cd %~dp0SportInfoFS\
"%~dp0php-%bit%\php.exe" "%~dp0SportInfoFS\SportInfoFS.php"

pause