@ECHO OFF
chcp 65001
color 0a

ECHO.
ECHO.Останавливаем Веб-сервер ...

C:
cd C:\SportInfo\nginx\
nginx.exe -s stop
TIMEOUT 5