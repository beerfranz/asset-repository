<?php
namespace App\Assets\Voter;

use App\Security\Entity\User;
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
				if ( $this->security->isGranted('ROLE_USER') ) { return true; }
				break;
			case 'ASSET_WRITE':
				if ( $this->security->isGranted('ROLE_USER') ) { return true; }
				break;
		}

		return false;
	}
}
