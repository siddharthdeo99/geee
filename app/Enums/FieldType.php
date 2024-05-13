<?php

namespace App\Enums;

enum FieldType: string
{
    case CHECKBOX = 'checkbox';
    case CHECKBOXLIST = 'checkboxlist';
    case TEXT = 'text';
    case DATETIME = 'datetime';
    case TIME = 'time';
    case DATE = 'date';
    case RADIO = 'radio';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case EDITOR = 'editor';
    case COLORPICKER = 'colorpicker';
    case TAGSINPUT = 'tagsinput';

    public function label(): string
    {
        return match ($this) {
            self::CHECKBOX => 'Checkbox',
            self::CHECKBOXLIST => 'Checkbox List',
            self::TEXT => 'Text',
            self::DATETIME => 'DateTime',
            self::TIME => 'Time',
            self::DATE => 'Date',
            self::RADIO => 'Radio',
            self::TEXTAREA => 'TextArea',
            self::SELECT => 'Select',
            self::EDITOR => 'Editor',
            self::COLORPICKER => 'Color Picker',
            self::TAGSINPUT => 'Tags Input',
        };
    }
}
