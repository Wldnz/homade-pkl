<?php

namespace App;

enum StatusDelivery : string
{
    case WAIT_FOR_CONFIRMATION =  "wait_for_confirmation";
    case PROCESS = "process";
    case WAIT_FOR_PICK_UP = "wait_for_pick_up";
    case ON_THE_WAY = "on_the_way";
    case DELIVERED = "delivered";
}
