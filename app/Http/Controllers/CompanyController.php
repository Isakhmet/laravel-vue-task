<?php

namespace App\Http\Controllers;

use App\Helpers\ExcelFields;
use App\Models\Company;
use App\Models\Fact;
use App\Models\Forecast;
use ArielMejiaDev\LarapexCharts\LarapexChart;
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

    public function chart()
    {
        $data        = [];
        $companyName = 'company1';

        $facts = Fact::with('company')
                     ->whereHas(
                         'company', function ($q) use ($companyName) {
                         $q->where('name', '=', $companyName);
                     }
                     )
                     ->get(['Qoil', 'company_id', 'date'])
        ;

        if ($facts->count() > 0) {
            foreach ($facts as $fact) {
                $data['fact']['date'][] = $fact->date;
                $data['fact']['Qoil'][] = $fact->Qoil;
            }
        }

        $forecasts = Forecast::with('company')
                     ->whereHas(
                         'company', function ($q) use ($companyName) {
                         $q->where('name', '=', $companyName);
                     }
                     )
                     ->whereIn('date', $data['fact']['date'])
                     ->get(['Qoil', 'company_id', 'date'])
        ;

        if ($forecasts->count() > 0) {
            foreach ($forecasts as $forecast) {
                $data['forecast']['Qoil'][] = $forecast->Qoil;
            }
        }

        $chart = (new LarapexChart)->lineChart()
                                   ->setTitle($companyName)
                                   ->setSubtitle('Qoil.')
                                   ->addData('Fact', $data['fact']['Qoil'])
                                   ->addData('Forecast', $data['forecast']['Qoil'])
                                   ->setXAxis($data['fact']['date'])
        ;

        return view('chart', compact('chart'));
    }
}
