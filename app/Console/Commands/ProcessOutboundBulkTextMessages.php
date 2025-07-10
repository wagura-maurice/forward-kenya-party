<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\OutboundTextMessage;
use App\Models\OutboundBulkTextMessage;

class ProcessOutboundBulkTextMessages extends Command
{
    protected $dateTimeStamp;
    protected $status;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:outbound-bulk-text-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for processing outbound bulk text messages into text messages for sending';

    public function __construct()
    {
        parent::__construct();
        $this->dateTimeStamp = Carbon::now();
        $this->status = OutboundBulkTextMessage::PROCESSING;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            OutboundBulkTextMessage::whereIn('_status', [
                OutboundBulkTextMessage::PENDING,
                OutboundBulkTextMessage::PROCESSING
            ])
            ->where('start_at', '<=', $this->dateTimeStamp->copy()->startOfDay())
            ->where('end_at', '>=', $this->dateTimeStamp->copy()->endOfDay())
            ->chunk(100, function ($outboundBulkTextMessages) {
                foreach ($outboundBulkTextMessages as $outboundBulkTextMessage) {
                    // Initialization flags
                    $process = false;
                    $startAt = Carbon::parse($outboundBulkTextMessage->start_at);
                    $endAt = Carbon::parse($outboundBulkTextMessage->end_at);
                    $lastProcessedAt = $outboundBulkTextMessage->last_processed_at ? Carbon::parse($outboundBulkTextMessage->last_processed_at) : null;

                    // Switch statement for different schedule types
                    switch ($outboundBulkTextMessage->schedule) {
                        case OutboundBulkTextMessage::DEFAULT:
                            // If last_processed_at is not set (null), proceed with processing
                            if (!$lastProcessedAt) {
                                $process = true;
                                $this->status = OutboundBulkTextMessage::PROCESSED;
                            }
                            break;

                        case OutboundBulkTextMessage::DAILY:
                            // Check if last_processed_at is set and is not today
                            if ($lastProcessedAt && !$lastProcessedAt->copy()->isToday()) {
                                $process = true;
                            } elseif (!$lastProcessedAt) { // If last_processed_at is not set (null), proceed with processing
                                $process = true;
                            }

                            // Mark as processed if the date of today plus 1 day is beyond end_at date
                            if ($this->dateTimeStamp->copy()->addDay()->isAfter($endAt)) {
                                $this->status = OutboundBulkTextMessage::PROCESSED;
                            }
                            break;

                        case OutboundBulkTextMessage::WEEKLY:
                            // Check if today is the same day of the week as start_at
                            if ($this->dateTimeStamp->copy()->dayOfWeek == $startAt->copy()->dayOfWeek) {
                                // Check if last_processed_at is set and is not today
                                if ($lastProcessedAt && !$lastProcessedAt->copy()->isToday()) {
                                    $process = true;
                                } elseif (!$lastProcessedAt) { // If last_processed_at is not set (null), proceed with processing
                                    $process = true;
                                }
                            }

                            // Mark as processed if the date of today plus 1 week is beyond end_at date
                            if ($this->dateTimeStamp->copy()->addWeek()->isAfter($endAt)) {
                                $this->status = OutboundBulkTextMessage::PROCESSED;
                            }
                            break;

                        case OutboundBulkTextMessage::MONTHLY:
                            // Check if last_processed_at is set and if adding one month to last_processed_at results in today's date ($this->dateTimeStamp)
                            if ($lastProcessedAt && $lastProcessedAt->copy()->addMonth()->isSameDay($this->dateTimeStamp->copy())) {
                                $process = true;
                            } elseif (!$lastProcessedAt) { // If last_processed_at is not set (null), proceed with processing
                                $process = true;
                            }

                            // Mark as processed if the date of today plus 1 month is beyond end_at date
                            if ($this->dateTimeStamp->copy()->addMonth()->isAfter($endAt)) {
                                $this->status = OutboundBulkTextMessage::PROCESSED;
                            }
                            break;

                        case OutboundBulkTextMessage::QUARTERLY:
                            // Check if last_processed_at is set and if adding three months to last_processed_at results in today's date ($this->dateTimeStamp)
                            if ($lastProcessedAt && $lastProcessedAt->copy()->addMonths(3)->isSameDay($this->dateTimeStamp->copy())) {
                                $process = true;
                            } elseif (!$lastProcessedAt) { // If last_processed_at is not set (null), proceed with processing
                                $process = true;
                            }

                            // Mark as processed if the date of today plus 3 months is beyond end_at date
                            if ($this->dateTimeStamp->copy()->addMonths(3)->isAfter($endAt)) {
                                $this->status = OutboundBulkTextMessage::PROCESSED;
                            }
                            break;
                    }

                    if ($process) {
                        $contacts = json_decode($outboundBulkTextMessage->contacts, true);

                        if (!is_array($contacts)) {
                            continue;
                        }

                        foreach ($contacts as $contact) {
                            OutboundTextMessage::create([
                                '_uid' => Str::uuid(),
                                'category_id' => $outboundBulkTextMessage->category_id,
                                'outbound_bulk_text_message_id' => $outboundBulkTextMessage->id,
                                'content' => _parse($outboundBulkTextMessage->content, array_filter([
                                    'name' => $contact['name'] ? ucwords($contact['name']) : 'member'
                                ])),
                                'telephone' => $contact['telephone'],
                            ]);
                        }

                        // Update the last_processed_at timestamp to mark the message as processed
                        $outboundBulkTextMessage->last_processed_at = $this->dateTimeStamp->copy();
                        $outboundBulkTextMessage->_status = $this->status;
                        $outboundBulkTextMessage->save();
                    }
                }
            });
        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());
        }
    }
}
