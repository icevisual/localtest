@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION 

set ifo=abcdefghijklmnopqrstuvwxyz0123456789

echo 从第4个字符开始，截取5个字符：
echo %ifo:~3,5%
echo 从倒数第14个字符开始，截取5个字符：
echo %ifo:~-14,5%
pause
