<?php

declare(strict_types=1);

namespace App\EconumoBundle\Command;

use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\UserServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangeUserEmailCommand extends Command
{
    protected static $defaultName = 'app:change-user-email';

    protected static $defaultDescription = 'Change an e-mail of a user';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserServiceInterface $userService
    ) {
        parent::__construct(self::$defaultName);
    }


    protected function configure(): void
    {
        $this
            ->addArgument('old-email', InputArgument::REQUIRED, 'Old e-mail')
            ->addArgument('new-email', InputArgument::REQUIRED, 'New e-mail');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $oldEmail = trim((string)$input->getArgument('old-email'));
        $newEmail = trim((string)$input->getArgument('new-email'));

        $user = $this->userRepository->getByEmail(new Email($oldEmail));
        $this->userService->updateEmail($user->getId(), new Email($newEmail));

        $io->success(sprintf('User e-mail is successfully updated! (id: %s)', $user->getId()));

        return Command::SUCCESS;
    }
}
