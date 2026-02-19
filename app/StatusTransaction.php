<?php

namespace App;

enum StatusTransaction : string
{
    case PENDING = "pending";
    case PAID = "paid";
    case SUCCESS  = "success";
    case FAILED = "failed";
    case CANCELLED_BY_ADMIN = "cancelled_by_admin";
    case CANCELLED_BY_CUSTOMER = "cancelled_by_customer"; 
}
