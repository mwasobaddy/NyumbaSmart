<?php

namespace App\Livewire\PropertyRenovations;

use App\Models\PropertyRenovation;
use App\Models\RenovationExpense;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ExpenseManagement extends Component
{
    use WithPagination, WithFileUploads;
    
    public $renovation;
    public $renovationId;
    
    // New expense form
    public $showExpenseForm = false;
    public $vendor_id;
    public $expense_category;
    public $title;
    public $description;
    public $amount;
    public $expense_date;
    public $payment_method;
    public $receipt_number;
    public $receipt;
    
    // Search and filter
    public $search = '';
    public $categoryFilter = '';
    public $vendorFilter = '';
    public $dateRange = '';
    
    // Common expense categories
    public $expenseCategories = [
        'materials' => 'Building Materials',
        'labor' => 'Labor',
        'permits' => 'Permits & Fees',
        'design' => 'Design & Planning',
        'equipment' => 'Equipment Rental',
        'utilities' => 'Utilities',
        'inspection' => 'Inspection',
        'cleaning' => 'Cleaning',
        'other' => 'Other',
    ];
    
    // Common payment methods
    public $paymentMethods = [
        'cash' => 'Cash',
        'check' => 'Check',
        'm-pesa' => 'M-Pesa',
        'bank_transfer' => 'Bank Transfer',
        'credit_card' => 'Credit Card',
        'other' => 'Other',
    ];
    
    protected $rules = [
        'vendor_id' => 'nullable|exists:vendors,id',
        'expense_category' => 'required|string',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'amount' => 'required|numeric|min:0',
        'expense_date' => 'required|date',
        'payment_method' => 'required|string',
        'receipt_number' => 'nullable|string|max:100',
        'receipt' => 'nullable|file|max:5120', // 5MB max
    ];
    
    public function mount($renovationId = null)
    {
        if ($renovationId) {
            $this->renovationId = $renovationId;
            $this->loadRenovation();
        }
        
        $this->expense_date = now()->format('Y-m-d');
    }
    
    public function loadRenovation()
    {
        $this->renovation = PropertyRenovation::with(['property', 'vendors', 'expenses.vendor'])
            ->findOrFail($this->renovationId);
            
        // Verify authorization
        if (!$this->authorizeAccess()) {
            session()->flash('error', 'You do not have permission to manage expenses for this renovation.');
            return redirect()->route('dashboard');
        }
    }
    
    private function authorizeAccess()
    {
        $user = Auth::user();
        
        // Check if user is admin or landlord of this property
        return $user->hasRole(['Developer', 'Admin']) ||
               ($user->hasRole('Landlord') && $user->properties->contains('id', $this->renovation->property_id));
    }
    
    public function showAddExpenseForm()
    {
        $this->reset([
            'vendor_id', 'expense_category', 'title', 'description', 
            'amount', 'payment_method', 'receipt_number', 'receipt'
        ]);
        $this->expense_date = now()->format('Y-m-d');
        $this->showExpenseForm = true;
    }
    
    public function cancelAddExpense()
    {
        $this->showExpenseForm = false;
    }
    
    public function addExpense()
    {
        $this->validate();
        
        $expenseData = [
            'property_renovation_id' => $this->renovationId,
            'vendor_id' => $this->vendor_id,
            'expense_category' => $this->expense_category,
            'title' => $this->title,
            'description' => $this->description,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'payment_method' => $this->payment_method,
            'receipt_number' => $this->receipt_number,
            'added_by' => Auth::id(),
        ];
        
        // Handle receipt upload
        if ($this->receipt) {
            $path = $this->receipt->store('public/renovations/' . $this->renovationId . '/receipts');
            $expenseData['receipt_path'] = str_replace('public/', '', $path);
        }
        
        // Create the expense
        $expense = RenovationExpense::create($expenseData);
        
        // Update the renovation's actual cost
        $totalExpenses = $this->renovation->expenses->sum('amount') + $this->amount;
        $this->renovation->update([
            'actual_cost' => $totalExpenses
        ]);
        
        // Reset form
        $this->showExpenseForm = false;
        
        session()->flash('message', 'Expense added successfully.');
        $this->dispatch('expenseAdded');
    }
    
    public function deleteExpense($expenseId)
    {
        $expense = RenovationExpense::findOrFail($expenseId);
        
        // Check if user is authorized to delete this expense
        $user = Auth::user();
        $isAuthorized = $user->hasRole(['Developer', 'Admin']) || 
                         $expense->added_by === $user->id ||
                         ($user->hasRole('Landlord') && $user->properties->contains('id', $this->renovation->property_id));
        
        if (!$isAuthorized) {
            session()->flash('error', 'You do not have permission to delete this expense.');
            return;
        }
        
        // Delete receipt file if exists
        if ($expense->receipt_path) {
            $path = 'public/' . $expense->receipt_path;
            if (\Storage::exists($path)) {
                \Storage::delete($path);
            }
        }
        
        // Get amount before deletion
        $deletedAmount = $expense->amount;
        
        // Delete the expense
        $expense->delete();
        
        // Update the renovation's actual cost
        $this->renovation->update([
            'actual_cost' => $this->renovation->expenses->sum('amount') - $deletedAmount
        ]);
        
        session()->flash('message', 'Expense deleted successfully.');
        $this->dispatch('expenseAdded');
    }
    
    public function render()
    {
        $expensesQuery = $this->renovation->expenses()->with('vendor')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('receipt_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vendor', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('expense_category', $this->categoryFilter);
            })
            ->when($this->vendorFilter, function ($query) {
                $query->where('vendor_id', $this->vendorFilter);
            })
            ->when($this->dateRange, function ($query) {
                if ($this->dateRange === 'this_month') {
                    $query->whereMonth('expense_date', now()->month)
                          ->whereYear('expense_date', now()->year);
                } elseif ($this->dateRange === 'last_month') {
                    $lastMonth = now()->subMonth();
                    $query->whereMonth('expense_date', $lastMonth->month)
                          ->whereYear('expense_date', $lastMonth->year);
                } elseif ($this->dateRange === 'this_year') {
                    $query->whereYear('expense_date', now()->year);
                }
            });
        
        $expenses = $expensesQuery->latest()->paginate(10);
        
        // Get unique categories and vendors for filtering
        $uniqueCategories = $this->renovation->expenses->pluck('expense_category')->unique();
        
        return view('livewire.property-renovations.expense-management', [
            'expenses' => $expenses,
            'uniqueCategories' => $uniqueCategories,
        ]);
    }
}
