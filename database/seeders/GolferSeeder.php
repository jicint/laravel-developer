<?php

namespace Database\Seeders;

use App\Models\Golfer;
use Illuminate\Database\Seeder;

class GolferSeeder extends Seeder
{
    public function run(): void
    {
        // TASK 2: Ensure unique debitor_account numbers even on multiple runs
        // Get the next available debitor_account number
        $nextAccountNumber = Golfer::max('debitor_account') ?? 0;
        
        // Create 100 golfers with consecutive debitor_account numbers
        // This ensures each debitor_account is assigned only once
        for ($i = 1; $i <= 100; $i++) {
            $nextAccountNumber++;
            
            Golfer::factory()->create([
                'debitor_account' => $nextAccountNumber,
            ]);
        }
    }
}
