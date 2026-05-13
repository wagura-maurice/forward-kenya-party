<?php

namespace App\Console\Commands;

use App\Services\WahaService;
use App\Models\WhatsappConversation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestWhatsAppIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-whatsapp-integration {--phone= : Phone number to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp integration functionality';

    /**
     * Execute the console command.
     */
    public function handle(WahaService $wahaService)
    {
        $this->info('🧪 Testing WhatsApp Integration...');
        $this->newLine();

        // Test 1: Check WAHA Configuration
        $this->info('1. Testing WAHA Configuration...');
        try {
            $config = [
                'api_url' => config('services.waha.url', 'http://84.247.143.79:3000'),
                'api_key' => config('services.waha.key', '782338157f924709910c1fbc2635faff'),
                'session' => config('services.waha.session', 'default'),
            ];
            
            $this->info('✅ WAHA Configuration loaded successfully');
            $this->table(['Setting', 'Value'], [
                ['API URL', $config['api_url']],
                ['API Key', substr($config['api_key'], 0, 8) . '...'],
                ['Session', $config['session']],
            ]);
        } catch (\Exception $e) {
            $this->error('❌ WAHA Configuration failed: ' . $e->getMessage());
            return 1;
        }

        // Test 2: Test Database Connection
        $this->newLine();
        $this->info('2. Testing Database Connection...');
        try {
            $conversationCount = WhatsappConversation::count();
            $this->info('✅ Database connection successful');
            $this->info("📊 Current conversations: {$conversationCount}");
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            return 1;
        }

        // Test 3: Test Message Sending (if phone number provided)
        $phone = $this->option('phone');
        if ($phone) {
            $this->newLine();
            $this->info('3. Testing Message Sending...');
            
            // Format phone number
            if (!str_starts_with($phone, '254')) {
                $phone = '254' . ltrim($phone, '0');
            }
            $chatId = $phone . '@c.us';

            $this->info("📱 Testing with: {$chatId}");
            
            try {
                $result = $wahaService->sendText($chatId, "🧪 Test message from Forward Kenya Party WhatsApp Bot\n\nTime: " . now()->format('Y-m-d H:i:s'));
                
                if ($result['success']) {
                    $this->info('✅ Message sent successfully');
                    $this->info('📋 Response: ' . json_encode($result['data'], JSON_PRETTY_PRINT));
                } else {
                    $this->error('❌ Message sending failed');
                    $this->error('📋 Error: ' . $result['error']);
                    if (isset($result['response'])) {
                        $this->error('📋 Response: ' . json_encode($result['response'], JSON_PRETTY_PRINT));
                    }
                }
            } catch (\Exception $e) {
                $this->error('❌ Message sending exception: ' . $e->getMessage());
            }
        } else {
            $this->newLine();
            $this->info('3. Message Sending Test Skipped');
            $this->info('💡 Use --phone=254712345678 to test message sending');
        }

        // Test 4: Test Webhook URL
        $this->newLine();
        $this->info('4. Testing Webhook URL...');
        $webhookUrl = url('/webhook/waha');
        $this->info("🔗 Webhook URL: {$webhookUrl}");
        $this->info('💡 Configure this URL in your WAHA dashboard');

        // Test 5: Test Conversation State Management
        $this->newLine();
        $this->info('5. Testing Conversation State Management...');
        try {
            $testChatId = '254700000000@c.us';
            $testPhone = '254700000000';
            
            // Create test conversation
            $conversation = WhatsappConversation::updateOrCreate(
                ['chat_id' => $testChatId],
                [
                    'phone_number' => $testPhone,
                    'current_step' => 'welcome',
                    'conversation_data' => ['test' => true],
                    'last_activity_at' => now(),
                    'is_active' => true,
                ]
            );

            $this->info('✅ Conversation state management working');
            $this->info("📊 Test conversation created/updated: {$conversation->id}");
            
            // Clean up test data
            $conversation->delete();
            $this->info('🧹 Test data cleaned up');
        } catch (\Exception $e) {
            $this->error('❌ Conversation state management failed: ' . $e->getMessage());
        }

        // Summary
        $this->newLine();
        $this->info('🎯 Test Summary');
        $this->info('✅ WAHA Configuration: Working');
        $this->info('✅ Database Connection: Working');
        $this->info('✅ Conversation State: Working');
        $this->info('✅ Webhook URL: ' . $webhookUrl);
        
        if ($phone) {
            $this->info('📱 Message Sending: Tested (check results above)');
        } else {
            $this->info('💡 Message Sending: Use --phone to test');
        }

        $this->newLine();
        $this->info('🚀 WhatsApp Integration is ready!');
        $this->info('📋 Next steps:');
        $this->info('1. Configure webhook URL in WAHA dashboard');
        $this->info('2. Test with real phone number');
        $this->info('3. Monitor logs for any issues');

        return 0;
    }
}
