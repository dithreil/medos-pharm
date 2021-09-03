<?php

declare(strict_types=1);

namespace App\Security;

use App\DataProvider\UserDataProvider;
use App\Entity\Employee;
use App\Entity\User;
use App\Manager\EmployeeManager;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class EmployeeAuthenticator extends AbstractGuardAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_security_login';

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * @var EmployeeManager
     */
    private EmployeeManager $employeeManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EmployeeRepository $employeeRepository
     * @param EmployeeManager $employeeManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $passwordEncoder,
        EmployeeRepository $employeeRepository,
        EmployeeManager $employeeManager
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->employeeRepository = $employeeRepository;
        $this->employeeManager = $employeeManager;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        $credentials = $this->fetchCredentials($request);

        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST')
            && array_key_exists('user_type', $credentials)
            && $credentials['user_type'] === 'employee';
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        $credentials = $this->fetchCredentials($request);
        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|object|UserInterface|null
     * @throws NonUniqueResultException
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var Employee $employee */
        $employee = $this->employeeRepository->loadUserByUsername($credentials['username']);

        if (!$employee instanceof Employee) {
            throw new CustomUserMessageAuthenticationException('Incorrect login or password!');
        } elseif (!$employee->isActive()) {
            throw new CustomUserMessageAuthenticationException('Incorrect login or password!');
        }

        return $employee;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $passwordCheck = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        if (!$passwordCheck) {
            throw new CustomUserMessageAuthenticationException('Incorrect login or password!');
        }
        return $passwordCheck;
    }

    /**
     * @param mixed $credentials
     * @return string|null
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): JsonResponse
    {
        /** @var Employee $employee */
        $employee = $token->getUser();
        $employeeId = $employee->getId();

        $this->employeeManager->clearEmployeeLoginAttempts($employeeId);

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $credentials = $this->fetchCredentials($request);

        $employee = $this->employeeRepository->loadUserByUsername($credentials['username']);

        if ($employee instanceof Employee) {
            $this->employeeManager->addEmployeeLoginAttempt($employee->getId());
        }

        $data = [
            'error' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function fetchCredentials(Request $request): array
    {
        return json_decode($request->getContent(), true) ?? [];
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            'error' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
