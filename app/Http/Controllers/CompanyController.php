<?php

namespace App\Http\Controllers;

use App\Helpers\ExcelFields;
use App\Models\Company;
use App\Models\Fact;
use App\Models\Forecast;
use Illuminate\Http\Request;


class CompanyController extends Controller
{
    public function fileImportExport()
    {
        return view('import');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return bool[]
     */
    public function fileImport(Request $request)
    {
        $fields = (new ExcelFields())->transform(
            $request->file('file')
                    ->store('temp')
        );

        foreach ($fields['companies'] as $company) {
            try {
                Company::create(
                    [
                        'name' => $company,
                    ]
                );
            } catch (\Exception $exception) {
                continue;
            }
        }

        foreach ($fields['fact'] as $key => $facts) {
            foreach ($facts as $fact) {
                try {
                    $companyId = Company::where('name', $fact['company'])
                                        ->first()->id
                    ;
                    $result    = Fact::where('company_id', $companyId)
                                     ->where('date', date('Y-m-d', strtotime($fact['date'])))
                                     ->first()
                    ;

                    if (!isset($result)) {
                        Fact::create(
                            [
                                'Qliq'       => $fact['Qliq'],
                                'Qoil'       => $fact['Qoil'],
                                'company_id' => $companyId,
                                'date'       => date('Y-m-d', strtotime($fact['date'])),
                            ]
                        );
                    } else {
                        $result->update(
                            [
                                'Qliq' => $fact['Qliq'] + $result->Qliq,
                                'Qoil' => $fact['Qoil'] + $result->Qoil,
                            ]
                        );
                    }
                } catch (\Exception $exception) {
                    return [
                        'success' => false,
                        'message' => $exception->getMessage(),
                    ];
                }
            }
        }

        foreach ($fields['forecast'] as $key => $forecasts) {
            foreach ($forecasts as $forecast) {
                try {
                    $companyId = Company::where('name', $forecast['company'])
                                        ->first()->id
                    ;
                    $result    = Forecast::where('company_id', $companyId)
                                         ->where('date', date('Y-m-d', strtotime($forecast['date'])))
                                         ->first()
                    ;

                    if (!isset($result)) {
                        Forecast::create(
                            [
                                'Qliq'       => $forecast['Qliq'],
                                'Qoil'       => $forecast['Qoil'],
                                'company_id' => $companyId,
                                'date'       => date('Y-m-d', strtotime($forecast['date'])),
                            ]
                        );
                    } else {
                        $result->update(
                            [
                                'Qliq' => $forecast['Qliq'] + $result->Qliq,
                                'Qoil' => $forecast['Qoil'] + $result->Qoil,
                            ]
                        );
                    }
                } catch (\Exception $exception) {
                    return [
                        'success' => false,
                        'message' => $exception->getMessage(),
                    ];
                }
            }
        }

        return [
            'success' => true,
        ];
    }
}
