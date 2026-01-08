<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Payment;
use Illuminate\Console\Command;

class RecalculateCampaignRaisedAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:recalculate-raised
                            {--campaign= : Recalculate specific campaign by ID}
                            {--dry-run : Preview changes without applying them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate raised_amount for campaigns based on paid payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $campaignId = $this->option('campaign');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get campaigns to process
        $query = Campaign::query();
        if ($campaignId) {
            $query->where('id', $campaignId);
        }
        $campaigns = $query->get();

        if ($campaigns->isEmpty()) {
            $this->error('No campaigns found.');
            return 1;
        }

        $this->info("Processing {$campaigns->count()} campaign(s)...");
        $this->newLine();

        $fixed = 0;
        $unchanged = 0;

        foreach ($campaigns as $campaign) {
            // Calculate correct amount from paid payments
            $correctAmount = Payment::where('campaign_id', $campaign->id)
                ->where('status', 'paid')
                ->sum('amount');

            $currentAmount = $campaign->raised_amount;

            if ($currentAmount != $correctAmount) {
                $difference = $correctAmount - $currentAmount;
                $diffSign = $difference > 0 ? '+' : '';

                $this->line(sprintf(
                    '<fg=yellow>Campaign #%d</> "%s"',
                    $campaign->id,
                    $campaign->title
                ));
                $this->line(sprintf(
                    '  Rp %s → Rp %s (<fg=%s>%sRp %s</>)',
                    number_format($currentAmount, 0, ',', '.'),
                    number_format($correctAmount, 0, ',', '.'),
                    $difference > 0 ? 'green' : 'red',
                    $diffSign,
                    number_format(abs($difference), 0, ',', '.')
                ));

                if (!$dryRun) {
                    $campaign->update(['raised_amount' => $correctAmount]);
                    $this->info('  ✓ Fixed');
                } else {
                    $this->warn('  → Would be fixed');
                }

                $this->newLine();
                $fixed++;
            } else {
                $unchanged++;
                if ($this->getOutput()->isVerbose()) {
                    $this->line(sprintf(
                        '<fg=green>Campaign #%d</> "%s": Rp %s (OK)',
                        $campaign->id,
                        $campaign->title,
                        number_format($currentAmount, 0, ',', '.')
                    ));
                }
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->line("  - Campaigns checked: {$campaigns->count()}");
        $this->line("  - Fixed: {$fixed}");
        $this->line("  - Unchanged: {$unchanged}");

        if ($dryRun && $fixed > 0) {
            $this->newLine();
            $this->warn("Run without --dry-run to apply changes.");
        }

        return 0;
    }
}
