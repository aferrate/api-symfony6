<?php

namespace App\Domain\Command;

interface CommandBusInterface
{
    public function execute(CommandInterface $command): mixed;
}
