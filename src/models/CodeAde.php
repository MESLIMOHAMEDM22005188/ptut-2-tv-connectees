<?php

namespace Models;

use JsonSerializable;
use PDO;

/**
 * Class CodeAde
 *
 * Code ADE entity
 *
 * @package Models
 */
class CodeAde extends Model implements Entity, JsonSerializable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string (year | group | halfGroup | teacher)
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string | int
     */
    private $code;

    public static function find() {
        $sql = "SELECT * FROM ecran_code_ade";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
        return $result;
    }
    
    public function insert() {
        $database = $this->getDatabase();
        $request = $database->prepare('INSERT INTO ecran_code_ade (type, title, code) VALUES (:type, :title, :code)');

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);

        $request->execute();

        return $database->lastInsertId();
    }

    /**
     * @inheritDoc
     */
    public function update() {
        $request = $this->getDatabase()->prepare('UPDATE ecran_code_ade SET title = :title, code = :code, type = :type WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);

        $request->execute();

        return $request->rowCount();
    }

    public function getCodeWithNoYearSet(){
        $codeList = [];
        $request = $this->getDatabase()->prepare('SELECT * FROM ecran_code_ade WHERE ecran_code_ade.code NOT IN (SELECT code FROM ecran_ade_years)');
        $request->execute();
        while($row = $request->fetch()){
            $codeList[] = $row['code'];
        }
        return $codeList;
    }

    public function getCodeOfAYear($year){
        $codeList = [];
        $request = $this->getDatabase()->prepare('SELECT code FROM ecran_ade_years WHERE year = ?');
        $request->execute([$year]);
        while($row = $request->fetch()){
            $codeList[] = $row['code'];
        }
        return $codeList;
    }

    public function addYearForCode($code, $year){
        $request = $this->getDatabase()->prepare('INSERT INTO ecran_ade_years VALUES (?,?)');
        $request->execute([$code,$year]);
    }

    public function deleteYearForCode($code){
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_ade_years WHERE code=?');
        $request->execute([$code]);
    }

    public function getAllAdeCode(){
        $codeList = [];
        $request = $this->getDatabase()->prepare('SELECT code FROM ecran_ade_years');
        $request->execute();
        while($row = $request->fetch()){
            $codeList[] .= $row['code'];
        }
        return $codeList;
    }

    /**
     * @inheritDoc
     */
    public function delete() {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_code_ade WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }


    /**
     * @inheritDoc
     */
    public function get($id) {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade WHERE id = :id LIMIT 1');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getList() {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade ORDER BY id DESC LIMIT 1000');

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Check if a code ADE already exist with this title or with this code
     *
     * @param $title
     * @param $code
     *
     * @return array|mixed
     */
    public function checkCode($title, $code) {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade WHERE title = :title OR code = :code LIMIT 2');

        $request->bindParam(':title', $title, PDO::PARAM_STR);
        $request->bindParam(':code', $code, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * List of codes related to type
     *
     * @param $type
     *
     * @return CodeAde[]
     */
    public function getAllFromType($type) {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade WHERE type = :type ORDER BY id DESC LIMIT 500');

        $request->bindParam(':type', $type, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get a code ADE thanks his code
     *
     * @param $code
     *
     * @return mixed|CodeAde
     */
    public function getByCode($code) {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade WHERE code = :code LIMIT 1');

        $request->bindParam(':code', $code, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
    }

    /**
     *
     *
     * @param $id
     *
     * @return array|mixed
     */
    public function getByAlert($id) {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade JOIN ecran_code_alert ON ecran_code_ade.id = ecran_code_alert.code_ade_id WHERE alert_id = :id LIMIT 100');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @inheritDoc
     */
    public function setEntity($data) {
        $entity = new CodeAde();

        $entity->setId($data['id']);
        $entity->setTitle($data['title']);
        $entity->setCode($data['code']);
        $entity->setType($data['type']);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function setEntityList($dataList) {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data);
        }
        return $listEntity;
    }

    /**
     * @return int|string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param $code
     */
    public function setCode($code) {
        $this->code = intval($code);
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id) {
        $this->id = intval($id);
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }
}
