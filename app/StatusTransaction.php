<?php

namespace App;

enum StatusTransaction : string
{
    case PENDING = "pending";
    case PAID = "paid";
    case SUCCESS  = "success";
    case FAILED = "failed";
    case CANCELLED ="cancelled";
}
