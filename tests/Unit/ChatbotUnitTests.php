<?php

namespace Tests\Unit;

use App\Http\Controllers\ChatbotController;
use Tests\TestCase;

class ChatbotUnitTests extends TestCase
{
    public function test_chatbot_controller_exists()
    {
        // Simply test that the controller can be instantiated
        $controller = new ChatbotController();
        
        $this->assertInstanceOf(ChatbotController::class, $controller);
    }
    
    public function test_chatbot_has_required_properties()
    {
        // Test that the controller has the required properties
        // using reflection to access private properties
        
        $controller = new ChatbotController();
        $reflection = new \ReflectionClass($controller);
        
        $this->assertTrue($reflection->hasProperty('apiKey'));
        $this->assertTrue($reflection->hasProperty('baseUrl'));
        
        $apiKeyProp = $reflection->getProperty('apiKey');
        $apiKeyProp->setAccessible(true);
        $this->assertNotEmpty($apiKeyProp->getValue($controller));
        
        $baseUrlProp = $reflection->getProperty('baseUrl');
        $baseUrlProp->setAccessible(true);
        $this->assertEquals('https://api.openai.com/v1/chat/completions', $baseUrlProp->getValue($controller));
    }
}