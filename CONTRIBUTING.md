###PHP
Contributors should follow PSR-2, the PHP coding style guideline, with the following exceptions:

- You MUST use tabs for indenting instead of 4 spaces.
- Opening braces for classes, methods, functions, or condition MUST go on the same line, and closing braces MUST go on the next line after the body.
- Use single quote (') instead of double quote (") which has a performance issue, also, to easily insert HTML as string without confusion.


###HTML
- Use valid HTML5.
- All tags should have it's own line, except `<br />`.
- Use double quote (") instead of single quote (') to be easily inserted into PHP without confusion.
- Close all elements, including the void (e.g. `<div></div> <br /> <meta />`).


###Logging
TUATS implements Laravel's logging facilities, which provides the eight logging levels defined in RFC 5424:
- Emergency: system is unusable
- Alert: action must be taken immediately
- Critical: critical conditions
- Error: error conditions
- Warning: warning conditions
- Notice: normal but significant condition
- Info: informational messages
- Debug: debug-level messages