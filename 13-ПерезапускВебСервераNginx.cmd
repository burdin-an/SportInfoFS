@ECHO OFF
chcp 65001
color 0a

ECHO.
ECHO.Перезапускаем Веб-сервер ...
C:
cd %~dp0nginx\
%~dp0nginx\nginx.exe -s reload
TIMEOUT 5