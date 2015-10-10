@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION 

set ifo=abcdefghijklmnopqrstuvwxyz0123456789

echo 从第4个字符开始，截取5个字符：
echo %ifo:~3,5%
echo 从倒数第14个字符开始，截取5个字符：
echo %ifo:~-14,5%

set str=萨达速度大师傅说到底发生
echo %str:速度=sadasd%

　　echo 正在运行的这个批处理：
　　echo 完全路径：%0
　　echo 去掉引号：%~0
　　echo 所在分区：%~d0
　　echo 所处路径：%~p0
　　echo 文件名：%~n0
　　echo 扩展名：%~x0
　　echo 文件属性：%~a0
　　echo 修改时间：%~t0
　　echo 文件大小：%~z0


echo 输出完毕，按任意键退出&&pause>nul&&exit
