<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Paging
{
    private $entityClass;
    private $route;
    private $limit;
    private $currentPage;
    private $criteria = [];
    private $order = [];
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route): void
    {
        $this->route = $route;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    public function getManager() : EntityManagerInterface
    {
        return $this->manager;
    }

    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return PaginatedRepresentation
     */
    public function getData(): PaginatedRepresentation
    {
        // Si la valeur de la page ou de la limit est non numérique
        if (!is_numeric($this->currentPage) || !is_numeric($this->limit)) {
            // Redirection vers le ExceptionSubscriber
            throw new BadRequestHttpException(
                'A non-numeric value encountered'
            );
        }

        // Si la valeur de la page ou de la limit est négative
        if (($this->currentPage < 0) || ($this->limit < 0)) {
            // Redirection vers le ExceptionSubscriber
            throw new BadRequestHttpException(
                'A negative value encountered'
            );
        }

        // Si la valeur de la page ou de la limit est négative
        if ($this->currentPage == 0) {
            // Redirection vers le ExceptionSubscriber
            throw new BadRequestHttpException(
                'A page cannot be zero'
            );
        }

        // Si la valeur de la page ou de la limit est négative
        if ($this->limit == 0) {
            // Redirection vers le ExceptionSubscriber
            throw new BadRequestHttpException(
                'Warning: Division by zero'
            );
        }

        // Offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Récupère les éléments
        $repository = $this->manager->getRepository($this->entityClass);
        $total = count($repository->findBy($this->criteria));
        $numberOfPages = ceil($total / $this->limit);
        $data = $repository->findBy($this->criteria, $this->order, $this->limit, $offset);

        $collection = new CollectionRepresentation($data);

        return new PaginatedRepresentation(
            $collection,
            $this->route,
            array(),
            $this->currentPage,
            $this->limit,
            $numberOfPages,
            'page',
            'limit',
            true,
            $total
        );
    }
}
