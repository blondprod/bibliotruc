<?php
	#Include the connect.php file
	include('zBF_connect.php');
	#Connect to the database
	//connection String
	$connect = mysql_connect($hostname, $username, $password)
	or die('Could not connect: ' . mysql_error());
	//Select The database
	$bool = mysql_select_db($database, $connect);
	if ($bool === False){
	   print "can't find $database";
	}

	if (isset($_GET['update']))
	{
		// UPDATE COMMAND 
		$update_query = "UPDATE `bibli_a_vacances` 
		SET 
			`debut_vacances`='".$_GET['debut_vacances']."' ,
			`fin_vacances`='".$_GET['fin_vacances']."' ,
			`estSupp_vacances`='".$_GET['estSupp_vacances']."'
		WHERE 
			`id_vacances`='".$_GET['id_vacances']."'";
		 $result = mysql_query($update_query) or die("SQL Error 1: " . mysql_error());
		 echo $result;
	}
	else
	{
		$pagenum = $_GET['pagenum'];
		$pagesize = $_GET['pagesize'];
		$start = $pagenum * $pagesize;
		#$query = "SELECT SQL_CALC_FOUND_ROWS * FROM bibli_a_emprunt WHERE estSupp_emprunt = '0' LIMIT $start, $pagesize";
		$query = "
		SELECT 
			SQL_CALC_FOUND_ROWS * 
		FROM 
			bibli_a_vacances
		WHERE 
			estSupp_vacances = '0' 
		LIMIT 
			$start, $pagesize";

		$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
		$sql = "SELECT FOUND_ROWS() AS `found_rows`;";
		$rows = mysql_query($sql);
		$rows = mysql_fetch_assoc($rows);
		$total_rows = $rows['found_rows'];

/* */
	$filterquery = "";
	
	// filter data.
	if (isset($_GET['filterscount']))
	{
		$filterscount = $_GET['filterscount'];
		
		if ($filterscount > 0)
		{
			$where = " WHERE estSupp_vacances = '0' AND (";
			$tmpdatafield = "";
			$tmpfilteroperator = "";
			for ($i=0; $i < $filterscount; $i++)
		    {
				// get the filter's value.
				$filtervalue = $_GET["filtervalue" . $i];
				// get the filter's condition.
				$filtercondition = $_GET["filtercondition" . $i];
				// get the filter's column.
				$filterdatafield = $_GET["filterdatafield" . $i];
				// get the filter's operator.
				$filteroperator = $_GET["filteroperator" . $i];
				
				if ($tmpdatafield == "")
				{
					$tmpdatafield = $filterdatafield;			
				}
				else if ($tmpdatafield <> $filterdatafield)
				{
					$where .= ")AND(";
				}
				else if ($tmpdatafield == $filterdatafield)
				{
					if ($tmpfilteroperator == 0)
					{
						$where .= " AND ";
					}
					else $where .= " OR ";	
				}
				
				// build the "WHERE" clause depending on the filter's condition, value and datafield.
				switch($filtercondition)
				{
					case "CONTAINS":
						$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
						break;
					case "DOES_NOT_CONTAIN":
						$where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
						break;
					case "EQUAL":
						$where .= " " . $filterdatafield . " = '" . $filtervalue ."'";
						break;
					case "NOT_EQUAL":
						$where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";
						break;
					case "GREATER_THAN":
						$where .= " " . $filterdatafield . " > '" . $filtervalue ."'";
						break;
					case "LESS_THAN":
						$where .= " " . $filterdatafield . " < '" . $filtervalue ."'";
						break;
					case "GREATER_THAN_OR_EQUAL":
						$where .= " " . $filterdatafield . " >= '" . $filtervalue ."'";
						break;
					case "LESS_THAN_OR_EQUAL":
						$where .= " " . $filterdatafield . " <= '" . $filtervalue ."'";
						break;
					case "STARTS_WITH":
						$where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
						break;
					case "ENDS_WITH":
						$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
						break;
				}
								
				if ($i == $filterscount - 1)
				{
					$where .= ")";
				}
				
				$tmpfilteroperator = $filteroperator;
				$tmpdatafield = $filterdatafield;			
			}
			// build the query.
			$query = "
			SELECT 
				* 
			FROM 
				bibli_a_vacances
			".$where;
			
			$filterquery = $query;
			$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
			$sql = "SELECT FOUND_ROWS() AS `found_rows`;";
			$rows = mysql_query($sql);
			$rows = mysql_fetch_assoc($rows);
			$new_total_rows = $rows['found_rows'];		
			$query = "
			SELECT 
				* 
			FROM 
				bibli_a_vacances
			".$where." 
			LIMIT 
				$start, $pagesize";	
					
			$total_rows = $new_total_rows;	
		}
	}
	
	if (isset($_GET['sortdatafield']))
	{
	
		$sortfield = $_GET['sortdatafield'];
		$sortorder = $_GET['sortorder'];
		
		if ($sortorder != '')
		{
			if ($_GET['filterscount'] == 0)
			{
				if ($sortorder == "desc")
				{
					$query = "
					SELECT * FROM bibli_a_vacances
					WHERE estSupp_vacances = '0' ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
				}
				else if ($sortorder == "asc")
				{
					$query = "
					SELECT * FROM bibli_a_vacances
					WHERE estSupp_vacances = '0' ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
				}
			}
			else
			{
				if ($sortorder == "desc")
				{
					$filterquery .= " ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
				}
				else if ($sortorder == "asc")	
				{
					$filterquery .= " ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
				}
				$query = $filterquery;
			}		
		}
	}
	
	$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

	$orders = null;
/* */
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$numeroEtudiant = '<a href="../user/'.$row['uid_emprunt'].'/contact">'.$row['field_numero_tudiant_value'].'</a>';
			$prenomEtudiant = '<a href="../user/'.$row['uid_emprunt'].'">'.utf8_encode($row['field_prenom_value']).'</a>';
			$nomEtudiant = '<a href="../user/'.$row['uid_emprunt'].'">'.utf8_encode($row['field_nom_value']).'</a>';
			
			$employees[] = array(
				'id_vacances' => $row['id_vacances'],
				'debut_vacances' => $row['debut_vacances'],
				'fin_vacances' => $row['fin_vacances'],
				'estSupp_vacances' => $row['estSupp_vacances']
			  );
		}
				 
		$data[] = array(
		   'TotalRows' => $total_rows,
		   'Rows' => $employees
		);
		echo json_encode($data);
	}
?>