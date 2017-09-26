<?php

namespace Greg\ToDo\Http\Middleware;

use Greg\ToDo\Http\Redirect;

class NonAuthOnlyMiddleware extends Middleware
{
    public function run()
    {
        if (!empty($_SESSION['user'])) {
            return true;
        }
        
        return new Redirect("/");
    }
}