<?php
	session_start();
	session_destroy();
	header("Location: news_express.php");
	exit; 
?>