<?php
	$verif = "select livres_id from devdrupal.bibli_livres where livres_numeroInventaire = '" . $numInventaire . "' and livres_estSupp = '0'";
	$queryverif = mysql_query($verif);
	if (!$queryverif) {
		echo 'Erreur MySQL : <br>' . $verif . '<br>' . mysql_error();
		exit;
	}
	else 
	{
		$rowverfi = mysql_fetch_array($queryverif);
		if (!empty($rowverfi)) 
		{
			$verif_idLivre = 'exist';
		}
		else
		{
			$verif_idLivre = '';
		}
	}
?>