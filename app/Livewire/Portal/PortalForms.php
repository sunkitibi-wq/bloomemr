<?php

namespace App\Livewire\Portal;

use App\Models\Patient;
use App\Models\PatientForm;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Intake & Consents')]
class PortalForms extends Component
{
    public ?Patient $patient = null;

    // Active form state
    public ?int $selectedFormId = null;

    public ?PatientForm $selectedForm = null;

    /** @var array<string, mixed> */
    public array $responses = [];

    public string $signature = '';

    public bool $isFormOpen = false;

    public function mount(): void
    {
        if (auth('portal')->check() && auth('portal')->user()->role === 'guardian') {
            $this->patient = Patient::where('portal_user_id', auth('portal')->id())
                ->with('patientForms.template')
                ->first();
        } else {
            $this->patient = Patient::where('practice_id', auth()->user()?->practice_id)
                ->with('patientForms.template')
                ->first();
        }
    }

    public function openForm(int $formId): void
    {
        $form = PatientForm::with('template')->findOrFail($formId);

        // Security check
        if ($this->patient && $form->patient_id !== $this->patient->id) {
            abort(403);
        }

        $this->selectedFormId = $form->id;
        $this->selectedForm = $form;
        $this->responses = [];
        $this->signature = '';

        // Initialize responses array from fields
        if ($form->template && is_array($form->template->fields)) {
            foreach ($form->template->fields as $field) {
                $this->responses[$field['name']] = in_array($field['type'], ['checkbox', 'yes_no'], true) ? false : '';
            }
        }

        $this->isFormOpen = true;
    }

    public function submitForm(): void
    {
        if (! $this->selectedForm) {
            return;
        }

        // Dynamically build validation rules based on form template fields
        $rules = [];
        $messages = [];

        if ($this->selectedForm->template && is_array($this->selectedForm->template->fields)) {
            foreach ($this->selectedForm->template->fields as $field) {
                $rule = [];
                if (isset($field['required']) && $field['required']) {
                    if (in_array($field['type'], ['checkbox', 'yes_no'], true)) {
                        $rule[] = 'accepted';
                    } else {
                        $rule[] = 'required';
                    }
                }
                if (in_array($field['type'], ['checkbox', 'yes_no'], true)) {
                    $rule[] = 'boolean';
                } else {
                    $rule[] = 'string';
                }

                $rules['responses.' . $field['name']] = implode('|', $rule);
                $messages['responses.' . $field['name'] . '.accepted'] = __('You must accept: :label', ['label' => $field['label']]);
                $messages['responses.' . $field['name'] . '.required'] = __('Field is required: :label', ['label' => $field['label']]);
            }
        }

        $rules['signature'] = 'required|string|min:3|max:100';

        $this->validate($rules, $messages);

        // Save submitted data
        $this->selectedForm->update([
            'status' => 'completed',
            'form_data' => $this->responses,
            'signature_name' => $this->signature,
            'signature_ip' => request()->ip() ?? '127.0.0.1',
            'signed_at' => now(),
        ]);

        session()->flash('message', __('Form submitted and electronically signed successfully.'));

        $this->isFormOpen = false;
        $this->selectedFormId = null;
        $this->selectedForm = null;

        // Reload data
        if ($this->patient) {
            $this->patient->load('patientForms.template');
        }
    }

    public function render(): View
    {
        $pendingForms = collect();
        $completedForms = collect();

        if ($this->patient) {
            $pendingForms = $this->patient->patientForms->where('status', 'pending');
            $completedForms = $this->patient->patientForms->where('status', 'completed');
        }

        return view('livewire.portal.portal-forms', [
            'pendingForms' => $pendingForms,
            'completedForms' => $completedForms,
        ])->layout('layouts.blank');
    }
}
