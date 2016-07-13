<!-- NO SCRIPT WARNING -->
<noscript>
<div>
    <h4>¡Espera un momento!</h4>
    <p>La página que estás viendo requiere JavaScript activado.
    Si lo has deshabilitado intencionalmente, por favor vuelve a activarlo o comunicate con soporte.</p>
</div>
</noscript>

<?php
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
?>

<div id="cabecera" align="left">
  <br />
  <img alt="logo" src="views/img/logo.png" width="237" height="67" align="left" />
  <img alt="mundolg" src="views/img/logo-mundolg.gif" width="225" height="60" />
</div>

<div class="fin" id="banner">
    <div class="banner_welcome">
        Bienvenido <?php echo "<a href='".$rootPath."?controller=users&amp;action=userProfile'>".$session->nombre."</a>"; ?>
        | 
        <a href="<?php echo $rootPath.'?controller=users&amp;action=logout';?>">Cerrar Sesi&oacute;n</a>
        |
        <a href="<?php echo $rootPath.'views/files/SOM_Portal_2_-_Quick_Start_Guide_-_Tiendas.pdf';?>" target="_blank">Ayuda</a>
    </div>
    <div class="banner_title">SOM Portal v2.0</div>
</div>

<!-- MENU -->
<div id="menu_div" class="menu">
<?php
#echo "<!-- debug navegador:".$navegador."-->\n";
include 'libs/Menu.php';
$menu = new Menu();
$menu->loadMenu($session,$navegador,$rootPath,$controller);
?>
</div>
<!-- END MENU -->