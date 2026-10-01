<?php

namespace App\Livewire\Admin\Dealers;

use App\Models\Dealer;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Dealer')]
class Form extends Component
{
    public ?Dealer $dealer = null;

    public string $name = '';

    public string $type = Dealer::TYPE_RETAIL;

    public string $contactPerson = '';

    public string $phone = '';

    public string $whatsapp = '';

    public string $email = '';

    public string $addressLine = '';

    public string $city = '';

    public string $state = '';

    public string $pincode = '';

    public string $countryCode = 'IN';

    public string $latitude = '';

    public string $longitude = '';

    public bool $isActive = true;

    public function mount(?Dealer $dealer = null): void
    {
        $this->authorize('dealers.manage');

        if ($dealer) {
            $this->dealer = $dealer;
            $this->name = $dealer->name;
            $this->type = $dealer->type;
            $this->contactPerson = (string) $dealer->contact_person;
            $this->phone = (string) $dealer->phone;
            $this->whatsapp = (string) $dealer->whatsapp;
            $this->email = (string) $dealer->email;
            $this->addressLine = (string) $dealer->address_line;
            $this->city = $dealer->city;
            $this->state = $dealer->state;
            $this->pincode = (string) $dealer->pincode;
            $this->countryCode = $dealer->country_code;
            $this->latitude = $dealer->latitude !== null ? (string) $dealer->latitude : '';
            $this->longitude = $dealer->longitude !== null ? (string) $dealer->longitude : '';
            $this->isActive = (bool) $dealer->is_active;
        }
    }

    public function save(): void
    {
        $this->authorize('dealers.manage');

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Dealer::TYPES)],
            'contactPerson' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'addressLine' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:128'],
            'state' => ['required', 'string', 'max:128'],
            'pincode' => ['nullable', 'string', 'max:16'],
            'countryCode' => ['required', 'string', 'size:2'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'isActive' => ['boolean'],
        ]);

        $dealer = $this->dealer ?? new Dealer;
        $dealer->fill([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'contact_person' => $validated['contactPerson'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'whatsapp' => $validated['whatsapp'] ?: null,
            'email' => $validated['email'] ?: null,
            'address_line' => $validated['addressLine'] ?: null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'] ?: null,
            'country_code' => strtoupper($validated['countryCode']),
            'latitude' => $validated['latitude'] !== '' ? $validated['latitude'] : null,
            'longitude' => $validated['longitude'] !== '' ? $validated['longitude'] : null,
            'is_active' => $validated['isActive'],
        ]);
        $dealer->save();

        session()->flash('success', 'Dealer saved.');

        $this->redirect(route('admin.dealers.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.dealers.form');
    }
}
