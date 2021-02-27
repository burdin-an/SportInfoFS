@ECHO OFF
chcp 65001
color 0a

C:
cd C:\SportInfo\nginx\
C:\SportInfo\nginx\nginx.exe -s stop
TIMEOUT 5