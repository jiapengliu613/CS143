<!DOCTYPE html>
<html>
<body>
<h1>Calculator</h1>
<p>
Ver 1.4 2/10/2016 by @Jiapeng Liu<br>
Type an expression in the following box (e.g., 10.5+20*3/25).
</p>
 


<h3>Please type your expression in the following box (e.g., 1 * 2.5 / 3) </h3>
<form action="calculator.php" method="GET">
    <input type="text" name="expr">
    <input type="submit" value = "Go!">
</form>
<?php 
if ($_GET["expr"]) {
	$equ = $_GET["expr"];
	//$equ = preg_replace('/\s*/', "", equ);
	//
	if (preg_match('/(\/\s*0\.*0*\s*$)|(\/\s*0\.*0*\s*(\+|\-|\*|\/))/', $equ)) {
		echo "Division by zero error";
	} else if (preg_match('/^((\s*\-?[1-9][0-9]*(\.[0-9]+)?\s*)|(\s*\-?0(\.[0-9]+)?\s*))([\+\-\*\/](\-)?((\s*[1-9][0-9]*(\.[0-9]+)?\s*)|(\s*0(\.[0-9]+)?\s*)))*$/', $equ)){
		$equ = preg_replace('/\s*\-\s*\-/','+', $equ);
		eval("\$ans = $equ ;");
		echo "Result = ".$ans;
	} else {
		echo "Input Expression is Invalid!";
	}
} 
?>

<ul>
    <li>Spaces are allowed between numbers and operators.
    <li>No 0 is allowed as the start of a number, for example, 1 / 03 is considered as invalid input, but 1 / 0.3 is valid.
    <li> No space is allowed inside a number, for example, 1 / 0 3 is invalid.

</ul>

</body>
</html>