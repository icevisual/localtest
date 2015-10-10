@echo off & setlocal enabledelayedexpansion
echo ----readini 1.ini----
call readini 1.ini
echo ----readini 1.ini abc----
call readini 1.ini abc
echo ----readini 1.ini abc me----
call readini 1.ini abc me
@echo on
pause