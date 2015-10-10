@echo off
for /f "skip=1" %%i in (config.ini) do echo %%i
echo #############################################
for /f "skip=1 delims== tokens=1,2" %%i in (config.ini) do if "%%i"=="hostname " echo 主机名为 :%%j&goto next1
:next1
for /f "skip=1 delims== tokens=1,2" %%i in (config.ini) do if "%%i"=="port " echo 端口号为 :%%j&goto next2
:next2
for /f "skip=1 delims== tokens=1,2" %%i in (config.ini) do if "%%i"=="username " echo 用户名为 :%%j&goto next3
:next3
for /f "skip=1 delims== tokens=1,2" %%i in (config.ini) do if "%%i"=="passwd " echo 密码为  :%%j&goto next4
:next4
echo #############################################
pause