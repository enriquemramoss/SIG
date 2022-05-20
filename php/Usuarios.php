<?php
if (session_status() == PHP_SESSION_ACTIVE) {
  
}
else
{
  session_start();
if (!isset($_SESSION['usuario'])||($_SESSION['usuario']!="Administrador"))
{
  header("location:Login.html");
}
}
include_once('datos.php');
include_once("validaciones.php");

if (isset($_GET['pa']) && $_GET['pa']=="i")
{
    $_POST["id"]=$_GET["ido"];
    eliminar();
    select();
}

if (isset($_GET['cp']) && $_GET['cp']=="i")
{
    selectprivilegios();
}

if (isset($_GET['ip']) && $_GET['ip']=="i")
{
    $_POST["privilegios"]=$_GET["stringprivilegio"];
    $_POST["nombre"]=$_GET["nombreprivilegio"];
    cambiarprivilegios();
}

if (isset($_GET['pp']) && $_GET['pp']=="i")
{
    $_POST["id"]=$_GET["ida"];
    activar();
    select();
}

if (isset($_GET['op']) && $_GET['op']=="i")
{
    $_POST["contraseña"]=$_GET["contraseña"];
    $_POST["usuario"]=$_GET["usuario"];
    $_POST["tipo"]=$_GET["tipo"];
    registrar();
    select();
}
if (isset($_GET['se']) && $_GET['se']=="i")
{
  select();
}

if (isset($_GET['wc']) && $_GET['wc']=="i")
{
    $_POST["nombre"]=($_GET["idusuarioM"]);
    $_POST["nombrep"]=($_GET["idM"]);
    $_POST["contraseña"]=($_GET["contraseñaM"]);
    $_POST["tipo"]=($_GET["selectedtipo2"]);
    modificar();
    select();
}

if (isset($_GET['wtc']) && $_GET['wtc']=="i")
{
    $_POST["nombre"]=($_GET["idusuarioM"]);
    $_POST["nombrep"]=($_GET["idM"]);
    $_POST["tipo"]=($_GET["selectedtipo2"]);
    modificar2();
    select();
}


class Usuario
{
  public $contraseña;
  public $nombre;
  public $tipo;
  public $disponibilidad;
  function __construct($nombre,$contraseña="",$tipo="",$disponibilidad="")
  {
  	if ($contraseña!="")
  	{
	$this->contraseña = $contraseña;
	$this->nombre = $nombre;
	$this->tipo = $tipo;
  $this->disponibilidad = $disponibilidad;
  
   }
   else
   {
	$conexion = conectar();
	$sql = "SELECT NombreUsuario,ContraseñaUsuario,TipoUsuario,VisibleUsuario".
	       " FROM usuarios WHERE NombreUsuario=?";
	$consulta = $conexion->prepare($sql);
    $consulta->bind_param("s",$nombre);
    $consulta->execute();
    $consulta->bind_result(
      	$this->nombre,
    	$this->contraseña,
    	$this->tipo,
    	$this->disponibilidad);
    $consulta->fetch();   
   }
  }
}

function registrar()
{
  $conn = conectar();
  $usuario = new Usuario("","","","");
  $usuario->nombre= validarTexto($_POST["usuario"]);
  $usuario->tipo=validarTexto($_POST["tipo"]);
  $visible=1;
  $sql = "INSERT INTO usuarios(NombreUsuario,ContraseñaUsuario,TipoUsuario,VisibleUsuario) VALUES(?,?,?,?)";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada &&  ($usuario->tipo=="Administrador" || $usuario->tipo=="Común"|| $usuario->tipo=="Invitado"))
  {
    $opciones = array(
      'salt' => "holacraiolaesteesunacontraseñacifrada".$usuario->nombre."%dr5#",
      'cost' => 12
    );
    $usuario->contraseña = password_hash($_POST["contraseña"],PASSWORD_DEFAULT,$opciones);
    $consultaPreparada->bind_param("sssi",
    $usuario->nombre, 
    $usuario->contraseña,
    $usuario->tipo,
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
    $sql = "SELECT NombreUsuario,TipoUsuario,VisibleUsuario FROM usuarios";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $usuario = new Usuario("","","","");
        $usuario->nombre = $row["NombreUsuario"];
        $usuario->tipo = $row["TipoUsuario"];
        $usuarios[] = $usuario;
        if($row["VisibleUsuario"]==1)
        {
          $usuario->disponibilidad = "Si";
        }
        if($row["VisibleUsuario"]==0)
        {
          $usuario->disponibilidad = "No";
        }
      }
      echo utf8_decode(json_encode($usuarios));    
    } else {
      return null;
    }
    $conn->close();
}

function selectprivilegios()
{
    $conn = conectar();
    $sql = "SELECT Nombre,Privilegio FROM privilegios";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $usuario = array();
        array_push($usuario,$row["Nombre"]);
        array_push($usuario,$row["Privilegio"]);
        $usuarios[] = $usuario;
      }
      echo utf8_decode(json_encode($usuarios));    
    } else {
      return null;
    }
    $conn->close();
}

function cambiarprivilegios()
{
  $privilegios =  $_POST["privilegios"];
  $nombre =  $_POST["nombre"];
  $conn = conectar();
  $sql = "UPDATE privilegios SET Privilegio = ? WHERE Nombre = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("ss",
    $privilegios,
    $nombre);
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

function eliminar()
{
  $usuario = new Usuario("","","","");
  $usuario->nombre = validarTexto($_POST["id"]);
  $activo = intval("0");
  $conn = conectar();
  $sql = "UPDATE usuarios SET VisibleUsuario = ? WHERE NombreUsuario = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("is",
    $activo,
    $usuario->nombre);
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
  $usuario = new Usuario("","","","");
  $usuario->nombre = validarTexto($_POST["id"]);
  $activo = intval("1");
  $conn = conectar();
  $sql = "UPDATE usuarios SET VisibleUsuario = ? WHERE NombreUsuario = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("is",
    $activo,
    $usuario->nombre);
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

function registrarPrimerUsuario()
{
  $conn = conectar();
  $usuario = new Usuario("","","","");
  $usuario->nombre= "root";
  $usuario->tipo="Master";
  $visible=1;
  $sql = "INSERT INTO usuarios(NombreUsuario,ContraseñaUsuario,TipoUsuario,VisibleUsuario) VALUES(?,?,?,?)";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $opciones = array(
      'salt' => "holacraiolaesteesunacontraseñacifrada".$usuario->nombre."%dr5#",
      'cost' => 12
    );
    $usuario->contraseña = password_hash("1234567890",PASSWORD_DEFAULT,$opciones);
    $consultaPreparada->bind_param("sssi",
    $usuario->nombre, 
    $usuario->contraseña,
    $usuario->tipo,
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

function modificar()
{
  $usuario = new Usuario("","","","");
  $usuario->tipo = validarTexto($_POST["tipo"]);
  $usuario->nombre = validarTexto($_POST["nombre"]);
  $contraseña = $_POST["contraseña"];
  $opciones = array(
    'salt' => "holacraiolaesteesunacontraseñacifrada".$usuario->nombre."%dr5#",
    'cost' => 12
  );
  $usuario->contraseña = password_hash($contraseña,PASSWORD_DEFAULT,$opciones);
  $nombre = validarTexto($_POST["nombrep"]);
  $conn = conectar();
  $sql = "UPDATE usuarios SET NombreUsuario = ?,ContraseñaUsuario = ?, TipoUsuario = ? WHERE NombreUsuario = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("ssss",
    $usuario->nombre,
    $usuario->contraseña,
    $usuario->tipo,
    $nombre);
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

function modificar2()
{
  $usuario = new Usuario("","","","");
  $usuario->tipo = validarTexto($_POST["tipo"]);
  $usuario->nombre = validarTexto($_POST["nombre"]);
  
  $nombre = validarTexto($_POST["nombrep"]);
  $conn = conectar();
  $sql = "UPDATE usuarios SET NombreUsuario = ?, TipoUsuario = ? WHERE NombreUsuario = ?";
  $consultaPreparada = $conn->prepare($sql);
  if ($consultaPreparada)
  {
    $consultaPreparada->bind_param("sss",
    $usuario->nombre,
    $usuario->tipo,
    $nombre);
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
//registrarPrimerUsuario();
?>