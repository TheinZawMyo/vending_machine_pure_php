<?php

namespace Theinzawmyo\VendingMachine\Controllers\Api;

use Theinzawmyo\VendingMachine\Auth\JwtAuth;

/**
 * API auth: issue JWT token from credentials.
 */
class AuthApiController
{
    private JwtAuth $jwtAuth;

    public function __construct(?JwtAuth $jwtAuth = null)
    {
        $this->jwtAuth = $jwtAuth ?? new JwtAuth();
    }

    public function login(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';
        if ($username === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password required']);
            return;
        }
        $token = $this->jwtAuth->tokenFromCredentials($username, $password);
        if ($token === null) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }
        echo json_encode(['token' => $token, 'type' => 'Bearer']);
    }

}
