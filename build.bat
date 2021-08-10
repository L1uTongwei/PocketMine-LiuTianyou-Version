@echo off
chcp 65001 >nul
TITLE PocketMine-MP server software for Minecraft: Pocket Edition
cd /d %~dp0
bin\php\php.exe -d enable_dl=On build.php
pause