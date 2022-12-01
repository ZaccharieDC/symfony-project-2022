<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advert>
 *
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function save(Advert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Advert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeRejected(\DateTimeImmutable $date, bool $flush = false): void
    {
        $deleteRejectedAdvertQuery = $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(Advert::class, 'a')
            ->where('a.createdAt < :today')
            ->andWhere('a.createdAt > :date')
            ->andWhere('a.state = :state');

        $deleteRejectedAdvertQuery->setParameters([
            'today' => new \DateTimeImmutable(),
            'date' => $date,
            'state' => 'rejected',
        ]);

        $query = $deleteRejectedAdvertQuery->getQuery();

        $query->execute();

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removePublished(\DateTimeImmutable $date, bool $flush = false): void
    {
        $deletePublishedAdvertQuery = $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(Advert::class, 'a')
            ->where('a.publishedAt < :today')
            ->andWhere('a.publishedAt > :date')
            ->andWhere('a.state = :state');

        $deletePublishedAdvertQuery->setParameters([
            'today' => new \DateTimeImmutable(),
            'date' => $date,
            'state' => 'published',
        ]);

        $query = $deletePublishedAdvertQuery->getQuery();

        $query->execute();

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
