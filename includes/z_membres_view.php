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
					 { name: 'uid'},
					 { name: 'mail'},
					 { name: 'name'},
					 { name: 'field_nom_value'},
					 { name: 'field_prenom_value'},
					 { name: 'field_numero_tudiant_value'},
					 { name: 'field_estprof_value'},
					 { name: 'field_caution_value'},
					 { name: 'field_enveloppes_value'}
                ],
				id: 'livres_id',
                url: '../includes/z_membres_data.php',
				
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
                    var data = "update=true&uid=" + rowdata.uid + "&mail=" + rowdata.mail + "&name=" + rowdata.name + "&field_numero_tudiant_value=" + rowdata.field_numero_tudiant_value;
					data = data + "&field_prenom_value=" + rowdata.field_prenom_value + "&field_nom_value=" + rowdata.field_nom_value;
					data = data + "&field_estprof_value=" + rowdata.field_estprof_value + "&field_caution_value=" + rowdata.field_caution_value + "&field_enveloppes_value=" + rowdata.field_enveloppes_value;
					
					$.ajax({
						dataType: 'json',
						url: '../includes/z_membres_data.php',
						data: data,
						success: function (data, status, xhr) {
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
                width: 700,
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
					  { text: 'Nom', datafield: 'field_nom_value', width: 100}
					  , { text: 'Prénom', datafield: 'field_prenom_value', width: 90}
					  , { text: 'Identifiant', datafield: 'field_numero_tudiant_value', width: 90}
					  , { text: 'e-mail', datafield: 'mail', width: 210 }
					  , { text: 'Prof.', datafield: 'field_estprof_value', width: 50 }
					  , { text: 'Caution', datafield: 'field_caution_value', width: 70 }
					  , { text: 'Enveloppes', datafield: 'field_enveloppes_value', width: 90 }
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
