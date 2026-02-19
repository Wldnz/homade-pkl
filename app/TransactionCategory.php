<?php

namespace App;

enum TransactionCategory : string
{
    case ORDER = 'order';
    case PRE_ORDER = 'pre_order';
}
