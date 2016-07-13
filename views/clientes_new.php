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
    $('#hdntype').val($('#cbotype').val());

    $('input').keyup(function(){
        $(this).val(function( i, val ) {
            return val.toUpperCase();
        });
    });
    $('textarea').keyup(function(){
        $(this).val(function( i, val ) {
            return val.toUpperCase();
        });
    });

    $("#moduleForm").validate({
        rules: {
            code: {
                required: true,
                minlength: 3,
                remote: {
                    url: <?php echo "'".$rootPath."?controller=clientes&action=verifyCodCliente'";?>,
                    type: "POST",
                    data: {
                        code: function() {
                            return $("#txtcodigo").val();
                        }
                    }
                }
            },
            name: {
                required: true,
                minlength: 3,
                remote: {
                    url: <?php echo "'".$rootPath."?controller=clientes&action=verifyNameCliente'";?>,
                    type: "POST",
                    data: {
                        name: function() {
                            return $("#txtnombre").val();
                        }
                    }
                }
            }
        },
        messages: {
            code: {required: "Campo requerido.", remote: "C&oacute;digo ya existe"},
            name: {required: "Campo requerido.",remote: "Nombre ya existe"}
        }
    });
    
    //arreglo de tipos segun combobox (filtro)
    tipos = $('#cbotype option').clone();
        
    //filtrar cada vez que se seleccione una nueva region
    $('#cboClass').change(function() {
        var classCliente = $('#cboClass').val();
    
        if(classCliente == "BY001"){
            autoSelectTipo(tipos, "CL1");
        }
        else if(classCliente == "BY002"){
            autoSelectTipo(tipos, "CL1");
        }
        else if(classCliente == "BY003"){
            autoSelectTipo(tipos, "CL1");
        }
        else if(classCliente == "BY004"){
            autoSelectTipo(tipos, "CL1");
        }
        else if(classCliente == "BY008"){
            autoSelectTipo(tipos, "CL1");
        }
        else if(classCliente == "BY009"){
            autoSelectTipo(tipos, "CL1");
        }
        else if(classCliente == "BY010"){
            autoSelectTipo(tipos, "CL1");
        }
        else if(classCliente == "BY011"){
            autoSelectTipo(tipos, "CL1");
        }
        else if(classCliente == "BY012"){
            autoSelectTipo(tipos, "CL1");
        }
        else{
            autoSelectTipo(tipos, "CL2");
        }
    });
});

function autoSelectTipo(tipos, option){
    $('#cbotype').empty();

    tipos.filter(function(idx, target) {
        return $(target).attr("value").indexOf(option) >= 0;
    }).appendTo('#cbotype');
}
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
                print_r($lista_tipos);
                print("<br />");
                print_r($lista_channels);
                print("<br />");
                print_r($lista_buyerclass);
                print("<br />");
                print_r($codigo_nuevo);
                print('</div>');
            }
            ?>
            <!-- END DEBUG -->

            <p class="titulos-form"><?php echo $titulo; ?></p>

            <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller=clientes&amp;action=clientesAdd';?>">
              <table width="457" height="118" border="0" align="center" class="texto">
                <tr>
                  <input name="formulario" type="hidden" value="SEGMENT" />
                  <td width="56">C&oacute;digo</td>
                  <td width="3">:</td>
                  <td width="380">
                      <?php if($session->privilegio == 1): ?>
                        <input name="code" type="text" id="txtcodigo" size="40"  value="<?php echo $codigo_nuevo; ?>" />
                      <?php else:?>
                        <input name="code" type="text" id="txtcodigo" size="40"  value="<?php echo $codigo_nuevo; ?>" disabled="disabled" />
                        <input name="code" type="hidden" value="<?php echo $codigo_nuevo; ?>"/>
                      <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td>Nombre</td>
                  <td>:</td>
                  <td>
                      <input name="name" type="text" id="txtnombre" class="inpt_left" size="40" />
                  </td>
                </tr>
                <tr>
                  <td>Buyer Class</td>
                  <td>:</td>
                  <td>
                        <?php
                        echo "<select id='cboClass' name='buyerclass'>\n";
                        while($row = $lista_buyerclass->fetch(PDO::FETCH_ASSOC)){
                            echo "<option value='$row[COD_BUYER_CLASS]'>$row[BUYER_CLASS_NAME]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Channel</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select name='channel'>\n";
                        while($row = $lista_channels->fetch(PDO::FETCH_ASSOC)){
                            echo "<option value='$row[COD_CHANNEL]'>$row[CHANNEL_NAME]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Tipo</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cbotype' disabled='disabled' name='type'>\n";
                        while($row = $lista_tipos->fetch(PDO::FETCH_ASSOC))
                        {
                            if($row['TIPO'] == 'CL1')
                                echo "<option value='$row[TIPO]'>Grandes</option>\n";
                            if($row['TIPO'] == 'CL2')
                                echo "<option value='$row[TIPO]'>Otros</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                        <input type="hidden" name="type" id="hdntype" value="" />
                  </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input name="usuario" type="hidden" value="<?php echo 'USUARIO'; ?>"/>
                        <?php $session->orig_timestamp = microtime(true); ?>
                        <input name="form_timestamp" type="hidden" value="<?php echo $session->orig_timestamp; ?>" />
                        <br />
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