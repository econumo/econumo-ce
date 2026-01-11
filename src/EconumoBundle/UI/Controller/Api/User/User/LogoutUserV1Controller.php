<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\User\User;

use App\EconumoBundle\Application\User\UserService;
use App\EconumoBundle\Application\User\Dto\LogoutUserV1RequestDto;
use App\EconumoBundle\UI\Controller\Api\User\User\Validation\LogoutUserV1Form;
use App\EconumoBundle\Application\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\EconumoBundle\UI\Service\Validator\ValidatorInterface;
use App\EconumoBundle\UI\Service\Response\ResponseFactory;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class LogoutUserV1Controller extends AbstractController
{
    public function __construct(private readonly UserService $userService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Logout
     *
     * @OA\Tag(name="User"),
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
     *                     ref=@Model(type=\App\EconumoBundle\Application\User\Dto\LogoutUserV1ResultDto::class)
     *                 )
     *             )
     *         }
     *     )
     * ),
     * @OA\Response(response=400, description="Bad Request", @OA\JsonContent(ref="#/components/schemas/JsonResponseError")),
     * @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent(ref="#/components/schemas/JsonResponseException")),
     *
     *
     * @return Response
     * @throws ValidationException
     */
    #[Route(path: '/api/v1/user/logout-user', name: 'api_user_logout_user', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $result = $this->userService->logoutUser($request->headers->get('Authorization'));

        return ResponseFactory::createOkResponse($request, $result);
    }
}
