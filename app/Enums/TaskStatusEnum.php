<?php

namespace App\Enums;

enum TaskStatusEnum: string
{

    case TODO = 'to_do';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
