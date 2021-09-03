<?php

declare(strict_types=1);

namespace App\Command;

use App\DataProvider\UserDataProvider;
use App\Manager\EmployeeManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateAdminUserCommand extends Command
{
    /**
     * @var EmployeeManager
     */
    private EmployeeManager $employeeManager;

    /**
     * @param EmployeeManager $employeeManager
     * @param string|null $name
     */
    public function __construct(
        EmployeeManager $employeeManager,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->employeeManager = $employeeManager;
    }

    protected function configure(): void
    {
        $this->setName('app:users:create-admin');

        $definition = [
            new InputArgument('email', InputArgument::REQUIRED, 'E-Mail'),
            new InputArgument('phoneNumber', InputArgument::REQUIRED, 'Номер телефона'),
            new InputArgument('password', InputArgument::REQUIRED, 'Пароль')
        ];

        $this->setDefinition($definition);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Создание пользователя-администратора');

        if (!$input->getArgument('email')) {
            do {
                $email = $io->ask('E-Mail');

                if (empty($email)) {
                    $io->error('Необходимо указать E-Mail');
                }
            } while (empty($email));

            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('phoneNumber')) {
            do {
                $phoneNumber = $io->ask('Номер телефона');

                if (empty($phoneNumber)) {
                    $io->error('Необходимо указать номер телефона');
                }
            } while (empty($phoneNumber));

            $input->setArgument('phoneNumber', $phoneNumber);
        }

        if (!$input->getArgument('password')) {
            do {
                $password = $io->ask('Пароль');

                if (empty($password)) {
                    $io->error('Необходимо указать пароль');
                }
            } while (empty($password));

            $input->setArgument('password', $password);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $phoneNumber = $input->getArgument('phoneNumber');
        $password = $input->getArgument('password');


        try {
            $this->employeeManager->create(
                1001,
                $email,
                'Админ',
                'Админ',
                'Админ',
                $phoneNumber,
                $password,
                [UserDataProvider::ROLE_ADMIN]
            );
            $io->success('Администратор создан');
        } catch (\Exception $e) {
            $io->error('Ошибка создания пользователя: ' . $e->getMessage());
            return -1;
        }

        return 0;
    }
}
