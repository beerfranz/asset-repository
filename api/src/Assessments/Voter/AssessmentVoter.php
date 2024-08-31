<?php
namespace App\Assessments\Voter;

use App\Security\Entity\User;
use App\Assessments\Service\AuthorizationService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AssessmentVoter extends Voter
{
	private $security = null;

	public function __construct(Security $security, protected AuthorizationService $service)
	{
		$this->security = $security;
	}

	protected function supports($attribute, $subject): bool
	{
		$supportsAttribute = in_array($attribute, ['ASSESSMENT_READ', 'ASSESSMENT_WRITE', 'ASSESSMENT_ENABLED']);

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
			case 'ASSESSMENT_READ':
				if ( $this->security->isGranted('ROLE_USER') ) { return true; }
				break;
			case 'ASSESSMENT_WRITE':
				if ( $this->security->isGranted('ROLE_USER') ) { return true; }
				break;
			case 'ASSESSMENT_ENABLED':
				return $this->service->isAssessmentsEnabled($user);
				break;
		}

		return false;
	}
}
