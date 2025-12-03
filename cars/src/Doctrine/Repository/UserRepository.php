<?php

namespace App\Doctrine\Repository;

use App\Entity\User;
use App\Domain\Model\User as DomainUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $hasher)
    {
        parent::__construct($registry, User::class);

        $this->hasher = $hasher;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
        $this->_em->clear();
    }

    public function findOneByEmail(string $email): ?DomainUser
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function checkEmailRepeated(string $email, string $id): ?DomainUser
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->andWhere('u.id != :id')
            ->setParameter('val', $email)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function save(DomainUser $user): string
    {
        $password = $this->hasher->hashPassword($user, $user->getPassword());

        $user->setPassword($password);

        $this->_em->persist($user);
        $this->_em->flush();
        $this->_em->clear();

        return $user->getId();
    }

    public function update(DomainUser $domainUser): DomainUser
    {
        $user = $this->_em->getRepository(User::class)->findOneBy(['id' => $domainUser->getId()]);
        $user->setEmail($domainUser->getEmail());
        $user->setPassword($domainUser->getPassword());

        $this->save($user);

        return $user;
    }

    public function delete(DomainUser $user): void
    {
        $this->_em->remove($this->_em->getRepository(User::class)->findOneBy(['id' => $user->getId()]));
        $this->_em->flush();
        $this->_em->clear();
    }

    public function findAllUsers(int $page): array
    {
        $firstResult = ($page <= 0) ? 0 : $page * $_ENV['RESULTS_PER_PAGE'];

        $qb = $this->_em->createQueryBuilder();

        $users = $qb->select('u')
            ->from('App:User', 'u')
            ->setFirstResult($firstResult)
            ->setMaxResults($_ENV['RESULTS_PER_PAGE'])
            ->orderBy('u.email', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $users;
    }

    public function getEmailUsers(): array
    {
        $qb = $this->_em->createQueryBuilder();

        $users = $qb->select('u.email')
            ->from('App:User', 'u')
            ->getQuery()
            ->getResult()
        ;

        return $users;
    }
}
