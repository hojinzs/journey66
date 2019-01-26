<?php

namespace App\Http\Requests;

use App\waypoint;
use Illuminate\Foundation\Http\FormRequest;

class AuthByWaypoint extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $uwid = $this->header('UWID');
        $originKey = waypoint::where('UWID',$uwid)->first()->journey()->select('key')->first();
        $requestKey = $this->header('key');
        
        return $originKey['key'] == $requestKey;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }
}
