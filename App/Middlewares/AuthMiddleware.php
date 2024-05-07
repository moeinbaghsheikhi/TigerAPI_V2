<?php
namespace App\Middlewares;

use App\Auth\JWTAuth;
use App\Traits\ResponseTrait;

class AuthMiddleware {
    use JWTAuth;
    use ResponseTrait;

    public function handle($request) {
        // Check if the request path is public
        dd(getPath());
        if ($this->isPublicPath(getPath())) {
            return true; // Allow public paths
        }

        // Check if the request has a JWT token
        $token = $this->getTokenFromRequest($request);
        if (!$token) {
            return $this->sendResponse(null, "Unauthorized!", true, 401);
        }

        // Verify the JWT token
        if (!$this->verifyToken($token)) {
            return $this->sendResponse(null, "Unauthorized Token!", true, 401);
        }

        return true;
    }

    private function isPublicPath($path) {
        // Define public paths
        $publicPaths = ['v1/login' ,'v1/verify', 'v1/register']; // Add more public paths if needed

        // Check if the requested path is public
        return in_array($path, $publicPaths);
    }

    private function getTokenFromRequest($request) {
        // Get token from headers, query string, or request body
        $token = $request->headers ?? $request->query ?? $request->body ?? null;
        return $token;
    }
}
