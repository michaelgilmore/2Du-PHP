@echo off
echo open ftp.gilmore.cc>deploy.ftp
echo %1>>deploy.ftp
echo %2>>deploy.ftp
echo cd public_html>>deploy.ftp
echo cd todo>>deploy.ftp
echo mput *.css>>deploy.ftp
echo mput *.php>>deploy.ftp
echo mput *.sql>>deploy.ftp
echo mput *.txt>>deploy.ftp
echo mput *_ftp_script.bat>>deploy.ftp
echo cd user>>deploy.ftp
echo mput *.php>>deploy.ftp
echo cd ..>>deploy.ftp
echo cd list>>deploy.ftp
echo mput *.php>>deploy.ftp
echo disconnect>>deploy.ftp
echo quit>>deploy.ftp
@echo on
ftp -i -s:deploy.ftp
