@echo off
:: This Windows batch file sets open-editor.js as handler for subl:// protocol

if defined PROCESSOR_ARCHITEW6432 (set reg="%systemroot%\sysnative\reg.exe") else (set reg=reg)

%reg% ADD HKCR\subl /ve /d "URL:subl Protocol" /f
%reg% ADD HKCR\subl /v "URL Protocol" /d "" /f
%reg% ADD HKCR\subl\shell\open\command /ve /d "wscript \"%~dp0open-editor.js\" \"%%1\"" /f
