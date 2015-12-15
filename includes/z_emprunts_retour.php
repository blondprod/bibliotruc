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

		/*SQL numero inventaire livre*/
		$sqlNumLivre = 'select * from bibli_livres where livres_numeroInventaire = "'.$_GET['numlivre'].'"';
		$queryNumLivre = mysql_query($sqlNumLivre) or die('Could not find livres_id:<br> ' . mysql_error());
		$livres_id = '';
		while ($arrayNumLivre = mysql_fetch_array($queryNumLivre)) { $livres_id = $arrayNumLivre['livres_id']; }
		#echo 'livres_id='.$livres_id.'<br>';
		
		/*SQL numero etudiant*/
		$sqlNumEtudiant = 'select * from bibli_field_data_field_numero_tudiant where field_numero_tudiant_value = "'.$_GET['numetudiant'].'"';
		$queryNumEtudiant = mysql_query($sqlNumEtudiant) or die('Could not find uid:<br> ' . mysql_error());
		$uid = '';
		while ($arrayNumEtudiant = mysql_fetch_array($queryNumEtudiant)) { $uid = $arrayNumEtudiant['entity_id']; }
		#echo 'UID='.$uid.'<br>';

		/*SQL update */
		if ((empty($uid)) or (empty($livres_id))) { echo '<font color="RED"><strong>numéro(s) inconnu(s)</strong></font>'; }
		else 
		{
			/*SQL verif livre est sorti */
			$sqlIdEmprunt = "select * from bibli_a_emprunt where estSupp_emprunt='0' and `id_livres_emprunt`='".$livres_id."' and `uid_emprunt`='".$uid."'";
			#echo $sqlIdEmprunt;
			$queryIdEmprunt = mysql_query($sqlIdEmprunt) or die('Could not find id_emprunt:<br> ' . mysql_error());
			$IdEmprunt = '';
			while ($arrayIdEmprunt = mysql_fetch_array($queryIdEmprunt)) { $IdEmprunt = $arrayIdEmprunt['id_emprunt']; }
			if (empty($IdEmprunt)) { echo '<font color="RED"><strong>numéro(s) inconnu(s)</strong></font>'; }
			else 
			{
				/* SQL UPDATE bibli_a_emprunt set estSupp_emprunt = 1 */
				$sqlUpdate = "update `devdrupal`.`bibli_a_emprunt` set `estSupp_emprunt`='1' where `id_livres_emprunt`='".$livres_id."' and `uid_emprunt`='".$uid."'";
				mysql_query($sqlUpdate) or die ('Could not update bibli_a_emprunt:<br> ' . mysql_error());
				echo "Livre rendu : Merci ;)";
			}			
		}
	}
}