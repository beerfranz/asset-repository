<?php

namespace App\Security\Authenticator;

use App\Security\Authenticator\RogerAuthenticator;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
	
class HeaderAuthenticator extends RogerAuthenticator
{

	/**
	 * Called on every request to decide if this authenticator should be
	 * used for the request. Returning `false` will cause this authenticator
	 * to be skipped.
	 */
	public function supports(Request $request): ?bool
	{
		$emailHeader = $this->parameterBag->get('auth.header.email', 'X-Token-User-Email');
		return $request->headers->has($emailHeader);
	}

	public function authenticate(Request $request): Passport
	{

		$emailHeader = $this->parameterBag->get('auth.header.email', 'X-Token-User-Email');
		$email = $request->headers->get($emailHeader);
		if (null === $email) {
			// The token header was empty, authentication fails with HTTP Status
			// Code 401 "Unauthorized"
			
			throw new CustomUserMessageAuthenticationException('No email provided in the HTTP header ' . $emailHeader);
		}

		$roleHeader = $this->parameterBag->get('auth.header.roles', 'X-ROLES');
		$role = $request->headers->get($roleHeader);
		if (null === $role) {
			throw new CustomUserMessageAuthenticationException('No role provided in the HTTP header ' . $roleHeader);
		}

		$subHeader = $this->parameterBag->get('auth.header.sub', 'X-Token-User-Sub');
		$sub = $request->headers->get($subHeader);
		if (null === $sub) {
			throw new CustomUserMessageAuthenticationException('No identifier provided in the HTTP header ' . $subHeader);
		}

		$roles = explode(' ', $role);

		return $this->userPassport($email, $roles);
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		// on success, let the request continue
		return null;
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
		$data = [
			// you may want to customize or obfuscate the message first
			'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

			// or to translate this message
			// $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
		];

		return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}
}