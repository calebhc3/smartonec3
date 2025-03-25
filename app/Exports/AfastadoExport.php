<?php

namespace App\Exports;

use App\Models\Afastado;
use Maatwebsite\Excel\Concerns\FromCollection;

class AfastadoExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Afastado::all();
    }
}
