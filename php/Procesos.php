<?php
session_start();
if (!isset($_SESSION['usuario'])||($_SESSION['usuario']!="Administrador"))
{
  header("location:Login.html");
}
header("Content-Type: text/html;charset=utf-8");
include_once("validaciones.php");
include_once("datos.php");
if (isset($_GET['op']) && $_GET['op']=="i")
{
    $_POST["Proceso"]=$_GET["idproceso"];
    insertar();
}
if (isset($_GET['pa']) && $_GET['pa']=="i")
{
    $_POST["id"]=$_GET["ido"];
    eliminar();
}

if (isset($_GET['pp']) && $_GET['pp']=="i")
{
    $_POST["id"]=$_GET["ida"];
    activar();
}

if (isset($_GET['pf']) && $_GET['pf']=="i")
{
    $_POST["id"]=($_GET["idm"]);
    $_POST["Proceso"]=($_GET["idproceso2"]);
    modificar();
}

select();


class Proceso
{
  public $id=0;
  public $nombre = "";
  public $disponibilidad="";
	function Proceso()
	{

	}
}

function insertar()
{
  $proceso = new Proceso();
  $proceso->nombre = validarTexto($_POST["Proceso"]);
  $visible=1;
  $conn = conectar();
  $sql = "INSERT INTO procesos(NombreProceso,VisibleProceso) VALUES(?,?)";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("si",
      $proceso->nombre,
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
function select()
{
  $conn = conectar();
  $sql = "SELECT IdProceso,NombreProceso,VisibleProceso FROM procesos";
  $result = $conn->query($sql);
  $proceso;
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $proceso = new Proceso();
      $proceso->id = $row["IdProceso"];
      $proceso->nombre = $row["NombreProceso"];
      //echo$procesoo->disponibilidad;
      if($row["VisibleProceso"]==1)
      {
        $proceso->disponibilidad = "Si";
      }
      if($row["VisibleProceso"]==0)
      {
        $proceso->disponibilidad = "No";
      }
    	$procesos[] = $proceso;  	
    }
    
    echo utf8_decode(json_encode($procesos)); 
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
  $sql = "UPDATE procesos SET VisibleProceso = ? WHERE IdProceso = ?";
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
  $proceso = new Proceso();
  $proceso->id = intval($_POST["id"]);
  $activo = intval("1");
  $conn = conectar();
  $sql = "UPDATE procesos SET VisibleProceso = ? WHERE IdProceso = ?";
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
    return "error al activar";
  }
  $conn->close();
  //return select();  
}

function modificar()
{
  $proceso = new Proceso();
  $proceso->id = intval($_POST["id"]);
  //echo (validarTexto($_POST["Proceso"]));
  $proceso->nombre = validarTexto($_POST["Proceso"]);
  $conn = conectar();
  $sql = "UPDATE procesos SET NombreProceso = ? WHERE IdProceso = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("si",
    $proceso->nombre,
    $proceso->id);
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
?>