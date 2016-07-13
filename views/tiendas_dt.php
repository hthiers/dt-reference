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
<script type="text/javascript">
$(document).ready(function() {
    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'".$rootPath."?controller=tiendas&action=ajaxTiendasDt'";?>,
        "fnServerData": function ( sSource, aoData, fnDrawCallback ) {
            $.ajax({
                "dataType": 'json',
                "type": "GET",
                "url": sSource,
                "data": aoData,
                "success": fnDrawCallback
            });
        },
        //DOM mod params
        "sDom": '<"top"lpfi><"edit_buttons">rt<"clear">',
        "oLanguage": {
            "sInfo": "_TOTAL_ records",
            "sInfoEmpty": "0 records",
            "sInfoFiltered": "(from _MAX_ total records)"
        },
        //actions on draw
        "fnDrawCallback": function( oSettings, aoData ) {
//            $('#button').click(function(){
//                console.log("go to form");
//                
//                $('#action_type').val("edit_form");
//                $('#tienda_code').val($(this).val());
//
//                return false;
//            });

//            console.log();
        },
        //Custom filters params
        "fnServerParams": function ( aoData ){
            aoData.push(
                { "name": "filCliente", "value": $('#cboCliente').val() },
                { "name": "filRegion", "value": $('#cboRegion').val() },
                { "name": "filEstado", "value": $('#cboEstado').val() },
                { "name": "filTipo", "value": $('#chkTodos').val() }
            );
        },
        "aaSorting": [[1, "asc"]],
        "aoColumnDefs": [
            { "sWidth": "5%", "aTargets": [ 0 ] },
            { "sWidth": "15%", "aTargets": [ 1 ] },
            { "sWidth": "10%", "aTargets": [ 3 ] },
            { "sWidth": "10%", "aTargets": [ 4 ] },
            { "sWidth": "10%", "aTargets": [ 5 ] },
            { "sWidth": "10%", "aTargets": [ 6 ] },
            { "sWidth": "10%", "aTargets": [ 7 ] },
            { "sWidth": "10%", "aTargets": [ 8 ] },
            { "sWidth": "5%", "aTargets": [ 9 ] },
            { "sWidth": "5%", "aTargets": [ 10 ] },
            {
            "mDataProp": null, "aTargets": [-1,-2]
            <?php if($permiso_editar == 0){ echo ',"bVisible": false, "aTargets": [-1,-2]';}?>
            },
            {"bVisible": false, "aTargets": [2]},
            {"bSortable": false, "aTargets": [-1,-2]},
            {
                "fnRender": function ( oObj ) {
                    return '<button id=\"button\" class=\"input\" name=\"cod_tienda\" onclick=\"submitToForm()\" value="'+oObj.aData[0]+'">EDITAR</button>';
                    },
                    "aTargets": [-2]
            },
            {
                "fnRender": function ( oObj ) {
                    return '<input type=\"checkbox\" class=\"chk_row\" name=\"item_row[]\" value="'+oObj.aData[0]+'--'+oObj.aData[11]+'">';
                    },
                    "aTargets": [-1]
            }
        ],
        "sPaginationType": "full_numbers",
    });

    // Masive edition buttons div
    var sOut = "<button id='btn_edit_activar' class='input' style='height: 25px; margin-right: 5px;'>activar</button>";
    sOut += "<button id='btn_edit_desactivar' class='input' style='height: 25px;'>desactivar</button>";
    $("div.edit_buttons").html(sOut);
    $("div.edit_buttons").css({'text-align': 'right', 'padding': '3px'});

    /* Add event listeners to the two range filtering inputs */
    $('#cboCliente').change( function() { oTable.fnDraw(); } );
    $('#cboRegion').change( function() { oTable.fnDraw(); } );
    $('#cboEstado').change( function() { oTable.fnDraw(); } );

    /* 
    * Add event listener for opening and closing details
    * Note that the indicator for showing which row is open is not controlled by DataTables,
    * rather it is done here
    */
    $('#example tbody td').live('click', function (){
        var nTr = $(this).parents('tr')[0];
        var kids = $(this).children();
        
        // ignore custom columns with action objects (children elements)
        if(kids.length == 0){
            if ( oTable.fnIsOpen(nTr) )
            {
                /* This row is already open - close it */
                oTable.fnClose( nTr );
            }
            else
            {
                /* Open this row */
                oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
            }
        }
    });

    // check all selections
    $('#chkall_models').change(function(){
        if($('#chkall_models').is(':checked'))
            selectAllChks(true, "chk_row");
        else
            selectAllChks(false, "chk_row");
    });

    /* action buttons for form execution
     * 1=activa, 2=inactiva (cod_estado) 
     */
    $('#btn_edit_activar').click(function(){
       $('#edit_type').val("1");
       $('#action_type').val("selection");

       return true
    });
    
    $('#btn_edit_desactivar').click(function(){
       $('#edit_type').val("2");
       $('#action_type').val("selection");
       
       return true;
    });

    // form submition handling
    $('#dt_form').submit( function() {
        var sData = oTable.$('input').serialize();
        var actionType = $('#action_type').val();
        var urlAction = "";

//        console.log(actionType);
        
        if(actionType == 'edit_form'){
//            console.log("action: edit form");
            urlAction = "<?php echo $rootPath."?controller=".$controller."&amp;action=".$action."";?>";
            $('#action_type').val("");
            
            return true;
        }
        else if(actionType == 'selection'){
//            console.log("action: estado selection");
            urlAction = "<?php echo $rootPath."?controller=tiendas&action=tiendasEditSelection";?>";
            
            if(sData.length > 1){
                sData += "&edit_type="+$('#edit_type').val();
                
                var thetext = $.ajax({ type: "POST",
                    url: urlAction,
                    cache: false,
                    async: false,
                    data: sData
                }).responseText;

    //            console.log(thetext);

                $('#chkall_models').attr("checked",false);

                oTable.fnDraw();
            }
            
            return false;
        }
    });
    
    //arreglo de clientes segun combobox (filtro)
    clientes = $('#cboCliente option').clone();

    //filtrar cada vez que se seleccione una nueva region
    $('#chkTodos').change(function() {
        if($('#chkTodos').is(':checked')){
            $('#cboCliente').empty();
            
            clientes.filter(function(idx, target) {
                return $(target).attr("title").indexOf("ALL") >= 0 || $(target).attr("title").indexOf("CL1") >= 0;
            }).appendTo('#cboCliente');
        }
        else{
            $('#cboCliente').empty();
            
            clientes.appendTo('#cboCliente');
        }
    });
});

// seleccionar todos los checkbox
function selectAllChks(status, target){
    $("input."+target).each(function(index, item){
        $(item).attr("checked", status);
    });
}

/* Formating function for row details */
function fnFormatDetails ( oTable, nTr ){
    var aData = oTable.fnGetData( nTr );
    var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    sOut += '<tr><td style="border: 0;"><b>C&Oacute;DIGO BTK:</b> '+aData[11]+'</td>';
    sOut += '<td style="border: 0;"><b>DIRECCI&Oacute;N:</b> '+aData[2]+'</td>';
    sOut += '</tr>';
    sOut += '</table>';

    return sOut;
}

function submitToForm(){
//    console.log("EDITAR button clicked!");
                
    $('#action_type').val("edit_form");
//    $('#tienda_code').val($(this).val());

    return true;
}
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
                print_r($listado_clientes); print('<br />');
                print_r($listado_regiones); print('<br />');
                print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
                print($permiso_exportar); print($permiso_editar);
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
                    <?php if($permiso_exportar == 1): ?>
                    <td>
                        Exportar <br />
                        <?php echo "<a title='excel' id='exp_excel' href='?controller=".$controller."&amp;action=".$action_exp_excel."'><img alt='excel' src='views/img/excel07.png' /></a>"; ?>
                    </td>
                    <?php endif; ?>
                </tr>
            </table>
            <table id="dt_filtres">
                <tr>
                    <th>Clientes</th>
                    <th>Regi&oacute;n</th>
                    <th>Estado</th>
                </tr>
                <tr>
                    <td>
                        grandes <input id="chkTodos" title="filtro clientes" type="checkbox" name="chkTodos" value="todos" style="height: 13px; width: 13px; margin-right: 5px;" />

                        <?php
                        echo "<select class='cbo_max_width' name='cboCliente' id='cboCliente'>\n";
                        echo "<option selected title='ALL' value=''>TODOS</option>";
                        while($row = $listado_clientes->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[COD_CLIENTE]' title='$row[TIPO]'>$row[NOM_CLIENTE]</option>\n";
                            #echo "<option value='$row[NOM_CLIENTE]'>$row[NOM_CLIENTE]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo "<select name='cboRegion' id='cboRegion'>\n";
                        echo "<option selected value=''>TODOS</option>";
                        while($row = $listado_regiones->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[NOM_REGION]'>$row[NOM_REGION]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo "<select name='cboEstado' id='cboEstado'>\n";
                        echo "<option selected value=''>TODOS</option>";
                        while($row = $listado_estados->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[NOM_ESTADO]'>$row[NOM_ESTADO]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                    </td>
                </tr>
            </table>
            <!-- END CUSTOM FILTROS -->

            <!-- DATATABLE -->
            <div id="dynamic">
                <form id="dt_form" method="POST" action="<?php echo $rootPath."?controller=".$controller."&amp;action=".$action."";?>">
                    <table class="display" id="example">
                        <thead>
                            <tr class="headers">
                                <th>C&Oacute;DIGO</th>
                                <th>TIENDA</th>
                                <th>DIRECCI&Oacute;N</th>
                                <th>CLIENTE</th>
                                <th>AGRUPACI&Oacute;N</th>
                                <th>TIPO</th>
                                <th>COMUNA</th>
                                <th>CIUDAD</th>
                                <th>REGI&Oacute;N</th>
                                <th>ZONA</th>
                                <th>ESTADO</th>
                                <th>OPCIONES</th>
                                <th><input type="checkbox" id="chkall_models" class="chk_row" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="13" class="dataTables_empty">Loading data from server</td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td><input id="edit_type" type="hidden" name="edit_type" value="" /></td>
                            <td><input id="action_type" type="hidden" name="action_type" value="" /></td>
                            <td><input id="tienda_code" type="hidden" name="tienda_code" value="" /></td>
                        </tr>
                    </table>
                </form>
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