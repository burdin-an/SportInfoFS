@ECHO OFF
chcp 65001
color 0a

C:
cd %~dp0nginx\
start nginx.exe
TIMEOUT 5