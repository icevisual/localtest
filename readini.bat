rem @echo off & setlocal enabledelayedexpansion
echo 参数个数: %0 %1 %2 %3
set name=
set namelist=
set param=
set paramlist=
FOR /F "eol=; tokens=1* usebackq delims==" %%i IN (%1) do (
    rem echo %%i %%j
    set p=%%i
    rem echo p=!p!
    rem echo 取字符!p! !p:~0,1!  !p:~-1!  !p:~1,-1!
    if "!p:~0,1!"=="[" (
        if "!p:~-1!"=="]" (
            @echo 条件!p:~1,-1!
            set name=!p:~1,-1!
        )        
    )
    rem echo name=!name! namelist=!namelist! paramlist=!paramlist! 输入 %2
    if ""=="%2" (
        rem echo namelist=!namelist!  %%i
        if !namelist! EQU "" (
            if "!p:~1,-1!" NEQ "" (
                rem echo 步骤1 !namelist!
                set namelist=!p:~1,-1!
            )
        ) else (
            if "!p:~1,-1!" NEQ "" (
                rem echo 步骤2 uuuu!namelist!uuuu
                set namelist=!namelist!,!p:~1,-1!
            )
        )
    ) else if "!name!"=="%2" (
        if "%3"=="" (
            if "[!name!]" NEQ "%%i" (
                if "!paramlist!"=="" (
                    set paramlist=%%i
                ) else (
                    set paramlist=!paramlist!,%%i
                    rem echo  得到属性 %%i %%j
                )
           )
      ) else (
          if "%%i" == "%3" (
              set param=%%j
          )
      )
  )
)
    rem echo ------------------------
    rem echo name=!name!
if "%2" EQU "" (
    rem 名称列表
    echo "!namelist:~1!"
) else (
    if "%3" EQU "" (
        rem 属性列表
        echo "!paramlist!"
    ) else (
        rem 属性值
echo "!param!"
    )
)
rem @echo on