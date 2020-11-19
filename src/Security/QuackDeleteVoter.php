<?php

namespace App\Security;

use App\Entity\Quack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class QuackDeleteVoter extends Voter
{
    protected function supports(string $attribute, $subject)
    {
        if ($attribute != 'DELETE_QUACK') {
            return false;
        }

        if (!$subject instanceof Quack) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if ($token->getUser() != $subject->author) {
            return false;
        }
        return true;
    }
}