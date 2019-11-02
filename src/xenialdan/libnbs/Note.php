<?php

namespace xenialdan\libnbs;

/**
 * Represents a note played; contains the instrument and the key
 */
class Note
{
    /** @var int */
    public $instrument;
    /** @var int */
    public $key;

    /**
     * Note constructor.
     * @param int $instrument
     * @param int $key
     */
    public function __construct(int $instrument, int $key)
    {
        $this->instrument = $instrument;
        $this->key = $key;
    }

    /**
     * Gets instrument number
     * @return int
     */
    public function getInstrument(): int
    {
        return $this->instrument;
    }

    /**
     * Sets instrument number
     * @param int $instrument
     */
    public function setInstrument(int $instrument): void
    {
        $this->instrument = $instrument;
    }

    /**
     * @return int
     */
    public function getKey(): int
    {
        return $this->key;
    }

    /**
     * @param int $key
     */
    public function setKey(int $key): void
    {
        $this->key = $key;
    }

}