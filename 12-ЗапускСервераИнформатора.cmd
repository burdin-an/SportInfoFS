@ECHO OFF
chcp 65001
color 0a

echo Проверяем версию ОС ...
IF "%PROCESSOR_ARCHITECTURE%"=="x86" (set bit=x86) ELSE (set bit=x64)
echo %bit%
"%~dp0\php-%bit%\php.exe" "%~dp0\SportInfoFS\SportInfoFS.php"

TIMEOUT 5