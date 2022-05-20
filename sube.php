<?php
session_start();
if (!isset($_SESSION['usuario'])
{
  header("location:Login.html");
}
$nombre=$_FILES['archivo']['name'];
$guardado=$_FILES['archivo']['tmp_name'];
$tipo_archivo = $_FILES['archivo']['type'];
$tamano_archivo = $_FILES['archivo']['size'];
$rutap=$_GET["idproc"];
$carpeta=$_GET["idCarpeta"];

//compruebo si las características del archivo son las que deseo
		if (!((strpos($tipo_archivo, "pdf") || strpos($tipo_archivo, "xls") || strpos($tipo_archivo, "xlsb") || strpos($tipo_archivo, "xlsm") || strpos($tipo_archivo, "xlsx") || strpos($tipo_archivo, "doc") || strpos($tipo_archivo, "docx")  ) 
						&& ($tamano_archivo < 10000000))){
							echo "Archivo no se pudo guardar el archivo, tipo o tamaño incorrecto";  
		}else{
		if(!file_exists($carpeta.'/'.$rutap)){
			mkdir($carpeta.'/'.$rutap,0777,true);
			if(file_exists($carpeta.'/'.$rutap)){
				if(!file_exists(($carpeta.'/'.$rutap.'/'.$nombre)))
				{
					if(move_uploaded_file($guardado, $carpeta.'/'.$rutap.'/'.$nombre)){
						echo "Archivo guardado con exito";
					}else{
						echo "Archivo no se pudo guardar";
					}
				}
				else{
					echo "Archivo ya existe";
				}	
			}
		}else{
			if(!file_exists(($carpeta.'/'.$rutap.'/'.$nombre)))
				{
					if(move_uploaded_file($guardado, $carpeta.'/'.$rutap.'/'.$nombre)){
						echo "Archivo guardado con exito";
					}else{
						echo "Archivo no se pudo guardar";
					}
				}
				else{
					echo "Archivo ya existe";
				}	
		}
		}

?>