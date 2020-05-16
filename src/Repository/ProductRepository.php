<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // Récupère tous les produits avec une pagination
    public function findAllProducts($page, $limit)
    {
        // Construction de la requête
        $qb = $this->createQueryBuilder('p')
            // Sélectionne la table product
            ->select('p')
            // Définit l'ordre d'affichage par ordre alphabétique
            ->orderBy('p.brand', 'ASC');

        // Requête
        $query = $qb->getQuery();

        // Calcul les produits a afficher
        $firstResult = ($page - 1) * $limit;

        // Retourne le premier résultat avec setFirstResult()
        // Et retourne le maximum de résultat avec setMaxResults()
        $query->setFirstResult($firstResult)->setMaxResults($limit);

        // Instancie un objet Paginator qui va contenir uniquement les produits souhaités
        $paginator = new Paginator($query);

        // Si la page demandé ne correspond pas au compte
        if (($paginator->count() <= $firstResult) && $page != 1) {
            // Page 404, sauf pour la première page
            throw new NotFoundHttpException('La page demandée n\'existe pas.');
        }

        return $paginator;
    }


    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
