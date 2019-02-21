<?php

class GASource extends Source
{
    public function GetVisitorsCount() : int {
        return $this->IssueGA_API();
    }

    private function IssueGA_API() : int {
        // TODO: Request the actual data from GA
        return 150; // Temp
    }
}
?>