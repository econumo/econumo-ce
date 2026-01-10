<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Connection\ConnectionList;

use App\EconumoBundle\Application\Connection\ConnectionListService;
use App\EconumoBundle\Application\Connection\Dto\GetConnectionListV1RequestDto;
use App\EconumoBundle\Application\Connection\Dto\GetConnectionListV1ResultDto;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\UI\Controller\Api\Connection\ConnectionList\Validation\GetConnectionListV1Form;
use App\EconumoBundle\Application\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\EconumoBundle\UI\Service\Validator\ValidatorInterface;
use App\EconumoBundle\UI\Service\Response\ResponseFactory;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class GetConnectionListV1Controller extends AbstractController
{
    public function __construct(private readonly ConnectionListService $connectionListService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Get a user connections
     *
     * @OA\Tag(name="Connection"),
     * @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *         type="object",
     *         allOf={
     *             @OA\Schema(ref="#/components/schemas/JsonResponseOk"),
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="data",
     *                     ref=@Model(type=\App\EconumoBundle\Application\Connection\Dto\GetConnectionListV1ResultDto::class)
     *                 )
     *             )
     *         }
     *     )
     * ),
     * @OA\Response(response=400, description="Bad Request", @OA\JsonContent(ref="#/components/schemas/JsonResponseError")),
     * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/JsonResponseUnauthorized")),
     * @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent(ref="#/components/schemas/JsonResponseException")),
     *
     *
     * @return Response
     * @throws ValidationException
     */
    #[Route(path: '/api/v1/connection/get-connection-list', name: 'api_connection_get_connection_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $dto = new GetConnectionListV1RequestDto();
        $this->validator->validate(GetConnectionListV1Form::class, $request->query->all(), $dto);
        /** @var User $user */
        $user = $this->getUser();
        $result = $this->connectionListService->getConnectionList($dto, $user->getId());

        return ResponseFactory::createOkResponse($request, $result);
    }
}
