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

    //campos desactivados dependientes de otros
    if ($('#cbocliente')[0].selectedIndex == 0) {
        $('#txtnombre').attr('disabled', true);
    }
    if ($('#cboregion')[0].selectedIndex == 0) {
        $('#cbociudad').attr('disabled', true);
        $('#cbocomuna').attr('disabled', true);
    }

    //arreglo de ciudades y comunas (variables globales)
    cities = $('#cbociudad option').clone();
    comunas = $('#cbocomuna option').clone();

    //filtrado inicial de cbo (ciudad y comuna)
    loadCbos();

    //habilitar campos solo si hay un cliente seleccionado
    $('#cbocliente').change(function() {
        if ($('#cbocliente')[0].selectedIndex == 0) {
            $('#txtprenombre').val("");
            $('#txtnombre').attr('disabled', true);
        } else {
            $('#cbocliente option:selected').each(function(){
                $('#txtprenombre').val($(this).text());
                $('#hdnprename').val($(this).text());
            })

            $('#txtnombre').removeAttr('disabled');
        }
    });

    //filtrar cada vez que se seleccione una nueva region
    $('#cboregion').change(function() {
            if ($('#cboregion')[0].selectedIndex == 0) {
                $('#cbociudad').attr('disabled', true);
                $('#cbocomuna').attr('disabled', true);
            } else {
                $('#cbociudad').removeAttr('disabled');
                $('#cbocomuna').removeAttr('disabled');
            }

            var value = $(this).val();
            $('#cbociudad').empty();

            cities.filter(function(idx, target) {
                    return value == '' || $(target).attr("title").indexOf(value) >= 0;
            }).appendTo('#cbociudad');

            //filtrar comuna segun ciudad filtrada
            var valueCity = $("#cbociudad").val();
            $('#cbocomuna').empty();

            comunas.filter(function(idx, target) {
                    return valueCity == '' || $(target).attr("title").indexOf(valueCity) >= 0;
            }).appendTo('#cbocomuna');
    });

    //filtrar cada vez que se seleccione una nueva ciudad
    $('#cbociudad').change(function() {
            var value = $(this).val();
            $('#cbocomuna').empty();

            comunas.filter(function(idx, target) {
                    return value == '' || $(target).attr("title").indexOf(value) >= 0;
            }).appendTo('#cbocomuna');
    });

    function loadCbos(){
            /*
            * Filtro ciudades
            */
            //valor primera opci&oacute;n en cbo de regiones
            var region = $('#cboregion').val();

            //limpiar el cbo de ciudades para volver a construirlo
            $('#cbociudad').empty();

            //filtrar cbo con ciudades que coincidan con la regi&oacute;n en su "title"
            cities.filter(function(idx, target) {
                    return region == '' || $(target).attr("title").indexOf(region) >= 0;
            }).appendTo('#cbociudad');

            /*
            * Filtro comunas
            */
            var ciudad = $('#cbociudad').val();
            $('#cbocomuna').empty();
            comunas.filter(function(idx, target) {
                    return ciudad == '' || $(target).attr("title").indexOf(ciudad) >= 0;
            }).appendTo('#cbocomuna');
    }

    var validator = $("#moduleForm").validate({
        /*
        groups: {btkcliente: "cod_btk cod_cliente"},
        errorPlacement: function(error, element){
            if(element.attr("name") == "cod_btk" || element.attr("name") == "cod_cliente"){
                error.insertAfter("#cod_btk");
                error.insertAfter("#cbocliente");
            }
            else
                error.insertAfter(element);
        },
        */
        rules: {
            cod_tienda: {required: true},
            cod_btk: {
                required: true,
                remote: {
                    url: <?php echo "'".$rootPath."?controller=tiendas&action=verifyCodBTKbyCliente'";?>,
                    type: "POST",
                    data: {
                        cod_cliente: function() {
                            return $("#cbocliente").val();
                        }
                    }
                }
            },
            name: {
                required: true,
                remote: {
                    url: <?php echo "'".$rootPath."?controller=tiendas&action=verifyNameTienda'";?>,
                    type: "POST",
                    data: {
                        name: function() {
                            return $("#txtnombre").val().toUpperCase();
                        },
                        prename: function() {
                            return $("#txtprenombre").val().toUpperCase();
                        }
                    }
                }
            },
            cod_cliente: {
                required: true,
                remote: {
                    url: <?php echo "'".$rootPath."?controller=tiendas&action=verifyCodBTKbyCliente'";?>,
                    type: "POST",
                    data: {
                        cod_btk: function() {
                            return $("#cod_btk").val();
                        }
                    }
                }
            },
            cod_region: {required: true},
            cod_ciudad: {required: true},
            cod_comuna: {required: true},
            cod_zona: {required: true},
            cod_tipo: {required: true},
            cod_agrupacion: {required: true},
            cod_estado: {required: true}
        },
        messages: {
            cod_btk: {required: "Campo requerido.", remote: "C&oacute;digo BTK ya existe para el cliente"},
            cod_cliente: {required:  "Campo requerido.",remote: "Cliente ya existe para el BTK"},
            name: {required:  "Campo requerido.",remote: "Nombre inv&aacute;lido o ya existe para el cliente"}
        }
    });

    $('#btnlimpiar').click(function(){
        location.reload();
    });
    
    $('#cbocliente').change(function(){
        validator.resetForm();
        $('#cbocliente').valid();
//        $('#cod_btk').valid();
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

                    print_r($titulo); print("<br />"); 
                    print_r($lista_clientes); print("<br />"); print_r($codigo_nuevo); print("<br />");
                    print("<br />"); print_r($lista_regiones); print("<br />"); print_r($lista_ciudades);
                    print("<br />"); print_r($lista_comunas); print("<br />"); print_r($lista_zonas);
                    print("<br />"); print_r($lista_tipos); print("<br />"); print_r($lista_agrupaciones);
                    print("<br />"); print_r($lista_estados);print("<br />");print("<br />");
                    print_r($codigo_btk_nuevo);print("<br />");

                    if(isset($error))
                    {
                        print("ERROR: ");
                        print_r($error->errorInfo());
                    }

                    print('</div>');
            }
            ?>
            <!-- END DEBUG -->

            <p class="titulos-form"><?php echo $titulo; ?></p>

            <form id="moduleForm" name="form1" method="post" action="<?php echo $rootPath.'?controller=tiendas&amp;action=tiendasAdd';?>">
              <table border="0" align="center" class="texto">
                <tr>
                  <td width="80">C&oacute;digo</td>
                  <td width="3">:</td>
                  <td width="380">
                  <input name="cod_tienda" type="text" id="txtcodigo" size="40" value="<?php echo $codigo_nuevo; ?>" disabled="disabled" />
                  </td>
                </tr>
                <tr>
                  <td>Cliente</td>
                  <td>:</td>
                  <td>
                        <?php
                        echo "<select id='cbocliente' name='cod_cliente'>\n";
                                echo "<option value=''>SELECCIONAR</option>\n";
                        while($row = $lista_clientes->fetch(PDO::FETCH_ASSOC))
                        {
                                echo "<option value='$row[COD_CLIENTE]'>$row[NOM_CLIENTE]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                      <!--
                      TO DO
                      <input type="image" id="tooltip_info" class="ui-icon ui-icon-closethick" style="display: inline;" />-->
                      
                  </td>
                </tr>
                <tr>
                  <td>C&oacute;digo BTK</td>
                  <td>:</td>
                  <td>
                      <input name="cod_btk" type="text" id="cod_btk" class="inpt_left" size="40" value="<?php echo $codigo_btk_nuevo; ?>" />
                  </td>
                </tr>
                <tr>
                  <td>Nombre</td>
                  <td>:</td>
                  <td>
                      <input name="prename" type="text" id="txtprenombre" class="inpt_left" size="25" disabled="disabled" /> - <input name="name" type="text" id="txtnombre" size="38" />
                  </td>
                </tr>
                <tr>
                  <td>Direcci&oacute;n</td>
                  <td>:</td>
                  <td><textarea name="direccion" cols="70" id="txtdireccion" ></textarea></td>
                </tr>
                <tr>
                  <td>Regi&oacute;n</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cboregion' name='cod_region'>\n";
                        echo "<option value='' selected='selected'>SELECCIONAR</option>\n";
                        while($row = $lista_regiones->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option value='$row[COD_REGION]'>$row[NOM_REGION]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Ciudad</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cbociudad' name='cod_ciudad'>\n";
                        echo "<option title='' selected='selected' value=''>SELECCIONAR</option>\n";
                        while($row = $lista_ciudades->fetch(PDO::FETCH_ASSOC))
                        {
                                echo "<option title='$row[REGION_COD_REGION]' value='$row[COD_CIUDAD]'>$row[NOM_CIUDAD]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Comuna</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cbocomuna' name='cod_comuna'>\n";
                                echo "<option title='' value=''>SELECCIONAR</option>\n";
                        while($row = $lista_comunas->fetch(PDO::FETCH_ASSOC))
                        {
                                echo "<option title='$row[CIUDAD_COD_CIUDAD]' value='$row[COD_COMUNA]'>$row[NOM_COMUNA]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Zona</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cbozona' name='cod_zona'>\n";
                                echo "<option value=''>SELECCIONAR</option>\n";
                        while($row = $lista_zonas->fetch(PDO::FETCH_ASSOC))
                        {
                                echo "<option value='$row[COD_ZONA]'>$row[NOM_ZONA]</option>\n";
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
                        echo "<select id='cbotipo' name='cod_tipo'>\n";
                                echo "<option value=''>SELECCIONAR</option>\n";
                        while($row = $lista_tipos->fetch(PDO::FETCH_ASSOC))
                        {
                                echo "<option value='$row[COD_TIPO]'>$row[NOM_TIPO]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Agrupaci&oacute;n</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cboagrupacion' name='cod_agrupacion'>\n";
                                echo "<option value=''>SELECCIONAR</option>\n";
                        while($row = $lista_agrupaciones->fetch(PDO::FETCH_ASSOC))
                        {
                                echo "<option value='$row[COD_AGRUPACION]'>$row[NOM_AGRUPACION]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Estado</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select name='cod_estado'>\n";
                        while($row = $lista_estados->fetch(PDO::FETCH_ASSOC))
                        {
                                echo "<option value='$row[COD_ESTADO]'>$row[NOM_ESTADO]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                    <td colspan="3" class="submit">
                        <br />
                        <input type="hidden" name="cod_tienda" id="hdncodigo" value="<?php echo $codigo_nuevo; ?>" />
                        <input type="hidden" name="cod_btk" id="hdncod_btk" value="<?php echo $codigo_btk_nuevo; ?>" />
                        <input type="hidden" name="prename" id="hdnprename" value="" />
                        <?php $session->orig_timestamp = microtime(true); ?>
                        <input name="form_timestamp" type="hidden" value="<?php echo $session->orig_timestamp; ?>" />
                        <input name="Atras" type="reset" class="input" id="Atras"  onclick="window.location = '<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action_b.'';?>'"  value="Cancelar" />
                        &nbsp;&nbsp;
                        <input name="limpiar" type="button" class="input" id="btnlimpiar" value="Limpiar" />
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