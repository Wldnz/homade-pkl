<?php

namespace App;

enum TransactionPaymentProofStatus : string
{
    CASE WAIT_FOR_CONFIRMATION = 'wait_for_confirmation';
    CASE ACCEPTED = 'accepted';
    CASE REJECTED = 'rejected';
}
