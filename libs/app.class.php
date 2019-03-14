<?php

/**
 * Description of App
 *
 * @author Nganthoiba
 */
class App {
    protected static $router;
    
    public static function getRouter(){
        return self::$router;
    }
    
    public static function run($uri){
        self::$router = new Router($uri);
        /** Controller Class Name **/
        $controller = ucfirst(self::$router->getController()).'Controller';
        
        /** Action Name **/
        $method = strtolower(self::$router->getMethodPrefix().self::$router->getAction());
        
        /*** Controller Object ***/
        $obj = new $controller();
        if(method_exists($obj, $method)){
            //Controller Action may return view path
            $view_path = $obj->$method();
            
            if(trim($view_path) === "" || $view_path == null) {
                $view_obj = new View($obj->getData(),$view_path);
                $content = $view_obj->render();
                //print_r(array($content));

                $layout = self::$router->getRoute();
                $layout_path = VIEWS_PATH.DS.$layout.'.html';
                $layout_view_obj = new View(array("content"=>$content),$layout_path);
                echo $layout_view_obj->render();
            }
            else{
                echo $view_path;
            }
        }
        else {
            throw new Exception("Method '".$method."' of controller class '".$controller."' does not exist.");
        }
   
    }
}
