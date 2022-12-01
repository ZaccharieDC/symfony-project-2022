<?php

namespace App\Repository;

use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Picture>
 *
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }

    public function save(Picture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Picture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeUnlinked(\DateTimeImmutable $date, bool $flush = false): void
    {
        $deleteUnlinkedPictureQuery = $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(Picture::class, 'p')
            ->where('p.createdAt < :today')
            ->andWhere('p.createdAt > :date')
            ->andWhere('p.advert IS NULL');

        $deleteUnlinkedPictureQuery->setParameters([
            'today' => new \DateTimeImmutable(),
            'date' => $date,
        ]);

        $query = $deleteUnlinkedPictureQuery->getQuery();

        $query->execute();

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
