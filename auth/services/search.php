<?php
    function handleSearch($term, $userStorage) {
        $results = $userStorage->getAll();

        $term = strtolower($term);
        $filteredResults = array_filter($results, function($user) use ($term) {
            $username = strtolower($user['username']);
            $firstname = strtolower($user['name']);
            $lastname = strtolower($user['lastname']);
            $name = $firstname . ' ' . $lastname;

            return strpos($username, $term) !== false || strpos($firstname, $term) !== false || strpos($lastname, $term) !== false || strpos($name, $term) !== false;
        });

        return $filteredResults;
    }
?>