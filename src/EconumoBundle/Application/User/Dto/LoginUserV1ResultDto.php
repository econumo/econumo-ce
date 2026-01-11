<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use App\EconumoBundle\Application\User\Dto\CurrentUserResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"token", "user"}
 * )
 */
class LoginUserV1ResultDto
{
    /**
     * JWT-token
     * @var string
     * @OA\Property(example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NzYxNjY0NzcsImV4cCI6MTY3ODc1ODQ3Nywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiam9obkBzbm93LnRlc3QiLCJpZCI6ImFmZjIxMzM0LTk2ZjAtNGZiMS04NGQ4LTAyMjNkMDI4MDk1NCJ9.XwkQy4l6NsrZ3fr34yxQlpJdTKjLJFYrV9MKR8kcHwLJM1rauemaObzWHwAq2VkkLA8dZry-gLSUGiVdNpm5r_JrumNcJhLIGqvw57B_MgjpwcKGR4E7ijIxY2WKKVFbdqzbvggYMWHpfuJoYRfwEmuovxsOb2uxehMgjU0U-MjK8hd-2NgeRXCwxzKzFEKAzZnlI3pAOH7wzesLFNFrFM6cBp5Cb0eE9C-3ex8cweDHSnK_OlRVAMW_6A9iQNiMIZRUytt7ynjOSZ8sBTNlJBCnvvWFa1LQGHuxYUFt0_-Wci7WxwnKGNocot36Wg1F9Zx0052xQDCNrfhSAI86HvxmmJRNXLbi1eUNG0zrtwvkY1vja11AnpIxebdXoCHyqO0c0FCpOQicHVZgiiuNY_aTB7NC6CVUtN-yeohoLFavo18eqiJnvVwA8xReXSjGFIfP1RlRPSF1stXAJXVuxa7OreLmPReqJEeQwvpqpHxZ_inZiJZL8uzySvmTk5EW2WrHNxkxJ4yZFTdtSelsVXImz4Qgh9X2WABQLN3DuudENTNRSSMSp2QF_HdarfjivA1kLUByhdduQD4wUUHyzlOYhqbiGw8Bax8grL9u1yUhAh8YJA9RZdqYDX59ji6Pjs2XFiXBZ2P6-neCi_55SH88Lxy7144Pf1oSizZsP48")
     */
    public string $token;

    public CurrentUserResultDto $user;
}
