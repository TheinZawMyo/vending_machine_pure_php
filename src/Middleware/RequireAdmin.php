<?php

namespace Theinzawmyo\VendingMachine\Middleware;

use Theinzawmyo\VendingMachine\Auth\SessionAuth;

/**
 * Role-based access: allow only Admin.
 */
class RequireAdmin
{
    private SessionAuth $auth;

    public function __construct(?SessionAuth $auth = null)
    {
        $this->auth = $auth ?? new SessionAuth();
    }

    public function __invoke(callable $next): callable
    {
        return function (array $params) use ($next) {
            $this->auth->startSession();
            if (!$this->auth->isLoggedIn()) {
                $this->redirectToLogin();
            }
            if (!$this->auth->isAdmin()) {
                $this->forbidden();
            }
            $next($params);
        };
    }

    private function redirectToLogin(): void
    {
        header('Location: /login');
        exit;
    }

    private function forbidden(): void
    {
        http_response_code(403);
        echo 'Forbidden: Admin access required.';
        exit;
    }
}
