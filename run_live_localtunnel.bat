@echo off
title Sarraf-QMS - LocalTunnel Launcher
color 0B
echo Starting Sarraf-QMS Local Server on Port 8003...
start "" /b php artisan serve --port=8003
echo Starting LocalTunnel for Sarraf-QMS (https://sarraf-qms.loca.lt)...
npx localtunnel --port 8003 --subdomain sarraf-qms
pause
