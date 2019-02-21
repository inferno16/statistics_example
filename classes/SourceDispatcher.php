<?php
include_once 'classes/sources/Source.php';
include_once 'classes/sources/DatabaseSource.php';
include_once 'classes/sources/GASource.php';

class SourceDispatcher 
{
    static function Dispatch(string $source_name) {
        switch(strtolower($source_name)) {
            case 'database':
                return new DatabaseSource();
            case 'google analytics':
                return new GASource();
            default:
                return NULL;
        }
    }
}
?>