<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_start' => ['required'],
            'work_end' => ['required'],
            'note' => ['required', 'string'],
            'rests.*.start' => ['nullable'],
            'rests.*.end' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => '備考を記入してください。',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $workStart = Carbon::createFromFormat('H:i', $this->work_start);
            $workEnd = Carbon::createFromFormat('H:i', $this->work_end);

            if ($workStart >= $workEnd) {
                $validator->errors()->add('work_start', '出勤時間もしくは退勤時間が不適切な値です。');
            }

            $rests = $this->input('rests', []);
            foreach ($rests as $index => $rest) {
                if (!empty($rest['start']) && !empty($rest['end'])) {
                    $restStart = Carbon::parse($rest['start']);
                    $restEnd = Carbon::parse($rest['end']);

                    if ($restStart < $workStart || $restEnd > $workEnd) {
                        $validator->errors()->add("rests.{$index}", '休憩時間が勤務時間外です。');
                    }
                }
            }
        });
    }
}
