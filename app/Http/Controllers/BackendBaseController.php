<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class BackendBaseController extends Controller
{
    public function soft_delete($data){
        $data->deleted_by = admin()->id;
        $data->delete();

        return $data;
    }

    public function statusChange($modelData,$status)
    {
        $modelData->status = $status;
        $modelData->updated_by = admin()->id;
        $modelData->update();
    }
}
