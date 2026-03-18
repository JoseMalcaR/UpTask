<?php

namespace Controllers;

use MVC\Router;

class DashboardController {

    public static function dashboard(Router $router){

        session_start();
        
        isAuth();

        //Renderizar la vista
        $router->render('dashboard/index', [
            'titulo' => 'Proyectos'
        ]);
    }

    public static function crear_Proyecto(Router $router){

        session_start();
        isAuth();
        $alertas = [];

        //Renderizar la vista
        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
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