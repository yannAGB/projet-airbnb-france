<?php

namespace App\Security;

use App\Repository\AccessTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private AccessTokenRepository $repository
    ) {}

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $token = $this->repository->findOneByValue($accessToken);

        if (!$token || !$token->isValid()) {
            throw new BadCredentialsException('Token invalide ou expiré');
        }

        /* Retourne l'identifiant utilisateur (email) */
        return new UserBadge($token->getUser()->getEmail());
    }
}