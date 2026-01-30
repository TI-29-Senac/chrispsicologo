<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Psico\Core\Auth;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        // Mock das variáveis de ambiente necessárias
        $_ENV['JWT_SECRET'] = 'teste_secret_must_be_very_long_to_work_32chars';
    }

    public function testGenerateToken()
    {
        $token = Auth::generate(1, 'admin');
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        
        return $token;
    }

    /**
     * @depends testGenerateToken
     */
    public function testValidateToken($token)
    {
        $payload = Auth::validate($token);
        
        $this->assertNotNull($payload);
        $this->assertEquals(1, $payload->sub);
        $this->assertEquals('admin', $payload->role);
    }

    public function testInvalidToken()
    {
        $invalidToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.INVALID.SIGNATURE';
        $payload = Auth::validate($invalidToken);
        
        $this->assertNull($payload);
    }
}
