<?php
    require_once __DIR__ . '/../models/Group.php';
    require_once __DIR__ . '/../models/GroupMember.php';

    function handleCreateGroup($groupName, $ownerId, $groupStorage, $groupMemberStorage) {
        if (empty($groupName)) {
            return [
                'status' => 'error',
                'message' => 'Името на групата не може да бъде празно.'
            ];
        }

        if ($groupStorage->exists($groupName)) {
            return [
                'status' => 'error',
                'message' => 'Група с това име вече съществува.'
            ];
        }

        $group = new Group($groupName, $ownerId);
        $group = $groupStorage->add($group);

        $member = new GroupMember($group['id'], $ownerId);
        $result = $groupMemberStorage->add($member);

        return [
            'status' => 'success',
            'message' => 'Групата е успешно създадена.',
            'group' => $group
        ];
    }

    function handleRemoveGroup($groupId, $groupStorage, $groupMemberStorage) {
        if (!$groupStorage->exists($groupId)) {
            return [
                'status' => 'error',
                'message' => 'Групата не съществува.'
            ];
        }

        $groupStorage->remove($groupId);
        $groupMemberStorage->removeGroup($groupId);

        return [
            'status' => 'success',
            'message' => 'Групата е успешно премахната.'
        ];
    }

    function handleAddGroupMember($groupId, $memberId, $groupMemberStorage) {
        $member = new GroupMember($groupId, $memberId);
        $result = $groupMemberStorage->add($member);

        return [
            'status' => 'success',
            'message' => 'Потребителят е успешно добавен към групата.',
            'member' => $result
        ];
    }

    function handleRemoveGroupMember($groupId, $memberId, $groupMemberStorage) {
        if (!$groupMemberStorage->exists($groupId, $memberId)) {
            return [
                'status' => 'error',
                'message' => 'Потребителят не е член на групата.'
            ];
        }

        $groupMemberStorage->remove(new GroupMember($groupId, $memberId));

        return [
            'status' => 'success',
            'message' => 'Потребителят е успешно премахнат от групата.'
        ];
    }
?>