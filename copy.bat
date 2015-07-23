@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION 

SET WNMP_PATH=D:\wnmp\www
SET GZB_PATH=%WNMP_PATH%\gzb
SET LOCAL_PATH=%WNMP_PATH%\gzb_local
for /f "tokens=*" %%i in (source.txt) do (
	set gzb_file=%GZB_PATH%%%i
	set loc_file=%LOCAL_PATH%%%i
	copy  /Y !gzb_file! !loc_file!
	echo copy !gzb_file! !loc_file!
	pause
)

ping 127.0.0.1 >nul
exit