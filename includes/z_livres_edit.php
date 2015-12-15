<?php
//Include the connect.php file
include('zBF_connect.php');

//Connect to the database
$connect = mysql_connect($hostname, $username, $password)or die('Could not connect: ' . mysql_error());

//Select The database
$bool = mysql_select_db($database, $connect);
if ($bool === False){
   print "can't find $database";
}

//Set var
if (empty($_GET['save'])) { $save = '';} else { $save = $_GET['save'];}
if (empty($_GET['livres_id'])) { $livres_id = '';} else { $livres_id = $_GET['livres_id'];}

//QuerySQL 4 retrieve info livres
$query = "SELECT * FROM bibli_livres WHERE livres_id = '".$livres_id."'"; #echo $query;
$result = mysql_query($query)or die("SQL Error * where Livres_id: " . mysql_error());
while ($array = mysql_fetch_array($result))
{
	$livres_id = $array['livres_id'];
	$livres_titre = $array['livres_titre'];
	$livres_nomAuteurs = $array['livres_nomAuteurs'];
	$livres_prenomAuteurs = $array['livres_prenomAuteurs'];
	$livres_matiere1 = $array['livres_matiere1'];
	$livres_matiere2 = $array['livres_matiere2'];
	$livres_matiere3 = $array['livres_matiere3'];
	$livres_numeroInventaire = $array['livres_numeroInventaire'];
	$livres_excluPret = $array['livres_excluPret'];
	$livres_livresS = $array['livres_livresS'];
	$livres_remarquesEtat = $array['livres_remarquesEtat'];
	$livres_isbn = $array['$livres_isbn'];
}
mysql_close();

//Test to modify Or not
if ( ($save != 'MODIFIER') and ($save != 'VALIDER') )
{
	echo  '	
	<table>
		<tr>
			<th>Titre</th>
			<td>'.$livres_titre.'</td>
		</tr>
		<tr>
			<th>Nom auteur</th>
			<td>'.$livres_nomAuteurs.'</td>
		</tr>
		<tr>
			<th>Prénom auteur</th>
			<td>'.$livres_prenomAuteurs.'</td>
		</tr>
		<tr>
			<th>Matiere 1</th>
			<td>'.$livres_matiere1.'</td>
		</tr>
		<tr>
			<th>Matiere 2</th>
			<td>'.$livres_matiere2.'</td>
		</tr>
		<tr>
			<th>Matiere 3</th>
			<td>'.$livres_matiere3.'</td>
		</tr>
		<tr>
			<th>N&deg; inventaire</th>
			<td>'.$livres_numeroInventaire.'</td>
		</tr>
		<tr>
			<th>Exclu du pr&ecirc;ts</th>
			<td>'.$livres_excluPret.'</td>
		</tr>
		<tr>
			<th>Livre "S"</th>
			<td>'.$livres_livresS.'</td>
		</tr>
		<tr>
			<th>ISBN</th>
			<td>'.$livres_isbn.'</td>
		</tr>
		<tr>
			<th>Remarques / &eacute;tat</th>
			<td>'.$livres_remarquesEtat.'</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<form>
			<td>
				<input type="submit" name="save" value="MODIFIER">
				<input type="hidden" name="livres_id" value="'.$livres_id.'">
			</td>
			</form>
			<td><a href="../node/8"><input type="submit" name="save" value="RETOUR LIVRES"></a></td>
		</tr>
	</table>';
}
else if ($save == 'MODIFIER')
{
	echo '<h2>Modification de la fiche du livre</h2>';
	echo  '	
	<table>
		<form>
			<tr>
				<th>Titre</th>
				<td><input type="text" name="livres_titre" value="'.$livres_titre.'"></td>
			</tr>
			<tr>
				<th>Nom auteur</th>
				<td><input type="text" name="livres_nomAuteurs" value="'.$livres_nomAuteurs.'"></td>
			</tr>
			<tr>
				<th>Prénom auteur</th>
				<td><input type="text" name="livres_prenomAuteurs" value="'.$livres_prenomAuteurs.'"></td>
			</tr>
			<tr>
				<th>Matiere 1</th>
				<td><input type="text" name="livres_matiere1" value="'.$livres_matiere1.'"></td>
			</tr>
			<tr>
				<th>Matiere 2</th>
				<td><input type="text" name="livres_matiere2" value="'.$livres_matiere2.'"></td>
			</tr>
			<tr>
				<th>Matiere 3</th>
				<td><input type="text" name="livres_matiere3" value="'.$livres_matiere3.'"></td>
			</tr>
			<tr>
				<th>N&deg; inventaire</th>
				<td><input type="text" name="livres_numeroInventaire" value="'.$livres_numeroInventaire.'"></td>
			</tr>
			<tr>
				<th>Exclu du pr&ecirc;ts</th>
				<td><input type="text" name="livres_excluPret" value="'.$livres_excluPret.'"></td>
			</tr>
			<tr>
				<th>Livre "S"</th>
				<td><input type="text" name="livres_livresS" value="'.$livres_livresS.'"></td>
			</tr>
			<tr>
				<th>ISBN</th>
				<td><input type="text" name="livres_isbn" value="'.$livres_isbn.'"></td>
			</tr>
			<tr>
				<th>Remarques / &eacute;tat</th>
				<td><input type="text" name="livres_remarquesEtat" value="'.$livres_remarquesEtat.'"></td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td>
					<input type="submit" name="save" value="VALIDER">
					<input type="hidden" name="livres_id" value="'.$livres_id.'">
				</td>
				</form>
				<td><a href="../node/8"><input type="submit" name="save" value="RETOUR LIVRES"></a></td>
			</tr>
	</table>';
}
if ($save == 'VALIDER')
{
	//Connect to the database
	$connect = mysql_connect($hostname, $username, $password)or die('Could not connect: ' . mysql_error());

	//Select The database
	$bool = mysql_select_db($database, $connect);
	if ($bool === False){
	   print "can't find $database";
	}
	
	//QuerySQL 4 retrieve info livres
	$livresExist = '';
	$query = "UPDATE `bibli_livres` SET `livres_titre`='".$_GET['livres_titre']."',
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
    echo($query);
	$result = mysql_query($query)or die("SQL Error update table blibli_livres:<br> " . mysql_error());
	mysql_close();
	echo "Fiche du livre ".$_GET['livres_numeroInventaire']." enregistr&eacute;e<br><br>";
	echo '<a href="../node/8"><input type="submit" name="save" value="RETOUR LIVRES"></a>';
}