<?php


class Router
{
    private $routes;

    public function __construct()
    {
    $routesPath = ROOT.'/config/routes.php';
    $this->routes = include($routesPath);
    }

        /*Returns request string*/
    private function getURI()
    {
        if  (!empty($_SERVER['REQUEST_URI'])){
            return trim($_SERVER['REQUEST_URI'],'/');
        }
    }

    public function  run()
    {
        //        получить строку запроса
        $uri = $this->getURI();

//        проверить наличие такого запроса в routes.php
        foreach ($this->routes as $uriPattern => $path) {
//      сравниваем $uriPattern и $uri
            if (preg_match("~^$uriPattern$~",$uri)){


                //        если есть совпадение , определить какой контроллер и action  обрабатывают запрос




//                получаем внутренний путь из внешнего согласно правилу
                $internalRoute = preg_replace("~$uriPattern~",$path,$uri);




//                определить контроллер , экшн, параметры
                $segments = explode('/',$internalRoute);
                $controllerName = array_shift($segments).'Controller';
                $controllerName = ucfirst($controllerName);

                $actionName ='action'.ucfirst(array_shift($segments));

                $parameters = $segments;


                //        подключить файл класса - контроллера
                $controllerFile = ROOT.'/controllers/' . $controllerName .'.php';

                if (file_exists($controllerFile)){
                    include_once($controllerFile);
                }

//        создать объект , вызвать метод (т.е action )

                $controllerObject = new $controllerName;

                $result = call_user_func_array(array($controllerObject,$actionName),$parameters);
                if($result != null){
                     break;
                }
            }
        }

    }




}