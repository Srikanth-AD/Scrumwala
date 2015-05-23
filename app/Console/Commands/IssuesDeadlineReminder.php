<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use Mail;
use App\IssueStatus;
class IssuesDeadlineReminder extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'issuesDeadlineReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send issues with deadline within 24hrs via email';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentTimestamp = Carbon::now()->toDateTimeString();
        $tomorrowTimestamp = Carbon::now()->addDay()->toDateTimeString();
        $issuesWithDeadlineWithinADay =
            DB::table('issues')
            ->select('id','deadline','title')
            ->where('deadline', '>=', $currentTimestamp)
            ->where('deadline', '<', $tomorrowTimestamp)
            ->where('status_id', '!=', IssueStatus::getIdByMachineName('complete'))
            ->where('status_id', '!=', IssueStatus::getIdByMachineName('archive'))
            ->get();

        Mail::send('emails.issuesDeadlineReminder',
            ['issuesWithDeadlineWithinADay' => $issuesWithDeadlineWithinADay],
            function($message)
        {
            $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
            $message->to('adsrikanth@gmail.com', 'Srikanth AD')
                ->subject('Daily report: issues with deadlines within a day');
        });
    }

}
