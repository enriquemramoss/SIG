<?php
session_start();
if (!isset($_SESSION['usuario'])||($_SESSION['usuario']!="Administrador"))
{
  header("location:Login.html");
}
header("Content-Type: text/html;charset=utf-8");
include_once("validaciones.php");
include_once("datos.php");

if (isset($_GET['pp']) && $_GET['pp']=="i")
{
  $_POST["idfp"]=($_GET["idIndex"]);
  $_POST["Valor"]=$_GET["idIndexS"];
  insertarSeleccionado();
}
else
{

if (isset($_GET['up']) && $_GET['up']=="i")
{
  $_POST["Nombre"]=$_FILES['archivo']['name'];
  $array = explode('.', $_FILES['archivo']['name']);
  $_POST["Tipo"] = end($array);
  $_POST["idC"]=($_GET["idC"]);
  $_POST["IdProcedimiento"]=$_GET["idproc"];
  insertarLugardeArchivo();
  //select();
}
else{
if (isset($_GET['op']) && $_GET['op']=="i")
{
    $_POST["Nombre"]=$_GET["idprocedimiento"];
    $_POST["idC"]=($_GET["idC"]);
    $_POST["id"]=$_GET["idA"];
    insertar();
}
if (isset($_GET['pe']) && $_GET['pe']=="i")
{
    $_POST["id"]=$_GET["idoF"];
    $_POST["idP"]=$_GET["idoELM"];
    $_POST["idC"]=($_GET["idC"]);
    eliminadito();
}
if (isset($_GET['pa']) && $_GET['pa']=="i")
{
    $_POST["id"]=$_GET["ido"];
    $_POST["idC"]=($_GET["idC"]);
    eliminar();
}

if (isset($_GET['pp']) && $_GET['pp']=="i")
{
    $_POST["id"]=$_GET["ida"];
    $_POST["idC"]=($_GET["idC"]);
    activar();
}

if (isset($_GET['pf']) && $_GET['pf']=="i")
{
    $_POST["id"]=($_GET["idm"]);
    $_POST["idC"]=($_GET["idC"]);
    $_POST["Procedimiento"]=($_GET["idprocedimiento2"]);
    modificar();
}
if (isset($_GET['pd']) && $_GET['pd']=="i")
{
    selectprocesos();
}
else{ 
if(isset($_GET['ba']) && $_GET['ba']=="i")
{
  $_POST["id"]=($_GET["idC"]);
  select();
}
else 
{
  select();
}
}
}
  
}
//select();

class Formato
{
  public $idProcedimiento=0;
  public $nombre = "";
  function Procedimiento()
	{

	}
}

class Procedimiento
{
  public $id=0;
  public $nombre = "";
  public $disponibilidad="";
  public $documentos="";
  function Procedimiento()
	{

	}
}
class Documentos
{
  public $id="";
  public $idf="";
  public $nombre = "";
  public $seleccionado = "";
  function Documentos()
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

function insertar()
{
  $proceso = new Proceso();
  $procedimiento=new Procedimiento();
  $procedimiento->nombre = validarTexto($_POST["Nombre"]);
  $proceso->id = intval($_POST["id"]);
  $visible=1;
  $conn = conectar();
  $sql = "INSERT INTO procedimientos(IdProceso,NombreProcedimiento,VisibleProcedimiento) VALUES(?,?,?)";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("isi",
      $proceso->id,
      $procedimiento->nombre,
      $visible);
    $consultaPreparada->execute();
    return $consultaPreparada->store_result();  
  }
  else
  {
    return "error en la consulta";
  }
  $conn->close();
}

function insertarSeleccionado()
{
  $Valor = $_POST["Valor"];
  $id = $_POST["idfp"];
  $conn = conectar();
  $sql = "UPDATE formatoprocedimiento SET SeleccionadoFormatoProcedimiento = ? WHERE IdFormatoProcedimiento = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("ii",
    $Valor,
      $id);
    $consultaPreparada->execute();
    return $consultaPreparada->store_result();  
  }
  else
  {
    return "error en la consulta";
  }
  $conn->close();
}

function select()
{
  $conn = conectar();
  $proceso = new Proceso();
    if (isset($_GET['ba']) && $_GET['ba']=="i")
  {
  $proceso->id = intval($_POST["id"]);
  }
  else
  {
  $proceso->id = intval($_POST["idC"]);
  }
  $sql = "SELECT IdProcedimiento,NombreProcedimiento,VisibleProcedimiento FROM procedimientos pd,procesos pc WHERE pd.IdProceso=? and pd.IdProceso=pc.IdProceso";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
  $consultaPreparada->bind_param("i",
  $proceso->id);
  $consultaPreparada->execute();
  //return $consultaPreparada->store_result();  
  $result = $consultaPreparada->get_result();
  }
  
  //$result = $conn->query($sql);
  $procedimiento;
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $procedimiento = new Procedimiento();
      $procedimiento->id = $row["IdProcedimiento"];
      $procedimiento->nombre = $row["NombreProcedimiento"];
      //echo$procesoo->disponibilidad;
      if($row["VisibleProcedimiento"]==1)
      {
        $procedimiento->disponibilidad = "Si";
      }
      if($row["VisibleProcedimiento"]==0)
      {
        $procedimiento->disponibilidad = "No";
      }
      $procedimiento->documentos=documentos($row["IdProcedimiento"],$conn);
    	$procedimientos[] = $procedimiento;  	
    }
    ////////////////////////////////////////prueba//////////////////////777
    
    /////////////////////////termian prueba//////////////////////7
    echo utf8_decode(json_encode($procedimientos)); 
    //return json_encode($proceso);    
  } else {
    return null;
  }
  $conn->close();
}

function selectprocesos()
{
  $conn = conectar();
  $sql = "SELECT IdProceso,NombreProceso FROM procesos";
  $result = $conn->query($sql);
  $proceso;
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $proceso = new Proceso();
      $proceso->id = $row["IdProceso"];
      $proceso->nombre = $row["NombreProceso"];
    	$procesos[] = $proceso;  	
    }
    
    echo utf8_decode(json_encode($procesos)); 
    //print_r($procesos); 
    //return json_encode($proceso);    
  } else {
    return null;
  }
  $conn->close();
}


function eliminar()
{
  $proceso = new Proceso();
  $proceso->id = intval($_POST["id"]);
  $activo = intval("0");
  $conn = conectar();
  $sql = "UPDATE procedimientos SET VisibleProcedimiento = ? WHERE IdProcedimiento = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("ii",
    $activo,
    $proceso->id);
    $consultaPreparada->execute();
    //return $consultaPreparada->store_result();  
  }
  else
  {
    return "error al eliminar";
  }
  $conn->close();
  //return select();  
}

function activar()
{
  $procedimiento = new Procedimiento();
  $procedimiento->id = intval($_POST["id"]);
  $activo = intval("1");
  $conn = conectar();
  $sql = "UPDATE procedimientos SET VisibleProcedimiento = ? WHERE IdProcedimiento = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("ii",
    $activo,
    $procedimiento->id);
    $consultaPreparada->execute();
    //return $consultaPreparada->store_result();  
  }
  else
  {
    return "error al activar";
  }
  $conn->close();
  //return select();  
}

function modificar()
{
  $procedimiento = new Procedimiento();
  $procedimiento->id = intval($_POST["id"]);
  //echo (validarTexto($_POST["Proceso"]));
  $procedimiento->nombre = validarTexto($_POST["Procedimiento"]);
  $conn = conectar();
  $sql = "UPDATE procedimientos SET NombreProcedimiento = ? WHERE IdProcedimiento = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("si",
    $procedimiento->nombre,
    $procedimiento->id);
    $consultaPreparada->execute();
    //return $consultaPreparada->store_result();  
  }
  else
  {
    return "error al activar";
  }
  $conn->close();
  //return select();  
}

function insertarLugardeArchivo()
{
  $tipo=$_POST["Tipo"];
  $formato = new Formato();
  $formato->nombre = ($_POST["Nombre"]);
  $formato->idProcedimiento =  $_POST["IdProcedimiento"];
  $conn = conectar();
  $sql = "INSERT INTO formatoprocedimiento(IdProcedimientoFormatoProcedimiento,NombreFormatoProcedimiento,ExtensiónFormatoProcedimiento) VALUES(?,?,?)";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("iss",
    $formato->idProcedimiento,
      $formato->nombre,
      $tipo);
    $consultaPreparada->execute();
    return $consultaPreparada->store_result();  
  }
  else
  {
    //echo ( $formato->nombre);
    //echo ($formato->idProcedimiento);
    //return "error en la consulta";
    echo("error en la consulta");
  }
  $conn->close();
}
  function documentos($comodin,$conn)
  {
    $arreglo;
    $sql2 = "SELECT IdProcedimientoFormatoProcedimiento,IdFormatoProcedimiento,NombreFormatoProcedimiento,SeleccionadoFormatoProcedimiento FROM formatoprocedimiento fp, procedimientos pc Where fp.IdProcedimientoFormatoProcedimiento=? and pc.IdProcedimiento=fp.IdProcedimientoFormatoProcedimiento";
    $consultaPreparada2 = $conn->prepare($sql2);
    if ($consultaPreparada2)
    {
    $consultaPreparada2->bind_param("i",
    $comodin);
    $consultaPreparada2->execute();
    $result = $consultaPreparada2->get_result();
    }
    else
    {
      $nombres=new Documentos();
      return null;
    }
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $nombres=new Documentos();
        $nombres->id=$row["IdProcedimientoFormatoProcedimiento"];
        $nombres->idF=$row["IdFormatoProcedimiento"];
        $nombres->nombre=$row["NombreFormatoProcedimiento"];
        $nombres->seleccionado=$row["SeleccionadoFormatoProcedimiento"];
        $arreglo[]=$nombres;
      }
      return $arreglo;
    }
    else{
      $nombres=new Documentos();
      return null;
    }
}

function eliminadito()
{
  $formato = new Formato();
  $formato->idProcedimiento = intval($_POST["id"]);
  $idC=intval($_POST["idP"]);
  $formato=selectEliminar($formato);
  //echo utf8_decode(json_encode($formato));
  $conn = conectar();
  $sql = "DELETE FROM formatoprocedimiento WHERE IdFormatoProcedimiento = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("i",
    $formato->idProcedimiento);
    $consultaPreparada->execute();
    //mkdir('documents/'.$idC.'/pepe',0777,true);
    unlink('documents/procedimientos/'.$idC.'/'.$formato->nombre);
    //return $consultaPreparada->store_result();  
  }
  else
  {
    return "error al eliminar";
  }
  $conn->close();
  //return select();  
}

function selectEliminar($formato){
  $conn = conectar();
  $sql = "SELECT NombreFormatoProcedimiento FROM formatoprocedimiento WHERE IdFormatoProcedimiento=?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
  $consultaPreparada->bind_param("i",
  $formato->idProcedimiento);
  $consultaPreparada->execute();
  //return $consultaPreparada->store_result();  
  $result = $consultaPreparada->get_result();
  }
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $formato->nombre = $row["NombreFormatoProcedimiento"];
    }
    return $formato;    
  } else {
    return null;
  }
  $conn->close();
}
?>