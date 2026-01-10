<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Transaction\TransactionList;

use App\EconumoBundle\Application\Transaction\TransactionListService;
use App\EconumoBundle\Application\Transaction\Dto\ImportTransactionListV1RequestDto;
use App\EconumoBundle\UI\Controller\Api\Transaction\TransactionList\Validation\ImportTransactionListV1Form;
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

class ImportTransactionListV1Controller extends AbstractController
{
    public function __construct(private readonly TransactionListService $transactionListService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Import transactionList
     *
     * @OA\Tag(name="Transaction"),
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             required={"file", "mapping"},
     *             @OA\Property(
     *                 property="file",
     *                 type="string",
     *                 format="binary",
     *                 description="CSV file to import"
     *             ),
     *             @OA\Property(
     *                 property="mapping",
     *                 type="string",
     *                 description="JSON string with field mapping configuration",
     *                 example="{""account"":""Account Name"",""date"":""Transaction Date"",""amount"":""Amount"",""amountInflow"":null,""amountOutflow"":null,""category"":""Category"",""description"":""Description"",""payee"":""Merchant"",""tag"":null}"
     *             ),
     *             @OA\Property(
     *                 property="accountId",
     *                 type="string",
     *                 format="uuid",
     *                 description="Override account for all imported rows"
     *             ),
     *             @OA\Property(
     *                 property="date",
     *                 type="string",
     *                 format="date",
     *                 description="Override date for all imported rows (YYYY-MM-DD)"
     *             ),
     *             @OA\Property(
     *                 property="categoryId",
     *                 type="string",
     *                 format="uuid",
     *                 description="Override category for all imported rows"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Override description for all imported rows"
     *             ),
     *             @OA\Property(
     *                 property="payeeId",
     *                 type="string",
     *                 format="uuid",
     *                 description="Override payee for all imported rows"
     *             ),
     *             @OA\Property(
     *                 property="tagId",
     *                 type="string",
     *                 format="uuid",
     *                 description="Override tag for all imported rows"
     *             )
     *         )
     *     )
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
     *                     ref=@Model(type=\App\EconumoBundle\Application\Transaction\Dto\ImportTransactionListV1ResultDto::class)
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
    #[Route(path: '/api/v1/transaction/import-transaction-list', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $dto = new ImportTransactionListV1RequestDto();

        // Parse the mapping JSON before validation
        $mappingJson = $request->request->get('mapping');
        $mapping = [];
        if ($mappingJson !== null) {
            $mapping = json_decode($mappingJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ValidationException(
                    'Invalid mapping JSON',
                    Response::HTTP_BAD_REQUEST,
                    null,
                    ['mapping' => [json_last_error_msg()]]
                );
            }

            if (!is_array($mapping)) {
                throw new ValidationException(
                    'Invalid mapping JSON',
                    Response::HTTP_BAD_REQUEST,
                    null,
                    ['mapping' => ['Mapping must be a JSON object.']]
                );
            }
        }

        $data = array_merge(
            $request->request->all(),
            [
                'file' => $request->files->get('file'),
                'mapping' => $mapping
            ]
        );

        $this->validator->validate(ImportTransactionListV1Form::class, $data, $dto);

        /** @var User $user */
        $user = $this->getUser();
        $result = $this->transactionListService->importTransactionList($dto, $user->getId());

        return ResponseFactory::createOkResponse($request, $result);
    }
}
