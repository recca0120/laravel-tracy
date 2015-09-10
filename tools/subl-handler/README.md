subl-handler
============

## What is this?

This vbs script handles `subl:` url scheme to open it in your text editor.

Example link
``` subl://open?url=file://C:\htdocs\myapp\vendor\laravel\framework\src\Illuminate\Support\Facades\Facade.php&line=214 ```

I found it very useful when debugging Laravel based applications because Laravel's exception page use `subl:` links on every filename.
Of course it works only on local webserver (in your local filesystem).

## Compatibility

I tested this script on Windows 8 with Sublime 3, Netbeans 8 and Notepad++ 6.6.8 but it should work on other Windows systems as well.

Text editors that I know to support line numbers using `filename:###` syntax (option 1 in setup)
- sublime
- netbeans

Text editors that I know to support line numbers using `filename -n###` syntax (option 2 in setup)
- notepad++

Text editors that I know *not supporting* line numbers in filename:
- eclipse
- pspad
- notepad
- and many other editors that use standard windows file open syntax (`application.exe %1`)

## Installation

- copy the selected script to any location on your disk. I suggest to copy it to c: or c:\Users or something like this. The file will have to stay there because it will handle `subl:` links and follow it to your text editor
- run the script and follow instructions on the screen

## Usage

Click on any `subl:` link to see it working.

*Note for Mozilla Firefox users*: Firefox will ask you what to do with subl: links for the first time you click on it. Simply click the OK button and Firefox will choose the default action, which is this script since you installed it. Firefox will ask about this only once.

## How does it work? / Is it safe?

It is safe to run this script, it doesn't change any files in your system. It only adds some small entry into windows registry.

When you run this script without parameters it will run itself as administrator to be able to import .reg file into windows registry.
The imported .reg file will look something like this:
```
Windows Registry Editor Version 5.00

[HKEY_CLASSES_ROOT\subl]
@="URL:subl Protocol"
"URL Protocol"=""

[HKEY_CLASSES_ROOT\subl\shell]

[HKEY_CLASSES_ROOT\subl\shell\open]

[HKEY_CLASSES_ROOT\subl\shell\open\command]
@="\"wscript.exe\" \"C:\\subl-handler.vbs\" 1 \"C:\\Program Files\\Sublime Text 3\\sublime_text.exe\" %1"
```

This adds the subl-handler.vbs script as a handler for `subl:` protocol so when you click `subl:` link your browser will run the script. The script reads the link, parse it and run the selected text editor with proper command line arguments.
