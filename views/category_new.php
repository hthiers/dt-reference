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
                code: {
                    required: true,
                    remote: {
                        url: <?php echo "'".$rootPath."?controller=categories&action=verifyCodeCategory'";?>,
                        type: "POST",
                        data: {
                            code: function() {
                                return $("#txtcodigo").val();
                            },
                            target: 2
                        }
                    }
                },
                name: {
                    required: true,
                    remote: {
                        url: <?php echo "'".$rootPath."?controller=categories&action=verifyNameCategory'";?>,
                        type: "POST",
                        data: {
                            name: function() {
                                return $("#txtnombre").val();
                            },
                            target: 2
                        }
                    }
                },
                code_b: {required: true}
            },
            messages: {
                code: {required: "Campo requerido.", remote: "C&oacute;digo ya existe"},
                name: {required: "Campo requerido.", remote: "Nombre ya existe"},
                code_b: {required: "Campo requerido."}
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
                print_r($titulo);
                print("<br />");
                print_r($controller);print("<br />");print_r($action);print("<br />");
                print_r($action_b);print("<br />");print_r($lista_bu);print("<br />");
                print('</div>');
            }
            ?>
            <!-- END DEBUG -->

            <p class="titulos-form"><?php echo $titulo; ?></p>

            <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
              <table width="457" height="118" border="0" align="center" class="texto">
                <tr>
                 <input name="formulario" type="hidden" value="SEGMENT" />
                  <td width="56">BU</td>
                  <td width="3">:</td>
                  <td width="380">
                      <?php
                        echo "<select class='required' id='cbobu' name='code_b'>\n";
                        echo "<option value='' selected='selected'>SELECCIONAR</option>\n";
                        while($row = $lista_bu->fetch(PDO::FETCH_ASSOC))
                        {
                                echo "<option value='$row[COD_BU]'>$row[NAME_BU]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                    <td>C&oacute;digo</td>
                  <td>:</td>
                  <td><input class="required" minlength="1" name="code" type="text" id="txtcodigo" size="40"  value="" /></td>
                </tr>
                <tr>
                  <td>Nombre</td>
                  <td>:</td>
                  <td><input class="required" minlength="2" name="name" type="text" id="txtnombre" size="40" /></td>
                </tr>
                <tr>
                  <td colspan="3">
                      <br />
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