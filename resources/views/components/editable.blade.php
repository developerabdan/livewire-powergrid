@props([
    'primaryKey' => null,
    'row' => null,
    'field' => null,
    'theme' => null,
    'currentTable' => null,
    'tableName' => null,
    'showErrorBag' => null,
    'editable' => null,
])

@php
    $resolveContent = function (string $currentTable, string $field, \Illuminate\Database\Eloquent\Model|\stdClass $row): ?string {
        $currentField = $field;
        $replace = fn($content) => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
    
        /** @codeCoverageIgnore */
        if (str_contains($currentField, '.')) {
            $data = \Illuminate\Support\Str::of($field)->explode('.');
            $table = $data->get(0);
            $field = $data->get(1);
    
            if ($table === $currentTable) {
                return $replace($row->{$field});
            }
    
            return $replace($row->{$table}->{$field});
        }
    
        return $replace($row->{$field});
    };
    
    $fallback = html_entity_decode(strval(data_get($editable, 'fallback')), ENT_QUOTES, 'utf-8');
    $value = html_entity_decode(strval($resolveContent($currentTable, $field, $row)), ENT_QUOTES, 'utf-8');
    
    $content = !empty($value) || $value == '0' ? $value : $fallback;
    
    $params = [
        'theme' => $theme->name,
        'tableName' => $tableName,
        'id' => data_get($row, $primaryKey),
        'dataField' => $field,
        'content' => $content,
        'fallback' => $fallback,
    ];
@endphp
<div
    x-cloak
    x-data="pgEditable(@js($params))"
    style="width: 100% !important; height: 100% !important;"
>
    <div
        :class="{
            'py-2': theme == 'tailwind',
            'p-1': theme == 'bootstrap5',
        }"
        x-show="!showEditable"
        x-on:click="editable = true;"
        :id="`clickable-` + dataField + '-' + id"
        style="cursor: pointer; width: 100%; height: 100%;"
    >
        <span style="border-bottom: dotted 1px;">{{ $content }}</span>
    </div>
    <div
        x-show="showEditable && !hashError"
        style="margin-bottom: 4px"
    >
        {{ $input }}
    </div>
    @if ($showErrorBag)
        @error($field . '.' . $row->{$primaryKey})
            <div class="text-sm text-red-800 p-1 transition transition-all duration-200">
                {{ str($message)->replace($field . '.' . $row->{$primaryKey}, $field) }}
            </div>
        @enderror
    @endif
</div>
