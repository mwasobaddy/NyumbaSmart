<?php

namespace App\Livewire\VacateNotice;

use Livewire\Component;
use App\Models\VacateNotice;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Manager extends Component
{
    public $units;
    public $notices;
    public $unit_id;
    public $move_out_date;
    public $reason;
    public $notice_id;
    public $is_landlord;

    public function mount()
    {
        $user = Auth::user();
        $this->is_landlord = $user->hasRole('Landlord');
        
        if ($this->is_landlord) {
            // Landlord sees notices for their properties
            $propertyIds = $user->properties->pluck('id')->toArray();
            $this->units = Unit::whereIn('property_id', $propertyIds)->get();
            $this->notices = VacateNotice::whereIn('unit_id', $this->units->pluck('id'))
                ->with(['user', 'unit.property'])
                ->latest()
                ->get();
        } else {
            // Tenant sees only their notices
            $this->units = Unit::whereHas('invoices', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->get();
            $this->notices = $user->vacateNotices()
                ->with(['unit.property'])
                ->latest()
                ->get();
        }
    }
    
    public function create()
    {
        $data = $this->validate([
            'unit_id' => 'required|exists:units,id',
            'move_out_date' => 'required|date|after:' . Carbon::now()->addDays(29)->toDateString(),
            'reason' => 'nullable|string',
        ], [
            'move_out_date.after' => 'Move-out date must be at least 30 days from today (one month notice).'
        ]);
        
        $data['notice_date'] = Carbon::now()->toDateString();
        
        Auth::user()->vacateNotices()->create($data);
        $this->resetInput();
        $this->mount(); // Refresh the list
        session()->flash('status', 'Vacate notice submitted successfully. You have informed the landlord of your intention to move out.');
    }
    
    public function processNotice($id, $status)
    {
        if (!$this->is_landlord) {
            session()->flash('error', 'Only landlords can process vacate notices.');
            return;
        }
        
        $notice = VacateNotice::findOrFail($id);
        $notice->update(['status' => $status]);
        $this->mount(); // Refresh the list
        
        $statusMessage = $status === 'processed' ? 'processed' : 'pending';
        session()->flash('status', "Vacate notice marked as {$statusMessage}.");
    }
    
    public function delete($id)
    {
        $notice = VacateNotice::findOrFail($id);
        
        // Only allow deletion if the notice is still pending and it's the tenant who created it
        if ($notice->user_id === Auth::id() && $notice->status === 'pending') {
            $notice->delete();
            $this->mount(); // Refresh the list
            session()->flash('status', 'Vacate notice withdrawn successfully.');
        } else {
            session()->flash('error', 'You cannot withdraw this notice.');
        }
    }
    
    private function resetInput()
    {
        $this->notice_id = null;
        $this->unit_id = '';
        $this->move_out_date = '';
        $this->reason = '';
    }

    public function render()
    {
        return view('livewire.vacate-notice.manager');
    }
}
