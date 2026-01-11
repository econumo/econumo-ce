<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Budget\TransactionList;

use App\EconumoBundle\Application\Budget\TransactionListService;
use App\EconumoBundle\Application\Budget\Dto\GetTransactionListV1RequestDto;
use App\EconumoBundle\UI\Controller\Api\Budget\TransactionList\Validation\GetTransactionListV1Form;
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

class GetTransactionListV1Controller extends AbstractController
{
    public function __construct(private readonly TransactionListService $transactionListService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Get a budget transactions
     *
     * @OA\Tag(name="Budget"),
     * @OA\Parameter(
     *     name="budgetId",
     *     in="query",
     *     required=true,
     *     @OA\Schema(type="string"),
     *     description="Budget ID",
     * ),
     * @OA\Parameter(
     *     name="periodStart",
     *     in="query",
     *     required=true,
     *     @OA\Schema(type="string"),
     *     description="Period start (Y-m-d)",
     * ),
     * @OA\Parameter(
     *     name="categoryId",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="string"),
     *     description="Category Id",
     * ),
     * @OA\Parameter(
     *     name="tagId",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="string"),
     *     description="Tag Id",
     * ),
     * @OA\Parameter(
     *     name="envelopeId",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="string"),
     *     description="Envelope Id",
     * ),
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
     *                     ref=@Model(type=\App\EconumoBundle\Application\Budget\Dto\GetBudgetTransactionListV1ResultDto::class)
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
    #[Route(path: '/api/v1/budget/get-transaction-list', name: 'api_budget_get_transaction_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $dto = new GetTransactionListV1RequestDto();
        $this->validator->validate(GetTransactionListV1Form::class, $request->query->all(), $dto);
        /** @var User $user */
        $user = $this->getUser();
        $result = $this->transactionListService->getTransactionList($dto, $user->getId());

        return ResponseFactory::createOkResponse($request, $result);
    }
}
