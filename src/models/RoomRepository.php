<?php

namespace Models;

class RoomRepository extends Model{

    public function exist($name): bool
    {
        $sql = "SELECT * FROM ecran_rooms WHERE name LIKE '%" . $name . "%'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$name]);
        if($stmt->fetch()){
            return true;
        }
        return false;
    }

    public function add($name) : void {
        $sql = "INSERT INTO ecran_rooms(name) VALUES (?)";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$name]);
    }

    /**
     * @return Room[]
     */
    public function getAllComputerRooms(){
        $sql = "SELECT * FROM ecran_rooms WHERE isComputerRoom=TRUE";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        $roomList = [];

        while($row = $stmt->fetch()){
            $roomList[] = new Room($row['name']);
        }
        return $roomList;
    }

    public function getAllRoom(){
        $sql = "SELECT * FROM ecran_rooms";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        $roomList = [];

        while($row = $stmt->fetch()){
            $roomList[] = new Room($row['name']);
        }
        return $roomList;
    }

    public function lockRoom($roomName, $motif, $endDate){
        $sql = "INSERT INTO secretary_lock_room(roomName, motif, lockEndDate) VALUES (?,?,?)";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$roomName,$motif, $endDate]);
    }

    public function isRoomLocked($roomName){
        $date = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM secretary_lock_room WHERE roomName = ? AND lockEndDate > ?";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$roomName,$date]);
        if($stmt->fetch()){
            return true;
        }
        return false;
    }

    public function getMotifLock($roomName){
        $sql = "SELECT * FROM secretary_lock_room WHERE roomName = ?";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$roomName]);
        if($row = $stmt->fetch()){
            return [$row['motif'],$row['lockEndDate']];
        }
        return null;
    }

    public function unlockRoom($roomName){
        $sql = "DELETE FROM secretary_lock_room WHERE roomName = ?";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$roomName]);
    }

    public function resetComputerRoomCheck(){
        $sql = "UPDATE ecran_rooms SET isComputerRoom=0";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([]);
    }
    public function updateComputerRoom($roomName, $value){
        $sql = "UPDATE ecran_rooms SET isComputerRoom=? WHERE name=?";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$value,$roomName]);
    }


}
