<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;

class SetupMollieWebhook extends Command
{
    protected $signature = 'mollie:setup-webhook';
    protected $description = 'Set up the Mollie webhook URL';

    public function handle()
    {
        $webhookUrl = config('cashier.webhook_url');

        if (!$webhookUrl) {
            $this->error('Please set the MOLLIE_WEBHOOK_URL in your .env file');
            return 1;
        }

        try {
            $mollie = Cashier::mollie();
            $webhooks = $mollie->webhooks->page();

            // Check if our webhook URL already exists
            $exists = collect($webhooks)->contains(function ($webhook) use ($webhookUrl) {
                return $webhook->url === $webhookUrl;
            });

            if (!$exists) {
                // Create the webhook
                $mollie->webhooks->create([
                    'url' => $webhookUrl,
                    'enabled' => true,
                ]);

                $this->info('Mollie webhook created successfully');
            } else {
                $this->info('Mollie webhook already exists');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to set up Mollie webhook: ' . $e->getMessage());
            return 1;
        }
    }
}
 