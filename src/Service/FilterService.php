<?php

namespace App\Service;

class FilterService
{
    public function getCommandsByDate($search, $commands)
    {
        if (!empty($search)) {
            $int1= strtotime($search['date1']);
            $int2= strtotime($search['date2']);

            $result = [];
            foreach ($commands as $command) {
                $intCommand = strtotime($command['created_at']);
                if (($intCommand >= $int1) && ($intCommand <= $int2)) {
                    array_push($result, $command);
                }
            }
            return $result;
        }
    }
}
