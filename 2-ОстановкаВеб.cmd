@ECHO OFF
chcp 65001
color 0a

ECHO.
ECHO.Останавливаем Веб-сервер ...

C:
cd %~dp0nginx\
%~dp0nginx\nginx.exe -s stop
TIMEOUT 5