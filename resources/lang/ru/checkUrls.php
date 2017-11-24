<?php

return [

    'label'                 => 'Тесты URL',
    'create'                => 'Добавить новый URL',
    'edit'                  => 'Редактировать URL',
    'none'                  => 'Вы можете добавить URLы, которые должны будут запрашиваться переодически и после ' .
                               'того, как сборка завершится.',
    'title'                 => 'Заголовок',
    'titleTip'              => 'Административная панель',
    'url'                   => 'URL',
    'frequency'             => 'Частота',
    'is_report'             => 'Оповещать при провале',
    'length'                => ':time Минут',
    'warning'               => 'Ссылка не может быть сохранена. Пожалуйста, проверьте форму ниже.',
    'last_status'           => 'Статус',
    'successful'            => 'Онлайн',
    'failed'                => 'Оффлайн',
    'untested'              => 'Неизвестен',
    'last_seen'             => 'Последнее посещение',

    // Notifications
    'down_message'          => ':link похоже недоступен',
    'recovered_message'     => ':link снова онлайн',
    'down_sms_message'      => ':link проекта ":project" похоже недоступен! Последний раз был онлайн :last',
    'never_sms_message'     => ':link проекта ":project" похоже недоступен! До этого никогда не был онлайн',
    'recovered_sms_message' => ':link проекта ":project" снова онлайн',
    'down_subject'          => 'URL недоступен',
    'recovered_subject'     => 'URL снова онлайн',

];
