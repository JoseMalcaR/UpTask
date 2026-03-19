<?php

namespace Controllers;

use MVC\Router;
use Model\Proyecto;

class DashboardController {

    public static function dashboard(Router $router){

        session_start();
        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        //Renderizar la vista
        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_Proyecto(Router $router){

        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
                //Crear una nueva instancia de Proyecto
                $proyecto = new Proyecto($_POST);
    
                //Validar que no haya campos vacios
                $alertas = $proyecto->validarProyecto();
    
                if(empty($alertas)) {
                    //Generar una URL unica
                    $hash = md5(uniqid());
                    $proyecto->url = $hash;
    
                    //Almacenar el creador del proyecto
                    $proyecto->propietarioId = $_SESSION['id'];
    
                    //Guardar el proyecto
                    $proyecto->guardar();
    
                    //Redireccionar
                    header('Location: /proyecto?id=' . $proyecto->url);
                    exit;
                }
        }

        //Renderizar la vista
        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router){

        session_start();
        isAuth();

        $token = $_GET['id'] ;

        if(!$token) {
            header('Location: /dashboard');
            exit;
        }

        $proyecto = Proyecto::where('url', $token);

        if(!$proyecto || (string) $proyecto->propietarioId !== (string) $_SESSION['id']) {
            header('Location: /dashboard');
            exit;
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }
    


    public static function perfil(Router $router){

        session_start();
        
        isAuth();

        //Renderizar la vista
        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil'
        ]);
    }
}