<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidOwnerValidator extends ConstraintValidator
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint): void
    {
        /* @var App\Validator\IsValidOwner $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            $this->context->buildViolation($constraint->anonymousMessage)
                ->addViolation();
            return;
        }

        // allow admin users to change owners
        if ($this->security->isGranted('ASSET_ADMIN')) {
            return;
        }

        // Right now, owner is a string
        // if (!$value instanceof User) {
        //     throw new \InvalidArgumentException('@IsValidOwner constraint must be put on a property containing a User object');
        // }

        // if ($value->getId() !== $user->getId()) {
        //     $this->context->buildViolation($constraint->message)
        //         ->addViolation();
        // }

    }
}
