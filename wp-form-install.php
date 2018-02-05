<form name="install_find" method="POST" onsubmit="return form_validation()">
Nombre: <input type="text" id="nombre" name="nombre" /><br />
<input type="submit" value="Aceptar"/>
</form>

<script type="text/javascript">
function form_validation() {
/* Check the Customer Name for blank submission*/
var nombre = document.forms["install_find"]["nombre"].value;
if (nombre == "" || nombre == null) {
alert("El campo nombre es obligatorio.");
return false;
}
}
</script>