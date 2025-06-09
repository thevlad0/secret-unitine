<?php
    function handleGetAllGroups($groupStorage, $groupMemberStorage, $userStorage) {
        $groups = $groupStorage->getAll();

        if (is_array($groups)) {
            foreach ($groups as &$group) {
                $groupMembers = $groupMemberStorage->get($group['id']);
                
                $memberList = [];
                if (is_array($groupMembers)) {
                    foreach ($groupMembers as $member) {
                        if ($group['ownerId'] !== $member['memberId']) {
                            $user = $userStorage->getById($member['memberId']);
                            if ($user) {
                                $memberList[] = $user;
                            }
                        }
                    }
                }

                $group['users'] = $memberList;
            }
        } else {
            $groups = [];
        }

        return [
            'status' => 'success',
            'message' => 'Успешно извлечени групи.',
            'groups' => $groups
        ];
    }

    function handleGetUserGroups($userId, $groupStorage, $groupMemberStorage, $userStorage) {
        $allGroupsData = handleGetAllGroups($groupStorage, $groupMemberStorage, $userStorage)['groups'];

        $groupList = [];
        if (!empty($allGroupsData)) {
            foreach ($allGroupsData as $group) {
                if (isset($group['ownerId']) && $group['ownerId'] === (int)$userId) {
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

    function handleGetGroupMembers($groupId, $groupStorage, $groupMemberStorage, $userStorage) {
        if ($groupStorage->exists($groupId)) {
            return [
                'status' => 'error',
                'message' => 'Група с такова ID не съществува.',
                'members' => []
            ];
        }
        
        $groupMembers = $groupMemberStorage->get($groupId);

        $memberList = [];
        if (is_array($groupMembers)) {
            foreach ($groupMembers as $member) {
                $user = $userStorage->getById($member['memberId']);
                if ($user) {
                    $memberList[] = $user;
                }
            }
        }

        return [
            'status' => 'success',
            'message' => 'Успешно извлечени членове на групата.',
            'members' => $memberList
        ];
    }
?>