@echo off
echo open ftp.gilmore.cc>deploy.ftp
echo %1>>deploy.ftp
echo %2>>deploy.ftp
echo cd public_html>>deploy.ftp
echo cd todo>>deploy.ftp
echo put %3>>deploy.ftp
echo disconnect>>deploy.ftp
echo quit>>deploy.ftp
@echo on
ftp -i -s:deploy.ftp
