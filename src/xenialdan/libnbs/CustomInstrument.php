<?php

declare(strict_types=1);

namespace xenialdan\libnbs;

class CustomInstrument
{
    /** @var string The name of the instrument. */
    public $name;
    /** @var string The sound file of the instrument (just the filename, not the path). */
    public $soundFile;
    /** @var int The pitch of the sound file. Just like the note blocks, this ranges from 0-87. Default is 45. */
    public $pitch;
    /** @var bool Whether the piano should automatically press keys with this instrument when the marker passes them (0 or 1). */
    public $pressKey;

    /**
     * CustomInstrument constructor.
     * @param string $name
     * @param string $soundFile
     * @param int $pitch
     * @param bool $pressKey
     */
    public function __construct(string $name, string $soundFile, int $pitch = 45, bool $pressKey = false)
    {
        $this->name = $name;
        $this->soundFile = $soundFile;
        $this->pitch = $pitch;
        $this->pressKey = $pressKey;
    }
}