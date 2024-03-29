<?php

declare(strict_types=1);

namespace inxomnyaa\libnbs;

use pocketmine\utils\BinaryStream;
use function unpack;

class NBSBinaryStream extends BinaryStream{
	public function getString() : string{
		return $this->get(unpack("I", $this->get(4))[1]);
	}
}