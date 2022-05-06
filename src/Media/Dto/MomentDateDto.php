<?php

namespace App\Media\Dto;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class MomentDateDto
{
    public string $recordedOn;

    public int $count;

    /** @var MomentDto[] */
    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: MomentDto::class)))]
    public array $moments;
}
