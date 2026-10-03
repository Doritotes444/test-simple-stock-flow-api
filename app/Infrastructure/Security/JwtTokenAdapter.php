<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Ports\Outbound\ITokenGeneratorPort;
use App\Domain\Model\User;

class JwtTokenAdapter implements ITokenGeneratorPort
{
    private string $secret;

    public function __construct(?string $secret = null)
    {
        $this->secret = $secret ?? (string) env('JWT_SECRET', 'default_secret_key_sena_123');
    }

    public function generateToken(User $user): string
    {
        $header = base64_encode((string) json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode((string) json_encode([
            'sub' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue(),
            'role' => $user->getRole(),
            'exp' => time() + (60 * 60 * 24), // 24 horas
        ]));

        $signature = hash_hmac('sha256', "{$header}.{$payload}", $this->secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return "{$header}.{$payload}.{$base64UrlSignature}";
    }

    public function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;
        $expectedSignature = hash_hmac('sha256', "{$header}.{$payload}", $this->secret, true);
        $base64UrlExpectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

        if (!hash_equals($base64UrlExpectedSignature, $signature)) {
            return null;
        }

        $decodedPayload = json_decode((string) base64_decode($payload), true);
        if (!$decodedPayload || (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time())) {
            return null;
        }

        return $decodedPayload;
    }
}
