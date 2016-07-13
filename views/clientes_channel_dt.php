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
            width: 34%;
        }
        .paging_full_numbers {
            width: 43%;
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

//IDEA PARA UN BOTON DE LIMPIAR INPUT DE FILTRO DE BUSQUEDA POR PALABRA------------
function clearFilter(){
    $('#example').value='';
    $('#example').focus();
};
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

        <table class="display" id="example">
            <thead>
                <tr class="headers">
                    <th>COD CHANNEL</th>
                    <th>NOMBRE CHANNEL</th>
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

                <td><?php echo $item['COD_CHANNEL'];?></td>
                <td><?php echo $item['CHANNEL_NAME'];?></td>
                <td>
                    <form method="post"  action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
                    <?php 
                        echo "<input name='code' type='hidden' value='$item[COD_CHANNEL]' />\n";
                        echo "<input name='name' type='hidden' value='$item[CHANNEL_NAME]' />\n";

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