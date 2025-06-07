<?php
    interface Storage {
        public function get($key);
        public function add($item);
        public function remove($key);
        public function exists($key);
    }
?>