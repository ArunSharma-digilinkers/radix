<?php

namespace App\Livewire\Admin\Export;

use App\Models\ExportMarket;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Export Market')]
class Form extends Component
{
    public ?ExportMarket $market = null;

    public string $countryName = '';

    public string $slug = '';

    public string $isoCode = '';

    public string $isoNumeric = '';

    public string $blurb = '';

    public int $sortOrder = 0;

    public bool $isActive = true;

    public function mount(?ExportMarket $market = null): void
    {
        $this->authorize('export.manage');

        if ($market) {
            $this->market = $market;
            $this->countryName = $market->getTranslation('country_name', 'en') ?? '';
            $this->slug = $market->slug;
            $this->isoCode = $market->iso_code;
            $this->isoNumeric = $market->iso_numeric !== null ? (string) $market->iso_numeric : '';
            $this->blurb = $market->getTranslation('blurb', 'en') ?? '';
            $this->sortOrder = $market->sort_order;
            $this->isActive = (bool) $market->is_active;
        }
    }

    public function save(): void
    {
        $this->authorize('export.manage');

        $this->slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->countryName);

        $validated = $this->validate([
            'countryName' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('export_markets', 'slug')->ignore($this->market?->id)],
            'isoCode' => ['required', 'string', 'size:2', Rule::unique('export_markets', 'iso_code')->ignore($this->market?->id)],
            'isoNumeric' => ['nullable', 'integer', 'between:1,999', Rule::unique('export_markets', 'iso_numeric')->ignore($this->market?->id)],
            'blurb' => ['nullable', 'string'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
        ]);

        $market = $this->market ?? new ExportMarket;
        $market->fill([
            'slug' => $validated['slug'],
            'iso_code' => strtoupper($validated['isoCode']),
            'iso_numeric' => $validated['isoNumeric'] !== '' ? $validated['isoNumeric'] : null,
            'sort_order' => $validated['sortOrder'],
            'is_active' => $validated['isActive'],
        ]);
        $market->setTranslation('country_name', 'en', $validated['countryName']);
        $market->setTranslation('blurb', 'en', $validated['blurb'] ?? '');
        $market->save();

        session()->flash('success', 'Market saved.');

        $this->redirect(route('admin.export.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.export.form');
    }
}
