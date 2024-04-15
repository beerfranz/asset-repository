<?php

namespace App\State;

use ApiPlatform\State\Pagination\Pagination;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

class RogerStateFacade
{

    public function __construct(
        protected RequestStack $request,
        protected LoggerInterface $logger,
        protected Security $security,
        protected Pagination $pagination,
    ) {
        
    }

    public function setService($service) {
        $this->service = $service;
    }

    public function getService() {
        return $this->service;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getCurrentRequest() {
        return $this->request->getCurrentRequest();
    }

    public function getLogger() {
        return $this->logger;
    }

    public function getSecurity() {
        return $this->security;
    }

    public function getPagination() {
        return $this->pagination;
    }
}
