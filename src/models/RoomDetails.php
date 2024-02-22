<?php

namespace Models;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class RoomDetails {
    public static function getFromExcel($roomName, $filePath) {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);

            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            if (isset($cells[3]) && strtolower(trim($cells[3])) == strtolower(trim($roomName))) {
                return [
                    'name' => $cells[3],
                    'capacity' =>  $cells[4],
                    'equipment' => $cells[5],
                    'cables' =>  $cells[6],
                ];
            }
        }

        return null; // Room not found
    }


}
