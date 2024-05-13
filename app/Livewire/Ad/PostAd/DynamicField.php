<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\AdFieldValue;
use App\Models\CategoryField;
use App\Models\Field;
use Filament\Forms\Components\{Checkbox,  DatePicker, DateTimePicker, TextInput, Radio, Select, TimePicker, Textarea};
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class DynamicField extends Component implements HasForms
{
    use InteractsWithForms;

    #[Reactive]
    public $id;
    public ?array $data = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->populateDataFromSavedValues();
    }

    /**
     * Populate data property from saved values.
     */
    protected function populateDataFromSavedValues(): void
    {
        $savedValues = AdFieldValue::where('ad_id', $this->id)
        ->pluck('value', 'field_id')
        ->map(function ($value) {
            return json_decode($value, true);
        })->toArray();

        foreach ($savedValues as $fieldId => $value) {
            $field = Field::find($fieldId);
            if ($field) {
                $this->data[$field->name] = $value;
            }
        }
    }

    /**
     * Get fields for the Ad.
     */
    protected function getFieldsForAd()
    {
        $ad = Ad::find($this->id);
        if (!$ad) return collect([]);
        return CategoryField::where('category_id', $ad->category_id)
            ->with('field')
            ->get();
    }

    /**
     * Define form components based on fields.
     */
    public function form(Form $form): Form
    {
        $fields = $this->getFieldsForAd();
        $components = $this->mapFieldsToComponents($fields);
        return $form->schema($components)->statePath('data');
    }

    /**
     * Map fields to form components.
     */
    protected function mapFieldsToComponents($fields)
    {
        $components = [];
        foreach ($fields as $fieldData) {
            // Check if the field relationship is not null
            if (!$fieldData->field) {
                // Skip this iteration if the field is null
                continue; 
            }
            $fieldType = $fieldData->field->type->value;
            switch ($fieldType) {
                case 'text':
                    $components[] = TextInput::make($fieldData->field->id)->label($fieldData->field->name)->label($fieldData->field->name)->required($fieldData->field->required)->live(onBlur: true);
                    break;
                case 'select':
                    $components[] = Select::make($fieldData->field->id)->label($fieldData->field->name)->options($fieldData->field->options)->required($fieldData->field->required)->live(onBlur: true);
                    break;
                case 'checkbox':
                    $components[] = Checkbox::make($fieldData->field->id)->label($fieldData->field->name)->required($fieldData->field->required)->live(onBlur: true);
                    break;

                case 'radio':
                    $components[] = Radio::make($fieldData->field->id)->label($fieldData->field->name)->options($fieldData->field->options)->required($fieldData->field->required)->live(onBlur: true);
                    break;
                case 'datetime':
                    $components[] = DateTimePicker::make($fieldData->field->id)->label($fieldData->field->name)->required($fieldData->field->required)->live(onBlur: true);
                    break;
                case 'date':
                    $components[] = DatePicker::make($fieldData->field->id)->label($fieldData->field->name)->required($fieldData->field->required)->live(onBlur: true);
                    break;
                case 'time':
                    $components[] = TimePicker::make($fieldData->field->id)->label($fieldData->field->name)->required($fieldData->field->required)->live(onBlur: true);
                    break;
                case 'textarea':
                    $components[] = Textarea::make($fieldData->field->id)->label($fieldData->field->name)->required($fieldData->field->required)->live(onBlur: true);
                    break;
            }
        }
        return $components;
    }

    /**
     * Handle updates to component properties.
     */
    public function updated($name, $value)
    {
        $userId = auth()->id();
        if (!$userId) abort(403, 'Unauthorized action.');
        $this->saveFieldValue($name, $value, $userId);
    }

    /**
     * Save field value to the database.
     */
    protected function saveFieldValue($name, $value, $userId)
    {
        $fieldName = str_replace('data.', '', $name);
        $field = Field::find($fieldName);
        if (!$field) return;
        $ad = Ad::find($this->id);
        if (!$ad || $ad->user_id !== $userId) abort(403, 'Unauthorized action.');
        AdFieldValue::updateOrCreate(['ad_id' => $this->id, 'field_id' => $field->id], ['value' => json_encode($value)]);
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.ad.post-ad.dynamic-field');
    }
}
