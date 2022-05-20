<?php
session_start();
if (!isset($_SESSION['usuario']))
{
  header("location:Login.html");
}
header("Content-Type: text/html;charset=utf-8");
include_once("validaciones.php");
include_once("datos.php");


  if (isset($_GET['ba']) && $_GET['ba']=="i")
  {
    $_POST["id"]=($_GET["idP"]);
    $_POST["tipo"]=($_GET["idTipo"]);
    select();
  }

  if (isset($_GET['be']) && $_GET['be']=="i")
  {
    selectmenu();
  }


class Menu
{
  public $id=0;
  public $nombre = "";

  function Menu()
	{

	}
}

class Proceso
{
  public $id=0;
  public $nombre = "";
  //public $disponibilidad="";
	function Proceso()
	{

	}
}

class Procedimiento
{
  public $id=0;
  public $nombre = "";
  //public $disponibilidad="";
  function Procedimiento()
	{

	}
}


class Anexo
{
  public $id=0;
  public $nombre = "";
	function Anexo()
	{

	}
}
class Mostrar
{
  public $id=0;
  public $nombreProceso = "";
  public $nombreProcedimiento = "";
  public $nombreAnexo = "";
  //public $nombreProcedimiento = "";

  function Menu()
	{

	}
}

function selectmenu()
{
  $conn = conectar();
  $sql = "SELECT IdProceso,NombreProceso FROM procesos Where VisibleProceso=1";
  $result = $conn->query($sql);
  $menu;
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $menu = new Menu();
      $menu->id = $row["IdProceso"];
      $menu->nombre = $row["NombreProceso"];
    	$menus[] = $menu;  	
    }
    
    echo utf8_decode(json_encode($menus));  
  } else {
    return null;
  }
  $conn->close();
}

function select()
{
  $tipo=$_POST["tipo"];
  $conn = conectar();
  $sql = "SELECT Privilegio FROM privilegios  WHERE Nombre=?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
  $consultaPreparada->bind_param("s",
  $tipo);
  $consultaPreparada->execute();
  //return $consultaPreparada->store_result();  
  $result = $consultaPreparada->get_result();
  }
  //$result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $tipo = $row["Privilegio"];
    }   
  $tipo = explode (" ", $tipo);
  }
  
  //////////////////////////
  $proceso=new Proceso();
  $proceso->id=$_POST["id"];
  $conn = conectar();
  $sql = "SELECT IdProcedimiento,NombreProcedimiento FROM procesos pc,procedimientos pd Where pc.IdProceso=? and pc.IdProceso=pd.IdProceso and VisibleProceso=1 and VisibleProcedimiento=1 ";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
  $consultaPreparada->bind_param("i",
  $proceso->id);
  $consultaPreparada->execute();
  //return $consultaPreparada->store_result();  
  $result = $consultaPreparada->get_result();
  }
  $procedimiento;
  $anexo;
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $procedimiento=array();
      array_push($procedimiento,$row["NombreProcedimiento"]);
      array_push($procedimiento,ultimoprocedimento($row["IdProcedimiento"],$tipo));
      $anexo=anexos($row["IdProcedimiento"],$tipo);
      array_push($procedimiento,$row["IdProcedimiento"]);
      $procedimientos[] = $procedimiento; 
      $anexos[] = $anexo;  	
    }
    $arreglochido[]=$procedimientos;
    $arreglochido[]=$anexos;
    echo utf8_decode(json_encode( $arreglochido));  

   }
  else {
    return null;
  }
  $conn->close();
}

function ultimoprocedimento($idP,$tipo){
  $pdf=" ";
  $doc=" ";
  $docx=" ";
  $xls=" ";
  $xlsb=" ";
  $xlsx=" ";
  $xlsm=" ";

  if($tipo[0]=="1")
  {
    $pdf="pdf";
  }
  if($tipo[1]=="1")
  {
    $doc="doc";
    $docx="docx";
  }
  if($tipo[2]=="1")
  {
    $xls="xls";
    $xlsb="xlsb";
    $xlsx="xlsx";
    $xlsm="xlsm";
  }


  $conn = conectar();
  $sql = "SELECT NombreFormatoProcedimiento FROM procedimientos pd, formatoprocedimiento fp Where pd.IdProcedimiento=? and pd.IdProcedimiento=fp.IdProcedimientoFormatoProcedimiento and VisibleProcedimiento=1 and fp.SeleccionadoFormatoProcedimiento=1 and (fp.ExtensiónFormatoProcedimiento=? or fp.ExtensiónFormatoProcedimiento=? or fp.ExtensiónFormatoProcedimiento=? or fp.ExtensiónFormatoProcedimiento=? or fp.ExtensiónFormatoProcedimiento=? or fp.ExtensiónFormatoProcedimiento=? or fp.ExtensiónFormatoProcedimiento=?)";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
  $consultaPreparada->bind_param("isssssss",
  $idP,
  $pdf,
  $doc,
  $docx,
  $xls,
  $xlsb,
  $xlsx,
  $xlsm);
  $consultaPreparada->execute();
  //return $consultaPreparada->store_result();  
  $result = $consultaPreparada->get_result();
  }

  $procedimientoN= array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push($procedimientoN,$row["NombreFormatoProcedimiento"]);
    }
    return $procedimientoN;  
   }
  else {
    return "";
  }
  $conn->close();
}

function anexos($idProcedimiento,$tipo)
{
  $conn = conectar();
  $sql = "SELECT NombreAnexo,IdAnexo FROM anexos an,procedimientos pd Where pd.IdProcedimiento=? and an.IdProcedimiento=pd.IdProcedimiento and VisibleAnexo=1";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
  $consultaPreparada->bind_param("i",
  $idProcedimiento);
  $consultaPreparada->execute();
  //return $consultaPreparada->store_result();  
  $result = $consultaPreparada->get_result();
  }
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $anexo=array();
      array_push($anexo,$row["NombreAnexo"]);
      array_push($anexo,ultimoanexo($row["IdAnexo"],$tipo));
      array_push($anexo,$row["IdAnexo"]);
      $anexos[] = $anexo;  	
    }
    return $anexos;  

   }
  else {
    return "";
  }
  $conn->close();
}

function ultimoanexo($idA,$tipo){
  $pdf=" ";
  $doc=" ";
  $docx=" ";
  $xls=" ";
  $xlsb=" ";
  $xlsx=" ";
  $xlsm=" ";

  if($tipo[0]=="1")
  {
    $pdf="pdf";
  }
  if($tipo[1]=="1")
  {
    $doc="doc";
    $docx="docx";
  }
  if($tipo[2]=="1")
  {
    $xls="xls";
    $xlsb="xlsb";
    $xlsx="xlsx";
    $xlsm="xlsm";
  }

  $conn = conectar();
  $sql = "SELECT NombreFormatoAnexo FROM anexos an, formatoanexo fa Where an.IdAnexo=? and an.IdAnexo=fa.IdAnexoFormatoAnexo and VisibleAnexo=1 and fa.SeleccionadoFormatoAnexo=1 and (fa.ExtensiónFormatoAnexo=? or fa.ExtensiónFormatoAnexo=? or fa.ExtensiónFormatoAnexo=? or fa.ExtensiónFormatoAnexo=? or fa.ExtensiónFormatoAnexo=? or fa.ExtensiónFormatoAnexo=? or fa.ExtensiónFormatoAnexo=?)";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
  $consultaPreparada->bind_param("isssssss",
  $idA,
  $pdf,
  $doc,
  $docx,
  $xls,
  $xlsb,
  $xlsx,
  $xlsm);
  $consultaPreparada->execute();
  //return $consultaPreparada->store_result();  
  $result = $consultaPreparada->get_result();
  }

  $anexoN= array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push( $anexoN,$row["NombreFormatoAnexo"]);
    }
    return $anexoN;  
   }
  else {
    return "";
  }
  $conn->close();
}
?>