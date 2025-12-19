<?php

namespace App\Models;

class Userinfo extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'userinfo';
    }

    /**
     * Get chart data for user registrations
     */
    public static function getChartData($year, $month)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        $chartData = [];
        
        foreach ($items as $item) {
            if (isset($item['created_at'])) {
                $date = strtotime($item['created_at']);
                $itemYear = date('Y', $date);
                $itemMonth = date('m', $date);
                $itemDay = date('d', $date);
                
                if ($itemYear == $year && $itemMonth == $month) {
                    $day = intval($itemDay);
                    if (!isset($chartData[$day])) {
                        $chartData[$day] = 0;
                    }
                    $chartData[$day]++;
                }
            }
        }
        
        return $chartData;
    }
}

