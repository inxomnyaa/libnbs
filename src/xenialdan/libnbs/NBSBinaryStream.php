<?php

declare(strict_types=1);

namespace xenialdan\libnbs;

use pocketmine\utils\BinaryStream;

class NBSBinaryStream extends BinaryStream
{
    public function getString(): string
    {
        return $this->get(\unpack("I", $this->get(4))[1]);
    }
}