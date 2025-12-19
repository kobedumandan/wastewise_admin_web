<?php

namespace App\Models;

class amount_paid extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'amount_paid';
    }

    /**
     * Get total amount
     */
    public static function getTotal()
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        $total = 0;
        foreach ($items as $item) {
            if (isset($item['amount'])) {
                $total += floatval($item['amount']);
            }
        }
        
        return (object)['total_amount' => $total];
    }
}

