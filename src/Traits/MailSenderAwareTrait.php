<?php

declare(strict_types=1);

namespace App\Traits;

use App\Manager\Mail\MailManager;

trait MailSenderAwareTrait
{
    /**
     * @var MailManager
     */
    protected MailManager $mailManager;

    /**
     * @required
     * @param MailManager $mailManager
     */
    public function setMailManager(MailManager $mailManager): void
    {
        $this->mailManager = $mailManager;
    }
}
