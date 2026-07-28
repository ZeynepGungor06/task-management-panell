<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends FormRequest
{
    
    public function authorize()
    {
        return Auth::check();
    }

    
    public function rules(): array
    {
        return [
            'file'=>'required|file|mimes:pdf,doc,docx,jpg,png,txt,zip|max:5120',
        ];
    }
    public function messages(){
        return[
            'file.required'=>'Lütfen yüklemek için bir dosya seçin.',
            'file.file'=>'Yüklenen veri geçerli bir dosya formatında olmalıdır',
            'file.mimes'=>'Sadece PDF,WORD,JPG,PNG, TXT, ZIP dosyaları yüklenebilir.',
            'file.max'=>'Dosya boyutu en fazla 5MB olabilir',

        ];
    }
}
