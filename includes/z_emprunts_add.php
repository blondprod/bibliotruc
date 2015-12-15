<form>
	<input type="text" placeholder="N&deg; &eacute;tudiant" name="numetudiant"> <br>
	<input type="text" placeholder="N&deg; livre" name="numlivre"> <br>
    <input type="submit" value="Enregistrer" name="save"> <br>
</form>

<!-- PHP script -->
<?php
if (isset($_GET['save']))
{
	if ((empty($_GET['numetudiant'])) && (empty($_GET['numlivre']))) { echo '<font color="RED"><strong>Merci d\'indiquer un num&eacute;ro d\'&eacute;tudiant ou un num&eacute;ro de livre</strong></font>'; }
	else 
	{
		/*connection String*/
		include('zBF_connect.php');
		$connect = mysql_connect($hostname, $username, $password) or die('Could not connect: ' . mysql_error());

		/*Select The database*/
		$bool = mysql_select_db($database, $connect);
		if ($bool === False){ print "can't find $database"; }
	
		/*SQL numero livre exclu*/
		$sqlExclu = 'select * from bibli_livres where livres_numeroInventaire = "'.$_GET['numlivre'].'"';
		$queryExclu = mysql_query($sqlExclu) or die('Could not find livres_id:<br> ' . mysql_error());
		$Exclu = '';
		while ($arrayExclu = mysql_fetch_array($queryExclu)) { $Exclu = $arrayExclu['livres_excluPret']; }
		if ($Exclu == 'oui') { echo '<p><font color="RED"><strong>livre ne peut pas être prété</strong></font><br>'; }
		else
		{
			/*SQL numero inventaire livre*/
			$sqlNumLivre = 'select * from bibli_livres where livres_numeroInventaire = "'.$_GET['numlivre'].'"';
			$queryNumLivre = mysql_query($sqlNumLivre) or die('Could not find livres_id:<br> ' . mysql_error());
			$livres_id = '';
			while ($arrayNumLivre = mysql_fetch_array($queryNumLivre)) { $livres_id = $arrayNumLivre['livres_id']; }
			#echo 'Idlivre='.$livres_id.'<br>';
			if ((empty($livres_id)) or (empty($_GET['numlivre']))) { echo '<p><font color="RED"><strong>livre inexistant</strong></font><br>'; }
			else 
			{
				/*SQL numero etudiant*/
				$sqlNumEtudiant = 'select * from bibli_field_data_field_numero_tudiant where field_numero_tudiant_value = "'.$_GET['numetudiant'].'"';
				$queryNumEtudiant = mysql_query($sqlNumEtudiant) or die('Could not find uid:<br> ' . mysql_error());
				$uid = '';
				while ($arrayNumEtudiant = mysql_fetch_array($queryNumEtudiant)) { $uid = $arrayNumEtudiant['entity_id']; }
				#echo 'UID='.$uid.'<br>';
				if ((empty($uid)) or (empty($_GET['numetudiant']))) { echo '<p><font color="RED"><strong>membres inexistant</strong></font>';	}
				else
				{
					/*SQL livres sorti*/
					$sqlLivreSorti = 'select * from bibli_a_emprunt where estSupp_emprunt = "0" and id_livres_emprunt = "'.$livres_id.'"';
					$queryLivreSorti = mysql_query($sqlLivreSorti) or die('Could not find livre Sorti:<br> ' . mysql_error());
					$id_emprunt = '';
					while ($arrayLivreSorti = mysql_fetch_array($queryLivreSorti)) { $id_emprunt = $arrayLivreSorti['id_emprunt']; }
					#echo 'id_emprunt='.$id_emprunt.'<br>';
					if (!empty($id_emprunt)) { echo '<p><font color="RED"><strong>Livre d&eacute;j&agrave; sorti</strong></font>'; }
					else
					{
						/*SQL verif prof ou etudiant*/
						$sqlProf = 'select * from bibli_field_data_field_estprof where entity_id = "'.$uid.'"';
						$queryProf = mysql_query($sqlProf) or die('Could not find estProf:<br> ' . mysql_error());
						$Prof = '';
						while ($arrayProf = mysql_fetch_array($queryProf)) { $Prof = $arrayProf['field_estprof_value']; }
						#echo 'nbEmprunt='.$nbEmprunt.'<br>';
						if ($Prof == 1) { $quotasLivre = 4; $quotasDelai = 61; }
						else { $quotasLivre = 1; $quotasDelai = 21; }
						
						/*SQL verif caution*/
						$sqlCaution = 'select * from bibli_field_data_field_caution where entity_id = "'.$uid.'"';
						$queryCaution = mysql_query($sqlCaution) or die('Could not find estProf:<br> ' . mysql_error());
						$caution = '';
						while ($arrayCaution = mysql_fetch_array($queryCaution)) { $caution = $arrayCaution['field_caution_value']; }
						#echo 'nbEmprunt='.$nbEmprunt.'<br>';
						if ($caution != 1) { echo '<p><font color="RED"><strong>Ce membre n\'a pas vérsé la caution !</strong></font>'; }
						else
						{
							/*SQL nombre emprunts*/
							$sqlNbEmprunt = 'SELECT COUNT(id_livres_emprunt) AS nbEmprunt FROM bibli_a_emprunt WHERE estSupp_emprunt = "0" AND uid_emprunt = "'.$uid.'"';
							$queryNbEmprunt = mysql_query($sqlNbEmprunt) or die('Could not find livre Sorti:<br> ' . mysql_error());
							$nbEmprunt = '';
							while ($arrayNbEmprunt = mysql_fetch_array($queryNbEmprunt)) { $nbEmprunt = $arrayNbEmprunt['nbEmprunt']; }
							#echo 'nbEmprunt='.$nbEmprunt.'<br>';
							if ($nbEmprunt > $quotasLivre) { echo '<p><font color="RED"><strong>Ce membre &agrave; d&eacute;j&agrave; emprunt&eacute; '.($quotasLivre+1).' livres !</strong></font>'; }
							else
							{
								/*SQL livre S*/
								$sqlLivreS = "
								SELECT
									livres_livresS
								FROM
									bibli_a_emprunt
									JOIN bibli_livres ON (bibli_livres.livres_id = bibli_a_emprunt.id_livres_emprunt)
									JOIN bibli_users ON (bibli_users.uid = bibli_a_emprunt.uid_emprunt)
								WHERE
									estSupp_emprunt = 0
									AND uid = '".$uid."'";
								$LivreS = '';
								$queryLivreS = mysql_query($sqlLivreS) or die('Could not find livre S:<br> ' . mysql_error());
								while ($arrayLivreS = mysql_fetch_array($queryLivreS)) { $LivreS = $arrayLivreS['livres_livresS']; }
								if ($LivreS == 'oui') { echo '<p><font color="RED"><strong>Ce membre &agrave; d&eacute;j&agrave; emprunt&eacute;s un livre "S"</strong></font>'; }
								else
								{
									/*SQL Vacances*/
									$VerifdateRetour = date('Y-m-d', strtotime('+'.$quotasDelai.' day'));
									$sqlVerifVac = "select DATE_FORMAT(debut_vacances,'%a %d %b %Y') as debutVac , debut_vacances from bibli_a_vacances where estSupp_vacances = 0 and '".$VerifdateRetour."' between debut_vacances and fin_vacances";
									$queryVerifVac = mysql_query($sqlVerifVac) or die('Could not find vacances:<br> ' . mysql_error());
									while ($arrayVerifVac = mysql_fetch_array($queryVerifVac)) { $VerifVac = $arrayVerifVac['debutVac']; $dateretourok = $arrayVerifVac['debut_vacances'];}									
									if (empty($VerifVac)) { $dateRetour = "DATE_ADD(NOW(),INTERVAL ".$quotasDelai." DAY)"; $retourLivre = date('D d F Y', strtotime('+'.$quotasDelai.' day')); }
									else { $dateRetour = "'".$dateretourok."'"; $retourLivre = $VerifVac; }
									
									/*Insert emprunt to db*/
									$sqlInsertEmprunt = "
									INSERT INTO `devdrupal`.`bibli_a_emprunt` 
									( 
										`date_emprunt`
										, `id_livres_emprunt`
										, `uid_emprunt`
										, `dateRetour_emprunt`
									) 
									VALUES 
									( 
										NOW()
										, '".$livres_id."'
										, '".$uid."'
										, ".$dateRetour."
									)";
									mysql_query($sqlInsertEmprunt) or die (mysql_error());	
									echo "<p><strong>Enregistrement r&eacute;ussi ; retour le ".$retourLivre;
								}
							}
						}
					}
				}
			}
		}
	}
}