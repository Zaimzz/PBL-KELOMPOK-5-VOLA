<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Resolved = 'resolved';
    case Rejected = 'rejected';
}
