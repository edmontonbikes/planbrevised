<?php

$datafromdb='this is a test with single (\') and double (") quotes';
$datafromdb=rawurlencode($datafromdb);

$htmloutput='
<html>
<body>

Text Field (for display): <input id=textField type=text size="60" value=""><br>
Text Field (for submit): <input id=textField2 type=text size="60" value="">
';

$htmloutput.='<script>
var jsdata="'.$datafromdb.'";
textField.value = unescape(jsdata);
textField2.value = jsdata;
document.write("Hello World!")
</script>

<script>
function getArgs() {
var args = new Object();
var query = location.search.substring(1);
var pairs = query.split("&");
for(var i = 0; i < pairs.length; i++) {
var pos = pairs[i].indexOf("=");
if (pos == -1) continue;
var argname = pairs[i].substring(0,pos);
var value = pairs[i].substring(pos+1);
args[argname] = unescape(value);
}
return args;
}
var args = getArgs();
document.write(args.a); 
document.write("<br>");
document.write(args.email); 
</script>

</body>
</html>
';

echo $htmloutput;
?> 