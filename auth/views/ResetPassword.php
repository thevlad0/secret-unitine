<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form id="forgotten-password-section">
        <label class="input-label" id="forgotten-password"> Забравена парола? </label>  
        <label class="hidden-input-label" for="hidden-email" id="hidden-email-label">Имейл</label>
        <input class="hidden-input-field" type="email" id="hidden-email" inputmode="email" placeholder="Въведете имейл за изпращане на код" required>
        <button class="hidden-button" type="button" id="send-code-btn">Изпрати код</button>

        <label class="hidden-input-label" id="code-label">Изпратихме код за потвърждение на имейла Ви. Моля, въведете го по-долу:</label>
        <input class="hidden-input-field" type="text" id="code" inputmode="numeric" placeholder="Въведете получения код" required>

        <label class="hidden-input-label" for="username-field" id="username-label"> Промяна на паролата: </label>
        <input class="hidden-input-field" type="text" id="username-field" placeholder="Въведете потребителско име" required>                 
        <input class="hidden-input-field" type="password" id="new-password" placeholder="Въведете новата си парола" required>
        <button class="hidden-button" type="button" id="forgotten-password-btn"> Промяна на паролата </button>
        <label class="hidden-input-label" id="response-label"></label>
    </form>
</body>
</html>