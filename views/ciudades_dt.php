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
        .dataTables_length {
            width: 32%;
        }
        .paging_full_numbers {
            width: 45%;
        }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min-custom.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
        $('#example').dataTable({
                "sDom": '<"top"lpfi>rt<"clear">',
                "oLanguage": {
                    "sInfo": "_TOTAL_ records",
                    "sInfoEmpty": "0 records",
                    "sInfoFiltered": "(from _MAX_ total records)"
                },
                "aoColumnDefs": [{
                    "bSortable": false, "aTargets": [2]
                    <?php if($permiso_editar == 0){ echo ',"bVisible": false, "aTargets": [2]';}?>
                }],
                "sPaginationType": "full_numbers"
        });
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
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print_r($controller);print("<br />");
            print_r($action);print("<br />");
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <!-- ERRORES/MENSAJES -->
        <p class="titulos-form"><?php echo $titulo; ?></p>

        <?php 
        if (isset($error_flag))
            if(strlen($error_flag) > 0)
                echo $error_flag;
        ?>
        <!-- END ERRORES/MENSAJES -->

        <table class="display" id="example">
            <thead>
                <tr class="headers">
                    <th>COD CIUDAD</th>
                    <th>NAME CIUDAD</th>
                    <th>OPTIONS</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // $listado es una variable asignada desde el controlador SegmentsController.		
            while($item = $listado->fetch(PDO::FETCH_ASSOC))
            {
            ?>
            <tr>

                <td><?php echo $item['COD_CIUDAD'];?></td>
                <td><?php echo $item['NOM_CIUDAD'];?></td>
                <td>
                    <form method="post"  action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
                    <?php 
                        echo "<input name='code' type='hidden' value='$item[COD_CIUDAD]' />\n";
                        echo "<input name='name' type='hidden' value='$item[NOM_CIUDAD]' />\n";
                        echo "<input name='code_b' type='hidden' value='$item[REGION_COD_REGION]' />\n";

                        echo "<input class='input' type='submit' value='EDITAR' />\n";
                    ?>
                    </form>
                </td>
            </tr>
            <?php
            }
            ?>
            </tbody>
        </table>

        <div class="spacer"></div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>