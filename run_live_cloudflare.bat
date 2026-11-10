@echo off
title Sarraf-QMS - Live Cloudflare Launcher
color 0A
echo Starting Sarraf-QMS Local Server on Port 8003...
start "" /b php artisan serve --port=8003
echo Starting Cloudflare Tunnel for Sarraf-QMS...
"C:\Users\Mujtaba Arshad\.gemini\antigravity\cloudflared.exe" tunnel --url http://127.0.0.1:8003
pause
