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
        @import "views/css/jquery.tooltip.css";
        .dataTables_length {
            width: 28%;
        }
        .paging_full_numbers {
            width: 49%;
        }
        .ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
            float: none; 
        }
        .ui-dialog .ui-dialog-buttonpane button {
            margin: .5em .4em .5em 1.5em; 
            cursor: pointer; 
        }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min-custom.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.jeditable.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.tooltip.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    
    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'".$rootPath."?controller=models&action=ajaxModelsDt'";?>,
        "fnServerData": function ( sSource, aoData, fnDrawCallback ){
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

        //Custom filters params
        "fnServerParams": function ( aoData ){
            aoData.push(
                { "name": "filBrand", "value": $('#cboBrand').val() },
                { "name": "filBu", "value": $('#cboBu').val() },
                { "name": "filCategory", "value": $('#cboCategory').val() },
                { "name": "filGbu", "value": $('#cboGbu').val() },
                { "name": "filSegment", "value": $('#cboSegment').val() },
                { "name": "filEstado", "value": $('#cboEstado').val() },
                { "name": "filPremium", "value": $('#chkPremium:checked').length },
                { "name": "filAtribute", "value": $('#cboTarget').val() }
            );
        },

        //JEditable params
        "fnDrawCallback": function ( oSettings ) {
//            var thisRow = oTable.fnSettings().aoData[0]._aData;
//            var premiumVal = thisRow[9];
//            console.log(premiumVal);

            //Show editable rows if edition mode is ON
            if($('#modeToggler').val() == "desactivar")
            {
                //Editable selects for SEGMENT
                $('#example tbody td.editseg_select').editable( <?php echo "'".$rootPath."?controller=models&action=ajaxModelsEdit'";?>, {
                        "callback"      : function( sValue, y ) {
                                var aPos = oTable.fnGetPosition(this);
                                //console.log(oTable.fnGetData(aPos[0]));
                                //console.log(sValue);

                                oTable.fnDraw();
                        },
                        "submitdata"    : function (settings, self) {
                                var aPos = oTable.fnGetPosition( this );    
                                var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                                return {idData: aData[0], suffix: aData[1], gbu: aData[8], target_col: 1}; //take idData from first column
                        },
                        indicator : "Saving...",
                        tooltip   : "Click to change...",
                        loaddata  : function(value, settings) {
                                var aPos = oTable.fnGetPosition( this );    
                                var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                                return {current: value, gbu: aData[8]}
                        },
                        loadurl   : <?php echo "'".$rootPath."?controller=segments&action=listSegmentsJSON'";?>,
                        type      : "select",
                        submit    : "OK",
                        height    : "14px"
                });

                //Editable selects for SUB SEGMENT
                $('#example tbody td.editsub_select').editable( <?php echo "'".$rootPath."?controller=models&action=ajaxModelsEdit'";?>, {
                        callback      : function( sValue, y ) {
                                var aPos = oTable.fnGetPosition(this);
                                //console.log(oTable.fnGetData(aPos[0]));
                                //console.log(sValue);

                                oTable.fnDraw();
                        },
                        submitdata    : function (settings, self) {
                                var aPos = oTable.fnGetPosition( this );    
                                var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                                return {idData: aData[0], suffix: aData[1], gbu: aData[8], target_col: 2}; //take idData from first column
                        },
                        indicator : "Saving...",
                        tooltip   : "Click to change...",
                        loaddata  : function(value, settings, self) {
                                var aPos = oTable.fnGetPosition( this );    
                                var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;

                                return {current: value, gbu: aData[8]}
                        },
                        loadurl   : <?php echo "'".$rootPath."?controller=segments&action=listSubSegmentsJSON'";?>,
                        type      : "select",
                        submit    : "OK",
                        height    : "14px"
                } );

                //Editable selects for MICRO SEGMENT
                $('#example tbody td.editmicro_select').editable( <?php echo "'".$rootPath."?controller=models&action=ajaxModelsEdit'";?>, {
                        callback      : function( sValue, y ) {
                                var aPos = oTable.fnGetPosition(this);
                                //console.log(oTable.fnGetData(aPos[0]));
                                //console.log(sValue);

                                oTable.fnDraw();
                        },
                        submitdata    : function (settings, self) {
                                var aPos = oTable.fnGetPosition( this );    
                                var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                                return {idData: aData[0], suffix: aData[1], gbu: aData[8], target_col: 3}; //take idData from first column
                        },
                        indicator : "Saving...",
                        tooltip   : "Click to change...",
                        loaddata  : function(value, settings) {
                            var aPos = oTable.fnGetPosition( this );    
                            var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                            return {current: value, gbu: aData[8]}
                        },
                        loadurl   : <?php echo "'".$rootPath."?controller=segments&action=listMicroSegmentsJSON'";?>,
                        type      : "select",
                        submit    : "OK",
                        height    : "14px"
                } );

                //Editable selects for BRAND
                $('#example tbody td.editbrand_select').editable( <?php echo "'".$rootPath."?controller=models&action=ajaxModelsEdit'";?>, {
                        callback      : function( sValue, y ) {
                                //var aPos = oTable.fnGetPosition(this);
                                //console.log(oTable.fnGetData(aPos[0]));
                                //console.log(sValue);

                                oTable.fnDraw();
                        },
                        submitdata    : function (settings, self) {
                                var aPos = oTable.fnGetPosition( this );    
                                var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                                return {idData: aData[0], suffix: aData[1], gbu: aData[8], target_col: 5}; //take idData from first column
                        },
                        indicator : "Saving...",
                        tooltip   : "Click to change...",
                        loaddata  : function(value, settings) {
                            return {current: value}
                        },
                        loadurl   : <?php echo "'".$rootPath."?controller=brands&action=listBrandsJSON'";?>,
                        type      : "select",
                        submit    : "OK",
                        height    : "14px"
                } );

                //Editable selects for ESTADO
                <?php if ($permiso_mod_estado['VER'] == 1): ?>
                $('#example tbody td.editestado_select').editable( <?php echo "'".$rootPath."?controller=models&action=ajaxModelsEdit'";?>, {
                        "callback"      : function( sValue, y ) {
                                //var aPos = oTable.fnGetPosition(this);
                                //console.log(oTable.fnGetData(aPos[0]));
                                //console.log(sValue);

                                oTable.fnDraw();
                        },
                        "submitdata"    : function (settings, self) {
                                var aPos = oTable.fnGetPosition( this );    
                                var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                                return {idData: aData[0], suffix: aData[1], gbu: aData[8], target_col: 6}; //take idData from first column
                        },
                        indicator : "Saving...",
                        tooltip   : "Click to change...",
                        loaddata  : function(value, settings) {
                            return {current: value}
                        },
                        loadurl   : <?php echo "'".$rootPath."?controller=models&action=listEstadosJSON'";?>,
                        type      : "select",
                        submit    : "OK",
                        height    : "14px"
                });
                <?php endif; ?>
            }
        },

        //Col params
        "bAutoWidth": false,
        "aoColumnDefs": [
            { "sClass": "editseg_select", "aTargets": [ 2 ] },
            { "sClass": "editsub_select", "aTargets": [ 3 ] },
            { "sClass": "editmicro_select", "aTargets": [ 4 ] },
            { "sClass": "editbrand_select", "aTargets": [ 6 ] },
            { "sClass": "editestado_select", "aTargets": [ 7 ] },
            { "sWidth": "20%", "aTargets": [ 0 ] },
            { "sWidth": "20%", "aTargets": [ 1 ] },
            { "sWidth": "10%", "aTargets": [ 2 ] },
            { "sWidth": "10%", "aTargets": [ 3 ] },
            { "sWidth": "10%", "aTargets": [ 4 ] },
            { "sWidth": "10%", "aTargets": [ 5 ] },
            { "sWidth": "10%", "aTargets": [ 6 ] },
            { "sWidth": "7%", "aTargets": [ 7 ] },
            { "sWidth": "3%", "aTargets": [ 7 ] },
            { "bVisible": false, "aTargets": [8] },
            { "mDataProp": null, "aTargets": [-1,-2] },
            { "bSortable": false, "aTargets": [-1,-2] },
            { "bVisible": false, "aTargets": [-1,-2] },
            {
                "fnRender": function ( oObj ) {
                    var str_inputs = '<input type=\"checkbox\" class=\"chk_row\" name=\"item_row[]\" value="'+oObj.aData[0]+'">';
                    
                    return str_inputs;
                    },
                    "bUseRendered": true,
                    "aTargets": [-1]
            },
            {
                "fnRender": function ( oObj ) {
                    var str_value = oObj.aData[9];
                    if(str_value == null)
                        str_value = '0';
                    
                    /* Atributos llegan separados por coma para cada modelo
                     * por esto es necesario buscar en esa serie los codigos 
                     * que se necesiten.
                     * ej: buscar premium (cod_atribute=1): str_value.indexOf('1')
                     * si se encuentra el indice deberia ser >= 0.
                     */
                    if(str_value.indexOf('1') >= 0)
                        var str_inputs = '<span id="premium_ico" class="ui-icon ui-icon-star" style="float: left;" title="Modelo premium."></span>'+oObj.aData[0];
                    else
                        var str_inputs = oObj.aData[0];
                    
                    return str_inputs;
                    },
                    "bUseRendered": false,
                    "aTargets": [0]
            }
            <?php if($permiso_editar == 0): ?>
            ,{ "bVisible": false, "aTargets": [-1] }
            <?php endif; ?>
        ],
        "sPaginationType": "full_numbers"
    });

    // Masive edition buttons div
    var sOut = "";
    <?php if ($permiso_mod_estado['VER'] == 1): ?>
        sOut += "<button id='btn_edit_activar' class='input' style='height: 25px; margin-right: 5px;'>activar</button>";
        sOut += "<button id='btn_edit_desactivar' class='input' style='height: 25px; margin-right: 5px;'>desactivar</button>";
    <?php endif; ?>
    <?php if ($permiso_mod_premium['VER'] == 1): ?>
        sOut += "<button id='btn_edit_premium' title='Marcar o desmarcar un modelo como premium.' class='input' style='height: 25px;'>premium</button>";
    <?php endif; ?>    
    $("div.edit_buttons").html(sOut);
    $("div.edit_buttons").css({'display': 'none','text-align': 'right', 'padding': '3px'});

    // Custom Filter events
    $('#cboBrand').change( function() { oTable.fnDraw(); } );
    $('#cboBu').change( function() { oTable.fnDraw(); } );
    $('#cboCategory').change( function() { oTable.fnDraw(); } );
    $('#cboGbu').change( function() { oTable.fnDraw(); } );
    $('#cboSegment').change( function() { oTable.fnDraw(); } );
    $('#cboEstado').change( function() { oTable.fnDraw(); } );
    $('#chkPremium').change( function() { oTable.fnDraw(); } );
    $('#cboTarget').change( function() { oTable.fnDraw(); } );

    $('#modeToggler').click(function (){
        // show/hide selection column
        fnShowHide(9);
        $("div.edit_buttons").toggle();

        // change button text
        if($('#modeToggler').val() == 'activar'){
            $('td.btn_edicion').css('background-color','yellow');
            $('#modeToggler').val('desactivar');
        }
        else if($('#modeToggler').val() == 'desactivar'){
            $('td.btn_edicion').css('background-color','#f5f5f5');
            $('#modeToggler').val('activar');
        }

        // check all selections
        $('#chkall_models').change(function(){
            if($('#chkall_models').is(':checked')){
                selectAllChks(true, "chk_row");
                $('#btn_edit_premium').attr('disabled', 'disabled'); 
            }
            else{
                selectAllChks(false, "chk_row");
                $('#btn_edit_premium').removeAttr('disabled');
            }
        });
        
        // redraw datatable
        oTable.fnDraw();
    });
    
    $('#btn_edit_activar').click(function(){
       $('#edit_type').val("1");

       return true
    });
    $('#btn_edit_desactivar').click(function(){
//       console.log("desactivar!");
       $('#edit_type').val("3");
       
       return true;
    });
    $('#btn_edit_premium').click(function(){
       $('#edit_type').val("");
       $('#premium_flag').val("1");

       return true
    });
    
    $('#btn_edit_premium').tooltip({
        delay: 0
    });    
    
    $('#dt_form').submit( function() {
        var sData = oTable.$('input').serialize();
        var action = "";

        if(sData != ''){
            if($('#edit_type').val() != ""){
//                console.log("edition");
                action = "modelsEditSelection";
                sData += "&edit_type="+$('#edit_type').val();
            }
            else if($('#premium_flag').val() != ""){
//                console.log("premium");
                action = "modelsPremiumSelection";
                sData += "&premium_flag="+$('#premium_flag').val();
            }

            var thetext = $.ajax({ type: "POST",
                url:"<?php echo $rootPath."?controller=models&action=";?>"+action,
                cache: false,
                async: false,
                data: sData
            }).responseText;

//            console.log(thetext);

            oTable.fnDraw();
            $('#chkall_models').attr("checked",false);

            return false;
        }
        else{
            return false;
        }
    });

    $("#exp_excel").click(function(e){
        var link = $(this).attr("href");
        link = "<?php echo $rootPath."?controller=models&action=exportToExcelFiltered";?>";

        var filBrand = $('#cboBrand').val();
        var filBu = $('#cboBu').val();
        var filCategory = $('#cboCategory').val();
        var filGbu = $('#cboGbu').val();
        var filSegment = $('#cboSegment').val();
        var filEstado = $('#cboEstado').val();
        var filPremium = $('#chkPremium:checked').length;
        var filAtribute = $('#cboTarget').val();
        
        var sSearch = $('#example_filter').children().children().val();

        $("#dialog-exp-models").css("visibility","visible");

        $("#dialog-exp-models").dialog({
            autoOpen: true,
            resizable: true,
            height: 320,
            modal: true,
            buttons: {
                "Exportar": function(){
                    var colData = $(this).find("input").serialize();
                    link += "&"+colData;
                    link += "&filBrand="+filBrand;
                    link += "&filBu="+filBu;
                    link += "&filCategory="+filCategory;
                    link += "&filGbu="+filGbu;
                    link += "&filSegment="+filSegment;
                    link += "&filEstado="+filEstado;
                    link += "&filPremium="+filPremium;
                    link += "&filAtribute="+filAtribute;
                    link += "&sSearch="+sSearch;

                    //console.log(link);

                    $( this ).dialog("close");
                    document.location = link;
                },
                "Cancelar": function(){
                    $( this ).dialog( "close" );
                }
            },
            beforeClose: function(event, ui){
                console.log($(this).children("input"));
            }
        });
        
        return false;
    });
    
//    $('#chk_premium').change(function(){
//        if($('#chk_premium').is(':checked')){
//            
//        }
//        
//        oTable.fnDraw();
//    });
});

// seleccionar todos los checkbox
function selectAllChks(status, target){
    $("input."+target).each(function(index, item){
        $(item).attr("checked", status);
    });
}

// mostrar u ocultar una columna
function fnShowHide(iCol){
    /* Get the DataTables object again - this is not a recreation, just a get of the object */
    var oTable = $('#example').dataTable();
     
    var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
    oTable.fnSetColumnVis( iCol, bVis ? false : true );
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

        <?php require('templates/dialogs.tpl.php'); #session & header ?>

        <!-- DEBUG -->
        <?php 
        if($debugMode)
        {
            print('<div id="debugbox">');

            print_r($titulo); print('<br />');
            print_r($lista_brands); print('<br />');print_r($lista_bu); print('<br />');
            print_r($lista_category); print('<br />');print_r($lista_gbu); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print($permiso_editar); print($permiso_exportar);

            print("<br/>");print("<br/>");
            
            print_r($permiso_mod_estado);
            
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
                <?php if($permiso_editar == 1): ?>
                <td class="btn_edicion">
                    Modo Edici&oacute;n <br />
                    <?php
                    echo " <input type='button' id='modeToggler' value='activar' />";
                    ?>
                </td>
                <?php endif; ?>
                <td>
                    Premium <input type="checkbox" id="chkPremium" name="chk_premium" style="height: 13px; width: 13px" />
                </td>
                <td>
                    Target <br /> 
                    <?php
                    echo "<select name='cboTarget' id='cboTarget'>\n";
                    echo "<option selected value=''>TODOS</option>";
                    while($row = $lista_targets->fetch(PDO::FETCH_ASSOC))
                    {
                        echo "<option value='$row[COD_ATRIBUTE]'>$row[NAME_ATRIBUTE]</option>\n";
                    }
                    echo "</select>\n";
                    ?>
                </td>
            </tr>
        </table>
        <table id="dt_filtres">
            <tr>
                <th>Brand</th>
                <th>BU</th>
                <th>Category</th>
                <th>GBU</th>
                <th>Segment</th>
                <th>Estado</th>
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
                <td>
                    <?php
                    echo "<select name='cboEstado' id='cboEstado'>\n";
                    echo "<option selected value=''>TODOS</option>";
                    while($row = $lista_estados->fetch(PDO::FETCH_ASSOC))
                    {
                        echo "<option value='$row[NAME_ESTADO]'>$row[NAME_ESTADO]</option>\n";
                    }
                    echo "</select>\n";
                    ?>
                </td>
            </tr>
        </table>
        <!-- END CUSTOM FILTROS -->

        <div id="edit_sign">&iexcl;edici&oacute;n activada!</div>

        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="POST">
                <table class="display" id="example">
                    <thead>
                        <tr class="headers">
                            <th>MODEL</th>
                            <th>MODEL SUFFIX</th>
                            <th>SEGMENT</th>
                            <th>SUB SEGMENT</th>
                            <th>MICRO SEGMENT</th>
                            <th>GBU</th>
                            <th>BRAND</th>
                            <th>ESTADO</th>
                            <th>GBU</th>
                            <th><input type="checkbox" id="chkall_models" class="chk_row" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty">Loading data from server</td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <input id="edit_type" type="hidden" name="edit_type" value="" />
                            <input id="premium_flag" type="hidden" name="premium_flag" value="" />
                        </td>
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