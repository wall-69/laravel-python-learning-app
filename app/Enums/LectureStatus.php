<?php

namespace App\Enums;

enum LectureStatus: string
{
    case DRAFT = "draft";
    case PUBLIC = "public";

    public static function values(): array
    {
        return array_column(self::cases(), "value");
    }
}
