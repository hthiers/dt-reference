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
    
    $("#moduleForm").validate();
    
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
    
    $('#hdnType').val(option);
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
                print_r($code);
                print("<br />");
                print_r($name);
                print("<br />");
                print_r($type);
                print("<br />");
                print_r($buyerclass);print("<br />");print_r($channel);
                print("<br />");print_r($lista_buyerclass);print("<br />");
                print_r($lista_channels);print("<br />");
                print('</div>');
            }
            ?>
            <!-- END DEBUG -->
        
	        <p class="titulos-form"><?php echo $titulo; ?></p>
        
            <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller=clientes&amp;action=clientesEdit';?>">
              <table class="texto">
                <tr>
                    <td>C&oacute;digo</td>
                    <td width="3">:</td>
                    <td>
                      <input class="required" minlength="1" name="code" type="text" id="txtcodigo" size="40"  value="<?php echo $code; ?>" readonly="readonly" />
                    </td>
                </tr>
                <tr>
                  <td>Nombre</td>
                  <td>:</td>
                  <td><input class="required" minlength="2" name="name" type="text" id="txtnombre" size="40" value="<?php echo $name; ?>" readonly="readonly" /></td>
                </tr>
                <tr>
                    <td>Buyer Class</td>
                    <td>:</td>
                    <td>
                        <?php
                        echo "<select id='cboClass' name='buyerclass'>\n";
                        while($row = $lista_buyerclass->fetch(PDO::FETCH_ASSOC))
                        {
                            if($row['COD_BUYER_CLASS'] == $buyerclass)
                                echo "<option selected='true' value='$row[COD_BUYER_CLASS]'>$row[BUYER_CLASS_NAME]</option>\n";
                            else
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
                        echo "<select id='cboClass' name='channel'>\n";
                        while($row = $lista_channels->fetch(PDO::FETCH_ASSOC))
                        {
                            if($row['COD_CHANNEL'] == $channel)
                                echo "<option selected='true' value='$row[COD_CHANNEL]'>$row[CHANNEL_NAME]</option>\n";
                            else
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
                        echo "<select id='cbotype' name='type' disabled='disabled'>\n";
                        while($row = $lista_tipos->fetch(PDO::FETCH_ASSOC))
                        {
                            $selected = "";
                            if($row['TIPO'] == $type)
                                $selected = "selected='selected'";
                            
                            if($row['TIPO'] == 'CL1')
                                echo "<option value='$row[TIPO]' ".$selected.">Grandes</option>\n";
                            if($row['TIPO'] == 'CL2')
                                echo "<option value='$row[TIPO]' ".$selected.">Otros</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                        <input type="hidden" name="type" id="hdntype" value="" />
                    </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <br />
                    <input name="old_code" type="hidden" id="oculto" value="<?php echo $code; ?>" />
                    <input name="old_name" type="hidden" id="oculto" value="<?php echo $name; ?>" />
                    <input name="type" type="hidden" id="hdnType" value="" />
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