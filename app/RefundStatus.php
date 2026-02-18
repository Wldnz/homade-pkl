<?php

namespace App;

enum RefundStatus : string
{
    case NONE = 'none';
    case PENDING = 'pending';
    case SUCCESS = 'success';
}
