<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\GLAccountGroup;

class ChartOfAccounts extends Component
{
    public function render()
    {
        // Fetch all groups with their active accounts in one query
        $allGroups = GLAccountGroup::with(['accounts' => function ($q) {
            $q->where('is_inactive', 0)->orderBy('code');
        }])->orderBy('id')->get();

        // Normalize account_class values to handle: 'Assets', 'asset', 'assets', etc.
        $normalize = function ($value) {
            $map = [
                'assets'      => 'asset',
                'asset'       => 'asset',
                'liabilities' => 'liability',
                'liability'   => 'liability',
                'equities'    => 'equity',
                'equity'      => 'equity',
                'incomes'     => 'income',
                'income'      => 'income',
                'expenses'    => 'expense',
                'expense'     => 'expense',
            ];
            return $map[strtolower(trim($value))] ?? strtolower(trim($value));
        };

        $classes = ['asset', 'liability', 'equity', 'income', 'expense'];
        $grouped_data = [];

        foreach ($classes as $class) {
            $grouped_data[$class] = $allGroups->filter(
                fn($g) => $normalize($g->account_class) === $class
            );
        }

        return view('livewire.chart-of-accounts', [
            'grouped_data' => $grouped_data
        ])->layout('layouts.app');
    }
}
