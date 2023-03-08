<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sales>
 */
class SalesOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'remarks' => 'nothing',
            'customer_id' => 1,
            'gross_total' => 100,
            'vat' => 0,
            'gst' => 0,
            'advance_tax' => 0,
            'other_tax' => 0,
            'other_charges' => 0,
            'net_total' => 0,
            'status' => 1,
            'user_id'=>1
        ];
    }
}
