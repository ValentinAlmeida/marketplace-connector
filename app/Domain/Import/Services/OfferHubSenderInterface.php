<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Entity\Import;

interface OfferHubSenderInterface
{
    public function send(array $offers, Import $import): Import;
}
