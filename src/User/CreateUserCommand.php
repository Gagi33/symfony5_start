<?php
    namespace App\User;

    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use Doctrine\ORM\EntityManagerInterface;
    use App\User\User;

    class CreateUserCommand extends Command
    {
        protected static $defaultName = "app:create-user";

        private $passwordEncoder;
        private $entityManager;

        public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
        {
            $this->passwordEncoder = $passwordEncoder;
            $this->entityManager = $entityManager;

            parent::__construct();
        }

        protected function configure()
        {
            $this
                ->setDescription("Creates a new user.")
                ->setHelp('This command allow you to create a user...')
                ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
                ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
                ->addArgument('password', InputArgument::REQUIRED, 'User password');
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $output->writeln(
                [
                    'User Creator',
                    '============',
                    '',
                ]
            );

            $user = (new User())
            ->setEmail($input->getArgument('email'))
            ->setUsername($input->getArgument('username'))
            ->setRoles(['ROLE_ADMIN']);

            $password = $this->passwordEncoder->encodePassword($user, $input->getArgument('password'));

            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return 0;
        }
    }