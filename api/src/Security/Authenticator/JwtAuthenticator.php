<?php

namespace App\Security\Authenticator;

use App\Security\Authenticator\RogerAuthenticator;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Contracts\Cache\ItemInterface;

class JwtAuthenticator extends RogerAuthenticator
{

	/**
	 * Called on every request to decide if this authenticator should be
	 * used for the request. Returning `false` will cause this authenticator
	 * to be skipped.
	 */
	public function supports(Request $request): ?bool
	{
		return $this->parameterBag->get('auth.jwt.enabled') &&
		 ($request->headers->has('Authorization') || $request->cookies->has('access_token'));
	}

	/**
	 * @param Request $request
	 *
	 * @return Passport
	 */
	public function authenticate(Request $request): Passport
	{
		if ($request->headers->has('Authorization'))
			$this->token = $request->headers->get('Authorization');
		elseif ($request->cookies->has('access_token'))
			$this->token = $request->cookies->get('access_token');
		else
			throw new AuthenticationException('Token not in header nor cookie');

		// Get token from header
		$jwtToken = $this->token;
		if (true === str_starts_with($jwtToken, 'Bearer ')) {
			$jwtToken = str_replace('Bearer ', '', $jwtToken);
		}

		// Decode the token
		$parts = explode('.', $jwtToken);
		if (count($parts) !== 3) {
			throw new AuthenticationException('Invalid token');
		}

		$header = json_decode(base64_decode($parts[0]));

		// Validate token
		try {
			if ($this->parameterBag->get('jwt_skip_verify'))
				$decodedToken = JWT::jsonDecode(JWT::urlsafeB64Decode($parts[1]));
			else
				$decodedToken = JWT::decode($jwtToken, $this->getJwks($header->alg), $headerRef);
		} catch (Exception $e) {
			echo $e->getMessage();exit;
			throw new AuthenticationException($e->getMessage());
		}
		return $this->userPassport($decodedToken->sub, $decodedToken->email, $decodedToken->roles);
	}

	/**
	 * @param Request $request
	 * @param TokenInterface $token
	 * @param string $firewallName
	 *
	 * @return Response|null
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		// on success, let the request continue
		return null;
	}

	/**
	 * @param Request $request
	 * @param AuthenticationException $exception
	 *
	 * @return Response|null
	 */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
		$data = [
			'error' => strtr($exception->getMessageKey(), $exception->getMessageData())
		];

		return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}

	/**
	 * @return array
	 */
	private function getJwks($defaultAlg): array
	{
		$jwkData = $this->cacheApp->get('jwk_keys', function(ItemInterface $item) {
			$jwkData = json_decode(
				file_get_contents($this->parameterBag->get('jwks_url')),
				true
			);

			$item->expiresAfter(3600);
			$item->tag(['authentication']);

			return $jwkData;
		});

		return JWK::parseKeySet($jwkData, $defaultAlg);
	}
}
