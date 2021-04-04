<?php

namespace App\Manager;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
     * UserManager constructor.
     *
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->manager = $manager;
        $this->encoder = $passwordEncoder;
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
        $user->setAvatar($avatar);
        $this->manager->persist($user);

        return $user;
    }

    public function formatBirthDate($date)
    {
        $date = new \DateTime($date);

        return $date;
    }

    public function formatMobileNumber($number)
    {
        // Allow only Digits, remove all other characters.
        $number = preg_replace("/[^\d]/", "", $number);

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
}
