<?php

namespace App\Manager;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var CacheManager
     */
    private $imagineCacheManager;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ContainerInterface           $container
     * @param CacheManager                 $imagineCacheManager
     */
    public function __construct(
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        ContainerInterface $container,
        CacheManager $imagineCacheManager)
    {
        $this->manager             = $manager;
        $this->encoder             = $passwordEncoder;
        $this->container           = $container;
        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function createUser(
        string $username,
        string $email,
        string $password,
        array $roles = null,
        string $firstname,
        string $lastname,
        $birthDate,
        $mobileNumber,
        string $occupation,
        $assigned_tasks = null,
        $avatar
    ): User {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        if (null !== $roles) {
            $user->setRoles($roles);
        }
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setDateOfBirth($this->formatBirthDate($birthDate));
        $user->setMobileNumber($this->formatMobileNumber($mobileNumber));
        $user->setOccupation($occupation);
        if (null !== $assigned_tasks) {
            $user->addAssignedTask($assigned_tasks);
        }
        $user->setAvatar($avatar);
        $this->manager->persist($user);

        return $user;
    }

    public function formatBirthDate($date)
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        return $date;
    }

    public function formatMobileNumber($number)
    {
        // Allow only Digits, remove all other characters.
        $number = preg_replace("/[^\d]/", "", $number);
        $number = substr($number, 0, 15);

        return is_numeric($number) ? $number : null;
    }

    public function hasRightToDeleteTask(User $user, Task $task): bool
    {
        if ('ROLE_ANONYMOUS' == $user->getRoles()[0]) {
            return false;
        }

        if ('ROLE_ADMIN' == $user->getRoles()[0] && 'ROLE_ANONYMOUS' == $task->getCreatedBy()->getRoles()[0]) {
            return true;
        }

        if ($user === $task->getCreatedBy()) {
            return true;
        }

        return false;
    }

    public function hasRightToDeleteUser(User $user, User $userToDelete): bool
    {
        if ($userToDelete === $user or $user->getRoles()[0] != 'ROLE_ADMIN') {
            return false;
        }

        if ($userToDelete->getRoles()[0] == 'ROLE_ANONYMOUS') {
            return false;
        }

        return true;
    }

    public function uploadFile(User $user, $avatarFile, $destination = null)
    {
        $destination = $destination??$this->container->getParameter('app.user.avatar_dir');
        $filename = md5($user->getEmail()).'.'.$avatarFile->guessExtension();
        $avatarFile->move(
            $destination,
            $filename
        );
        $this->imagineCacheManager->remove('img/profile/'.$filename);

        return $filename;
    }
}
