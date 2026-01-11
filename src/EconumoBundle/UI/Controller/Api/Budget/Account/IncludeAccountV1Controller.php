<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Budget\Account;

use App\EconumoBundle\Application\Budget\AccountService;
use App\EconumoBundle\Application\Budget\Dto\IncludeAccountV1RequestDto;
use App\EconumoBundle\UI\Controller\Api\Budget\Account\Validation\IncludeAccountV1Form;
use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\EconumoBundle\UI\Service\Validator\ValidatorInterface;
use App\EconumoBundle\UI\Service\Response\ResponseFactory;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class IncludeAccountV1Controller extends AbstractController
{
    public function __construct(private readonly AccountService $accountService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Include an account to a budget
     *
     * @OA\Tag(name="Budget"),
     * @OA\RequestBody(@OA\JsonContent(ref=@Model(type=\App\EconumoBundle\Application\Budget\Dto\IncludeAccountV1RequestDto::class))),
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
     *                     ref=@Model(type=\App\EconumoBundle\Application\Budget\Dto\IncludeAccountV1ResultDto::class)
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
    #[Route(path: '/api/v1/budget/include-account', name: 'api_budget_include_account', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $dto = new IncludeAccountV1RequestDto();
        $this->validator->validate(IncludeAccountV1Form::class, $request->request->all(), $dto);
        /** @var User $user */
        $user = $this->getUser();
        $result = $this->accountService->includeAccount($dto, $user->getId());

        return ResponseFactory::createOkResponse($request, $result);
    }
}
