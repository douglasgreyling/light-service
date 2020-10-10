<?php

class NotImplementedException extends BadMethodCallException {
    public function errorMessage() {
        return 'Not implemented!';
    }
}
