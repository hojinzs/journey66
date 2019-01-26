<?php

namespace App\Http\Requests;

use App\journey;
use Illuminate\Foundation\Http\FormRequest;

class AuthByJourneyKey extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $ujid = $this->header('UJID');
        $requestKey = $this->header('key');
        $originKey = journey::select('key')->where('UJID',$ujid)->first();
        
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
            //
        ];
    }
}
