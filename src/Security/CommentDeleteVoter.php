<?php

namespace App\Security;

use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentDeleteVoter extends Voter
{
    protected function supports(string $attribute, $subject)
    {
        if ($attribute != 'DELETE_COMMENT') {
            return false;
        }

        if (!$subject instanceof Comment) {
            return true;
        }
        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if ($token->getUser() != $subject) {
            return false;
        }
        return true;
    }
}