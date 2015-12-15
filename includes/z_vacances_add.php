<form><h2>Début &nbsp;</h2>
	<select name="debut_jour">
    	<option value="01">1</option>
        <option value="02">2</option>
        <option value="03">3</option>
        <option value="04">4</option>
        <option value="05">5</option>
        <option value="06">6</option>
        <option value="07">7</option>
        <option value="08">8</option>
        <option value="09">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
        <option value="30">30</option>
        <option value="31">31</option>
    </select><br>
    <select name="debut_mois">
    	<option value="01">Janvier</option>
        <option value="02">Février</option>
        <option value="03">Mars</option>
        <option value="04">Avril</option>
        <option value="05">Juin</option>
        <option value="06">Juillet</option>
        <option value="07">Août</option>
        <option value="08">Septembre</option>
        <option value="09">Octobre</option>
        <option value="10">Novembre</option>
        <option value="12">Décembre</option>
    </select><br>
    <select name="debut_annee">
    	<option value="2013">2013</option>
    	<option value="2014">2014</option>
    	<option value="2015">2015</option>
    	<option value="2016">2016</option>
    	<option value="2017">2017</option>
    	<option value="2018">2018</option>
    	<option value="2019">2019</option>
    	<option value="2020">2020</option>
    	<option value="2021">2021</option>
    	<option value="2022">2022</option>
    </select>
    <br>
<form><h2>Fin &nbsp;</h2>
	<select name="fin_jour">
    	<option value="01">1</option>
        <option value="02">2</option>
        <option value="03">3</option>
        <option value="04">4</option>
        <option value="05">5</option>
        <option value="06">6</option>
        <option value="07">7</option>
        <option value="08">8</option>
        <option value="09">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
        <option value="30">30</option>
        <option value="31">31</option>
    </select><br>
    <select name="fin_mois">
    	<option value="01">Janvier</option>
        <option value="02">Février</option>
        <option value="03">Mars</option>
        <option value="04">Avril</option>
        <option value="05">Juin</option>
        <option value="06">Juillet</option>
        <option value="07">Août</option>
        <option value="08">Septembre</option>
        <option value="09">Octobre</option>
        <option value="10">Novembre</option>
        <option value="12">Décembre</option>
    </select><br>
    <select name="fin_annee">
    	<option value="2013">2013</option>
    	<option value="2014">2014</option>
    	<option value="2015">2015</option>
    	<option value="2016">2016</option>
    	<option value="2017">2017</option>
    	<option value="2018">2018</option>
    	<option value="2019">2019</option>
    	<option value="2020">2020</option>
    	<option value="2021">2021</option>
    	<option value="2022">2022</option>
    </select>
    <br>
    <input type="submit" value="Enregistrer" name="save"> <br>
</form>

<!-- PHP script -->
<?php
if (isset($_GET['save']))
{
	$debut = $_GET['debut_annee'].'-'.$_GET['debut_mois'].'-'.$_GET['debut_jour'];
	$fin = $_GET['fin_annee'].'-'.$_GET['fin_mois'].'-'.$_GET['fin_jour'];
	if ((empty($debut)) || (empty($fin))) { echo '<font color="RED"><strong>Merci d\'indiquer une date de d&eacute;but et/ou de fin de pr&eacute;riode de vacances</strong></font>'; }
	else 
	{
		/*Connection String*/
		include('zBF_connect.php');
		$connect = mysql_connect($hostname, $username, $password) or die('Could not connect: ' . mysql_error());

		/*Select The database*/
		$bool = mysql_select_db($database, $connect);
		if ($bool === False){ print "can't find $database"; }
		
		/*SQL verif isset vacances*/
		$sqlIsset = 'select id_vacances from bibli_a_vacances where (estSupp_vacances = "0") and (("'.date($debut).'" between debut_vacances and fin_vacances) or ("'.date($fin).'" between debut_vacances and fin_vacances))';
		#echo $sqlIsset;
		$queryIsset = mysql_query($sqlIsset) or die('Could not find vacances_id:<br> ' . mysql_error());
		$Isset = '';
		while ($arrayIsset = mysql_fetch_array($queryIsset)) { $Isset = $arrayIsset['id_vacances']; }
		if (!empty($Isset)) { echo '<p><font color="RED"><strong>Cette période est déjà enregistrée</strong></font><br>'; }	
		else {
			$sqlVacancesAdd = "insert into `devdrupal`.`bibli_a_vacances` ( `debut_vacances`, `fin_vacances`) values ( '".date($debut)."', '".date($fin)."')";
			mysql_query($sqlVacancesAdd);	
			echo "<p><strong>Enregistrement r&eacute;ussi";
		}	
	}
}