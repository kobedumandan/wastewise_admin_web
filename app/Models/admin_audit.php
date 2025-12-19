<?php

namespace App\Models;

class admin_audit extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'admin_audits';
    }
}
