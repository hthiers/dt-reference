<?php
/**
 * JQuery Dialogs Template
 * @author Hernan Thiers
 */
?>
<div id="dialog-confirm" title="Confirmar acci&oacute;n" style="visibility: hidden;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 2px 10px 0;"></span>Este usuario ser&aacute; eliminado para siempre. &iquest;Desea seguir?</p>
</div>
<div id="dialog-exp-models" title="Opciones de exportaci&oacute;n" style="visibility: hidden;">
    <table class="">
        <tr>
            <td colspan="2">Seleccionar columnas:</td>
        </tr>
        <tr>
            <td>model</td>
            <td><input type="checkbox" name="column[]" checked="checked" value="COD_MODEL" /></td>
        </tr>
        <tr>
            <td>suffix</td>
            <td><input type="checkbox" name="column[]" checked="checked" value="COD_MODEL_SUFFIX" /></td>
        </tr>
        <tr>
            <td>segments</td>
            <td><input type="checkbox" name="column[]" checked="checked" value="SEGMENTS" /></td>
        </tr>
        <tr>
            <td>gbu</td>
            <td><input type="checkbox" name="column[]" checked="checked" value="COD_GBU" /></td>
        </tr>
        <tr>
            <td>brand</td>
            <td><input type="checkbox" name="column[]" checked="checked" value="COD_BRAND" /></td>
        </tr>
        <tr>
            <td>estado</td>
            <td><input type="checkbox" name="column[]" checked="checked" value="COD_ESTADO" /></td>
        </tr>
        <!--<tr>
            <td>premium</td>
            <td><input type="checkbox" name="column[]" checked="checked" value="PREMIUM" /></td>
        </tr>-->
        <tr>
            <td>atributos</td>
            <td><input type="checkbox" name="column[]" checked="checked" value="ATRIBUTE" /></td>
        </tr>
    </table>
</div>

<!-- KEEP DIALOGS CLOSED -->
<script type="text/javascript" language="javascript">
    $("#dialog-confirm").dialog({ autoOpen: false});
    $("#dialog-exp-models").dialog({ autoOpen: false});
</script>