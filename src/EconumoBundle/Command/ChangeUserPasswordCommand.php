<?php

declare(strict_types=1);

namespace App\EconumoBundle\Command;

use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\User\UserPasswordServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangeUserPasswordCommand extends Command
{
    protected static $defaultName = 'app:change-user-password';

    protected static $defaultDescription = 'Change user password';

    public function __construct(
        private readonly UserPasswordServiceInterface $userPasswordService,
        private readonly UserRepositoryInterface $userRepository
    ) {
        parent::__construct(self::$defaultName);
    }


    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail')
            ->addArgument('password', InputArgument::REQUIRED, 'New password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = trim((string)$input->getArgument('email'));
        $password = trim((string)$input->getArgument('password'));

        $user = $this->userRepository->getByEmail(new Email($email));
        $this->userPasswordService->updatePassword($user->getId(), $password);

        $io->success(sprintf('Password changed successfully for the user %s! (id: %s)', $email, $user->getId()));

        return Command::SUCCESS;
    }
}
