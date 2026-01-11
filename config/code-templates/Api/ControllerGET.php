<?php

declare(strict_types=1);

namespace _CG_APPROOT_\UI\Controller\Api\_CG_MODULE_\_CG_SUBJECT_;

use _CG_APPROOT_\Application\_CG_MODULE_\_CG_SUBJECT_Service;
use _CG_APPROOT_\Application\_CG_MODULE_\Dto\_CG_ACTION__CG_SUBJECT__CG_VERSION_RequestDto;
use _CG_APPROOT_\UI\Controller\Api\_CG_MODULE_\_CG_SUBJECT_\Validation\_CG_ACTION__CG_SUBJECT__CG_VERSION_Form;
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

class _CG_ACTION__CG_SUBJECT__CG_VERSION_Controller extends AbstractController
{
    public function __construct(private readonly _CG_SUBJECT_Service $_CG_SUBJECT_LCFIRST_Service, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * _CG_ACTION_ _CG_SUBJECT_
     *
     * @OA\Tag(name="_CG_MODULE_"),
     * @OA\Parameter(
     *     name="id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(type="string"),
     *     description="_CG_SUBJECT_ ID",
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
     *                     ref=@Model(type=\_CG_APPROOT_\Application\_CG_MODULE_\Dto\_CG_ACTION__CG_SUBJECT__CG_VERSION_ResultDto::class)
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
    #[Route(path: '_CG_URL_', methods: ['_CG_METHOD_'])]
    public function __invoke(Request $request): Response
    {
        $dto = new _CG_ACTION__CG_SUBJECT__CG_VERSION_RequestDto();
        $this->validator->validate(_CG_ACTION__CG_SUBJECT__CG_VERSION_Form::class, $request->query->all(), $dto);
        /** @var User $user */
        $user = $this->getUser();
        $result = $this->_CG_SUBJECT_LCFIRST_Service->_CG_ACTION_LCFIRST__CG_SUBJECT_($dto, $user->getId());

        return ResponseFactory::createOkResponse($request, $result);
    }
}
