<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id != null):

#privs
if($session->privilegio == 1 or $session->privilegio == 2):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
	@import "views/css/datatable.css";
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/ColReorder.min.js"></script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		var oTable = $('#example').dataTable({
                    "bProcessing": true,
                    "bServerSide": true,
                    "sAjaxSource": <?php echo "'".$rootPath."/controllers/ajaxModelsController.php'";?>,
                    "fnServerData": function ( sSource, aoData, fnDrawCallback ) {
                        $.ajax( {
                            "dataType": 'json', 
                            "type": "GET", 
                            "url": sSource, 
                            "data": aoData, 
                            "success": fnDrawCallback
                        });
                    },
                    "sDom": '<"top"lpfi>rt<"clear">',
                    "oLanguage": {
                        "sInfo": "_TOTAL_ records",
                        "sInfoEmpty": "0 records",
                        "sInfoFiltered": "(from _MAX_ total records)"
                    },
                    "fnServerParams": function ( aoData ) {
                        aoData.push( 
                            { "name": "filBrand", "value": $('#cboBrand').val() },
                            { "name": "filBu", "value": $('#cboBu').val() },
                            { "name": "filCategory", "value": $('#cboCategory').val() },
                            { "name": "filGbu", "value": $('#cboGbu').val() },
                            { "name": "filSegment", "value": $('#cboSegment').val() }
                        );
                    },
                    "fnDrawCallback": function ( oSettings ) {
			/* Need to redo the counters if filtered or sorted */
                        var k = 0;
                        var total = oSettings.aiDisplay.length + oSettings._iDisplayStart;
                        
                        for ( var i=oSettings._iDisplayStart, iLen=total ; i<iLen ; i++ )
                        {
                            $('td:eq(8)', oSettings.aoData[ oSettings.aiDisplay[k] ].nTr ).text( i+1 );
                            
                            k++;
                        }
                    },
                    "bAutoWidth": false,
                    "aoColumnDefs": [
                        {
                            //asigna data en tabla
                            "mDataProp": null,
                            "aTargets": [-1,-2]
                        },
                        {"bSortable": false, "aTargets": [-1,-2]},
                        {
                            "fnRender": function ( oObj ) {
                                return '<form \n\
                                    method=\"post\" action=\"<?php echo $rootPath."?controller=".$controller."&amp;action=".$action."";?>\">\n\
                                    <input type=\"hidden\" name=\"code\" value="'+oObj.aData[1]+'">\n\
                                    <input id=\"button\" class=\"input\" type=\"submit\" value="EDITAR"></form>';
                             },
                             "aTargets": [-1]
                        },
                        { "sWidth": "15%", "aTargets": [ 0 ] },
                        { "sWidth": "20%", "aTargets": [ 1 ] },
                        { "sWidth": "10%", "aTargets": [ 2 ] },
                        { "sWidth": "10%", "aTargets": [ 3 ] },
                        { "sWidth": "7%", "aTargets": [ 4 ] },
                        { "sWidth": "10%", "aTargets": [ 5 ] },
                        { "sWidth": "10%", "aTargets": [ 6 ] },
                        { "sWidth": "10%", "aTargets": [ 7 ] },
                        { "sWidth": "3%", "aTargets": [ 8 ] },
                        { "sWidth": "5%", "aTargets": [ 9 ] }
                    ]
		});

		/* Add event listeners to the two range filtering inputs */
		$('#cboBrand').change( function() { oTable.fnDraw(); } );
		$('#cboBu').change( function() { oTable.fnDraw(); } );
                $('#cboCategory').change( function() { oTable.fnDraw(); } );
                $('#cboGbu').change( function() { oTable.fnDraw(); } );
                $('#cboSegment').change( function() { oTable.fnDraw(); } );
	});
</script>

</head>
<body id="dt_example" class="ex_highlight_row">

<?php
require('templates/menu.tpl.php'); #banner & menu
?>
	<!-- CENTRAL -->
	<div id="central">
        <div id="contenido">

            <!-- DEBUG -->
            <?php 
            if($debugMode)
            {
                    print('<div id="debugbox">');
                    print_r($titulo); print('<br />');
                    print_r($lista_brands); print('<br />');print_r($lista_bu); print('<br />');
                    print_r($lista_category); print('<br />');print_r($lista_gbu); print('<br />');
                    print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
                    print('</div>');
            }
            ?>
            <!-- END DEBUG -->

            <p class="titulos-form"><?php echo $titulo; ?></p>

            <!-- ERRORES -->
            <?php
            if (isset($error_flag))
                    if(strlen($error_flag) > 0)
                            echo $error_flag;
            ?>
            <!-- END ERRORES -->

            
            <!-- CUSTOM FILTROS -->
            <table id="dt_filtres">
                <tr>
                    <th>Brand</th>
                    <th>BU</th>
                    <th>Category</th>
                    <th>GBU</th>
                    <th>Segment</th>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo "<select name='cboBrand' id='cboBrand'>\n";
                        echo "<option selected value=''>TODOS</option>";
                        while($row = $lista_brands->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[COD_BRAND]'>$row[NAME_BRAND]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo "<select name='cboBu' id='cboBu'>\n";
                        echo "<option selected value=''>TODOS</option>";
                        while($row = $lista_bu->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[COD_BU]'>$row[NAME_BU]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo "<select name='cboCategory' id='cboCategory'>\n";
                        echo "<option selected value=''>TODOS</option>";
                        while($row = $lista_category->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[COD_CATEGORY]'>$row[NAME_CATEGORY]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo "<select name='cboGbu' id='cboGbu'>\n";
                        echo "<option selected value=''>TODOS</option>";
                        while($row = $lista_gbu->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[COD_GBU]'>$row[NAME_GBU]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo "<select name='cboSegment' id='cboSegment'>\n";
                        echo "<option selected value=''>TODOS</option>";
                        while($row = $lista_segments->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[COD_SEGMENT]'>$row[NAME_SEGMENT]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                    </td>
                </tr>
            </table>
            <!-- END CUSTOM FILTROS -->

            <div id="dynamic">
            <table class="display" id="example">
                <thead>
                    <tr>
                        <th>MODEL</th>
                        <th>MODEL SUFFIX</th>
                        <th>SEGMENT</th>
                        <th>SUB SEGMENT</th>
                        <th>MICRO SEGMENT</th>
                        <th>GBU</th>
                        <th>BRAND</th>
                        <th>ESTADO</th>
                        <th>N°</th>
                        <th>OPTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10" class="dataTables_empty">Loading data from server</td>
                    </tr>
                </tbody>
            </table>
            </div>

            <div class="spacer"></div>

    	</div>
	</div>
	<!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>