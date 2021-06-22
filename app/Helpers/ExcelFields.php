<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelFields
{
    public function transform($path)
    {
        $sheetData    = $this->sheetToArray($path);
        $headers      = [];
        $data         = [];
        $fact         = [];
        $forecast     = [];
        $factQliq     = 0;
        $forecastQliq = 0;
        $companies    = [];

        foreach ($sheetData[0] as $ceil => $row) {
            if (isset($row)) {
                $headers[$row] = $ceil;
            }
        }

        foreach ($sheetData[1] as $ceil => $row) {
            $iteratorStart = $headers['fact'];
            $iteratorEnd   = $headers['forecast'];

            if ($ceil >= $iteratorStart) {
                if ($ceil < $iteratorEnd) {
                    if (isset($row)) {
                        if (strcmp($row, 'Qoil') === 0) {
                            $factQliq     = $ceil - 1;
                            $forecastQoil = $iteratorEnd - 1;
                        }
                    }
                } else {
                    if (isset($row)) {
                        if (strcmp($row, 'Qoil') === 0) {
                            $forecastQliq = $ceil - 1;
                        }
                    }
                }
            }
        }

        foreach ($sheetData[2] as $ceil => $row) {
            $iteratorStart = $headers['fact'];
            $iteratorEnd   = $headers['forecast'];

            if ($ceil >= $iteratorStart) {
                if ($ceil < $iteratorEnd) {
                    if ($ceil <= $factQliq) {
                        $fact[$row]['Qliq'] = $ceil;
                    } else {
                        $fact[$row]['Qoil'] = $ceil;
                    }
                } else {
                    if (isset($row)) {
                        if ($ceil <= $forecastQliq) {
                            $forecast[$row]['Qliq'] = $ceil;
                        } else {
                            $forecast[$row]['Qoil'] = $ceil;
                        }

                    }
                }
            }
        }

        foreach ($sheetData as $key => $ceils) {
            if ($key > 2) {
                foreach ($fact as $date => $product) {
                    $companies[$ceils[1]]                 = $ceils[1];
                    $data['fact'][$key][$date]['company'] = $ceils[1];
                    $data['fact'][$key][$date]['date']    = $date;
                    $data['fact'][$key][$date]['Qliq']    = $ceils[$product['Qliq']];
                    $data['fact'][$key][$date]['Qoil']    = $ceils[$product['Qoil']];
                }

                foreach ($forecast as $date => $product) {
                    $data['forecast'][$key][$date]['company'] = $ceils[1];
                    $data['forecast'][$key][$date]['date']    = $date;
                    $data['forecast'][$key][$date]['Qliq']    = $ceils[$product['Qliq']];
                    $data['forecast'][$key][$date]['Qoil']    = $ceils[$product['Qoil']];
                }
            }
        }

        $data['companies'] = $companies;

        return $data;
    }

    /**
     * @param $path
     *
     * @return array
     */
    public function sheetToArray($path)
    {
        $spreadsheet = IOFactory::load("../storage/app/" . $path);
        $sheets      = $spreadsheet->getAllSheets();

        if (count($sheets) > 1) {
            foreach ($sheets as $sheet) {
                if (strcmp($sheet->getTitle(), 'таблица') === 0) {
                    $sheetData = $sheet->toArray();
                }
            }
        } else {
            $sheetData = $sheets[0]->toArray();
        }

        return $sheetData;
    }
}