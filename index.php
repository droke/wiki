<?php
include "elements/session_start.txt";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<header>
		<?php	
			$page = $_GET["p"];			
			
			$toread = "derp";
			$toedit = $toread;			
			
			if (!empty($page))
			{
			
				$page = ucfirst(spaceTo_($page));
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

			echo "<title>" . $page . " - The Universe</title>";
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
					$page = $_GET["p"];
				
					$file = file_get_contents("inc/" . $toread . ".txt", true);							
					
					$pageLinks = array();
					$categoryLinks = array();

					$variables = array();
					
					if (preg_match_all('|\!(.+)=(.+)\!|U', $file, $matches, PREG_OFFSET_CAPTURE))
					{						
						$split = "";
						
						for ($i=0; $i < count($matches[0]); $i++)
						{							
							$split = explode("=", $matches[0][$i][0]);
							
							$variables[str_replace("!", "", $split[0])] = str_replace("!", "", $split[1]);							
							
							$file = str_replace($matches[0][$i][0], "", $file);	
						}					
					}
					
					$file = trim($file);
					
					if (count($variables) > 0)
					{
						foreach ($variables as $k => $v)
						{

							if ($k == "title")
							{
								$page_title = $v;
							}
							
							if ($k == "redirect")
							{
								echo "<script>";
								echo "document.location.href = 'index.php?p=" . $v . "'";
								echo "</script>";
							}
							
							if ($k == "categories")
							{
								$cats = explode(",", $v);
								
								foreach ($cats as $cat)
								{
									array_push($categoryLinks, $cat);
								}
							}
						}
					}
					
					preg_match_all('#\[\[(.*?)\]\]#', $file, $matches, PREG_OFFSET_CAPTURE);
					
					foreach ($matches[1] as $match)
					{						
						
						$txt = $match[0];						
						
						$special = explode("|", $txt );
						$link = $special[0];
						$desc = $special[1];
						
						
						
													
						if (empty($desc))
						{
							$desc = $link;
						}
						
						$link = spaceTo_($link);
						
						// echo $link;
						// echo $desc;
						
						// echo "<br>";

						$text = $desc;

						if (!file_exists("inc/" . $link . ".txt"))
						{
							$text = "<font color=red>" . $text . "</font>";
						}						
												
						$text = "<a href='index.php?p=" . $link . "'>" . $text . "</a>";						
						$file = str_replace("[[" . $txt . "]]", $text, $file);					
					}
					
					$file = str_replace("\n", "<br />", $file);	
					
					$file = str_replace("[h1]", "<div class='page_title'>", $file, $count);
					$file = str_replace("[/h1]", "</div>", $file, $count);
					
					for ($i = 2; $i <= 3; $i++) 
					{					
						$file = str_replace("[h" . $i . "]", "<h" . $i . ">", $file, $count);
						$file = str_replace("[/h" . $i . "]", "</h" . $i . ">", $file, $count);
					}					
					
					preg_match_all("|\[img\](.*)\[\/img\]|U", $file, $matches);
					
					//<div class="rimage"><div class="img_title">TITLE</div><img src="http://upload.wikimedia.org/wikipedia/commons/d/dd/Big_dog_military_robots.jpg" /><div class="img_desc">asdasdads<br>adsadsads</div></div>
			
					foreach ($matches[1] as $match)
					{
						$exp = explode("|", $match);
						
						if (count($exp) > 0)
						{
							$src = $exp[0];
							$div = "";
							$desc = "";
							
							for ($i=1; $i<count($exp); $i++)
							{
								if ($exp[$i] == "right" & $div == "")
								{
									$div = "rimage";
								}
								elseif ($exp[$i] == "left" & $div == "")
								{
									$div = "limage";
								}
								elseif ($exp[$i] == "center" & $div == "")
								{
									$div = "image";
								}
								else
								{
									$desc = $exp[$i];
								}
							}
							
							if ($div == "")
							{
								$div = "rimage";
							}
							
							$rep = "";
							
							if ($div == "image")
							{
								$rep = $rep . "<div class='align_center'>";
							}
							
							$rep = $rep . "<div class='" . $div . "'><a href='" . $src . "'><img src='" . $src . "'/></a>";
							
							if ($desc != "")
							{
								$rep = $rep . "<br><div class='img_desc'>" . $desc . "</div>";
							}
							
							$rep = $rep . "</div>";

							if ($div == "image")
							{
								$rep = $rep . "</div>";
							}
							
							
							$file = str_replace("[img]" . $match . "[/img]", $rep, $file, $count);
						}						
					}

					// $file = str_replace("[img]", "<div class='rimage'><img src='", $file, $count);
					// $file = str_replace("[/img]", "' align='absmiddle' /></div>", $file, $count);
					
					// $file = str_replace("[cimg]", "<div class='image'><img src='", $file, $count);
					// $file = str_replace("[/cimg]", "' align='absmiddle' /></div>", $file, $count);
					
					echo "<div class='page_title'>" . $page_title;	

						echo "<div class='controls'>";
						
							preg_match("/Log\:/", $toedit, $match);
							if (!$match[0][0])
							{
								echo "<a href='index.php?p=Log:" . $toedit . "'><div class='button'>Log</div></a>";						
								echo "<a href='delete.php?p=" . $toedit . "'><div class='button'>Delete</div></a>";					
								echo "<a href='edit.php?p=" . $toedit . "'><div class='button'>Edit</div></a>";
							}
							else
							{
								$temp = str_replace("Log:", "", $toedit);
								echo "<a href='index.php?p=" . $temp . "'><div class='button'>View</div></a>";						
							}
								
						echo "</div>";
					
					echo "</div>";
					echo $file;
					?>
			</div>				
		</div>	

		<div class="footer">		
			<div class="page">		
				<?php	
					if (empty($categoryLinks))
					{
						array_push($categoryLinks, "Uncategorized");
					}					
							
					echo "<div class='categories'>";
					
					for ($i = 0; $i < count($categoryLinks); ++$i)
					{							
						$pg = ucfirst($categoryLinks[$i]);
						
						$linkName = underscoreToSpace($pg);							
						$link = "Category:" . spaceTo_($pg);
					
						if (!file_exists("inc/" . $categoryLinks[$i] . ".txt"))
						{
							echo "<a href='index.php?p=" . $link . "'><font color=red>" . $linkName . "</font></a>";
						}
						else
						{
							echo "<a href='index.php?p=" . $link . "'>" . $linkName . "</a>";
						}
												
						if ($i < count($categoryLinks)-1)
						{
							echo "<br>";
						}							
					}
					
					echo "</div>";

				?>	
			</div>				
		</div>

		<?php
			include 'elements/footer.txt';
		?>
	
	</body>
</html>