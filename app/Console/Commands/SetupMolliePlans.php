<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;

class SetupMolliePlans extends Command
{
    protected $signature = 'mollie:setup-plans';
    protected $description = 'Set up the Mollie subscription plans';

    private $plans = [
        'monthly' => [
            'name' => 'Monthly Plan',
            'amount' => [
                'currency' => 'EUR',
                'value' => '29.99',
            ],
            'interval' => '1 month',
            'description' => 'DHH Monthly Subscription',
        ],
        'yearly' => [
            'name' => 'Yearly Plan',
            'amount' => [
                'currency' => 'EUR',
                'value' => '299.99',
            ],
            'interval' => '12 months',
            'description' => 'DHH Yearly Subscription',
        ],
    ];

    public function handle()
    {
        try {
            $mollie = Cashier::mollie();

            foreach ($this->plans as $id => $plan) {
                try {
                    // Check if plan exists
                    $mollie->prices->get($id);
                    $this->info("Plan '{$id}' already exists");
                    continue;
                } catch (\Mollie\Api\Exceptions\ApiException $e) {
                    if ($e->getCode() !== 404) {
                        throw $e;
                    }
                }

                // Create the plan
                $mollie->prices->create([
                    'name' => $plan['name'],
                    'amount' => $plan['amount'],
                    'interval' => $plan['interval'],
                    'description' => $plan['description'],
                ]);

                $this->info("Created plan '{$id}'");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to set up Mollie plans: ' . $e->getMessage());
            return 1;
        }
    }
} 