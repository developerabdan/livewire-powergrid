@props([
    'theme' => '',
    'enabledFilters' => [],
    'column' => null,
    'inline' => null,
    'inputText' => null,
    'inputTextOptions' => [],
])
<div wire:ignore.self>
    @php
        $field = data_get($inputText, 'dataField') ?? data_get($inputText, 'field');
    @endphp
    @if(filled($inputText))
        <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
            <div class="relative">
                <select id="input_text_options"
                        class="power_grid {{ $theme->selectClass }} {{ data_get($column, 'headerClass') }}"
                        style="{{ data_get($column, 'headerStyle') }}"
                        wire:model.defer="filters.input_option_text.{{ $field }}"
                        wire:input.defer="filterInputTextOptions('{{ $field }}', $event.target.value, '{{ data_get($inputText, 'label') }}')">
                    @foreach($inputTextOptions as $key => $value)
                        <option value="{{ $key }}">{{ trans($value) }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                    <x-livewire-powergrid::icons.down class="w-4 h-4 dark:text-gray-300"/>
                </div>
            </div>
            <div class="mt-1">
                <input
                    data-id="{{ $field }}"
                    @if(isset($enabledFilters[$field]['disabled']) && boolval($enabledFilters[$field]['disabled']) === true) disabled @else
                    wire:model.debounce.800ms="filters.input_text.{{ $field  }}"
                    wire:input.debounce.800ms="filterInputText('{{ $field }}', $event.target.value, '{{ data_get($inputText, 'label') }}')"
                    @endif
                    type="text"
                    class="power_grid {{ $theme->inputClass }}"
                    placeholder="{{ empty($column)?data_get($inputText, 'label'):($column->placeholder?:$column->title) }}" />
            </div>
        </div>
    @endif
</div>
