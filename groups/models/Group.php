<?php
    class Group {
        private $groupName;
        private $ownerId;

        public function __construct($groupName, $ownerId) {
            $this->groupName = $groupName;
            $this->ownerId = $ownerId;
        }

        public function name() {
            return $this->groupName;
        }

        public function owner() {
            return $this->ownerId;
        }
    }
?>