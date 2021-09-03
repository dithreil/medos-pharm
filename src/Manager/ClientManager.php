<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataProvider\PatchEntityDataProvider;
use App\DataProvider\UserDataProvider;
use App\Entity\Client;
use App\Entity\ConfirmationToken;
use App\Exception\AppException;
use App\Manager\Mail\MailManager;
use App\Model\Client\CreateClientSchema;
use App\Model\Client\EditClientSchema;
use App\Model\Client\RegisterClientSchema;
use App\Model\User\PatchUserModelSchema;
use App\Model\PaginatedDataModel;
use App\Model\User\RestorePasswordModelSchema;
use App\Repository\ClientRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\MailSenderAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use App\Traits\UserPasswordEncoderAwareTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ClientManager
{
    use EntityManagerAwareTrait;
    use UserPasswordEncoderAwareTrait;
    use MailSenderAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    /**
     * @var ConfirmationTokenManager
     */
    private $confirmationTokenManager;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param ClientRepository $clientRepository
     * @param ConfirmationTokenManager $confirmationTokenManager
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ClientRepository $clientRepository,
        ConfirmationTokenManager $confirmationTokenManager
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->clientRepository = $clientRepository;
        $this->confirmationTokenManager = $confirmationTokenManager;
    }

    /**
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string|null $patronymic
     * @param \DateTimeImmutable $birthDate
     * @param string $phoneNumber
     * @param string $password
     * @param array $roles
     * @param string|null $snils
     * @param string|null $whatsapp
     * @param string|null $skype
     * @return Client
     */
    public function create(
        string $email,
        string $lastName,
        string $firstName,
        ?string $patronymic,
        \DateTimeImmutable $birthDate,
        string $phoneNumber,
        string $password,
        ?string $snils,
        ?string $whatsapp,
        ?string $skype,
        array $roles = []
    ): Client {
        $client = new Client($email, $lastName, $firstName, $patronymic, $birthDate, $phoneNumber, $roles);
        $encoded = $this->passwordEncoder->encodePassword($client, $password);
        $client->setPassword($encoded);
        $client->setSnils($snils);
        $client->setWhatsapp($whatsapp);
        $client->setSkype($skype);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $client;
    }

    /**
     * @param CreateClientSchema $schema
     * @return Client
     * @throws AppException
     */
    public function createClient(CreateClientSchema $schema): Client
    {
        $existClient = $this->clientRepository->findOneBy(['email' => $schema->email]);

        if ($existClient instanceof Client) {
            throw new AppException('Client with specified email already exists', Response::HTTP_BAD_REQUEST);
        }

        $existClient = $this->clientRepository->findOneBy(['phoneNumber' => $schema->phoneNumber]);

        if ($existClient instanceof Client) {
            throw new AppException('Client with specified phone already exists', Response::HTTP_BAD_REQUEST);
        }

        try {
            $birthDate = new \DateTimeImmutable($schema->birthDate);
        } catch (\Exception $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }

        return $this->create(
            $schema->email,
            $schema->lastName,
            $schema->firstName,
            $schema->patronymic,
            $birthDate,
            $schema->phoneNumber,
            $schema->password,
            $schema->snils,
            $schema->whatsapp,
            $schema->skype,
            [UserDataProvider::ROLE_CLIENT]
        );
    }

    /**
     * @param array $filters
     * @return PaginatedDataModel
     * @throws AppException
     */
    public function search(array $filters): PaginatedDataModel
    {
        try {
            $page = intval($filters['page'] ?? 1);
            $limit = intval($filters['limit'] ?? 10);

            $items = $this->clientRepository->search($filters, $page, $limit);
            $total = $this->clientRepository->countBy($filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    /**
     * @param string $id
     * @return Client
     * @throws AppException
     */
    public function get(string $id): Client
    {
        $client = $this->clientRepository->find($id);

        if (!$client instanceof Client) {
            throw new AppException('Client is not found', Response::HTTP_NOT_FOUND);
        }

        return $client;
    }

    /**
     * @param string $id
     * @param EditClientSchema $schema
     * @throws AppException
     */
    public function edit(string $id, EditClientSchema $schema): Client
    {
        $client = $this->get($id);

        $client->setEmail($schema->email);
        $client->setLastName($schema->lastName);
        $client->setFirstName($schema->firstName);
        $client->setPatronymic($schema->patronymic);
        $client->setPhoneNumber($schema->phoneNumber);
        $client->setSnils($schema->snils);
        $client->setSkype($schema->skype);
        $client->setWhatsapp($schema->whatsapp);

        $this->entityManager->flush();

        return $client;
    }

    /**
     * @param string $id
     * @param string $password
     * @return Client
     * @throws AppException
     */
    public function changePassword(string $id, string $password): Client
    {
        $client = $this->clientRepository->find($id);

        $encoded = $this->passwordEncoder->encodePassword($client, $password);
        $client->setPassword($encoded);
        $this->mailManager->sendTwigMailHtml(
            MailManager::NEW_PASSWORD_TEMPLATE,
            ['mail_to' => [$client->getEmail()]],
            ['user' => $client, 'password' => $password]
        );
        $this->entityManager->flush();

        return $client;
    }

    /**
     * @param string $id
     * @return Client
     * @throws AppException
     */
    public function enableClient(string $id): Client
    {
        $client = $this->clientRepository->find($id);
        $client->setActive(true);
        $this->entityManager->flush();

        return $client;
    }

    /**
     * @param string $id
     * @return Client
     * @throws AppException
     */
    public function disableClient(string $id): Client
    {
        $client = $this->clientRepository->find($id);
        $client->setActive(false);
        $this->entityManager->flush();

        return $client;
    }

    /**
     * @param string $id
     * @return Client
     * @throws AppException
     */
    public function addClientLoginAttempt(string $id): Client
    {
        $client = $this->clientRepository->find($id);
        $client->addLoginAttempt();

        if ($client->isLoginAttemptsOverLimit()) {
            $client->setActive(false);
        }

        $this->entityManager->flush();

        return $client;
    }

    /**
     * @param string $id
     * @return Client
     */
    public function clearClientLoginAttempts(string $id): Client
    {
        $client = $this->clientRepository->find($id);
        $client->clearLoginAttempts();
        $this->entityManager->flush();

        return $client;
    }

    /**
     * @param RegisterClientSchema $schema
     * @return Client
     * @throws AppException
     */
    public function register(RegisterClientSchema $schema): Client
    {
        if ($schema->password !== $schema->confirmPassword) {
            throw new AppException('Passwords must match', Response::HTTP_BAD_REQUEST);
        }

        $client = $this->clientRepository->findOneBy(['phoneNumber' => $schema->phoneNumber]);

        if ($client instanceof Client) {
            throw new AppException('A client with the same phone already exists', Response::HTTP_BAD_REQUEST);
        }

        $client = $this->clientRepository->findOneBy(['email' => $schema->email]);

        if ($client instanceof Client) {
            throw new AppException('A client with the same email already exists', Response::HTTP_BAD_REQUEST);
        }

        try {
            $birthDate = new \DateTimeImmutable($schema->birthDate);
        } catch (\Exception $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }

        $client = $this->create(
            $schema->email,
            $schema->lastName,
            $schema->firstName,
            $schema->patronymic,
            $birthDate,
            $schema->phoneNumber,
            $schema->password,
            $schema->snils,
            $schema->whatsapp,
            $schema->skype,
            [UserDataProvider::ROLE_CLIENT]
        );
        $client->setActive(false);

        $emailConfirmationToken = $this->confirmationTokenManager->createEmailConfirmation($client);

        $this->entityManager->flush();

        $confirmationLink = $this->urlGenerator->generate(
            'app.security.confirm_email',
            ['token' => $emailConfirmationToken->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->mailManager->sendTwigMailHtml(
            MailManager::CONFIRM_EMAIL_TEMPLATE,
            ['mail_to' => [$client->getEmail()]],
            ['user' => $client, 'confirmation_link' => $confirmationLink]
        );
        return $client;
    }

    /**
     * @param string $token
     * @throws AppException
     */
    public function confirmEmail(string $token): void
    {
        $confirmationToken = $this->confirmationTokenManager->getEmailConfirmationToken($token);

        if (!$confirmationToken instanceof ConfirmationToken) {
            throw new AppException('Confirmation token not found', Response::HTTP_NOT_FOUND);
        }

        if (!$confirmationToken->isValid()) {
            $this->entityManager->remove($confirmationToken);
            $this->entityManager->flush();
            throw new AppException('Confirmation token not found', Response::HTTP_NOT_FOUND);
        }

        $client = $confirmationToken->getClient();
        $client->setActive(true);

        $this->entityManager->remove($confirmationToken);
        $this->entityManager->flush();
    }

    /**
     * @param RestorePasswordModelSchema $schema
     * @throws AppException
     */
    public function restorePassword(RestorePasswordModelSchema $schema): void
    {
        $client = $this->clientRepository->findOneBy(['email' => $schema->email]);
        if ($client instanceof Client) {
            $password = bin2hex(random_bytes(4));
            $encoded = $this->passwordEncoder->encodePassword($client, $password);
            $client->setPassword($encoded);
            $this->entityManager->flush();
            if ($schema->email !== null) {
                $this->mailManager->sendTwigMailHtml(
                    MailManager::NEW_PASSWORD_TEMPLATE,
                    ['mail_to' => [$client->getEmail()]],
                    ['user' => $client, 'password' => $password]
                );
            }
        }
    }

    /**
     * @param EditClientSchema $schema
     * @return Client
     * @throws AppException
     */
    public function editProfile(EditClientSchema $schema): Client
    {
        $client = $this->getLoggedInUser();

        if (!$client instanceof Client) {
            throw new AppException('Auth error', Response::HTTP_FORBIDDEN);
        }

        $client = $this->edit($client->getId(), $schema);

        $this->mailManager->sendTwigMailHtml(
            MailManager::PROFILE_CHANGED_TEMPLATE,
            ['mail_to' => [$client->getEmail()]],
            ['user' => $client]
        );

        return $client;
    }
}
