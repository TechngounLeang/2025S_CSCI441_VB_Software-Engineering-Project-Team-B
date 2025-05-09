<?php

namespace Tests\Unit;

use App\Http\Controllers\LanguageController;
use Illuminate\Http\Request;
use Tests\TestCase;

class LanguageUnitTests extends TestCase
{
    public function test_controller_returns_redirect_response()
    {
        $controller = new LanguageController();
        
        $response = $controller->switchLang('en');
        
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }
}