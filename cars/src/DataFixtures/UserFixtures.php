<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Ramsey\Uuid\Uuid;
use App\Domain\Repository\UserRepositoryInterface;

class UserFixtures extends Fixture
{
    private $hasher;
    private $userRepoBackup;

    public function __construct(UserPasswordHasherInterface $hasher, UserRepositoryInterface $userRepoBackup)
    {
        $this->hasher = $hasher;
        $this->userRepoBackup = $userRepoBackup;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User(Uuid::uuid4());

        $password = $this->hasher->hashPassword($user, 'test');

        $user->setEmail('test@test.com');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $this->userRepoBackup->save($user);
    }
}
