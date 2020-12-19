<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @param string $order
     * @param bool $sold
     * @param int|null $maxResults
     * @return Property[]|null Returns an array of Property objects
     */
    public function findAlreadySold(bool $sold, string $order, ?int $maxResults=null): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.sold = :value')
            ->setParameter('value', $sold)
            ->orderBy('p.id', $order)
            ->setMaxResults($maxResults)
            ->leftJoin('p.pictures','pic')
            ->addSelect('pic')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param PropertySearch $search
     * @return Query
     */
    public function getPropertiesNotSoldQuery(PropertySearch $search): Query
    {
        return $this->propertiesSearchQueryBuilder($search)
                ->leftJoin('p.pictures','pic')
                ->addSelect('pic')
                ->getQuery()
            ;
    }

    /**
     * @return Query
     */
    public function getPropertiesQuery(): Query
    {
        $queryBuilder = $this->getAllPropertiesQueryBuilder();

        return $queryBuilder->getQuery();
    }

    /**
     * @param  $date
     * @return Property[]|null Returns an array of Property objects
     */
    public function find_by_date($date): ?array
    {
        return $this->createQueryBuilder('p')
            ->where('p.createdAt < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param bool $sold
     * @return Property[] Returns an array of Property objects
     * @throws Exception
     */
    public function my_find(bool $sold): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $this->whereCurrentYear($queryBuilder);

        return $queryBuilder
            ->andWhere('p.sold = :val')
            ->setParameter('val', $sold)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * @param PropertySearch $search
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function my_count(PropertySearch $search)
    {
        $queryBuilder = $this->propertiesSearchQueryBuilder($search);

        $queryBuilder->select('count(p)');

        return $queryBuilder
                ->getQuery()
                ->getSingleScalarResult()
            ;
    }

    /**
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function my_dql_count()
    {
        return $this->_em->createQuery('SELECT COUNT(p) FROM App\Entity\Property p')
                ->getSingleScalarResult()
        ;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @throws Exception
     */
    private function whereCurrentYear(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->andWhere('p.createdAt BETWEEN :start AND :end')
            ->setParameter('start', new \Datetime(date('Y').'-01-01'))  // Date entre le 1er janvier de cette année
            ->setParameter('end',   new \Datetime(date('Y').'-12-31'))  // Et le 31 décembre de cette année
        ;
    }

    /**
     * @param PropertySearch $search
     * @return QueryBuilder
     */
    private function propertiesSearchQueryBuilder(PropertySearch $search): QueryBuilder
    {
        $queryBuilder = $this->getPropertiesNotSoldQueryBuilder();

        if ($search->getMaxPrice()){
            $queryBuilder = $queryBuilder
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $search->getMaxPrice())
            ;
        }

        if ($search->getMinSurface()){
            $queryBuilder = $queryBuilder
                ->andWhere('p.surface >= :minSurface')
                ->setParameter('minSurface', $search->getMinSurface())
            ;
        }

        if ($search->getOptions()->count() > 0){
            $key = 0;
            foreach ($search->getOptions() as $option) {
                $key++;
                $queryBuilder = $queryBuilder
                    ->andWhere(":option$key MEMBER OF p.options")
                    ->setParameter("option$key", $option);
            }
        }

        if ($search->getLat() && $search->getLng() && $search->getDistance()) {
            $queryBuilder = $queryBuilder
                ->andWhere('(6353 * 2 * ASIN(SQRT(POWER(SIN((p.lat - :lat) *  pi()/180 / 2), 2) + COS(p.lat * pi()/180) * COS(:lat * pi()/180) * POWER(SIN((p.lng - :lng) * pi()/180 / 2), 2) ))) <= :distance')
                ->setParameter('lng', $search->getLng())
                ->setParameter('lat', $search->getLat())
                ->setParameter('distance', $search->getDistance());
        }

        return $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    private function getPropertiesNotSoldQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.sold = false')
            ;
    }

    /**
     * @return QueryBuilder
     */
    private function getAllPropertiesQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p');
    }

    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
