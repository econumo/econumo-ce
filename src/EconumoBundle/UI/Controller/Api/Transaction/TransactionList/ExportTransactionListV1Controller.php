<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Transaction\TransactionList;

use App\EconumoBundle\Application\Transaction\TransactionListService;
use App\EconumoBundle\Application\Transaction\Dto\ExportTransactionListV1RequestDto;
use App\EconumoBundle\UI\Controller\Api\Transaction\TransactionList\Validation\ExportTransactionListV1Form;
use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\EconumoBundle\UI\Service\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class ExportTransactionListV1Controller extends AbstractController
{
    public function __construct(private readonly TransactionListService $transactionListService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Export TransactionList
     *
     * @OA\Tag(name="Transaction"),
     * @OA\Parameter(
     *     name="accountId",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="string"),
     *     description="Comma-separated account IDs",
     * ),
     * @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\MediaType(
     *         mediaType="text/csv",
     *         @OA\Schema(type="string", format="binary")
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
    #[Route(path: '/api/v1/transaction/export-transaction-list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $dto = new ExportTransactionListV1RequestDto();
        $this->validator->validate(ExportTransactionListV1Form::class, $request->query->all(), $dto);
        /** @var User $user */
        $user = $this->getUser();
        $rows = $this->transactionListService->exportTransactionList($dto, $user->getId());

        $handle = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return new Response(
            $content,
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="transactions.csv"',
            ]
        );
    }
}
