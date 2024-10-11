<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\Adhoc as BaseAdhoc;
use Illuminate\Contracts\Queue\ShouldQueue;

class Adhoc extends BaseAdhoc implements ShouldQueue{}
