<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../includes/jqwidgets/styles/jqx.base.css" type="text/css" />
    <link rel="stylesheet" href="../includes/jqwidgets/styles/jqx.classic.css" type="text/css" />
    <script type="text/javascript" src="../includes/jqwidgets/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxmenu.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxcheckbox.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxlistbox.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxdropdownlist.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxgrid.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxdata.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxgrid.selection.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxgrid.edit.js"></script>
    <script type="text/javascript" src="../includes/jqwidgets/jqxgrid.pager.js"></script>	
	<script type="text/javascript" src="../includes/jqwidgets/jqxgrid.filter.js"></script>		
	<script type="text/javascript" src="../includes/jqwidgets/jqxgrid.sort.js"></script>		
    <script type="text/javascript">
        $(document).ready(function () {
            // prepare the data
            var data = {};
			var theme = 'classic';

            var source =
            {
                 datatype: "json",
                 datafields: [
					 { name: 'livres_id'},
					 { name: 'livres_titre'},
					 { name: 'livres_nomAuteurs'},
					 { name: 'livres_prenomAuteurs'},
					 { name: 'livres_matiere1'},
					 { name: 'livres_matiere2'},
					 { name: 'livres_matiere3'},
					 { name: 'livres_numeroInventaire'},
					 { name: 'livres_excluPret'},
					 { name: 'livres_livresS'},
					 { name: 'livres_remarquesEtat'},
					 { name: 'livres_isbn'},
					 { name: 'livres_estSupp'}
                ],
				id: 'livres_id',
                url: '../includes/z_livres_data.php',
				
				/* */
				filter: function()
				{
					// update the grid and send a request to the server.
					$("#jqxgrid").jqxGrid('updatebounddata');
				},
				sort: function()
				{
					// update the grid and send a request to the server.
					$("#jqxgrid").jqxGrid('updatebounddata');
				},
				/* */				
				
				root: 'Rows',
				beforeprocessing: function(data)
				{		
					source.totalrecords = data[0].TotalRows;
				},		
						
                updaterow: function (rowid, rowdata, commit) {
			        // synchronize with the server - send update command
                    var data = "update=true&livres_titre=" + rowdata.livres_titre + "&livres_nomAuteurs=" + rowdata.livres_nomAuteurs + "&livres_prenomAuteurs=" + rowdata.livres_prenomAuteurs;
					data = data + "&livres_matiere1=" + rowdata.livres_matiere1 + "&livres_matiere2=" + rowdata.livres_matiere2  + "&livres_matiere3=" + rowdata.livres_matiere3 + "&livres_numeroInventaire=" + rowdata.livres_numeroInventaire;
					data = data + "&livres_excluPret=" + rowdata.livres_excluPret + "&livres_livresS=" + rowdata.livres_livresS  + "&livres_remarquesEtat=" + rowdata.livres_remarquesEtat + "&livres_isbn=" + rowdata.livres_isbn;
					data = data + "&livres_id=" + rowdata.livres_id;
                    console.log(data);

					$.ajax({
						dataType: 'json',
						url: '../includes/z_livres_data.php',
						data: data,
						success: function (data, status, xhr) {
                            console.log(data);
							// update command is executed.
							commit(true);
						}
					});		
                }
				
            };
			
 		    var dataadapter = new $.jqx.dataAdapter(source);
           // initialize jqxGrid
            $("#jqxgrid").jqxGrid(
            {
                width: 1100,
				selectionmode: 'singlecell',
                source: dataadapter,
                theme: theme,
				filterable: true,
				sortable: true,
				editable: true,
				autoheight: true,
				pageable: true,
				virtualmode: true,
				rendergridrows: function()
				{
					  return dataadapter.records;     
				},
                columns: [
                      { text: 'Titre', datafield: 'livres_titre', width: 130 },
                      { text: 'Nom', datafield: 'livres_nomAuteurs', width: 100 },
                      { text: 'Prénom', datafield: 'livres_prenomAuteurs', width: 100 },
                      { text: 'Matiere 1', datafield: 'livres_matiere1', width: 80 },
                      { text: 'Matiere 2', datafield: 'livres_matiere2', width: 80 },
					  { text: 'Matiere 3', datafield: 'livres_matiere3', width: 80 },
                      { text: 'N° inventaire', datafield: 'livres_numeroInventaire', width: 100 },
					  { text: 'Exclu du prêt', datafield: 'livres_excluPret', width: 100 },
					  { text: 'Livre "S"', datafield: 'livres_livresS', width: 100 },
					  { text: 'ISBN', datafield: 'livres_isbn', width: 100 },
                      { text: 'Remarques / état', datafield: 'livres_remarquesEtat', width: 130 }
                  ]
            });
        });
    </script>
</head>
<?php
$resu = "
<body class='default'>
	<div id='jqxgrid'>
	</div>
</body>";
echo $resu;

?>
</html>
