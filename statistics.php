<?php
include_once 'classes/Stats.php';
include_once 'classes/SourceDispatcher.php';

ProcessRequest();

function ProcessRequest() : void {
    $stat = new Stats();
    ExecuteQuery(GetQuery(), $stat);
    echo json_encode($stat);

}

function GetQuery() {
    $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    if(!$query)
        return FALSE; // No query part in the uri

    parse_str($query, $arr);

    return (count($arr) === 0) ? FALSE : $arr;
}

function ExecuteQuery($query_parts, Stats &$stat) : void {
    if(!$query_parts) {
        $stat->SetError('Invalid or missing query!');
        return;
    }
    if(!$query_parts['source']) {
        $stat->SetError('No sources specified!');
        return;
    }
    foreach($query_parts as $key => $value) {
        switch($key) {
            case 'source':
                GetDataFromSources($value, $stat);
                break;
            // Add other parameters
            default:
                $stat->SetError("Unknown parameter '$key' specified!");
                break;
        }
    }
}

function GetDataFromSources($sources, Stats &$stat) : void {
    if(gettype($sources) === 'string')
        $sources = [$sources];
    if(gettype($sources) != 'array') {
        $stat->SetError('Unsupported source type!');
        return;
    }
    
    foreach($sources as $source) {
        $src = SourceDispatcher::Dispatch($source);
        if(!$src) {
            $stat->SetError("Unknown source '$src'!");
            $stat->ClearData();
            return;
        }

        $visitors = $src->GetVisitorsCount();
        if($visitors < 0) {
            $stat->SetError("Internal error with the '$source' source! Skipping...");
            continue;
        }
        $stat->AddData($source, $visitors);
    }
}

?>