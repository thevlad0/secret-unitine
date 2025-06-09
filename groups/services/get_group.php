<?php
    function handleGetAllGroups($groupStorage, $groupMemberStorage, $userStorage) {
        $groups = $groupStorage->getAll();

        foreach ($groups as &$group) {
            $groupMembers = $groupMemberStorage->get($group['id']);
            $memberList = [];

            if ($groupMembers) {
                foreach ($groupMembers as $member) {
                    $memberList[] = $userStorage->getById($member['memberId']);
                }
            }

            $group['users'] = $memberList;
        }

        return [
            'status' => 'success',
            'message' => 'Успешно извлечени групи.',
            'groups' => $groups
        ];
    }

    function handleGetUserGroups($userId, $groupStorage, $groupMemberStorage, $userStorage) {
        $userGroups = handleGetAllGroups($groupStorage, $groupMemberStorage, $userStorage);

        $groupList = [];
        if ($userGroups) {
            foreach ($userGroups as $group) {
                if ($group['ownerId'] === $userId) {
                    $groupList[] = $group;
                }
            }
        }

        return [
            'status' => 'success',
            'message' => 'Успешно извлечени групи на потребителя.',
            'groups' => $groupList
        ];
    }

    function handleGetGroupMembers($groupId, $groupMemberStorage, $userStorage) {
        $groupMembers = $groupMemberStorage->get($groupId);

        $memberList = [];
        if ($groupMembers) {
            foreach ($groupMembers as $member) {
                $memberList[] = $userStorage->getById($member['memberId']);
            }
        }

        return [
            'status' => 'success',
            'message' => 'Успешно извлечени членове на групата.',
            'members' => $memberList
        ];
    }
?>