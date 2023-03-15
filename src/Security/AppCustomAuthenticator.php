<?php

namespace App\Security;

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

use App\Service\UtilService;
use App\Service\UsuarioService;
use App\Service\UsuarioLogAccionService;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\HttpFoundation\RequestStack;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    private UtilService $_utilService;
    private UsuarioService $_usuarioService;
    private UsuarioLogAccionService $_usuarioLogAccionService;
    private RequestStack $_requestStack;


    public function __construct(UrlGeneratorInterface $urlGenerator, UtilService $utilService, UsuarioService $usuarioService, UsuarioLogAccionService $usuarioLogAccionService, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->_utilService = $utilService;
        $this->_usuarioService = $usuarioService;
        $this->_usuarioLogAccionService = $usuarioLogAccionService;
        $this->_requestStack = $requestStack;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        // Si el usuario esta dado de baja, muestro un cartel avisando la situación
        if ($this->_usuarioService->isUserEnabled($username)) {
            throw new CustomUserMessageAuthenticationException('El usuario ingresado ha sido dado de baja. Comuníquese con la Secretaría de Informática.');
        }        

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Actualiza fecha de última conexión e incrementa el contador de conexiones
        $this->_usuarioService->updateLoggingInformation($request->request->get('username'));

        // Audita Fecha y Hora de Login del Usuario a la Aplicación
        $this->_usuarioLogAccionService->logIn($request->getClientIp());

        // Seteo la variable globlal de la versión en GIT de la aplicación en session
        $session = $this->_requestStack->getSession();
        $session->set('realeaseGit', $this->_utilService->getRelease());

//        $this->_session->set('realeaseGit', $this->_utilService->getRelease());
//        $this->_session->

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_profesional_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
