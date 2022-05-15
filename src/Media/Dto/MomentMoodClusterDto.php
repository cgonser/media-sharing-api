<?php

namespace App\Media\Dto;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class MomentMoodClusterDto
{
    public string $mood;

    /** @var array MomentMoodDto[] */
    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: MomentMoodDto::class)))]
    public array $moments = [];
}