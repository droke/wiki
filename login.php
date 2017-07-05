<?php
	include "elements/session_start.txt";
	
	include 'mysql.php';

	function checkLogin($u, $p)
	{		
		$_SESSION['u']=$u;
		$_SESSION['p']=$p; 	
		$_SESSION['isadmin'] = 0;
	
		$u = addslashes($u);
		$p = addslashes($p);		
		
		$sql = "SELECT * FROM users WHERE username = '" . $u . "' AND pw = '". md5($p) . "'";
				
		include 'mysql.php';		
		$result=mysqli_query($link, $sql);
		
		//var_dump(mysql_fetch_array($result));
		
		if (!$_SESSION['verified'])
		{
			//var_dump(mysql_num_rows($result)) ;
		
			if ($result and mysqli_num_rows($result) > 0)
			{
				$_SESSION['ERROR'] = "";
				$_SESSION['verified'] = 1;	

				while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {					
					if ($row[3] == "100")
					{
						$_SESSION['isadmin'] = 1;	
					}					
				}
			} 
			else 
			{
				$_SESSION['ERROR'] = "Login Error";
			}
		}
		
		if ($_SESSION['verified'] != 1)
		{
			$_SESSION['ERROR'] = "Login Failed";
		}
		
		mysqli_close($link);
		
	}
	
	$page = $_GET['p'];
	$logout = $_GET['logout'];

	if (!empty($logout))
	{
		if ($logout == "1")
		{
			$_SESSION['u']="";
			$_SESSION['p']=""; 
			$_SESSION['ERROR'] = "";
			$_SESSION['verified'] = 0;
			$_SESSION['isadmin'] = 0;
		}
	}
	else
	{			
		$un = $_GET['un'];
		$pw = $_GET['pw'];			
		
		if ($_SESSION['verified'] != 1)
		{
			checkLogin($un, $pw);
		}
	}
	
	echo "<script type='text/javascript'>";
	echo "location.href='index.php?p=" . $page . "'"; 
	echo "</script>";	
	
?>