<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FailedLogin extends Model {
    protected $table = 'failed_logins';
    
    /**
     * Minutes to count failures
     *
     * @var int
     */
    protected static $attemptCount = 10;
    
    /**
     * Count login failure
     */
    public function countFailure() {
        $count = self::whereBetween('updated_at', [Carbon::now()->subMinutes(self::$attemptCount),Carbon::now()])->count();
        if ($count <= 0 AND rand(1,3) == 3) {
            self::truncate();
        } elseif (rand(0,9) >= 8) {
            // Randomly clean the table
            self::whereBetween('updated_at', [Carbon::createFromTimestamp(0),Carbon::now()->subMinutes(self::$attemptCount)])->delete();
        }
        return $count;
    }
}
