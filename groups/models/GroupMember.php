<?php
    class GroupMember {
        private $groupId;
        private $memberId;

        public function __construct($groupId, $memberId) {
            $this->groupId = $groupId;
            $this->memberId = $memberId;
        }

        public function group() {
            return $this->groupId;
        }

        public function member() {
            return $this->memberId;
        }
    }
?>