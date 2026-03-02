<?php

namespace App;

enum UserRole : string
{
    case CUSTOMER = "customer";
    case DRIVER = "driver";
    case ADMIN = "admin";
    case OWNER = "owner";
}
