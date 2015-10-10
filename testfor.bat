@echo off
@echo No 1
for /d %%i in (*) do @echo %%i
@echo No 2
for /d %%i in (???) do @echo %%i
@echo No 3
for /r . %%i in (*.php) do @echo %%i
@echo No 4
for /r %%i in (*.css) do @echo %%i
@echo No 5
for /l %%i in (0,2,5) do @echo %%i
@echo No 6
for /f %%i in (source.txt) do echo %%i
@echo No 7
for /f "delims=\ tokens=1,2,*" %%i in (source.txt) do echo %%i %%j %%k
@echo No 8
for /f "skip=1 delims=\ tokens=*" %%i in (source.txt) do echo %%i
@echo No 9
for /f "eol= delims=\ tokens=*" %%i in (source.txt) do echo %%i
@echo No 0

@echo No 0

@echo No 0

@echo No 0

@echo No 0

@echo No 0
pause