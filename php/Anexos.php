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

if (isset($_GET['up']) && $_GET['up']=="i")
{
  $_POST["Nombre"]=$_FILES['archivo']['name'];
  $array = explode('.', $_FILES['archivo']['name']);
  $_POST["Tipo"] = end($array);
  $_POST["idC"]=($_GET["idC"]);
  $_POST["IdProcedimiento"]=$_GET["idproc"];
  insertarLugardeArchivo();
}

if (isset($_GET['op']) && $_GET['op']=="i")
{
    $_POST["Nombre"]=$_GET["idprocedimiento"];
    $_POST["idC"]=($_GET["idC"]);
    $_POST["idC2"]=($_GET["idC2"]);
    $_POST["id2"]=($_GET["idA2"]);
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
if (isset($_GET['pc']) && $_GET['pc']=="i")
{
    selectprocesos();
}

if (isset($_GET['pd']) && $_GET['pd']=="i")
{
    $_POST["idC"]=($_GET["idC"]);
    selectprocedimientos();
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
    $_POST["Anexo"]=($_GET["idprocedimiento2"]);
    modificar();
}

if (isset($_GET['pdd']) && $_GET['pdd']=="i")
{
    $_POST["idC"]=($_GET["idA"]);
    selectprocedimientos();
}

if (isset($_GET['ba']) && $_GET['ba']=="i")
{
  $_POST["id"]=($_GET["idC2"]);
  //$_POST["id2"]=($_GET["idC2"]);
  select();
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
  //public $disponibilidad="";
  function Procedimiento()
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

class Anexo
{
  public $id=0;
  public $nombre = "";
  public $disponibilidad="";
  public $documentos="";
	function Anexo()
	{

	}
}

function select()
{
    $conn = conectar();
    $anexo = new Anexo();
    //$proceso = new Proceso();
    $procedimiento = new Procedimiento();
    if (isset($_GET['ba']) && $_GET['ba']=="i")
  {
    //$proceso->id = intval($_POST["id"]);
    $procedimiento->id = intval($_POST["id"]);
  }
  else
  {
    $procedimiento->id = intval($_POST["idC2"]);
  }
    $sql = "SELECT IdAnexo,NombreAnexo,VisibleAnexo FROM procedimientos pd,anexos a WHERE pd.IdProcedimiento=? and pd.IdProcedimiento=a.IdProcedimiento";
    $consultaPreparada = $conn->prepare($sql);
    if ($consultaPreparada)
    {
    $consultaPreparada->bind_param("i",
    $procedimiento->id);
    $consultaPreparada->execute();
    //return $consultaPreparada->store_result();  
    $result = $consultaPreparada->get_result();
    }
    
    //$result = $conn->query($sql);
    $anexo;
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $anexo = new Anexo();
        $anexo->id = $row["IdAnexo"];
        $anexo->nombre = $row["NombreAnexo"];
        //echo$procesoo->disponibilidad;
        if($row["VisibleAnexo"]==1)
        {
          $anexo->disponibilidad = "Si";
        }
        if($row["VisibleAnexo"]==0)
        {
          $anexo->disponibilidad = "No";
        }
        $anexo->documentos=documentos($row["IdAnexo"],$conn);
          $anexos[] = $anexo;  	
      }
      
      echo utf8_decode(json_encode($anexos)); 
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
  } else {
    return null;
  }
  $conn->close();
}

function selectprocedimientos()
{
  $conn = conectar();
  $proceso=new Proceso();
  $proceso->id = intval($_POST["idC"]);
  $sql = "SELECT IdProcedimiento,NombreProcedimiento FROM procedimientos WHERE IdProceso=?";
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
      $procedimientos[] = $procedimiento;  	
    }
  
    echo utf8_decode(json_encode($procedimientos)); 
    //return json_encode($proceso);   
  }  
  else {
      //echo "pepe";
    return null;
  }
  $conn->close();
}


function insertar()
{
  $proceso = new Proceso();
  $procedimiento=new Procedimiento();
  $anexo=new Anexo();
  $anexo->nombre = validarTexto($_POST["Nombre"]);
  $proceso->id = intval($_POST["id"]);
  $procedimiento->id = intval($_POST["id2"]);
  $visible=1;
  $conn = conectar();
  $sql = "INSERT INTO anexos(IdProcedimiento,NombreAnexo,VisibleAnexo) VALUES(?,?,?)";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("isi",
      $procedimiento->id,
      $anexo->nombre,
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


function eliminar()
{
  $anexo = new Anexo();
  $anexo->id = intval($_POST["id"]);
  $activo = intval("0");
  $conn = conectar();
  $sql = "UPDATE anexos SET VisibleAnexo = ? WHERE IdAnexo = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("ii",
    $activo,
    $anexo->id);
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
  $anexo = new Anexo();
  $anexo->id = intval($_POST["id"]);
  $activo = intval("1");
  $conn = conectar();
  $sql = "UPDATE anexos SET VisibleAnexo = ? WHERE IdAnexo = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("ii",
    $activo,
    $anexo->id);
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
  $anexo = new Anexo();
  $anexo->id = intval($_POST["id"]);
  //echo (validarTexto($_POST["Proceso"]));
  $anexo->nombre = validarTexto($_POST["Anexo"]);
  $conn = conectar();
  $sql = "UPDATE anexos SET NombreAnexo = ? WHERE IdAnexo = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("si",
    $anexo->nombre,
    $anexo->id);
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
  $sql = "INSERT INTO formatoanexo(IdAnexoFormatoAnexo,NombreFormatoAnexo,ExtensiónFormatoAnexo) VALUES(?,?,?)";
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
    $sql2 = "SELECT IdAnexoFormatoAnexo,IdFormatoAnexo,NombreFormatoAnexo,SeleccionadoFormatoAnexo FROM formatoanexo fp, anexos pc Where fp.IdAnexoFormatoAnexo=? and pc.IdAnexo=fp.IdAnexoFormatoAnexo";
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
        $nombres->id=$row["IdAnexoFormatoAnexo"];
        $nombres->idF=$row["IdFormatoAnexo"];
        $nombres->nombre=$row["NombreFormatoAnexo"];
        $nombres->seleccionado=$row["SeleccionadoFormatoAnexo"];
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
  $sql = "DELETE FROM formatoanexo WHERE IdFormatoAnexo = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("i",
    $formato->idProcedimiento);
    $consultaPreparada->execute();
   mkdir('documents/'.$idC.'/pepe',0777,true);
   unlink('documents/anexos/'.$idC.'/'.$formato->nombre);
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
  $sql = "SELECT NombreFormatoAnexo FROM formatoanexo WHERE IdFormatoAnexo=?";
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
      $formato->nombre = $row["NombreFormatoAnexo"];
    }
    return $formato;    
  } else {
    return null;
  }
  $conn->close();
}

function insertarSeleccionado()
{
  $Valor = $_POST["Valor"];
  $id = $_POST["idfp"];
  $conn = conectar();
  $sql = "UPDATE formatoanexo SET SeleccionadoFormatoAnexo = ? WHERE IdFormatoAnexo = ?";
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
?>