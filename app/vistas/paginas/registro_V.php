<?php
	session_start(); //se crea una sesion llamada verifica, esta sesión es exigida cuando se entra en la pagina que recibe los datos del formulario de registro, para evitar que un usuario recarge la pagina que recibe y cargue los datos nuevamente a la BD
	$verifica = 1906;  
    $_SESSION["verifica"] = $verifica; 
    
    // Se carga el header 
    require(RUTA_APP . "/vistas/inc/header.php");
?>
	<div class="contenedor_11">
		<div onclick="ocultarMenu()"">
			<div class="contenedor_12">
	    		<h2>Registro de usuarios</h2> 
				<form action="Registro_C/recibeRegistro" method="POST" name="registroGratis" onsubmit="return validar_01()">
                    <fieldset class="fieldset_4">
                      	<legend>Datos personales</legend> 
		    			<input type="text" name="nombre" id="Nombre" placeholder="Nombre" onchange="" autocomplete="off"><!-- return literal() se encuentra en validarFormulario.js -->
						<input type="text" name="apellido" id="Apellido" placeholder="Apellido" onchange="" autocomplete="off"><!--  return literal() se encuentra en validarFormulario.js -->
						<input type="text" name="cedula" id="Cedula" placeholder="Cedula" onchange="" autocomplete="off"><!--  return literal() se encuentra en validarFormulario.js -->
						<input type="text" name="telefono" id="Telefono" placeholder="Telefono" onchange="" autocomplete="off"><!--  return literal() se encuentra en validarFormulario.js -->
						<input type="text" name="correo" id="Correo" placeholder="Correo electronico" onchange="validarFormatoCorreo(); setTimeout(llamar_verificaCorreo,200);" onclick="ColorearCorreo()"; autocomplete="off">
                       	<div class="contenedor_2" id="Mostrar_verificaCorreo"></div><!-- recibe respuesta de ajax llamar_verificaCorreo()-->
					</fieldset>         
					<fieldset class="fieldset_4">
						<legend>Datos de accceso a la plataforma</legend>  
						<div>
							<input type="password" name="clave" id="Clave" placeholder="Contraseña" onchange="llamar_verificaClave()">
							<div class="contenedor_3" id="Mostrar_verificaClave"></div><!-- recibe respuesta de ajax llamar_verificaClave()-->
							<input type="password" name="confirmarClave" id="ConfirmarClave" placeholder="Repetir contraseña">
						</div>                               
                       	<input type="submit" name="Registrarse" value="Registrarse" style=" display: block; width: 120px;">
                   	</fieldset> 
				</form>
			</div>
		</div>
	</div>
		<footer>
	        <?php include(RUTA_APP . "/vistas/inc/footer.php");?>
		</footer>
	</body>
</html>