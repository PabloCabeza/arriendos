<?php
    session_start();

    class Login_C extends Controlador{
        public function __construct(){
            //Se accede al servidor de base de datos
            //Se solicita informacion a la BD, se llama al metodo modelo de la clase Controlador, que devuelve una instacia del objeto 
            $this->ConsultaLogin_M = $this->modelo("Login_M");
        }

        //Siempre cargara este metodo por defecto, solo sino se solicita otra metodo
        public function index(){
            //Se verifica si el usuario esta memorizado en las cookie de su computadora y las compara con la BD, para recuperar sus datos y autorellenar el formulario de inicio de sesion, las cookies de registro de usuario se crearon en validarSesion.php
            if(isset($_COOKIE["id_usuario"]) AND isset($_COOKIE["clave"])){//Si la variable $_COOKIE esta establecida o creada
                // echo "Cookie afiliado =" . $_COOKIE["id_usuario"] ."<br>";
                // echo "Cookie clave =" .  $_COOKIE["clave"] ."<br>";
                
                $Cookie_usuario = $_COOKIE["id_usuario"];
                            
                //Se CONSULTA el usuario registrados en el sistema con el correo dado como argumento
                $usuarioRec= $this->ConsultaLogin_M->consultarUsuarioRecordado($Cookie_usuario);
                $Datos=[
                    "usuarioRecord"=>$usuarioRec,
                ];
                    
                //Se entra al formulario de sesion que esta rellenado con los datos del usuario
                $this->vista("paginas/login_Vrecord", $Datos);
            }
            else{
                //Se carga la vista login_V
                $this->vista("paginas/login_V");
            }
        }

        public function ValidarSesion(){
            $Recordar = isset($_POST["recordar"]) == 1 ? $_POST["recordar"] : "No desea recordar";
            $Clave = $_POST["clave_Arr"];
            $Correo = $_POST["correo_Arr"];
            // echo "La clave es: " . $Clave . "<br>";
            // echo "El correo es: " . $Correo . "<br>";
            // echo "Desea recordar: " . $Recordar . "<br>";
                        
            //Se CONSULTA el usuario registrados en el sistema con el correo dado como argumento
            $usuarios = $this->ConsultaLogin_M->consultarAfiliados($Correo);
            $Datos=[
                "usuarios"=>$usuarios,
            ];
            
            foreach($Datos["usuarios"] as  $usuario){
                $ID_Afiliado = $usuario->ID_Afiliado;
                // echo "ID_Afiliado: " . $ID_Afiliado  . "<br>"; 
            } 
        
            //Se crean las cookies para recordar al usuario en caso de que $Recordar exista
            if(isset($_POST["recordar"]) && $_POST["recordar"] == 1){//si pidió memorizar el usuario, se recibe desde principal.php   
                 //1) Se crea una marca aleatoria en el registro de este usuario
                 //Se alimenta el generador de aleatorios
                 mt_srand (time());
                 //Se genera un número aleatorio
                 $Aleatorio = mt_rand(1000000,999999999);
                // //  echo "Nº aleatorio= " . $Aleatorio . "<br>"; 
        
                 //3) Se introduce una cookie en el ordenador del usuario con el identificador del usuario y la cookie aleatoria porque el usuario marca la casilla de recordar
                setcookie("id_usuario", $ID_Afiliado, time()+365*24*60*60, "/");
                setcookie("clave", $Clave, time()+365*24*60*60, "/");
                //  echo "Se han creado las Cookies en validarSesion" . "<br>";
        
                // echo "La cookie ID_Usuario = " . $_COOKIE["id_usuario"] . "<br>";
                // echo "La cookie clave = " . $_COOKIE["clave"] . "<br>"; 
                
                //4) Se ACTUALIZA la marca aleatoria en el registro correspondiente al usuario
                //  $Actualizar="UPDATE afiliado SET aleatorio='$Aleatorio' WHERE ID_Afiliado='$ID_Afiliado'";
                //  mysqli_query($conexion, $Actualizar);
            }
            // //Verifica si los campo que se van a recibir estan vacios
            if(empty($_POST["correo_Arr"]) || empty($_POST["clave_Arr"])){
        
                //  echo"<script>alert('Debe Llenar todos los campos vacios');window.location.href='../vista/principal.php';</script>";
            }
            else{
                //sino estan vacios se sanitizan y se reciben
                $Correo = filter_input( INPUT_POST, 'correo_Arr', FILTER_SANITIZE_STRING);
                $Clave = filter_input(INPUT_POST, 'clave_Arr', FILTER_SANITIZE_STRING);
                // echo "Correo recibido: " .  $Correo . "<br>";
                // echo "Clave recibida: " . $Clave . "<br>";
        
                //Se solicita informacion a la BD, se llama al metodo modelo de la clase Controlador, que devuelve una instacia del objeto 
                $this->ConsultaLogin_M = $this->modelo("Login_M");

                //Se CONSULTA la contraseña enviada, que sea igual a la contraseña de la BD
                $usuarios_2= $this->ConsultaLogin_M->consultarContraseña($ID_Afiliado);
                $Datos_2=[
                    "usuarios_2"=>$usuarios_2,
                ];

                // print_r($usuarios_2);
                // echo "<br>";

                foreach($Datos_2["usuarios_2"] as $usuario_2){
                    // echo "Clave Usuario cifrada= " . $usuario_2->clave . "<br>"; 
                    // echo "Clave Usuario descifrada= " .  password_verify($Clave,$usuario_2->clave) . "<br>";
                } 
        
                //se descifra la contraseña con un algoritmo de desencriptado.
                if($Correo == $usuario->correo AND $Clave == password_verify($Clave,$usuario_2->clave)){
                    //se crea una sesion que almacena el ID_ID_Afiliado exigida en todas las páginas de su cuenta
            
                    $_SESSION["ID_Afiliado"]= $ID_Afiliado;//se crea una $_SESSION llamada ID_ID_Afiliado que almacena el ID del ID_ID_Afiliado para  forzar a que entre a su cuenta solo despues de logearse.
                    $_SESSION["Nombre"] = $usuario->nombre;//se crea una $_SESSION llamada Nombre que almacena el Nombre del ID_ID_Afiliado
                    // // echo $_SESSION["Nombre"];
            
                    $ID_Afiliado = $_SESSION["ID_Afiliado"];
                    // echo $_SESSION["ID_Afiliado"] . "<br>";

                    //Se da acceso y se redirije a la pagina entrada.php
                    header("location:" . RUTA_URL . "/Entrada_C");                            
                }
                else{  ?>
                        <script>
                            alert("USUARIO y CONTRASEÑA no son correctas");
                            history.back();
                        </script>
                        <?php 
                }    
            }   
        }
        
        public function RecuperarClave(){
            $Correo= $_POST["correo"];
            //echo "Correo= " . $Correo . "<br>";
        
            //Generamos un numero aleatorio que será el código de recuperación de contraseña
            //alimentamos el generador de aleatorios
            mt_srand (time());
            // //generamos un número aleatorio
            $Aleatorio = mt_rand(100000,999999);
            // echo "Nº aleatorio= " . $Aleatorio . "<br>"; 
                    
            //Se INSERTA el código aleatorio en la tabla "codigo-recuperacion y se asocia al correo del usuario
            $this->ConsultaLogin_M->insertarCodigoAleatorio($Correo, $Aleatorio);
            
            //Se envia correo al usuario informandole el código que debe insertar para verificar
            $email_to = $Correo;
            $email_subject = "Recuperación de contraseña";  
            $email_message ="Código de recuperación de contraseña: " . $Aleatorio;
            $headers = 'From: '. "admin@horebi.com" ."\r\n".
        
            'Reply-To: '. "admin@horebi.com"."\r\n" .        
            'X-Mailer: PHP/' . phpversion();
        
            @mail($email_to, $email_subject, $email_message, $headers);
            
            //Se redirecciona, la función redireccionar se encuentra en url_helper.php
            redireccionar("/RecuperarClave_C/index/$Correo"); 
        }
    }
?>    