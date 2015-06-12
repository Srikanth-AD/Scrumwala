<?php namespace App\Http\Requests;
use App\Project;
use App\Http\Requests\Request;

class ProjectRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
            'name' => 'required|min:3|max:100',
            'slug' => 'required|alpha_dash|min:4|max:50',
            'issue_prefix' => 'required|alpha|min:3|max:10',
            'deadline' => 'sometimes|date_format:Y-m-d',
        ];
    }

}
