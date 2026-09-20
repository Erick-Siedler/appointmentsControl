<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'duration' => ['required', 'regex:/^\d{1,3}:[0-5]\d$/'],
            'project_id' => ['nullable', 'required_without:new_project_name', 'integer', 'exists:projects,id'],
            'new_project_name' => ['nullable', 'required_without:project_id', 'string', 'max:255'],
            'project_task' => ['nullable', 'string', 'max:255'],
            'occurrence' => ['nullable', 'string', 'max:255'],
            'internal_description' => ['nullable', 'string', 'max:5000'],
            'entry_type' => ['required', 'in:work,overtime'],
            'owner' => ['nullable', 'string', 'max:255'],
            'appointment_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('duration')) {
                    return;
                }

                [$hours, $minutes] = array_map('intval', explode(':', $this->string('duration')->toString()));

                if (($hours * 60) + $minutes < 1) {
                    $validator->errors()->add('duration', 'A duração deve ser maior que zero.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'new_project_name' => $this->filled('new_project_name')
                ? preg_replace('/\s+/u', ' ', trim($this->string('new_project_name')->toString()))
                : null,
            'project_task' => $this->nullableTrimmed('project_task'),
            'occurrence' => $this->nullableTrimmed('occurrence'),
            'internal_description' => $this->nullableTrimmed('internal_description'),
            'owner' => $this->nullableTrimmed('owner'),
        ]);
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Informe a data.',
            'date.date_format' => 'Informe uma data válida.',
            'duration.required' => 'Informe a duração.',
            'duration.regex' => 'Use o formato HH:MM, por exemplo 02:30.',
            'project_id.required_without' => 'Selecione um projeto ou cadastre um novo.',
            'project_id.exists' => 'O projeto selecionado não existe mais.',
            'new_project_name.required_without' => 'Informe o nome do novo projeto.',
            'new_project_name.max' => 'O nome do projeto pode ter no máximo 255 caracteres.',
            'project_task.max' => 'A tarefa pode ter no máximo 255 caracteres.',
            'occurrence.max' => 'A ocorrência pode ter no máximo 255 caracteres.',
            'internal_description.max' => 'A descrição interna pode ter no máximo 5.000 caracteres.',
            'entry_type.required' => 'Selecione o tipo de apontamento.',
            'entry_type.in' => 'Selecione um tipo de apontamento válido.',
            'owner.max' => 'O responsável pode ter no máximo 255 caracteres.',
        ];
    }

    private function nullableTrimmed(string $field): ?string
    {
        $value = trim($this->string($field)->toString());

        return $value === '' ? null : $value;
    }
}
