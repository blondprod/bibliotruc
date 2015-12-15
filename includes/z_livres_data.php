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
		$update_query = "UPDATE `bibli_livres` SET `livres_titre`='".$_GET['livres_titre']."',
		`livres_nomAuteurs`='".$_GET['livres_nomAuteurs']."',
		`livres_prenomAuteurs`='".$_GET['livres_prenomAuteurs']."',
		`livres_matiere1`='".$_GET['livres_matiere1']."',
		`livres_matiere2`='".$_GET['livres_matiere2']."',
		`livres_matiere3`='".$_GET['livres_matiere3']."',
		`livres_numeroInventaire`='".$_GET['livres_numeroInventaire']."',
		`livres_excluPret`='".$_GET['livres_excluPret']."',
		`livres_livresS`='".$_GET['livres_livresS']."',
		`livres_isbn`='".$_GET['livres_isbn']."',
		`livres_remarquesEtat`='".$_GET['livres_remarquesEtat']."' WHERE `livres_id`='".$_GET['livres_id']."'";
		 $result = mysql_query($update_query) or die("SQL Error 1: " . mysql_error());
		 #echo $result;
	}
	else
	{
		$pagenum = $_GET['pagenum'];
		$pagesize = $_GET['pagesize'];
		$start = $pagenum * $pagesize;
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM bibli_livres WHERE livres_estSupp = '0' LIMIT $start, $pagesize";

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
			$where = " WHERE livres_estSupp = '0' AND (";
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
			$query = "SELECT * FROM bibli_livres ".$where;
			$filterquery = $query;
			$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
			$sql = "SELECT FOUND_ROWS() AS `found_rows`;";
			$rows = mysql_query($sql);
			$rows = mysql_fetch_assoc($rows);
			$new_total_rows = $rows['found_rows'];		
			$query = "SELECT * FROM bibli_livres ".$where." LIMIT $start, $pagesize";		
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
					$query = "SELECT * FROM bibli_livres WHERE livres_estSupp = '0' ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
				}
				else if ($sortorder == "asc")
				{
					$query = "SELECT * FROM bibli_livres WHERE livres_estSupp = '0' ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
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
/* $numeroEtudiant = '<a href="../user/'.$row['uid_emprunt'].'/contact">'.$row['field_numero_tudiant_value'].'</a>'; */
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$titre = '<a href="../node/12?livres_id='.$row['livres_id'].'">'.$row['livres_titre'].'</a>';
			$employees[] = array(
				'livres_id' => $row['livres_id'],
				'livres_titre' => $titre,
				'livres_nomAuteurs' => $row['livres_nomAuteurs'],
				'livres_prenomAuteurs' => $row['livres_prenomAuteurs'],
				'livres_matiere1' => $row['livres_matiere1'],
				'livres_matiere2' => $row['livres_matiere2'],
				'livres_matiere3' => $row['livres_matiere3'],
				'livres_numeroInventaire' => $row['livres_numeroInventaire'],
				'livres_excluPret' => $row['livres_excluPret'],
				'livres_livresS' => $row['livres_livresS'],
				'livres_remarquesEtat' => $row['livres_remarquesEtat'],
				'livres_isbn' => $row['livres_isbn'],
				'livres_estSupp' => $row['livres_estSupp']
			  );
		}
		 
		$data[] = array(
		   'TotalRows' => $total_rows,
		   'Rows' => $employees
		);
		echo json_encode($data);
	}
?>