<?php
ini_set('display_error', 1);
ini_set('display_startup_error', 1);
include('inc/funciones.inc.php');
include('secure/ips.php');

$metodo_permitido = "POST";
$archivo = "../logs/log.log";
$dominio_autorizado = "localhost";
$ip = ip_in_ranges($_SERVER["REMOTE_ADDR"], $rango);
$txt_usuario_autorizado = "admin";
$txt_password_autorizado = "admin";

//SE VERIFRICA QUE LA DIRECCIÓN DE ORIGEN SEA AUTORIZADA
if(array_key_exists("HTTP_REFERER", $_SERVER)){
//VIENE DE UNA PAGINA DENTRO DEL SISTEMA

// SE VERIFIVA QUE LA DIRECCION DE ORIGEN SEA AUTORIZADA
   if(strpost($_SERVER["HTTP_REFERER"], $dominio_autorizado)){
    //EL REFERER DE DONDE VIENE LA PETICION ESTA AUTORIZADO

    //SE VERIFICA QUE LA DIRECCION IP ESTE AUTORIZADA
    if($ip === true){
        //LA DIRECCION IP DEL USUARIO SI ESTA AUTORIZADA

        //SE VERIFICA QUE EL USUARIO HAYA ENVIADO UNA PETICION AUTORIZADA
        if($_SERVER["REQUEST_METHOD"] == $metodo_permitido){

            //EL METODO ENVIADO POR EL USUARIO SI ESTA AUTORIZADO


            //LIMPIEZA DE VALORES DESDE EL FORMULARIO
            $valor_campo_usuario = (     (array_key_exists("txt_user",$_POST)) ? htmlspecialchars(stripslashes(trim($_POST["txt_user"])),ENT_QUOTES) : ""   );
            $valor_campo_password = (     (array_key_exists("txt_pass",$_POST)) ? htmlspecialchars(stripslashes(trim($_POST["txt_pass"])),ENT_QUOTES) : ""   );



            //SE VERIFICA QUE LOS VALORES DE LOS CAMPOS SEAN DIFERENTE A VACIO
            if(($valor_campo_usuario!="" || strlen($valor_campo_usuario) > 0) and ($valor_campo_password!="" || strlen($valor_campo_password) > 0) ){

                $usuario = preg_match('/^[a-zA-Z0-9]{1,10}+$/', $valor_campo_usuario);//SE VERIFICA CONH UN PATRON SI EL VALOR DEL CAMPO "USUARIO" CUMPLE CON LAS CONDICIONES ACEPTABLES (SE ACEPTAN NUMEROS)
                $password = preg_match('/^[a-zA-Z0-9]{1,10}+$/', $valor_campo_password);//SE VERIFICA CONH UN PATRON SI EL VALOR DEL CAMPO "USUARIO" CUMPLE CON LAS CONDICIONES ACEPTABLES (SE ACEPTAN NUMEROS)
                
                //SE VERIFICA QUE LOS RESULTADOS DEL PATRON SEAN EXCLUSIVAMENTE POSITIVOS O SATISFACTORIOS 
                if($usuario !== false and $usuario !== 0 and $password !== false and $password !== 0){
                    //EL USUARIO Y LA CONTRASEÑA SI POSEEN VALORES ACEPTADOS

                    if($valor_campo_usuario === $txt_usuario_autorizado and $valor_campo_password === $txt_password_autorizado){
                        //EL USUARIO INGRESO CREDENCIALES CORRECTAS
                        echo("HOLA MUNDO");
                        crear_editar_log($archivo, "EL CLIENTE INICIO SESION SATISFACTORIAMENTE", 1, $_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);

                    }else{
                        //EL USUARIO NO INGRESO LAS CREDENCIALES CORRECTAS
                        crear_editar_log($archivo, "CREDENCIALES INCORRECTAS ENVIADAS HACIA //$_SERVER ["HTTP_HOST"] $_SERVER["HTTP_REQUEST_URI"]",2,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                        header("HTTP/1.1 301 Moved Permanently");
                        header("Location: ../?status=7")

                    }

                    

                }else {
                    crear_editar_log($archivo, "ENVIO DE DATOS DEL FORMULARIO CON CARCATERES NO SOPORTADOS",3, $_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ../?status=7");
                    //LOS VALORES INGRESADOS EN LOS CAMPOS POSEEN CARACTERES NO SOPORTADOS
                }

            }else{
                crear_editar_log($archivo, "ENVIO DE CAMPOS VACIOS AL SERVIDOR",2, $_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ../?status=5");


            }

        }else{
            crear_editar_log($archivo, "ENVIO DE METODO NO AUTORIZADO",2, $_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ../?status=4");
            //EL METODO ENVIADO POR EL USUARIO NO ESTA AUTORIZADO
        }

    }else{
        crear_editar_log($archivo, "DIRECCION IP NO AUTORIZADA",2, $_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ../?status=3");

    }
   }else{
    crear_editar_log($archivo, "HA INTENTADO SUPLANTAR UN REFERER QUE NO ESTA AUTORIZADO",2, $_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ../?status=2");
    // EL REFERER DE DONDE VIENE LA PETICION ES DE UN ORIGEN DESCONOCIDO

   }
}else{
    crear_editar_log($archivo, "EL USUARIO INTENTO INGRESAR DE UNA MANERA INCORRECTA",2, $_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_USER_AGENT"]);
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ../?status=1");
// EL USUARIO DIGITO LA URL DESDE EL NAVEGADOR SIN PASAR POR FORMULARIO

}
?>