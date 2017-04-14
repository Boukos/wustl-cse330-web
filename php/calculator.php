<!DOCTYPE HTML>
<head>
	<meta charset="utf-8"/>
	<title>Calculator</title>
</head>

<body>
<?php

if(!isset($_GET['calc'])){
	echo "No specified calculation!";
}
else{
	$answer = $_GET['calc'];
	// filtering input
	$number1 = (float)$_GET["num1"];
	$number2 = (float)$_GET["num2"];
	
	if ($answer == "add") {
		$add = $number1 + $number2;
		echo "$number1 + $number2 = $add";     
	}
	elseif ($answer == "sub") {
		$sub = $number1 - $number2;		
		echo "$number1 - $number2 = $sub";     
	}
	elseif ($answer == "mul") {
		$mul = $number1 * $number2;		
		echo "$number1 * $number2 = $mul";     
	}
	elseif ($answer == "div") {
		if($number2 != 0){
			$div = $number1/$number2;
		}
		else{
			$div = "INFINITY";
		}
		echo "$number1 / $number2 = $div"; 
	}
}

?>

</body>
</html>