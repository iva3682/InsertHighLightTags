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
    private $wrapCharLeft = '';
    private $wrapCharRight = '';
    private $stringFill = '';

    public function __construct(int $position, int $length, string $wrapChars) {
        $this->position = $position;
        $this->length = $length;

        if($position) {
            $this->wrapCharLeft = mb_substr($wrapChars, 0, 1);

            if(mb_strlen($wrapChars) > 1) {
                $this->wrapCharRight = mb_substr($wrapChars, 1, 1);
            }
        }
        elseif(mb_strlen($wrapChars) > 0) {
            $this->wrapCharRight = mb_substr($wrapChars, 0, 1);
        }

        $this->stringFill = str_pad('', $length);
    }

    public function getPosition(): int { return $this->position; }
    public function getLength(): int { return $this->length; }
    public function getWrapCharLeft(): string { return $this->wrapCharLeft; }
    public function getWrapCharRight(): string { return $this->wrapCharRight; }
    public function getStringFill(): string { return $this->stringFill; }
}