<?php
// Written & debugged by: Tech Ngoun Leang
// Tested by: Tech Ngoun Leang
namespace Tests\Feature;

use Tests\TestCase;

class LanguageTests extends TestCase
{
    public function test_language_route_exists()
    {
        $routes = \Route::getRoutes();
        $hasRoute = false;
        
        foreach ($routes as $route) {
            if (str_contains($route->uri, 'language')) {
                $hasRoute = true;
                break;
            }
        }
        
        $this->assertTrue($hasRoute, 'Language route should exist');
    }
}