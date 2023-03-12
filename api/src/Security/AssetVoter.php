<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AssetVoter extends Voter
{
    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        $supportsAttribute = in_array($attribute, ['ASSET_READ', 'ASSET_WRITE']);

        return $supportsAttribute;
    }

    /**
     * @param string $attribute
     * @param $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case 'ASSET_READ':
                if ( in_array('ASSET_ADMIN', $user->getRoles()) or in_array('ASSET_READONLY', $user->getRoles()) ) { return true; }
                break;
            case 'ASSET_WRITE':
                if ( in_array('ASSET_ADMIN', $user->getRoles())) { return true; }
                break;
        }

        return false;
    }
}