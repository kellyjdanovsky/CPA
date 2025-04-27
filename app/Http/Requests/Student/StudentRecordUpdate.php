<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\Qs;

class StudentRecordUpdate extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:6|max:150',
            'gender' => 'required|string',
            'status' => 'required|string|in:Normal,ADRA,TEAM3',
            'phone' => 'sometimes|nullable|string|min:6|max:20',
            'phone2' => 'sometimes|nullable|string|min:6|max:20',
            'dob' => 'required|date',
            'age' => 'sometimes|nullable|numeric',
            'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:2048',
            'address' => 'required|string|min:6|max:120',
            'bg_id' => 'sometimes|nullable',
            'nom_p' => 'sometimes|nullable|string|max:100',
            'prof_p' => 'sometimes|nullable|string|max:100',
            'nom_m' => 'sometimes|nullable|string|max:100',
            'prof_m' => 'sometimes|nullable|string|max:100',
            'my_class_id' => 'required',
            'section_id' => 'required',

            'my_parent_id' => 'sometimes|nullable',
            'dorm_id' => 'sometimes|nullable',
        ];
    }

    public function attributes()
    {
        return  [
            'nal_id' => 'Nationality',
            'dorm_id' => 'Dormitory',
            'state_id' => 'State',
            'lga_id' => 'LGA',
            'bg_id' => 'Blood Group',
            'my_parent_id' => 'Parent',
            'my_class_id' => 'Class',
            'section_id' => 'Section',
        ];
    }

    protected function getValidatorInstance()
    {
        $input = $this->all();

        // Définir my_parent_id comme NULL car nous avons supprimé ce champ du formulaire
        $input['my_parent_id'] = NULL;

        $this->getInputSource()->replace($input);

        return parent::getValidatorInstance();
    }
}
