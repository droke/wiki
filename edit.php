<?php
include "elements/session_start.txt";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<header>
		<?php		
			$page = ucfirst(spaceTo_($_GET["p"]));
			
			$toread = "derp";
			$toedit = $toread;
			
			if (!empty($page))
			{
				if(preg_match("/[\<\>\[\]\@#!?]/", $page))
				{
					$toread = "Invalid_Title"; 
					$toedit = "Invalid Title";	
				}
				elseif (file_exists("inc/" . $page . ".txt"))
				{
					$toread = $page; 
					$toedit = $toread;
				}
				else
				{
					$toread = "404";
					$toedit = $page;
				}
			}
			else
			{				
				$toread = "Home"; 
				$toedit = $toread;
			}	

			$page_title = underscoreToSpace($toedit);
			
			echo "<title>" . $page_title . " - The Universe</title>";
			
			preg_match("/Log\:/", $toedit, $match);
			if ($match[0][0])
			{
				echo "<script type='text/javascript'>";
				echo "location.href='index.php?p=" . $toedit . "'"; 
				echo "</script>";
			}
		?>
		
		<link type="text/css" rel="stylesheet" href="style.css" />
	</header>
	
	<body>			
		<?php
			include 'elements/sidebar.txt';		
			include 'elements/header.txt';
		?>
		
		<div class="container">		
			<div class="page">	
				
				<?php			
					echo "<div class='page_title'>" . $page_title;					
						echo "<div class='controls'>";
							echo "<a href='index.php?p=Log:" . $toedit . "'><div class='button'>Log</div></a>";					
							echo "<a href='delete.php?p=" . $toedit . "'><div class='button'>Delete</div></a>";
							echo "<a href='index.php?p=" . $toedit . "'><div class='button'>View</div></a>";
						echo "</div>";					
					echo "</div>";
				?>
				
				<div class="textwrapper">
					<form name="saveform" method="POST" action="">
						<textarea rows="30" id="editor" name="s"><?php if (file_exists("inc/" . $toedit . ".txt")) { include("inc/" . $toedit . ".txt"); } else { } ?></textarea>
						<button type="submit" name="p" value=<?php echo "'" . $toedit . "'" ?>>Submit</button>
					</form>
				</div>	

				<?php

				   if(isset($_POST['p']))
				   {
						if ($_SESSION['verified'] == 1)
						{
							$text = stripslashes($_POST["s"]);
							file_put_contents("inc/" . ucfirst($toedit) . ".txt", $text);
							echo "<script type='text/javascript'>";
							echo "location.href='index.php?p=" . $toedit . "'"; 
							echo "</script>";
							
							date_default_timezone_set('Australia/Perth');
							
							$user = spaceTo_($_SESSION['u']);
							
							$log = "(" . $_SERVER['REMOTE_ADDR'] . ") User [[User:" . $user . "|" . $user . "]] edited article on " . date('d/m/Y h:i:s a', time()) . ".\n";
							
							if (file_exists("inc/Log:" . $page . ".txt")) 
							{
								file_put_contents("inc/Log:" . $page . ".txt", $log, FILE_APPEND);
							}
							else
							{
								file_put_contents("inc/Log:" . $page . ".txt", $log);
							}
						}
						else
						{
							echo "<script type='text/javascript'>";
							echo "alert('You are not logged in!')"; 
							echo "</script>";
						}
				   }

				?>
			</div>
		</div>
		
		<?php
			include 'elements/footer.txt';
		?>
	</body>
</html>