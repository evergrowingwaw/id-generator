<?php

namespace Ave\IdGenerator;

interface IdGenerator
{
    public function generateId(string $group = 'default', int $start = 1): int;
}
