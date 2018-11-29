@echo off
echo open ftp.gilmore.cc>get_server_log.ftp
echo %1>>get_server_log.ftp
echo %2>>get_server_log.ftp
echo cd public_html>>get_server_log.ftp
echo cd todo>>get_server_log.ftp
echo get %3>>get_server_log.ftp
echo disconnect>>get_server_log.ftp
echo quit>>get_server_log.ftp
@echo on
ftp -i -s:get_server_log.ftp
