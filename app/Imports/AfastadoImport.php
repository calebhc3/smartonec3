<?php

namespace App\Imports;

use App\Models\Afastado;
use Maatwebsite\Excel\Concerns\ToModel;

class AfastadoImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Afastado([
            //
        ]);
    }
}
