<?php

namespace App\Core\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface AuthorizationVoterInterface
{
    public const CREATE = 'create';
    public const READ = 'read';
    public const DELETE = 'delete';
    public const UPDATE = 'update';
    public const FIND = 'find';

    public function supports(string $attribute, $subject): bool;

    public function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool;
}