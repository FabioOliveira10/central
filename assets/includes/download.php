<?php

include('config.php');
include('functions.php');

if (isglobaladmin())
{

	if(isset($_GET['export'])){
			function cleanData($str){ 
			$str = preg_replace("/\t/", "\\t", $str); 
			$str = preg_replace("/\n/", "\\n", $str); 
			} 
	
		$filename = "Mailing_List_" . date('Y-m-d-h-i-s') . ".xls"; 
		header("Content-Disposition: attachment; filename=\"$filename\""); 
		header("Content-Type: application/vnd.ms-excel"); 
		
		$flag = false;  
		$result = mysql_query("SELECT * FROM ".PREFIX."mailinglist ORDER BY id") or die('Query failed!'); 
		while(false !== ($row = mysql_fetch_assoc($result))) { 
			if(!$flag) { 
			# display field/column names as first row 
			echo implode("\t", array_keys($row)) . "\n"; 
			$flag = true; 
			} 
			array_walk($row, 'cleanData');
			echo implode("\t", array_values($row)) . "\n";
			}
	}

	if(isset($_GET['xdb']))
	{
		backup_tables(DBHOST,DBUSER,DBPASS,DBNAME,getdbs(DBHOST,DBUSER,DBPASS,PREFIX));	
	}
	
	$dfile = $_GET['bup'];
	if(isset($dfile))
	{
		switch($dfile){
			case "1":
				$target = '../style/style.php';
				$dname = 'CSS';
				$type = 'text/php';	
			break;
			default: 
				die("No file selected");	
			}
		
		//create timestamp
		$date = date('d-m-y');
		$now = time();
		$stamp = $date.'-'.$now;
		
		//assign data to file
		// file path and mode r = read
		$fo = fopen($target, 'r');
		//get file contents and work out the file content size in bytes
		$data = fread($fo, filesize($target));
		//close the file
		fclose($fo);
		
		//assign filename to be created
		$file = "backup-$dname-$stamp.php";
		//open the file and append data as not not over wright whats in there already 
		$fo = fopen($file, 'a');
		fwrite($fo, $data);
		fclose($fo);
		
		
		//offer file for download using headser
		header("Content-Disposition: attachment; filename=\"$file\"");
		header('Content-type: '.$type);
		//read the file or download will be empty
		readfile($file);
		//delete the new file
		unlink($file);	
	}

}
?>