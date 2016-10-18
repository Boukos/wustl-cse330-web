<?php

$userName = $_GET["user"];
$saniUserName = filter_var($userName, FILTER_SANITIZE_STRING);
$num1 = (float)$_GET["num1"];
$num2 = (float)$_GET["num2"];
$add = $num1 + $num2;
$sub = $num1 - $num2;
$mul = $num1 * $num2;
if($num2 != 0){
	$div = $num1/$num2;
}
else{
	$div = "INFINITY!!";
}
 
echo "hello"," ",$saniUserName;
echo "<br>";

if(!isset($_GET['symbol'])){
	echo "No calculation specified!";
}
else{
	$answer = $_GET['symbol'];
	if ($answer == "add") {          
		echo "$num1 + $num2 = $add";     
	}
	elseif ($answer == "sub") {          
		echo "$num1 - $num2 = $sub";     
	}
	elseif ($answer == "mul") {          
		echo "$num1 * $num2 = $mul";     
	}
	elseif ($answer == "div") {          
		echo "$num1 / $num2 = $div"; 
	}
}

?>