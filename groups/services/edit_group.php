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
        $groupMemberStorage->add($member);

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

        $groupMemberStorage->removeGroup($groupId);
        $groupStorage->remove($groupId);

        return [
            'status' => 'success',
            'message' => 'Групата е успешно премахната.'
        ];
    }

    function handleAddGroupMember($groupId, $memberId, $groupMemberStorage, $userStorage) {
        $member = new GroupMember($groupId, $memberId);
        $groupMember = $groupMemberStorage->add($member);
        $user = $userStorage->getById($memberId);

        return [
            'status' => 'success',
            'message' => 'Потребителят е успешно добавен към групата.',
            'user' => $user
        ];
    }

    function handleRemoveGroupMember($groupId, $memberId, $groupMemberStorage) {
        $groupMember = new GroupMember($groupId, $memberId);
        
        if (!$groupMemberStorage->exists($groupMember)) {
            return [
                'status' => 'error',
                'message' => 'Потребителят не е член на групата.'
            ];
        }

        $groupMemberStorage->remove($groupMember);

        return [
            'status' => 'success',
            'message' => 'Потребителят е успешно премахнат от групата.'
        ];
    }
?>