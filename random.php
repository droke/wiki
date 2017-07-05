<?php

if ($handle = opendir('inc')) {
    $pages = array(); 
	while (false !== ($entry = readdir($handle))) {		
		
        
        if ($entry == "." or $entry == "..") {
            
        }
        else {
            $pages[] = str_replace(".txt", "", $entry);
        }
    }
	
	echo count($pages);
	
	echo "<script type='text/javascript'>";
	echo "location.href='index.php?p=" . $pages[rand(0,count($pages))] . "'"; 
	echo "</script>";	

    closedir($handle);
}
?>