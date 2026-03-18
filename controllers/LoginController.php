<?php

namespace Controllers;
use MVC\Router;
use Model\Usuario;
use Classes\Email;

class LoginController {

    public static function login(Router $router){
        $alertas = [];


        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                //Verificar si el usuario existe
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || $usuario->confirmado) {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                } else {
                    //El usuario existe, verificar el password
                    if(password_verify($_POST['password'], $usuario->password)) {
                        //Inicar la sesión
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        //Redireccionar al dashboard
                        header('Location: /dashboard');
                }   else {
                        Usuario::setAlerta('error', 'Password incorrecto');
                    }
                }


            }
        }
        
        $alertas = Usuario::getAlertas();
        //Renderizar la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /');
    }


    public static function crear(Router $router){
        $alertas = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            $existeUsuario = Usuario::where('email', $usuario->email);
            
            if(empty($alertas)) {
                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    //Eliminar password2 para no guardarlo en la base de datos
                    unset($usuario->password2);

                    // Generar un token único
                    $usuario->crearToken();

                    // Crear el usuario
                    $usuario->guardar();

                    $resultado = $usuario->guardar();

                    //Enviar email de confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    if($resultado) {
                        header('Location: /mensaje');
                    }
            }
        }
    }
        //Renderizar la vista
        $router->render('auth/crear', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);

    }
   

    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar que el email exista
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
                if(empty($alertas)) {
                    $usuario = Usuario::where('email', $usuario->email);
                    if($usuario && $usuario->confirmado === "1") {
                        // Generar un nuevo token
                        $usuario->crearToken();
                        unset($usuario->password2);

                        // Actualizar el usuario
                        $usuario->guardar();

                        // Enviar el email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarInstrucciones();

                        // Alerta de exito
                        Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                    } else {
                        Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                    }
                }
         }

        //Renderizar la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvidé mi Password',
            'alertas' => $alertas
        ]);
    }


    public static function reestablecer(Router $router){
        $alertas = [];
        $token = s($_GET['token']);
        $mostrar = true;
        
        if(!$token) {
            header('Location: /');
        }
        //Idenficar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;

            


        }       
         if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $usuario->sincronizar($_POST);

            //Validar el nuevo password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                // Hashear el nuevo password
                $usuario->hashPassword();

                //Eliminar password2 para no guardarlo en la base de datos
                unset($usuario->password2);

                // Eliminar el token
                $usuario->token = null;

                // Guardar el usuario
                $resultado = $usuario->guardar();

                if($resultado) {
                    header('Location: /');
                }
            }

         }
            
            $alertas = Usuario::getAlertas();
            //Renderizar la vista
            $router->render('auth/reestablecer', [
                'titulo' => 'Reestablecer Password',
                'alertas' => $alertas,
                'mostrar' => $mostrar
            ]);
        }
  

    public static function mensaje(Router $router){
        //Renderizar la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);

    }

    public static function confirmar(Router $router){

        $token = s($_GET['token']);

        if(!$token) {
            header('Location: /');
        }

        //Encontrar el usuario con el token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            unset($usuario->password2);
            $usuario->token = null;

            // Guardar el usuario en la base de datos
            $usuario->guardar();

             Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }

        //Renderizar la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu Cuenta UpTask'
        ]);

    }

}