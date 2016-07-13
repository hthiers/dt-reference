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
	//arreglo de ciudades y comunas (variables globales)
	categories = $('#cbocategory option').clone();
	gbu = $('#cbogbu option').clone();

	//filtrado inicial de cbo (ciudad y comuna)
	loadCbos();

	//filtrar cada vez que se seleccione una nueva region
	$('#cbobu').change(function() {
		var value = $(this).val();
		$('#cbocategory').empty();
		
		categories.filter(function(idx, target) {
			return value == '' || $(target).attr("title").indexOf(value) >= 0;
		}).appendTo('#cbocategory');
		
		//filtrar comuna segun ciudad filtrada
		var valueCity = $("#cbocategory").val();
		$('#cbogbu').empty();
		
		gbu.filter(function(idx, target) {
			return valueCity == '' || $(target).attr("title").indexOf(valueCity) >= 0;
		}).appendTo('#cbogbu');
	});
	
	//filtrar cada vez que se seleccione una nueva ciudad
	$('#cbocategory').change(function() {
		var value = $(this).val();
		$('#cbogbu').empty();
		
		gbu.filter(function(idx, target) {
			return value == '' || $(target).attr("title").indexOf(value) >= 0;
		}).appendTo('#cbogbu');
	});

	function loadCbos(){
		/*
		* Filtro category
		*/
		//valor primera opción en cbo de regiones
		var bu = $('#cbobu').val();
		
		//limpiar el cbo de ciudades para volver a construirlo
		$('#cbocategory').empty();
		
		//filtrar cbo con ciudades que coincidan con la región en su "title"
		categories.filter(function(idx, target) {
			return bu == '' || $(target).attr("title").indexOf(bu) >= 0;
		}).appendTo('#cbocategory');
		
		/*
		* Filtro gbu
		*/
		var category = $('#cbocategory').val();
		$('#cbogbu').empty();
		gbu.filter(function(idx, target) {
			return category == '' || $(target).attr("title").indexOf(category) >= 0;
		}).appendTo('#cbogbu');
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
                    print_r($listado); print("<br />"); print_r($lista_gbu);print("<br />"); 
                    print_r($lista_category); print("<br />"); print_r($lista_bu);print("<br />");
                    print_r($lista_estados); print("<br />"); print_r($lista_segments);print("<br />");
                    print_r($lista_subsegments); print("<br />"); print_r($lista_microsegments);print("<br />");
                    print_r($lista_brands); print("<br />");
                    print_r($controller); print("<br />");print_r($action); print("<br />");
                    print_r($action_b); print("<br />");

                    print('</div>');
            }
            ?>
            <!-- END DEBUG -->

            <p class="titulos-form"><?php echo $titulo; ?></p>

            <!-- FORM -->
            <?php if($item = $listado->fetch(PDO::FETCH_ASSOC)): ?>
            <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
              <table width="457" height="118" border="0" align="center" class="texto">
                <tr>
                  <td>Modelo</td>
                  <td width="3">:</td>
                  <td>
                  	<input class="required" minlength="1" name="code" type="text" id="txtcodigo" size="40" value="<?php echo $item['COD_MODEL'];?>" readonly />
                  </td>
                </tr>
                <tr>
                  <td>Modelo Sufijo</td>
                  <td>:</td>
                  <td><input class="required" minlength="1" name="code_suffix" type="text" id="txtcodigo" size="40" value="<?php echo $item['COD_MODEL_SUFFIX']; ?>" readonly /></td>
                </tr>
                <tr>
                <tr>
                  <td>Estado</td>
                  <td>:</td>
                  <td>
                      <?php
                        echo "<select name='cod_estado'>\n";
                        while($row = $lista_estados->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_ESTADO'] == $item['COD_ESTADO'])
                                        echo "<option value='$row[COD_ESTADO]' selected='true'>$row[NAME_ESTADO]</option>\n";
                                else
                                        echo "<option value='$row[COD_ESTADO]'>$row[NAME_ESTADO]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                <tr>
                  <td>Segment</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select name='cod_segment'>\n";
                        while($row = $lista_segments->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_SEGMENT'] == $item['COD_SEGMENT'])
                                        echo "<option value='$row[COD_SEGMENT]' selected='true'>$row[NAME_SEGMENT]</option>\n";
                                else
                                        echo "<option value='$row[COD_SEGMENT]'>$row[NAME_SEGMENT]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Sub Segment</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select name='cod_sub_segment'>\n";
                        while($row = $lista_subsegments->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_SUB_SEGMENT'] == $item['COD_SUB_SEGMENT'])
                                        echo "<option value='$row[COD_SUB_SEGMENT]' selected='true'>$row[NAME_SUB_SEGMENT]</option>\n";
                                else
                                        echo "<option value='$row[COD_SUB_SEGMENT]'>$row[NAME_SUB_SEGMENT]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Micro Segment</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select name='cod_micro_segment'>\n";
                        while($row = $lista_microsegments->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_MICRO_SEGMENT'] == $item['COD_MICRO_SEGMENT'])
                                        echo "<option value='$row[COD_MICRO_SEGMENT]' selected='true'>$row[NAME_MICRO_SEGMENT]</option>\n";
                                else
                                        echo "<option value='$row[COD_MICRO_SEGMENT]'>$row[NAME_MICRO_SEGMENT]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>BU</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cbobu' name='cod_bu'>\n";
                        while($row = $lista_bu->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_BU'] == $item['COD_BU'])
                                        echo "<option value='$row[COD_BU]' selected='true'>$row[NAME_BU]</option>\n";
                                else
                                        echo "<option value='$row[COD_BU]'>$row[NAME_BU]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Categoria</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cbocategory' name='cod_category'>\n";
                        while($row = $lista_category->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_CATEGORY'] == $item['COD_CATEGORY'])
                                        echo "<option title='$row[BU_COD_BU]' value='$row[COD_CATEGORY]' selected='true'>$row[NAME_CATEGORY]</option>\n";
                                else
                                        echo "<option title='$row[BU_COD_BU]' value='$row[COD_CATEGORY]'>$row[NAME_CATEGORY]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>GBU</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select id='cbogbu' name='cod_gbu'>\n";
                        while($row = $lista_gbu->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_GBU'] == $item['COD_GBU'])
                                        echo "<option title='$row[CAT_COD_CATEGORY]' value='$row[COD_GBU]' selected='true'>$row[NAME_GBU]</option>\n";
                                else
                                        echo "<option title='$row[CAT_COD_CATEGORY]' value='$row[COD_GBU]'>$row[NAME_GBU]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>Brand</td>
                  <td>:</td>
                  <td>
                  	<?php
                        echo "<select name='cod_brand'>\n";
                        while($row = $lista_brands->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['COD_BRAND'] == $item['COD_BRAND'])
                                        echo "<option value='$row[COD_BRAND]' selected='true'>$row[NAME_BRAND]</option>\n";
                                else
                                        echo "<option value='$row[COD_BRAND]'>$row[NAME_BRAND]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                      <br />
                      <input name="old_code" type="hidden" id="oculto" value="<?php echo $item['COD_MODEL_SUFFIX']; ?>" />
                      <input name="form_timestamp" type="hidden" value="<?php echo microtime(true); ?>" />
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