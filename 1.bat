@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION 

set ifo=abcdefghijklmnopqrstuvwxyz0123456789

echo �ӵ�4���ַ���ʼ����ȡ5���ַ���
echo %ifo:~3,5%
echo �ӵ�����14���ַ���ʼ����ȡ5���ַ���
echo %ifo:~-14,5%

set str=�����ٶȴ�ʦ��˵���׷���
echo %str:�ٶ�=sadasd%

����echo �������е����������
����echo ��ȫ·����%0
����echo ȥ�����ţ�%~0
����echo ���ڷ�����%~d0
����echo ����·����%~p0
����echo �ļ�����%~n0
����echo ��չ����%~x0
����echo �ļ����ԣ�%~a0
����echo �޸�ʱ�䣺%~t0
����echo �ļ���С��%~z0


echo �����ϣ���������˳�&&pause>nul&&exit
