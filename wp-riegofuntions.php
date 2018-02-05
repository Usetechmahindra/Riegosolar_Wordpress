<?php

function testlibreria()
{
	echo "Llamada correcta a wp-riegofuntions.php";
}

function dbsession()
{
  $wp_session = WP_Session::get_instance();
  //Primero hacemos las conexiones
  $wp_session['serverdb'] = 'bbdd.riegosolar.net';
  $wp_session['dbuser'] = 'ddb86528';
  $wp_session['dbpass'] = 'Riegosolar77';
  $wp_session['dbname'] = 'ddb86528';
  $wp_session['charset'] = 'utf8';
}

function Redirect($url)
{
  $wp_session = WP_Session::get_instance();
  // echo $url;
  // Parametros instalación. Usuario y tlogon: window.open("http://localhost:8080/login?cid='username'&pwd='password'","mywindow")
  // Usar PHP de lectura de parametros gets.
  $urlparm=$url.'/riegosolar/loginget.php';
  $urlparm=$urlparm.'?usuario='.$wp_session['usuario'];
  $urlparm=$urlparm.'&pass='.$wp_session['pass64'];
  //echo $urlparm;
  // window.open("http://'.$urlparm.'") <-- Nueva ventana
  echo '<script type="text/javascript">
  window.open("http://'.$urlparm.'","_self")</script>';
}

function InstallUser()
{
    // creamos la sesion
    $wp_session = WP_Session::get_instance();
    //Primero hacemos las conexiones. temporal
	$mysqli = new mysqli($wp_session['serverdb'],$wp_session['dbuser'],$wp_session['dbpass'],$wp_session['dbname']) or die ("No se puede establecer la conexion!!!!"); 
	$mysqli->set_charset($wp_session['charset']);
	
    // La funcion lee las instalaciones en la B.D. de donDominio para retornarlas en el combo.
    $sql = "select raspberry.nombre,raspberry.url ";
    $sql .="from raspberry,usuario ";
    $sql .="where raspberry.idraspberry = usuario.idrapberry ";
    $sql .=" and raspberry.estado = 1 ";
	// Añadir filtro si hay datos
	if(!empty($wp_session['usuario']))
	{
	  $sql .=" and usuario.nombre='".$wp_session['usuario']."'";
	  $sql .=" and usuario.password='".$wp_session['vpass']."'";
	}
	
	$rinstall = $mysqli->query($sql);
    echo "<option value='0'> Seleccionar Instalacion </option>";
    //echo "<option value='http://riegosolar.net'> ".$sql." </option>";
    while($row = $rinstall->fetch_assoc()) {
      $vurl = $row["url"] ; //Asignamos la url
      $vinstall = substr($row["nombre"],0,50); // Asignamos el nombre del campo que quieras mostrar
      $vcombo = "<option value=".$vurl;
      if($_POST['cbinstall']==$vurl) {
          $vcombo = $vcombo. " SELECTED ";
      }
      $vcombo = $vcombo.">";
      $vcombo = $vcombo.$vinstall."</option>"; 
      echo $vcombo;
    } 
	//Cerramos el ciclo 
	mysqli_stmt_free_result($resparametros);
    mysqli_stmt_close($resparametros);
    Return 1;
}

function checkuserdb($vuser,$vpass)
{
    // creamos la sesion y comprobamos si el user ha dado al boton del form.
    $wp_session = WP_Session::get_instance();
    $wp_session['textsesion'] = "Sesion no iniciada.";
    //Validar las variables pasadas.
    if(!empty($vuser))
    {
        if(!empty($vpass))
        {
            $wp_session['textsesion'] = 'Se han introducido el usuario y contraseña.';
        }else {
            $wp_session['textsesion'] = 'No ha introducido la password.';
            return -1;          
        }
    }else
    {
        $wp_session['textsesion'] = 'No ha introducido un usuario.';
        return -1;
    }
    //Primero hacemos las conexiones
    dbsession();
    //Variable de sesion de selección de tabs
    $wp_session['stabindex'] = 0;
	
	$mysqli = new mysqli($wp_session['serverdb'],$wp_session['dbuser'],$wp_session['dbpass'],$wp_session['dbname']) or die ("No se puede establecer la conexion!!!!"); 
	$mysqli->set_charset($wp_session['charset']);
    
    // En la columna password se ha grabado el valor con la funcion MD5. update campo=MD5('valor');
    // Utilizar base64 para transferir de manera segura a las raspberrys
    $wp_session['usuario'] = $vuser;
    $wp_session['pass64'] = base64_encode($vpass);
    $wp_session['vpass'] = md5($vpass); // Encrypted Password
    
    // Comprobar la tabla de usuarios.
    $sql = "select nombre,password from usuario";
    $sql.= " where nombre ='".$vuser."'";
    $sql.= " and password ='".$wp_session['vpass']."'";
    //echo $sql;
    // Execute the query, or else return the error message.
    $consulta = $mysqli->query($sql);
	
    if ($consulta->num_rows > 0) {
        // Variable de tiempo de sesion.
        $wp_session['tlogon'] = time();
        $wp_session['minsesion'] = 10;
        $wp_session['usuario'] = $vuser;
        $wp_session['textsesion'] = 'Login establecido '.$_SESSION['tlogon'];
        return 1;
    }else {
        $wp_session['textsesion'] = 'Los datos introducidos no corresponden a ningun usuario.';
        return -1;
    }
    
}

function LoginAdmin($vuseradmin,$vpassadmin)
{
    // creamos la sesion y comprobamos si el user ha dado al boton del form.
    $wp_session = WP_Session::get_instance();
    $wp_session['textsesion'] = "Sesion no iniciada.";
    //Validar las variables pasadas.
    if(!empty($vuseradmin))
    {
        if(!empty($vpassadmin))
        {
            $wp_session['textsesion'] = 'Se han introducido el usuario y contraseña.';
        }else {
            $wp_session['textsesion'] = 'No ha introducido la password.';
            return false;          
        }
    }else
    {
        $wp_session['textsesion'] = 'No ha introducido un usuario.';
        return false;
    }
    //Primero hacemos las conexiones
    dbsession();
    //Variable de sesion de selección de tabs
    $wp_session['stabindex'] = 0;
	
	$mysqli = new mysqli($wp_session['serverdb'],$wp_session['dbuser'],$wp_session['dbpass'],$wp_session['dbname']) or die ("No se puede establecer la conexion!!!!"); 
	$mysqli->set_charset($wp_session['charset']);
	   
    // En la columna password se ha grabado el valor con la funcion MD5. update campo=MD5('valor');
    // Utilizar base64 para transferir de manera segura a las raspberrys
    $wp_session['useradmin'] = $vuseradmin;
    $wp_session['passadmin'] = md5($vpassadmin); // Encrypted Password
    
    // Comprobar la tabla de usuarios.
    $sql = "select idadmin,usuario,password from adminrapberry";
    $sql.= " where usuario ='".$vuseradmin."'";
    $sql.= " and password ='".$wp_session['passadmin']."'";
    //echo $sql;
    // Execute the query, or else return the error message.
	$consulta = $mysqli->query($sql);

    if ($consulta->num_rows > 0) {
        // Variable de tiempo de sesion.
        $wp_session['tlogon'] = time();
        $wp_session['minsesion'] = 10;
        $wp_session['textsesion'] = 'Login '.$_SESSION['tlogon'];
        return true;
    }else {
        $wp_session['textsesion'] = 'Los datos introducidos no corresponden a ningun usuario.';
        return false;
    }
}

function CheckLogin()
{
     $wp_session = WP_Session::get_instance();
     
     if(empty($wp_session['usuario']))
     {
        $wp_session['textsesion'] = "Sesión no iniciada.";
         return false;
     }
      if ($wp_session['tlogon'] + $wp_session['minsesion'] * 60 < time()) {
          $wp_session['textsesion'] = "Por razones de seguridad su sesión ha esperiado, vuelva a ingresar sus datos en el sistema.";
          return false;
          // session timed out
      }
      // Añadimos tiempo a la sesion
      $wp_session['tlogon'] = time();
     return true;
}
// ABM Usuarios
function findinstall($vnombreb)
{
  $wp_session = WP_Session::get_instance();
  $wp_session['textsesion'] = "Sesion no iniciada.";
  //Primero hacemos las conexiones
  dbsession();
  //Variable de sesion de selección de tabs
  $wp_session['stabindex'] = 0;
  
  $mysqli = new mysqli($wp_session['serverdb'],$wp_session['dbuser'],$wp_session['dbpass'],$wp_session['dbname']) or die ("No se puede establecer la conexion!!!!"); 
  $mysqli->set_charset($wp_session['charset']);
	
  // Comprobar la tabla de usuarios.
  $sql = "select idraspberry,nombre,url,estado,comentario from raspberry";
  $sql.= " where nombre = '".$vnombreb."'";
  //echo $sql;

  $consulta = $mysqli->query($sql);
  if ($consulta->num_rows > 0) {
     //echo $sql;
      // Variable de tiempo de sesion.
      $wp_session['tlogon'] = time();
      $wp_session['minsesion'] = 10;
      $wp_session['textsesion'] = 'Login '.$_SESSION['tlogon'];
      // Datos de la primera instalacion en sesion
	  $fila = $consulta->fetch_assoc();
      $wp_session['idraspberry'] = $fila['idraspberry'];
      $wp_session['nombre'] = $fila['nombre'];
      $wp_session['url'] = $fila['url'];
      $wp_session['estado'] = $fila['estado'];
      $wp_session['comentario'] = $fila['comentario'];
      return true;
  }else {
      $wp_session['textsesion'] = 'Los datos introducidos no corresponden a ninguna instalacion.';
      $wp_session['idraspberry'] = 0;
      $wp_session['nombre'] = "";
      $wp_session['url'] = "";
      $wp_session['estado'] = 0;
      $wp_session['comentario'] = "";
      return false;
  }
}

function cbinstall()
{
    // creamos la sesion
    $wp_session = WP_Session::get_instance();
    //Primero hacemos las conexiones. temporal
    $mysqli = new mysqli($wp_session['serverdb'],$wp_session['dbuser'],$wp_session['dbpass'],$wp_session['dbname']) or die ("No se puede establecer la conexion!!!!"); 
    $mysqli->set_charset($wp_session['charset']);
    // La funcion lee las instalaciones en la B.D. de donDominio para retornarlas en el combo.
    $sql = "select raspberry.idraspberry,raspberry.nombre ";
    $sql .="from raspberry ";
    $sql .="where raspberry.estado = 1 ";
	$rinstall = $mysqli->query($sql);
    echo "<option value='0'> Seleccionar Instalacion </option>";
    //echo "<option value='http://riegosolar.net'> ".$sql." </option>";
    while($row = $rinstall->fetch_assoc()) {
      $vurl = $row["idraspberry"] ; //Asignamos la url
      $vinstall = substr($row["nombre"],0,50); // Asignamos el nombre del campo que quieras mostrar
      $vcombo = "<option value=".$vurl;
      if($_POST['cbinstall']==$vurl) {
          $vcombo = $vcombo. " SELECTED ";
      }
      $vcombo = $vcombo.">";
      $vcombo = $vcombo.$vinstall."</option>"; 
      echo $vcombo;
    }
	//Cerramos el ciclo 
	mysqli_stmt_free_result($resparametros);
    mysqli_stmt_close($resparametros);
    Return 1;
}

function updateinstall()
{
  $wp_session = WP_Session::get_instance();
  //Primero hacemos las conexiones
  dbsession();
  // Ejecutar la sentencia
  $mysqli = new mysqli($wp_session['serverdb'],$wp_session['dbuser'],$wp_session['dbpass'],$wp_session['dbname']) or die ("No se puede establecer la conexion!!!!"); 
  $mysqli->set_charset($wp_session['charset']);
  //echo "Id rapberry:".$wp_session['idraspberry'];
  $wp_session['estado'] = 1;
  // Actualizar / Crea instalacion
  if (empty($wp_session['idraspberry']))
  {
  // Insert
    $sql = "insert into raspberry (nombre,url,estado,comentario) ";
    $sql .= " VALUES('".$_POST['nombre']."','".$_POST['url']."',".$wp_session['estado'].",'".$_POST['comentario']."')";
    //echo $sql;
  }else {
      // Update
      $sql = "update raspberry set nombre='".$_POST['nombre']."',url='".$_POST['url']."',estado=".$_POST['estado'].",comentario='".$_POST['comentario']."'";
      $sql .=" where idraspberry = ".$wp_session['idraspberry'];
  }
  if ($mysqli->query($sql) === FALSE) 
  {
    $wp_session['textsesion']="Error en grabación en base de datos:" . $mysqli->error;
    $mysqli->close();
    return false; 
  }
  // Coger el idrapberry que ha generado
  findinstall($_POST['nombre']);
  // Correcto.
  return true;
}

function updateuser($vidraspberry)
{
  $wp_session = WP_Session::get_instance();
  //Primero hacemos las conexiones
  dbsession();
  // Ejecutar la sentencia
  $mysqli = new mysqli($wp_session['serverdb'],$wp_session['dbuser'],$wp_session['dbpass'],$wp_session['dbname']) or die ("No se puede establecer la conexion!!!!"); 
  $mysqli->set_charset($wp_session['charset']);
  // Controlar valores
  if(empty($_POST['nombre']))
  {
    $wp_session['textsesion'] ="Debe de escribir el nombre del usuario.";
    return false;
  }
  if(empty($_POST['password']))
  {
    $wp_session['textsesion'] ="Debe de escribir la password para el usuario.";
    return false;
  } 
  // codificar password md5
  $usuario = $_POST['nombre'];
  $vpassmd5 = md5($_POST['password']);
  // Controlar si existe o hay que actualizar
  $sql = "select idusuario,nombre ";
  $sql .=" from usuario ";
  $sql .=" where idrapberry = ".$vidraspberry;
  $sql .=" and nombre='".$usuario."'";
  $rusuario = $mysqli->query($sql);
  if ($rusuario->num_rows > 0) {
    // Update
	$row = $rusuario->fetch_assoc();
    $sql = "update usuario set password='".$vpassmd5."' where idusuario=".$row["idusuario"];
    $wp_session['textsesion']="Usuario actualizado correctamente.";
    //echo $sql;
    
  }else {
    // Insert
    $sql = "insert into usuario (idrapberry,nombre,password) ";
    $sql .= " VALUES(".$vidraspberry.",'".$usuario."','".$vpassmd5."')";
    $wp_session['textsesion']="Usuario creado correctamente.";
    //echo $sql;
  }
  // Lanzar la sql
  if ($mysqli->query($sql) === FALSE) 
  {
    $wp_session['textsesion']="Error en grabación en base de datos:" . $mysqli->error;
    return false; 
  }
  return true;
}

?>