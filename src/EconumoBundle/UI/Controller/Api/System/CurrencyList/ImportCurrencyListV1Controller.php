<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\System\CurrencyList;

use App\EconumoBundle\Application\System\CurrencyListService;
use App\EconumoBundle\Application\System\Dto\ImportCurrencyListV1RequestDto;
use App\EconumoBundle\UI\Controller\Api\System\CurrencyList\Validation\ImportCurrencyListV1Form;
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

class ImportCurrencyListV1Controller extends AbstractController implements SystemApiInterface
{
    public function __construct(private readonly CurrencyListService $currencyListService, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Import currencies list
     *
     * @OA\Post(
     *     path="/api/v1/system/import-currency-list",
     *     tags={"System"},
     *     deprecated=true,
     *     @OA\RequestBody(@OA\JsonContent(ref=@Model(type=\App\EconumoBundle\Application\System\Dto\ImportCurrencyListV1RequestDto::class))),
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
     *                         ref=@Model(type=\App\EconumoBundle\Application\System\Dto\ImportCurrencyListV1ResultDto::class)
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
    #[Route(path: '/api/v1/system/import-currency-list', name: 'api_system_import_currency_list', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $dto = new ImportCurrencyListV1RequestDto();
        $this->validator->validate(ImportCurrencyListV1Form::class, $request->request->all(), $dto);
        $result = $this->currencyListService->importCurrencyList($dto);

        return ResponseFactory::createOkResponse($request, $result, "OK");
    }
}
