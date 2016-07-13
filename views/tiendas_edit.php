<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id != null):

#privs
if($session->privilegio > 0):
?>

<!-- JS & CSS -->
<script language="javascript">
$(document).ready(function(){
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
        
	//arreglo de ciudades y comunas (variables globales)
	cities = $('#cbociudad option').clone();
	comunas = $('#cbocomuna option').clone();
        $('#hdncliente').val($('#cbocliente').val());

	//filtrado inicial de cbo (ciudad y comuna)
	loadCbos();

	//filtrar cada vez que se seleccione una nueva region
	$('#cboregion').change(function() {
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
                console.log(value);
		$('#cbocomuna').empty();
		
		comunas.filter(function(idx, target) {
			return value == '' || $(target).attr("title").indexOf(value) >= 0;
		}).appendTo('#cbocomuna');
	});

	function loadCbos(){
		/*
		* Filtro ciudades
		*/
		//valor primera opción en cbo de regiones
		var region = $('#cboregion').val();
		
		//limpiar el cbo de ciudades para volver a construirlo
		$('#cbociudad').empty();
		
		//filtrar cbo con ciudades que coincidan con la región en su "title"
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
	};
        
        $("#moduleForm").validate();
});
</script>
<!-- END JS & CSS -->

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
                print_r($listado); print("<br />"); print_r($lista_cliente);
                print("<br />"); print_r($listado_regiones); print("<br />"); print_r($listado_ciudades);
                print("<br />"); print_r($listado_comunas); print("<br />"); print_r($listado_zonas);
                print("<br />"); print_r($listado_tipos); print("<br />"); print_r($listado_agrupaciones);
                print("<br />"); print_r($listado_estados);

                print('</div>');
            }
            ?>
            <!-- END DEBUG -->
        
	        <p class="titulos-form"><?php echo $titulo; ?></p>
        
        	<!-- FORM -->
        	<?php 
                if($item = $listado->fetch(PDO::FETCH_ASSOC)): 
                    //dividir nombre tienda
                    $nombre_tienda = explode("-", $item['NOM_TIENDA'], 2);
                ?>
            <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller=tiendas&amp;action=tiendasEdit';?>">
              <table border="0" align="center" class="texto">
                <tr>
                    <td>C&oacute;digo Tienda</td>
                  <td width="3">:</td>
                  <td><input class="required" minlength="1" name="txtcodigo" type="text" id="txtcodigo" size="40" value="<?php echo $item['COD_TIENDA'];?>" disabled="disabled" /></td>
                </tr>
                <tr>
                  <td>Cliente</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cbocliente' name='cod_cliente' disabled='disabled'>\n";
                        while($row = $lista_cliente->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_CLIENTE'] == $item['COD_CLIENTE'])
                                        echo "<option value='$row[COD_CLIENTE]' selected='selected'>$row[NOM_CLIENTE]</option>\n";
                                else
                                        echo "<option value='$row[COD_CLIENTE]'>$row[NOM_CLIENTE]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                      <input id="hdncliente" type="hidden" name="cod_cliente" value=""/>
                  </td>
                </tr>
                <tr>
                    <td>C&oacute;digo BTK</td>
                  <td>:</td>
                  <td>
                      <input class="required" minlength="1" name="txtcodigobtk" type="text" id="txtcodigo" size="40" value="<?php echo $item['COD_BTK']; ?>" disabled="disabled" />
                  </td>
                </tr>
                <tr>
                  <td>Nombre</td>
                  <td>:</td>
                  <td>
                      <input name="prename" type="text" id="txtprenombre" size="25" disabled="disabled" value="<?php echo $nombre_tienda[0]; ?>" /> - 
                      <input name="name" class="required" minlength="1" type="text" id="txtnombre" size="40" value="<?php echo $nombre_tienda[1]; ?>" />
                  </td>
                </tr>
                <tr>
                    <td>Direcci&oacute;n</td>
                  <td>:</td>
                  <td><textarea name="txtdireccion" cols="70" id="txtdireccion" ><?php echo $item['DIREC_TIENDA']; ?></textarea></td>
                </tr>
                <tr>
                    <td>Regi&oacute;n</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cboregion' name='cod_region'>\n";
                        while($row = $listado_regiones->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_REGION'] == $item['COD_REGION'])
                                        echo "<option value='$row[COD_REGION]' selected='true'>$row[NOM_REGION]</option>\n";
                                else
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
                        while($row = $listado_ciudades->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_CIUDAD'] == $item['COD_CIUDAD'])
                                        echo "<option title='$row[REGION_COD_REGION]' value='$row[COD_CIUDAD]' selected='true'>$row[NOM_CIUDAD]</option>\n";
                                else
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
                        while($row = $listado_comunas->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_COMUNA'] == $item['COD_COMUNA'])
                                        echo "<option title='$row[CIUDAD_COD_CIUDAD]' value='$row[COD_COMUNA]' selected='true'>$row[NOM_COMUNA]</option>\n";
                                else
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
                        echo "<select name='cod_zona'>\n";
                        while($row = $listado_zonas->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_ZONA'] == $item['COD_ZONA'])
                                        echo "<option value='$row[COD_ZONA]' selected='true'>$row[NOM_ZONA]</option>\n";
                                else
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
                        echo "<select name='cod_tipo'>\n";
                        while($row = $listado_tipos->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_TIPO'] == $item['COD_TIPO'])
                                        echo "<option value='$row[COD_TIPO]' selected='true'>$row[NOM_TIPO]</option>\n";
                                else
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
                        echo "<select name='cod_agrupacion'>\n";
                        while($row = $listado_agrupaciones->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_AGRUPACION'] == $item['COD_AGRUPACION'])
                                        echo "<option value='$row[COD_AGRUPACION]' selected='true'>$row[NOM_AGRUPACION]</option>\n";
                                else
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
                        while($row = $listado_estados->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_ESTADO'] == $item['COD_ESTADO'])
                                        echo "<option value='$row[COD_ESTADO]' selected='true'>$row[NOM_ESTADO]</option>\n";
                                else
                                        echo "<option value='$row[COD_ESTADO]'>$row[NOM_ESTADO]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                    <td colspan="3" class="submit">
                        <br />
                        <input type="hidden" name="txtcodigo" id="hdncodigo" value="<?php echo $item['COD_TIENDA']; ?>" />
                        <input type="hidden" name="txtcodigobtk" id="hdncod_btk" value="<?php echo $item['COD_BTK']; ?>" />
                        <input type="hidden" name="prename" id="hdnprename" value="<?php echo $nombre_tienda[0]; ?>" />
                        <?php $session->orig_timestamp = microtime(true); ?>
                        <input type="hidden" name="form_timestamp" value="<?php echo $session->orig_timestamp; ?>" />
                        
                        <input name="Atras" type="reset" class="input" id="Atras"  onclick="window.location = '<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action_b.'';?>'"  value="Cancelar" />
                        &nbsp;&nbsp;
                        <input name="button" type="submit" class="input" id="button" value="Guardar" />
                    </td>
                </tr>
              </table>
            </form>
            <?php endif; ?>
            <!-- END FORM -->
        
        </div>
	</div>
    <!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>