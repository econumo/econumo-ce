<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoadTestingController extends AbstractController
{
    /**
     * Load Testing controller
     *
     *
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/_/load-testing', name: 'api_load_testing', methods: ['GET'])]
    public function __invoke(): Response
    {
        sleep(1);
        return new JsonResponse(['success' => true]);
    }
}
