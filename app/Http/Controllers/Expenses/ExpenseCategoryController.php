<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the expense categories.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized access to expense categories.');
        }

        // Seed default categories if table is empty
        if (ExpenseCategory::count() === 0) {
            ExpenseCategory::seedDefaults();
        }

        $search = $request->input('search');
        $query = ExpenseCategory::withCount('expenses');

        if (! empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('expenses.categories.index', compact('categories', 'search'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name',
        ], [
            'name.required' => 'Please enter a category name.',
            'name.unique' => 'An expense category with this name already exists.',
        ]);

        $category = ExpenseCategory::create([
            'name' => trim($validated['name']),
            'is_active' => true,
        ]);

        ActivityLog::log(
            'create_expense_category',
            "Created expense category '{$category->name}'",
            'Expenses'
        );

        return redirect()->route('expenses.categories.index')
            ->with('success', "Expense category '{$category->name}' created successfully.");
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, ExpenseCategory $category): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name,'.$category->id,
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Please enter a category name.',
            'name.unique' => 'An expense category with this name already exists.',
        ]);

        $category->update([
            'name' => trim($validated['name']),
            'is_active' => (bool) $validated['is_active'],
        ]);

        ActivityLog::log(
            'update_expense_category',
            "Updated expense category '{$category->name}'",
            'Expenses'
        );

        return redirect()->route('expenses.categories.index')
            ->with('success', "Expense category '{$category->name}' updated successfully.");
    }

    /**
     * Toggle the category active status safely without deleting records.
     */
    public function toggleStatus(ExpenseCategory $category): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $category->is_active = ! $category->is_active;
        $category->save();

        $statusStr = $category->is_active ? 'activated' : 'deactivated';

        ActivityLog::log(
            'toggle_expense_category_status',
            "Expense category '{$category->name}' was {$statusStr}.",
            'Expenses'
        );

        return back()->with('success', "Category '{$category->name}' was {$statusStr} successfully.");
    }
}
