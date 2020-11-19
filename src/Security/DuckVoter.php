<?php


namespace App\Security;


use App\Entity\Duck;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DuckVoter extends Voter
{
    protected function supports(string $attribute, $subject)
    {
        if ($attribute != 'NOM') {
            return false;
        }

        if (!$subject instanceof Duck) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if ($token->getUser() != $subject) {
            return false;
        }
        return true;
    }
}