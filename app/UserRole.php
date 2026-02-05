<?php

namespace App;

enum UserRole : string
{
    const CUSTOMER = "customer";
    const ADMIN = "admin";
    const OWNER = "owner";
}
