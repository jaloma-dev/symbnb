<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class Pagination {

    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath) {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;

    }

    public function display() {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route,
        ]);
    }
    // Récupération du nombre de page
    public function getPages() {

        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle vous souhaiter paginer");
        }
        
        $total = count($this->manager->getRepository($this->entityClass)->findAll());

        $pages = ceil($total / $this->limit);
        return $pages;
    }

    // Récupération des données par page
    public function getData() {

        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle vous souhaiter paginer");
        }

        $offset = $this->currentPage * $this->limit - $this->limit;
        
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([],[], $this->limit, $offset);
        
        return $data;
    }
    
    // SETTERS
    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }   

    /**
     * Set the value of templatePath
     *
     * @return  self
     */ 
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    public function setCurrentPage($currentPage) {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function setEntityClass($entityClass) {
        $this->entityClass = $entityClass;
        return $this;
    }

    // GETTERS
    public function getRoute() {
        return $this->route;
    }
    public function getCurrentPage() {
        return $this->currentPage;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function getEntityClass() {
        return $this->entityClass;
    }
    /**
     * Get the value of templatePath
     */ 
    public function getTemplatePath()
    {
        return $this->templatePath;
    }


}