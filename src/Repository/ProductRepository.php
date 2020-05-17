<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
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
        // Vérifie que $page correspond à un nombre
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument est incorrecte (valeur : ' . $page . ').'
            );
        }

        // Si $page est inférieur à 1
        if ($page < 1) {
            throw new NotFoundHttpException('');
        }

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
        $query->setFirstResult($firstResult);

        // Retourne le maximum de résultat avec setMaxResults()
        $query->setMaxResults($limit);

        // Instancie un objet Paginator qui va contenir uniquement les commentaires souhaités
        $paginator = new Paginator($query);

        // Si la page demandée est supérieur au compte
        if (($paginator->count() <= $firstResult) && $page != 1) {
            // Page 404
            throw new NotFoundHttpException();
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
