
<?php
// Resolve IE bug ; more efficient than metas
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Fri, Jan 01 1900 00:00:00 GMT"); // Past date
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");
?>

<head>
   <title>Titre de la page</title>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <meta name="Pragma" content="no-cache" />
</head>
<body>

<?php
/*Connection String*/
include('zBF_connect.php');
$connect = mysql_connect($hostname, $username, $password) or die('Could not connect: ' . mysql_error());

/*Select The database*/
$bool = mysql_select_db($database, $connect);
if ($bool === False){ print "can't find $database"; }
	
if (isset($_GET['ID']))
{
	$sqlVacDel = "update `devdrupal`.`bibli_a_vacances` set `estSupp_vacances`='1' where `id_vacances`='".$_GET['ID']."' ";
	mysql_query($sqlVacDel);	
	echo "<p><strong>Effacement r&eacute;ussi";	
}

if (!isset($GET['ID']))
{
	$sqlVac = "
	select 
		id_vacances 
		, debut_vacances as debut
		, DATE_FORMAT(debut_vacances,'%a %d %b %Y') as debut_vacances
		, DATE_FORMAT(fin_vacances,'%a %d %b %Y') as fin_vacances
	from 
		bibli_a_vacances 
	where 
		estSupp_vacances = 0
	order by
		debut ASC
	";
	$queryVac = mysql_query($sqlVac) or die('Could not find vacances_id:<br> ' . mysql_error());	
	echo '
<table>
	<thead>
	<tr>
        <th>D&eacute;but</th>
		<th>Fin</th>
        <th></th>
	</tr>
	</thead>
	<tbody>
	';

	while ($arrayVac = mysql_fetch_array($queryVac)) 
	{ 
		$IdVac = $arrayVac['id_vacances']; 
		$DebutVac = $arrayVac['debut_vacances']; 
		$FinVac = $arrayVac['fin_vacances']; 
		echo '
	<tr>
		<form>
		<td>'.$DebutVac.'</td>
		<td>'.$FinVac.'</td>
		<td><input type="submit" name="action" value="effacer"></td>
		<input type="hidden" name="ID" value="'.$IdVac.'">
		</form>
	</tr>
		';
	}
	echo '
	</tbody>
</table>
	';
}


?>
</body>