<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id != null):

#privs
if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<script language="javascript">
$(document).ready(function(){
    //uppercase obligatorio
    $('input').focusout(function(){
        $(this).val(function( i, val ) {
            return val.toUpperCase();
        });
    });
    $('textarea').focusout(function(){
        $(this).val(function( i, val ) {
            return val.toUpperCase();
        });
    });

    $("#moduleForm").validate({
        rules: {
            txtnombre: {
                required: true,
                remote: {
                    url: <?php echo "'".$rootPath."?controller=segments&action=verifyNameSegment'";?>,
                    type: "POST",
                    data: {
                        txtnombre: function() {
                            return $("#txtnombre").val();
                        },
                        target: 3,
                        old_name: function() {
                            return $("#old_name").val();
                        }
                    }
                }
            },
            txtgbu: {required: true},
            txtcodigo: {required: true}
        },
        messages: {
            txtnombre: {required: "Campo requerido.", remote: "Nombre ya existe"}
        }
    });
});
</script>

</head>
<body>

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
                    print_r($code);print("<br />");
                    print_r($name);print("<br />");
                    print_r($cod_gbu);print('<br />');
                    print_r($lista_gbu);print('</div>');
                    
                }
                ?>
            <!-- END DEBUG -->
        
	        <p class="titulos-form"><?php echo $titulo; ?></p>
        
            <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller=segments&amp;action=microSegmentsEdit';?>">
              <table width="457" height="118" border="0" align="center" class="texto">
                <tr>
                    <td width="56">C&oacute;digo</td>
                  <td width="3">:</td>
                  <td width="380"><input class="required" minlength="1" name="txtcodigo" type="text" id="txtcodigo" size="40"  value="<?php echo $code; ?>" readonly="readonly"/></td>
                </tr>
                <tr>
                  <td>Nombre</td>
                  <td>:</td>
                  <td><input class="required" minlength="1" name="txtnombre" type="text" id="txtnombre" size="40" value="<?php echo $name; ?>" /></td>
                </tr>
                <tr>
                    <td>C&oacute;digo GBU</td>
                  <td>:</td>
                  <td>
                    <?php
                    echo "<select name ='txtgbu'>\n";
                    #echo "<option>--Select GBU--</option>\n";
                    while($row = $lista_gbu->fetch(PDO::FETCH_ASSOC))
                    {
                            if($row['COD_GBU'] == $cod_gbu)
                                    echo "<option name='txtgbu' selected='true' value='$row[COD_GBU]'>$row[NAME_GBU]</option>\n";
                            else
                                    echo "<option name='txtgbu' value='$row[COD_GBU]'>$row[NAME_GBU]</option>\n";
                    }
                    echo "</select>\n";
                    ?>
                    <input name="old_code" type="hidden" id="old_code" value="<?php echo $code; ?>" />
                    <input name="old_name" type="hidden" id="old_name" value="<?php echo $name; ?>" />
                    <input name="old_gbu" type="hidden" id="old_gbu" value="<?php echo $cod_gbu; ?>" />
                    <?php $session->orig_timestamp = microtime(true); ?>
                    <input name="form_timestamp" type="hidden" value="<?php echo $session->orig_timestamp; ?>" />
                  </td>
                </tr>
                <tr>
                    <td colspan="3" class="submit">
                        <input name="Atras" type="reset" class="input" id="Atras"  onclick="window.location = '<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action_b.'';?>'"  value="Cancelar" />
                        &nbsp;&nbsp;
                        <input name="button" type="submit" class="input" id="button" value="Guardar" />
                    </td>
                </tr>
              </table>
            </form>
        
        </div>
	</div>
    <!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>