<?php

namespace Models;

class RoomRepository extends Model{

    public function exist($name): bool
    {
        $sql = "SELECT * FROM ecran_rooms WHERE name LIKE ?";
        $stmt = self::getConnection()->prepare($sql);
        // Incluez les pourcentages directement dans la chaîne passée à execute()
        $stmt->execute(["%$name%"]);
        return (bool)$stmt->fetch();
    }


    public function add($name) : void {
        $sql = "INSERT INTO ecran_rooms(name) VALUES (?)";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$name]);
    }
    public function delete($name) : void {
    $sql = "DELETE FROM ecran_rooms WHERE name = ?";
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute([$name]);
    }



    /**
     * @return Room[]
     */
    public function getAllComputerRooms(){
        $sql = "SELECT DISTINCT name FROM ecran_rooms WHERE isComputerRoom=TRUE";
        //$sql = "SELECT * FROM ecran_rooms WHERE isComputerRoom=TRUE";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        $roomList = [];

        while($row = $stmt->fetch()){
            $roomList[] = new Room($row['name']);

        }
        return $roomList;
    }

    public function getAllRoom()
    {
        $sql = "SELECT DISTINCT name FROM ecran_rooms WHERE name NOT LIKE '%Amphi%' AND name NOT LIKE '%Labora%'";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        $roomList = [];

        while ($row = $stmt->fetch()) {
            // Vérifiez si le nom de la salle contient une virgule (ou tout autre indicateur de salles multiples).
            if (strpos($row['name'], ',') === false) {
                // Si aucune virgule n'est trouvée, ajoutez la salle à la liste.
                $roomList[] = new Room($row['name']);
            }

        }
        return $roomList;
    }

    public function getAllNonComputerRooms()
    {
        $sql = "SELECT DISTINCT name FROM ecran_rooms WHERE (name NOT LIKE '%Amphi%' AND name NOT LIKE '%Labora%') AND (isComputerRoom=FALSE OR isComputerRoom IS NULL)";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute();
        $roomList = [];

        while ($row = $stmt->fetch()) {
            // Vérifiez si le nom de la salle contient une virgule (ou tout autre indicateur de salles multiples).
            if (strpos($row['name'], ',') === false) {
                // Si aucune virgule n'est trouvée, ajoutez la salle à la liste.
                $roomList[] = new Room($row['name']);
            }

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
        return $stmt->execute([$value,$roomName]);
    }

    public function isComputerRoom($roomName) {
        $sql = "SELECT isComputerRoom FROM ecran_rooms WHERE name = ?";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute([$roomName]);
        $result = $stmt->fetchColumn();

        // Si la salle est une salle informatique, la fonction retourne TRUE, sinon FALSE
        return $result ? true : false;
    }

    public function markRoomsAsComputerRooms(array $roomNames) : void {
        $sql = "UPDATE ecran_rooms SET isComputerRoom = TRUE WHERE name IN (?, ?)";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($roomNames);
    }



}
