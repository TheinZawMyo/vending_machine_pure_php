<?php

namespace Theinzawmyo\VendingMachine\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Theinzawmyo\VendingMachine\Models\User;
use Theinzawmyo\VendingMachine\Repositories\UserRepository;

/**
 * JWT token-based authentication for API access.
 */
class JwtAuth
{
    private string $secret;
    private string $algorithm;
    private int $expiration;
    private UserRepository $userRepository;

    public function __construct(array $config = [], ?UserRepository $userRepository = null)
    {
        $config = array_merge(
            require __DIR__ . '/../../app/Config/jwt.php',
            $config
        );
        $this->secret = $config['secret_key'];
        $this->algorithm = $config['algorithm'] ?? 'HS256';
        $this->expiration = (int) ($config['expiration'] ?? 3600);
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    public function tokenFromCredentials(string $username, string $password): ?string
    {
        $user = $this->userRepository->verifyPassword($username, $password);
        if ($user === null) {
            return null;
        }
        $payload = [
            'sub' => (string) $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + $this->expiration,
        ];
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function userFromToken(string $token): ?User
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return $this->userRepository->find((int) $decoded->sub);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function userFromRequest(): ?User
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(\S+)/', $header, $m)) {
            return $this->userFromToken($m[1]);
        }
        return null;
    }
}
