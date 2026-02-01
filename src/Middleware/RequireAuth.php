<?php

namespace Theinzawmyo\VendingMachine\Middleware;

use Theinzawmyo\VendingMachine\Auth\SessionAuth;

/**
 * Require any authenticated user.
 */
class RequireAuth
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
                header('Location: /login');
                exit;
            }
            $next($params);
        };
    }
}
