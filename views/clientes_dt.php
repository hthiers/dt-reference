<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id != null):

#privs
if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
	@import "views/css/datatable.css";
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min-custom.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'".$rootPath."?controller=clientes&action=ajaxClientesDt'";?>,
        "fnServerData": function ( sSource, aoData, fnDrawCallback ) {
            $.ajax({
                "dataType": "json",
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
        //Custom filters params
        "fnServerParams": function ( aoData ) {
            aoData.push(
                { "name": "filTipo", "value": $('#cboTipo').val() }
            );
        },
        "aaSorting": [[1, "asc"]],
        "aoColumnDefs": [
            {
                //asigna data en tabla
                "mDataProp": null,
                "aTargets": [-1]
                <?php if($permiso_editar == 0){ echo ',"bVisible": false, "aTargets": [5]';}?>
            },
            { "bVisible": false, "aTargets": [4] },
            { "bSortable": false, "aTargets": [5] },
            {
                "fnRender": function ( oObj ) {
                    return '<form \n\
                        method=\"post\" action=\"<?php echo $rootPath."?controller=".$controller."&amp;action=".$action."";?>\">\n\
                        <input type=\"hidden\" name=\"cod_cliente\" value="'+oObj.aData[0]+'">\n\
                        <input id=\"button\" class=\"input\" type=\"submit\" value="EDITAR"></form>';
                },
                "aTargets": [-1]
            }
        ],
        "sPaginationType": "full_numbers"
    });

    /* Add event listeners to the two range filtering inputs */
    $('#cboTipo').change( function() { oTable.fnDraw(); } );
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
            print_r($listado); print('<br />');
            print_r($lista_tipos); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print_r($permiso_editar); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <?php 
        if (isset($error_flag))
            if(strlen($error_flag) > 0)
                echo $error_flag;
        ?>

        <!-- CUSTOM FILTROS -->
        <table id="dt_filtres">
            <tr>
                <td>Tipo</td>
                <td>
                    <?php
                    echo "<select name='cboTipo' id='cboTipo'>\n";
                        echo "<option selected='selected' value=''>Todos</option>";
                    while($row = $lista_tipos->fetch(PDO::FETCH_ASSOC))
                    {
                        if($row['TIPO'] == 'CL1')
                            echo "<option value='$row[TIPO]'>Grandes</option>\n";
                        else
                            echo "<option value='$row[TIPO]'>Otros</option>\n";
                    }
                    echo "</select>\n";
                    ?>
                </td>
            </tr>
        </table>
        <!-- END CUSTOM FILTROS -->
        
        <!-- DATATABLE -->
        <div id="dynamic">
        <table class="display" id="example">
            <thead>
                <tr class="headers">
                    <th>COD CLIENTE</th>
                    <th>NAME CLIENTE</th>
                    <th>BUYER CLASS</th>
                    <th>CHANNEL</th>
                    <th>TIPO</th>
                    <th>OPTIONS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="11" class="dataTables_empty">Loading data from server</td>
                </tr>
            </tbody>
        </table>
        </div>
        <!-- END DATATABLE -->

        <div class="spacer"></div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>