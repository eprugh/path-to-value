<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>Provision Path to Value in an account</title>
	
</head>
<body id="index">
	<form action="P2V_provisioning.php" method="post" accept-charset="utf-8">
		<div id="username">
			<label for="user_name">User name:</label> <input type="text" name="api_user_name" value="" id="user_name" />
		</div>		
		<div id="password">
			<label for="password">Password:</label> <input type="password" name="api_password" value="" id="password" />
		</div>
		<div id="programs">
			<div>
				<input type="checkbox" name="Activation" value="yes" id="activation" /> <label for="activation">Account Activation &amp; Setup</label>
			</div>
			<div>
				<input type="checkbox" name="Welcome" value="yes" id="welcome_program" /> <label for="welcome_program">Welcome Program</label>
			</div>
			<div>
				<input type="checkbox" name="Commerce" value="yes" id="commerce_program" /> <label for="commerce_program">Commerce Program</label>
			</div>
		</div>
		<p><input type="submit" value="Continue &rarr;"></p>
	</form>
</body>
</html>