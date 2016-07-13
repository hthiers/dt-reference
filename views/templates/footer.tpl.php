<?php
#session
if($session->id != null):
#if(isset($_SESSION['usuario'])):

#privs
if($session->privilegio > 0):
?>
<div id="info">
  <p class="Estilo1"> LG ELECTRONICS CHILE - SOM Portal v2.0</p>
</div>

</body>
</html>
<?php
else:
	echo '<script language="JavaScript">alert("Usted No Posee Privilegios Suficientes "); document.location = "'.$rootPath.'"</script>';
endif; #privileges
else:
	session_destroy();
	echo '<script language="JavaScript">alert("Debe Identificarse"); document.location = "'.$rootPath.'"</script>';
endif; #session
?>