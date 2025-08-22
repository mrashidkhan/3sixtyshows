<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PayPalWebhookService;

class SetupPayPalWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paypal:setup-webhook
                           {--url= : Custom webhook URL (defaults to APP_URL/webhooks/payment/paypal)}
                           {--list : List existing webhooks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup PayPal webhook endpoint';

    private $webhookService;

    public function __construct(PayPalWebhookService $webhookService)
    {
        parent::__construct();
        $this->webhookService = $webhookService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('list')) {
            return $this->listWebhooks();
        }

        return $this->createWebhook();
    }

    /**
     * Create new webhook endpoint
     */
    private function createWebhook()
    {
        $url = $this->option('url') ?: config('app.url') . '/webhooks/payment/paypal';

        $this->info("Setting up PayPal webhook endpoint...");
        $this->info("URL: {$url}");
        $this->info("Mode: " . config('paypal.mode'));

        if (!$this->confirm('Do you want to create this webhook endpoint?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $result = $this->webhookService->createWebhookEndpoint($url);

        if ($result) {
            $this->info("✅ Webhook created successfully!");
            $this->table(['Property', 'Value'], [
                ['Webhook ID', $result['id']],
                ['URL', $result['url']],
                ['Status', $result['status'] ?? 'Unknown'],
                ['Event Types', count($result['event_types'] ?? [])]
            ]);

            $this->warn("⚠️  Important: Add this to your .env file:");
            $this->line("PAYPAL_WEBHOOK_ID={$result['id']}");

            if (count($result['event_types'] ?? []) > 0) {
                $this->info("\n📋 Subscribed to these events:");
                foreach ($result['event_types'] as $event) {
                    $this->line("  - {$event['name']}");
                }
            }

            $this->info("\n🔧 Next steps:");
            $this->line("1. Add PAYPAL_WEBHOOK_ID to your .env file");
            $this->line("2. Test webhook with: php artisan paypal:test-webhook");
            $this->line("3. Monitor webhook logs in the admin panel");

        } else {
            $this->error("❌ Failed to create webhook endpoint.");
            $this->line("Check the logs for more details.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * List existing webhooks
     */
    private function listWebhooks()
    {
        $this->info("Fetching existing PayPal webhooks...");
        $this->info("Mode: " . config('paypal.mode'));

        $result = $this->webhookService->listWebhooks();

        if ($result && isset($result['webhooks'])) {
            $webhooks = $result['webhooks'];

            if (empty($webhooks)) {
                $this->info("No webhooks found.");
                return Command::SUCCESS;
            }

            $this->info("Found " . count($webhooks) . " webhook(s):");

            $tableData = [];
            foreach ($webhooks as $webhook) {
                $tableData[] = [
                    $webhook['id'],
                    $webhook['url'],
                    $webhook['status'] ?? 'Unknown',
                    count($webhook['event_types'] ?? [])
                ];
            }

            $this->table(['ID', 'URL', 'Status', 'Events'], $tableData);

            // Show event details for first webhook
            if (!empty($webhooks[0]['event_types'])) {
                $this->info("\nEvent types for first webhook:");
                foreach ($webhooks[0]['event_types'] as $event) {
                    $this->line("  - {$event['name']}");
                }
            }

        } else {
            $this->error("❌ Failed to fetch webhooks.");
            $this->line("Check the logs for more details.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
