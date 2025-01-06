<?php
// core/Router.php

class Router {
    public function route() {
        // Determine controller and action from GET parameters
        $controller = isset($_GET['controller']) ? ucfirst(strtolower($_GET['controller'])) . 'Controller' : 'HomeController';
        $action = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';
        
        // Path to controller file
        $controllerFile = __DIR__ . '/../app/controllers/' . $controller . '.php';
        
        // Check if controller file exists
        if(file_exists($controllerFile)) {
            require_once $controllerFile;
            
            // Check if class exists
            if(class_exists($controller)) {
                $controllerObject = new $controller();
                
                // Check if action method exists
                if(method_exists($controllerObject, $action)) {
                    $controllerObject->$action();
                } else {
                    // Action not found
                    $_SESSION['message'] = "Action '$action' not found in controller '$controller'.";
                    $_SESSION['message_type'] = 'error';
                    $this->loadErrorView();
                }
            } else {
                // Controller class not found
                $_SESSION['message'] = "Controller class '$controller' not found.";
                $_SESSION['message_type'] = 'error';
                $this->loadErrorView();
            }
        } else {
            // Controller file not found
            $_SESSION['message'] = "Controller file '$controllerFile' not found.";
            $_SESSION['message_type'] = 'error';
            $this->loadErrorView();
        }
    }

    private function loadErrorView() {
        // You can choose to load a general error view or specify per module
        // For simplicity, let's create a general error view in 'app/views/errors/error.php'
        include __DIR__ . '/../app/views/errors/error.php';
        exit();
    }
}
?>