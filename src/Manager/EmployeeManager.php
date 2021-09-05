<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataProvider\EmployeeDataProvider;
use App\DataProvider\UserDataProvider;
use App\Entity\Employee;
use App\Exception\AppException;
use App\Manager\Mail\MailManager;
use App\Model\Employee\CreateEmployeeSchema;
use App\Model\Employee\DailyScheduleItem;
use App\Model\Employee\EditEmployeeSchema;
use App\Model\PaginatedDataModel;
use App\Model\User\RestorePasswordModelSchema;
use App\Repository\EmployeeRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\MailSenderAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use App\Traits\UserPasswordEncoderAwareTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class EmployeeManager
{
    use EntityManagerAwareTrait;
    use UserPasswordEncoderAwareTrait;
    use MailSenderAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;
    /**
     * @var ConfirmationTokenManager
     */
    private ConfirmationTokenManager $confirmationTokenManager;

    /**
     * @param EmployeeRepository $employeeRepository
     * @param ConfirmationTokenManager $confirmationTokenManager
     */
    public function __construct(
        EmployeeRepository $employeeRepository,
        ConfirmationTokenManager $confirmationTokenManager
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->confirmationTokenManager = $confirmationTokenManager;
    }

    /**
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string|null $patronymic
     * @param string $phoneNumber
     * @param string $password
     * @param array $roles
     * @return Employee
     * @throws AppException
     */
    public function create(
        string $email,
        string $lastName,
        string $firstName,
        ?string $patronymic,
        string $phoneNumber,
        string $password,
        array $roles = []
    ): Employee {

        $employee = new Employee($email, $lastName, $firstName, $patronymic, $phoneNumber, $roles);
        $encoded = $this->passwordEncoder->encodePassword($employee, $password);
        $employee->setPassword($encoded);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        return $employee;
    }

    /**
     * @param CreateEmployeeSchema $schema
     * @return Employee
     * @throws AppException
     */
    public function createEmployee(CreateEmployeeSchema $schema): Employee
    {
        $existEmployee = $this->employeeRepository->findOneBy(['email' => $schema->email]);

        if ($existEmployee instanceof Employee) {
            throw new AppException('Employee with specified email already exists', Response::HTTP_BAD_REQUEST);
        }

        $existEmployee = $this->employeeRepository->findOneBy(['phoneNumber' => $schema->phoneNumber]);

        if ($existEmployee instanceof Employee) {
            throw new AppException('Employee with specified phone already exists', Response::HTTP_BAD_REQUEST);
        }

        $existEmployee = $this->employeeRepository->findOneBy(['code' => $schema->code]);

        if ($existEmployee instanceof Employee) {
            throw new AppException('Employee with specified code already exists', Response::HTTP_BAD_REQUEST);
        }

        return $this->create(
            $schema->email,
            $schema->lastName,
            $schema->firstName,
            $schema->patronymic,
            $schema->phoneNumber,
            $schema->password,
            [UserDataProvider::ROLE_EMPLOYEE]
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

            $items = $this->employeeRepository->search($filters, $page, $limit);
            $total = $this->employeeRepository->countBy($filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    /**
     * @param string $id
     * @return Employee
     */
    public function addEmployeeLoginAttempt(string $id): Employee
    {
        $employee = $this->employeeRepository->find($id);
        $employee->addLoginAttempt();

        if ($employee->isLoginAttemptsOverLimit()) {
            $employee->setActive(false);
        }

        $this->entityManager->flush();

        return $employee;
    }

    /**
     * @param string $id
     * @return Employee
     */
    public function clearEmployeeLoginAttempts(string $id): Employee
    {
        $employee = $this->employeeRepository->find($id);
        $employee->clearLoginAttempts();
        $this->entityManager->flush();

        return $employee;
    }

    /**
     * @param string $id
     * @return Employee
     * @throws AppException
     */
    public function get(string $id): Employee
    {
        $employee = $this->employeeRepository->find($id);

        if (!$employee instanceof Employee) {
            throw new AppException('Employee is not found', Response::HTTP_NOT_FOUND);
        }

        return $employee;
    }

    /**
     * @param string $id
     * @param string $email
     * @param string $phoneNumber
     * @return Employee
     * @throws AppException
     */
    public function edit(
        string $id,
        string $email,
        string $phoneNumber
    ): Employee {
        $employee = $this->get($id);
        $employee->setEmail($email);
        $employee->setPhoneNumber($phoneNumber);

        $this->entityManager->flush();

        return $employee;
    }

    /**
     * @param string $id
     * @param string $password
     * @return Employee
     * @throws AppException
     */
    public function changePassword(string $id, string $password): Employee
    {
        $employee = $this->employeeRepository->find($id);

        $encoded = $this->passwordEncoder->encodePassword($employee, $password);
        $employee->setPassword($encoded);
        $this->mailManager->sendTwigMailHtml(
            MailManager::NEW_PASSWORD_TEMPLATE,
            ['mail_to' => [$employee->getEmail()]],
            ['user' => $employee, 'password' => $password]
        );

        $this->entityManager->flush();

        return $employee;
    }

    /**
     * @param string $id
     * @return Employee
     */
    public function enableEmployee(string $id): Employee
    {
        $employee = $this->employeeRepository->find($id);
        $employee->setActive(true);
        $this->entityManager->flush();

        return $employee;
    }

    /**
     * @param string $id
     * @return Employee
     */
    public function disableEmployee(string $id): Employee
    {
        $employee = $this->employeeRepository->find($id);
        $employee->setActive(false);
        $this->entityManager->flush();

        return $employee;
    }

    /**
     * @param RestorePasswordModelSchema $schema
     * @throws AppException
     */
    public function restorePassword(RestorePasswordModelSchema $schema): void
    {
        try {
            $employee = $this->employeeRepository->loadUserByUsername($schema->email);
        } catch (NonUniqueResultException $exception) {
            throw new AppException("Invalid email", Response::HTTP_NOT_FOUND, $exception);
        }

        if ($employee instanceof Employee) {
            $password = bin2hex(random_bytes(4));
            $encoded = $this->passwordEncoder->encodePassword($employee, $password);
            $employee->setPassword($encoded);
            $this->entityManager->flush();
            $this->mailManager->sendTwigMailHtml(
                MailManager::NEW_PASSWORD_TEMPLATE,
                ['mail_to' => [$employee->getEmail()]],
                ['user' => $employee, 'password' => $password]
            );
        }
    }

    /**
     * @param EditEmployeeSchema $schema
     * @return Employee
     * @throws AppException
     */
    public function editProfile(EditEmployeeSchema $schema): Employee
    {
        $employee = $this->getLoggedInUser();
        $employeeId = $employee->getId();

        if (!$employee instanceof Employee) {
            throw new AppException('Auth error', Response::HTTP_FORBIDDEN);
        }
        return $this->edit(
            $employeeId,
            $schema->email,
            $schema->phoneNumber
        );
    }

    /**
     * @param int $code
     * @return Employee|null
     */
    public function findByCode(int $code): ?Employee
    {
        $result = $this->employeeRepository->findOneBy(['code' => $code]);

        return $result;
    }
}
