<?php

namespace App\Security\Voter;
use App\Entity\Helo;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class HeloVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['PIN_EDIT', 'PIN_DELETE','PIN_VIEW','FILE_DOWNLOAD'])
            && $subject instanceof \App\Entity\Helo;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {

            case 'PIN_EDIT':
                // logic to determine if the user can EDIT
                return $user->isVerified() && $user == $subject->getUser() ;
            case 'PIN_DELETE ':
                // logic to determine if the user can EDIT
                return $user->isVerified() && $user == $subject->getUser() or in_array("ROLE_ADMIN",$user->getRoles());
            case 'PIN_VIEW' :
                return $this->canView($subject,$user);
            case 'FILE_DOWNLOAD' :
                return $this->canDownload($subject,$user);

        }

        return false;
    }
    private function canView(Helo $helo, User $user): bool
    {
        return ($user == $helo->getUser() and $helo->getIsPublished()) or(!$helo->getIsPublished());
    }
    private function canDownload(Helo $helo, User $user): bool
    {

        return $helo->getUser()->AmIfollowing($user);
    }

}
