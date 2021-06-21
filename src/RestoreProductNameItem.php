<?php
namespace iva3682\InsertHighLightTags;

/**
 * RestoreProductNameItem short summary.
 *
 * RestoreProductNameItem description.
 *
 * @version 1.0
 * @author Иван
 */
class RestoreProductNameItem
{
    private $position = 0;
    private $length = 0;
    private $stringFill = '';

    public function __construct(int $position, int $length) {
        $this->position = $position;
        $this->length = $length;
        $this->stringFill = str_pad('', $length);
    }

    public function getPosition(): int { return $this->position; }
    public function getLength(): int { return $this->length; }
    public function getStringFill(): string { return $this->stringFill; }
}