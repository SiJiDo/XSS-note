<?php
	session_start();
	$xss = $_POST['xss'];
	setcookie("user", "sijidou",time()+3600*24*30);
?>

<html>
<head>
	<meta charset="utf-8">
	<title>xss test</title>
</head>
<body>
	<form action="" method="POST">
		<input type="text" name="xss" value="" />
		<input type="submit" name="submit" value="submit" />
	</form>
	<p>
	<?php echo $xss?>
	</p>
	<?php var_dump($_COOKIE)?>
</body>
</html>
