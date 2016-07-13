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
    $("#moduleForm").validate();
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
                print_r($channel_code);
                print("<br />");
                print('</div>');
            }
            ?>
            <!-- END DEBUG -->

            <p class="titulos-form"><?php echo $titulo; ?></p>

            <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
              <table class="texto">
                <tr>
                    <td width="56">C&oacute;digo</td>
                    <td width="3">:</td>
                    <td>
                        <input class="required" minlength="1" name="code" type="text" id="txtcodigo" size="40"  value="<?php echo $channel_code; ?>" readonly />
                    </td>
                </tr>
                <tr>
                  <td>Nombre</td>
                  <td>:</td>
                  <td><input class="required" minlength="2" name="name" type="text" id="txtnombre" size="40" /></td>
                </tr>
                <tr>
                  <td colspan="3">
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