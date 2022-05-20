<?php
header("Content-Type: text/html;charset=utf-8");
function validarTexto($texto)
{
	//$texto=htmlentities($texto,ENT_COMPAT,'utf-8');
	$texto=str_replace("'","",$texto);
	//$texto=utf8_encode($texto);
	return $texto;
}
function validarFecha($fecha)
{
	return date_create($fecha);
}

function validarSexo($sexo)
{
	if ($sexo==="Femenino")
		return "Femenino";
	else
		return "Masculino";
}

function validarSiNo($valor)
{
	if ($valor==="on" || $valor==="S" || $valor==="Si" || $valor===true || $valor==="true" || $valor === "SI")
		return "S";
	else
		return "N";
}

?>