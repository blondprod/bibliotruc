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
		$update_query = "UPDATE `bibli_a_emprunt` SET `date_emprunt`='".$_GET['date_emprunt']."',
		`dateRetour_emprunt`='".$_GET['dateRetour_emprunt']."',
		`id_livres_emprunt`='".$_GET['id_livres_emprunt']."',
		`uid_emprunt`='".$_GET['uid_emprunt']."' WHERE `id_emprunt`='".$_GET['id_emprunt']."'";
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
			bibli_users 
			JOIN bibli_field_data_field_nom ON ( bibli_field_data_field_nom.entity_id = bibli_users.uid )
			JOIN bibli_field_data_field_prenom ON ( bibli_field_data_field_prenom.entity_id = bibli_users.uid )
			JOIN bibli_field_data_field_estprof ON ( bibli_field_data_field_estprof.entity_id = bibli_users.uid )
			JOIN bibli_field_data_field_caution ON ( bibli_field_data_field_caution.entity_id = bibli_users.uid )
			JOIN bibli_field_data_field_enveloppes ON ( bibli_field_data_field_enveloppes.entity_id = bibli_users.uid )
			JOIN bibli_field_data_field_numero_tudiant ON ( bibli_field_data_field_numero_tudiant.entity_id = bibli_users.uid )
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
			$where = " AND (";
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
				bibli_users 
				JOIN bibli_field_data_field_nom ON ( bibli_field_data_field_nom.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_prenom ON ( bibli_field_data_field_prenom.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_estprof ON ( bibli_field_data_field_estprof.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_caution ON ( bibli_field_data_field_caution.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_enveloppes ON ( bibli_field_data_field_enveloppes.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_numero_tudiant ON ( bibli_field_data_field_numero_tudiant.entity_id = bibli_users.uid )
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
				bibli_users 
				JOIN bibli_field_data_field_nom ON ( bibli_field_data_field_nom.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_prenom ON ( bibli_field_data_field_prenom.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_estprof ON ( bibli_field_data_field_estprof.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_caution ON ( bibli_field_data_field_caution.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_enveloppes ON ( bibli_field_data_field_enveloppes.entity_id = bibli_users.uid )
				JOIN bibli_field_data_field_numero_tudiant ON ( bibli_field_data_field_numero_tudiant.entity_id = bibli_users.uid )
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
					SELECT * FROM 			
						bibli_users 
						JOIN bibli_field_data_field_nom ON ( bibli_field_data_field_nom.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_prenom ON ( bibli_field_data_field_prenom.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_estprof ON ( bibli_field_data_field_estprof.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_caution ON ( bibli_field_data_field_caution.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_enveloppes ON ( bibli_field_data_field_enveloppes.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_numero_tudiant ON ( bibli_field_data_field_numero_tudiant.entity_id = bibli_users.uid )
					ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
				}
				else if ($sortorder == "asc")
				{
					$query = "
					SELECT * FROM 			
						bibli_users 
						JOIN bibli_field_data_field_nom ON ( bibli_field_data_field_nom.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_prenom ON ( bibli_field_data_field_prenom.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_estprof ON ( bibli_field_data_field_estprof.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_caution ON ( bibli_field_data_field_caution.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_enveloppes ON ( bibli_field_data_field_enveloppes.entity_id = bibli_users.uid )
						JOIN bibli_field_data_field_numero_tudiant ON ( bibli_field_data_field_numero_tudiant.entity_id = bibli_users.uid )
					ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
				}
			}
			else
			{
				if ($sortorder == "DESC")
				{
					$filterquery .= " ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
				}
				else if ($sortorder == "ASC")	
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
			$mail = '<a href="../user/'.$row['uid'].'/contact">'.$row['mail'].'</a>';
			$prenomEtudiant = '<a href="../user/'.$row['uid'].'">'.utf8_encode($row['field_prenom_value']).'</a>';
			$nomEtudiant = '<a href="../user/'.$row['uid'].'">'.utf8_encode($row['field_nom_value']).'</a>';
			if ($row['field_estprof_value'] == 1) {$estProf='oui';} else {$estProf='non';}
			if ($row['field_caution_value'] == 1) {$caution='oui';} else {$caution='non';}
			if ($row['field_enveloppes_value'] == 1) {$enveloppes='oui';} else {$enveloppes='non';}
			
			$employees[] = array(
				'uid' => $row['uid']
				, 'mail' => $mail
				, 'name' => $row['name']
				, 'field_nom_value' => $nomEtudiant
				, 'field_prenom_value' => $prenomEtudiant
				, 'field_estprof_value' => $estProf
				, 'field_caution_value' => $caution
				, 'field_enveloppes_value' => $enveloppes
				, 'field_numero_tudiant_value' => $row['field_numero_tudiant_value']
			  );
		}
				 
		$data[] = array(
		   'TotalRows' => $total_rows,
		   'Rows' => $employees
		);
		echo json_encode($data);
	}
?>