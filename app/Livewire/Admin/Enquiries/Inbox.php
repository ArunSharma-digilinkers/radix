<?php

namespace App\Livewire\Admin\Enquiries;

use App\Models\Enquiry;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Sales-role inbox. Deliberately never declares a public Enquiry (or
 * collection of them) property — everything the component needs to act on
 * is a scalar id/string. $enquiry->$hidden (ip_address, user_agent,
 * internal_notes) only governs toArray()/toJson() serialisation, not what a
 * bound model property would expose in Livewire's wire snapshot payload, so
 * the safer shape is to never bind the model at all.
 */
#[Layout('layouts::admin')]
#[Title('Enquiries')]
class Inbox extends Component
{
    use WithPagination;

    /** open | overdue | all */
    public string $filter = 'open';

    public string $search = '';

    /** @var array<int, string> Keyed by enquiry id — populated fresh each render(). */
    public array $noteDrafts = [];

    public function mount(): void
    {
        $this->authorize('enquiries.manage');
    }

    public function updating($property): void
    {
        if (in_array($property, ['filter', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function markResponded(int $id): void
    {
        $this->authorize('enquiries.manage');

        Enquiry::findOrFail($id)->markResponded();
    }

    public function saveNote(int $id): void
    {
        $this->authorize('enquiries.manage');

        Enquiry::whereKey($id)->update(['internal_notes' => $this->noteDrafts[$id] ?? '']);
        session()->flash('success', 'Note saved.');
    }

    public function export()
    {
        $this->authorize('enquiries.manage');

        $rows = $this->baseQuery()->orderByDesc('created_at')->get([
            'id', 'type', 'name', 'email', 'phone', 'company', 'city', 'status', 'created_at', 'responded_at',
        ]);

        return Response::streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Type', 'Name', 'Email', 'Phone', 'Company', 'City', 'Status', 'Received', 'Responded']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id, $row->type, $row->name, $row->email, $row->phone, $row->company, $row->city,
                    $row->status, $row->created_at, $row->responded_at,
                ]);
            }

            fclose($handle);
        }, 'enquiries-'.now()->format('Y-m-d').'.csv');
    }

    private function baseQuery()
    {
        return Enquiry::query()
            ->when($this->filter === 'open', fn ($query) => $query->open())
            ->when($this->filter === 'overdue', fn ($query) => $query->overdue())
            ->when($this->search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('company', 'like', '%'.$this->search.'%')));
    }

    public function render()
    {
        $enquiries = $this->baseQuery()->orderByDesc('created_at')->paginate(20);

        foreach ($enquiries as $enquiry) {
            $this->noteDrafts[$enquiry->id] ??= $enquiry->internal_notes ?? '';
        }

        return view('livewire.admin.enquiries.inbox', ['enquiries' => $enquiries]);
    }
}
