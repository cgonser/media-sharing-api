<?php

namespace App\User\Exception;

use App\Core\Exception\ResourceNotFoundException;

class BillingAddressNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Billing Address not found';
}
