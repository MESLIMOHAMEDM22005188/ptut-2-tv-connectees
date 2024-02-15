<?php

namespace Models;

use JsonSerializable;
use PDO;

/**
 * Class Alert
 *
 * Alert entity
 *
 * @package Models
 */
class Schedule extends Model implements Entity, JsonSerializable
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var CodeAde[]
     */
    private $codes;

    /**
     * Return the alert corresponding to an ID
     *
     * @param $id
     *
     * @return null
     */
    public function get($id) {
      return null;
    }

    /**
     * @param int $begin
     * @param int $numberElement
     *
     * @return array|Alert[]
     */
    public function getList() {
        return [];
    }

    /**
     * @return CodeAde[]
     */
    public function getCodes() {
        return $this->codes;
    }

    /**
     * @param CodeAde[] $codes
     */
    public function setCodes($codes) {
        $this->codes = $codes;
    }


    public function jsonSerialize() {
        return get_object_vars($this);
    }
}
