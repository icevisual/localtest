@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION 

SET WNMP_PATH=D:\wnmp\www
SET GZB_PATH=%WNMP_PATH%\gzb
SET LOCAL_PATH=%WNMP_PATH%\gzb_local
for /f "tokens=*" %%i in (source.txt) do (
	set gzb_file="%GZB_PATH%%%i"
	set loc_file="%LOCAL_PATH%%%i"
	
	if not exist !loc_file! (  
		
	rem ) else ( 
	rem	md !loc_file! 
		echo mk !loc_file! 
	)
	

	xcopy !gzb_file! !loc_file! /E /Y  >nul
	echo COPY SUCCESS !gzb_file!
)

exit


