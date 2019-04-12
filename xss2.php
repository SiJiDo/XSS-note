<?php
	$xss = $_POST['xss'];
	setcookie('user', 'sijidou', time()+3600*24);
	//header("Content-Security-Policy:script-src 'unsafe-inline' 'self';default-src 'self';");
	//header("Content-Security-Policy:default-src 'self'; script-src 'unsafe-inline';");
	//header("Content-Security-Policy:default-src 'unsafe-inline' 'self';");
	//header("Content-Security-Policy:script-src 'unsafe-inline';");
	//header("Content-Security-Policy:connect-src http://www.baidu.com/ ");
	//header("Content-Security-Policy:child-src http://www.baidu.com/ ");
	//header("Content-Security-Policy:child-src *;default-src *");
	//header("Content-Security-Policy:object-src data:;default-src *");
	//header("Content-Security-Policy:object-src javascript:;default-src *");
	//header("Content-Security-Policy:default-src 'self'");
	//header("Content-Security-Policy:script-src 'self' 'unsafe-inline';default-src 'self'");
?>

<!DOCTYPE html>
<html>
<head>
	<title>xss</title>
	<meta charset="utf-8">
	<!--<meta http-equiv="Content-Security-Policy" content="default-src 'self';">-->
</head>
<body>
	<textarea name="xss" form="xss" rows="3" cols="150"></textarea>
	<form action="" method="POST" id="xss">
		<input type="submit" value="submit" name="submit" />
	</form>
	<p><?php echo $xss ?></p>
</body>
</html>
