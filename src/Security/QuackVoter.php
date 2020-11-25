<?php

namespace App\Security;

use App\Entity\Quack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class QuackVoter extends Voter
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
        return $token->getUser() == $subject->getAuthor();
    }
}