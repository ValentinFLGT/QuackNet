<?php

namespace App\Security;

use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
    protected function supports(string $attribute, $subject)
    {
        if ($attribute != 'DELETE_COMMENT') {
            return false;
        }

        if (!$subject instanceof Comment) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        return $token->getUser() == $subject->getAuthor();
    }
}