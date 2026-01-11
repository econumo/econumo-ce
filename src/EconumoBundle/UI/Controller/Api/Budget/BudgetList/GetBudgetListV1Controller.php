<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Budget\BudgetList;

use App\EconumoBundle\Application\Budget\BudgetListService;
use App\EconumoBundle\Application\Budget\Dto\GetBudgetListV1RequestDto;
use App\EconumoBundle\UI\Controller\Api\Budget\BudgetList\Validation\GetBudgetListV1Form;
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

class GetBudgetListV1Controller extends AbstractController
{
    public function __construct(private readonly BudgetListService $budgetListService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Get a user's budgets
     *
     * @OA\Tag(name="Budget"),
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
     *                     ref=@Model(type=\App\EconumoBundle\Application\Budget\Dto\GetBudgetListV1ResultDto::class)
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
    #[Route(path: '/api/v1/budget/get-budget-list', name: 'api_budget_get_budget_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $dto = new GetBudgetListV1RequestDto();
        $this->validator->validate(GetBudgetListV1Form::class, $request->query->all(), $dto);
        /** @var User $user */
        $user = $this->getUser();
        $result = $this->budgetListService->getBudgetList($dto, $user->getId());

        return ResponseFactory::createOkResponse($request, $result);
    }
}
