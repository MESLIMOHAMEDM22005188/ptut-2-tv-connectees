<?php

namespace Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReader
{

    public function __construct()
    {
    }


    public function readExcel($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $columnIndices = range('D', 'G');
        $filteredData = [];

        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($columnIndices as $columnIndex) {
                $cellValue = $sheet->getCell($columnIndex . $row->getRowIndex())->getValue();
                $rowData[] = $cellValue;
            }
            $filteredData[] = $rowData;
        }

        return $filteredData;
    }

}