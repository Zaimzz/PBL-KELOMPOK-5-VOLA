<?php

namespace App\Enums;

enum EventStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case AwaitingPayment = 'awaiting_payment';
    case Rejected = 'rejected';
    case Active = 'active';
    case Closed = 'closed';
    case Finished = 'finished';
}
