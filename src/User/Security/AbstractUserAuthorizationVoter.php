<?php

namespace App\User\Security;

use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\String\UnicodeString;

abstract class AbstractUserAuthorizationVoter extends Voter implements AuthorizationVoterInterface
{
    public function supports(string $attribute, $subject): bool
    {
        if (!$this->isSubjectSupported($subject)) {
            return false;
        }

        if (!in_array($attribute, $this->getActionsHandled(), true)) {
            return false;
        }

        return true;
    }

    abstract public function isSubjectSupported($subject): bool;

    public function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::CREATE => $this->canCreate($subject, $user),
            self::READ => $this->canRead($subject, $user),
            self::UPDATE => $this->canUpdate($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::FIND => $this->canFind($subject, $user),
            default => $this->handleCustomAction($attribute, $subject, $user),
        };
    }

    protected function handleCustomAction(string $attribute, object $subject, User $user): bool
    {
        $methodName = 'can'.ucfirst((new UnicodeString($attribute))->camel());

        if (!method_exists($this, $methodName)) {
            throw new \LogicException('This code should not be reached!');
        }

        return $this->$methodName($subject, $user);
    }

    protected function getActionsHandled(): array
    {
        return [
            self::CREATE,
            self::READ,
            self::UPDATE,
            self::DELETE,
            self::FIND
        ];
    }

    protected function userCanModifyEntity(object $subject, User $user): bool
    {
        return $user === $subject->getUser();
    }

    protected function canCreate($subject, User $user): bool
    {
        return true;
    }

    protected function canRead($subject, User $user): bool
    {
        return $this->userCanModifyEntity($subject, $user);
    }

    protected function canUpdate($subject, User $user): bool
    {
        return $this->userCanModifyEntity($subject, $user);
    }

    protected function canDelete($subject, User $user): bool
    {
        return $this->userCanModifyEntity($subject, $user);
    }

    protected function canFind($subject, User $user): bool
    {
        return true;
    }
}