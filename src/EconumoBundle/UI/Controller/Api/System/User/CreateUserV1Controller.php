<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\System\User;

use App\EconumoBundle\Application\System\UserService;
use App\EconumoBundle\Application\System\Dto\CreateUserV1RequestDto;
use App\EconumoBundle\UI\Controller\Api\System\User\Validation\CreateUserV1Form;
use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\UI\Middleware\ProtectSystemApi\SystemApiInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\EconumoBundle\UI\Service\Validator\ValidatorInterface;
use App\EconumoBundle\UI\Service\Response\ResponseFactory;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class CreateUserV1Controller extends AbstractController implements SystemApiInterface
{
    public function __construct(private readonly UserService $userService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Create user
     *
     * @OA\Post(
     *     path="/api/v1/system/create-user",
     *     tags={"System"},
     *     deprecated=true,
     *     @OA\RequestBody(@OA\JsonContent(ref=@Model(type=\App\EconumoBundle\Application\System\Dto\CreateUserV1RequestDto::class))),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/JsonResponseOk"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         ref=@Model(type=\App\EconumoBundle\Application\System\Dto\CreateUserV1ResultDto::class)
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request", @OA\JsonContent(ref="#/components/schemas/JsonResponseError")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/JsonResponseUnauthorized")),
     *     @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent(ref="#/components/schemas/JsonResponseException"))
     * ),
     *
     *
     * @return Response
     * @throws ValidationException
     */
    #[Route(path: '/api/v1/system/create-user', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $dto = new CreateUserV1RequestDto();
        $this->validator->validate(CreateUserV1Form::class, $request->request->all(), $dto);
        $baseUrl = $request->getSchemeAndHttpHost();
        $result = $this->userService->createUser($baseUrl, $dto);

        return ResponseFactory::createOkResponse($request, $result);
    }
}
