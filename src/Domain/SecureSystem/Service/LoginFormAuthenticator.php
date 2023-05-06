<?php

namespace App\Domain\SecureSystem\Service;

use App\Domain\Contract\Repository\PageRepositoryInterface;
use App\Domain\Identity\Repository\UserRepositoryInterface;
use App\Domain\SecureSystem\Passport\TwoFactorBadge;
use App\Infrastructure\Doctrine\Repository\Identity\UserRepository;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    public const TWO_FACTOR_ROUTE = '2fa';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepositoryInterface $userRepository,
        private readonly PageRepositoryInterface $repository,
        private readonly TwoFactorService $twoFactorService
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        $badgeList = [
            new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token'))
        ];

        return new Passport(
            userBadge: new UserBadge($email),
            credentials: new PasswordCredentials($request->request->get('password', '')),
            badges: $badgeList
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($token->getUser() && $this->twoFactorService->isEnabled()) {
            $user = $this->userRepository->findOneByEmail($token->getUser()->getUserIdentifier());

            if ($user !== null && $user->getTwoFactorCode() !== null) {
                return new RedirectResponse(self::TWO_FACTOR_ROUTE);
            }
        }

        return new RedirectResponse($this->generateStarterUri());
    }

    public function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function generateStarterUri(): string
    {
        return $this->repository->findAll()[0]?->getAliasUri() ?? $this->urlGenerator->generate('page_admin');
    }
}