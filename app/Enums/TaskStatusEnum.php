<?php

namespace App\Enums;

enum TaskStatusEnum: string
{

    case TODO = 'to_do';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';


    public static function notDone():array
    {
        return array_filter(self::cases(), fn(TaskStatusEnum $taskStatusEnum) => $taskStatusEnum != TaskStatusEnum::DONE);
    }
}
