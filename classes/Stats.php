<?php
class Stats implements \JsonSerializable
{
    private $data;
    private $error;
    private $message;

    public function __construct() {
        $this->data = [];
        $this->error = FALSE;
        $this->message = "";
    }

    public function SetError(string $message) : void {
        $this->message = $message;
        $this->error = TRUE;
    }

    public function ClearData() {
        $this->data = [];
    }

    public function AddData(string $source_name, int $value) : void {
        $this->data[$source_name] = $value;
    }

    public function jsonSerialize() : array {
        return get_object_vars($this);
    }
}
?>