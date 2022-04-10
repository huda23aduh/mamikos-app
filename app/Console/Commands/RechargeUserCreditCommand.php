<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RechargeUserCreditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:recharge_user_credit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'recharge user credit';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::where('role', '0')
            ->update([
                'credits'=> DB::raw('credits+1'),
            ]);
        return 0;
    }
}
