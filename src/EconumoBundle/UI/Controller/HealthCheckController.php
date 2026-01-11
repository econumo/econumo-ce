<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Health-Check controller
     *
     *
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/_/health-check', name: 'api_health_check', methods: ['GET'])]
    public function __invoke(): Response
    {
        $response = [
            'database' => null,
            'messenger' => null,
        ];
        $status = true;
        try {
            $response['database'] = $this->entityManager->getConnection()->connect();
        } catch (Exception) {
            $response['database'] = false;
            $status = false;
        }

        return new JsonResponse($response, ($status ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
