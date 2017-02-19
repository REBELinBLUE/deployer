<?php

return [

    'label'        => 'Переменные окружения',
    'create'       => 'Добавить новую переменную',
    'edit'         => 'Редактировать переменную',
    'name'         => 'Переменная',
    'value'        => 'Значение',
    'warning'      => 'Переменная не может быть сохранена. Пожалуйста, проверьте форму ниже.',
    'description'  => 'Иногда вам будут нужны некоторые переменный окружения, заданные во время сборки, которые вы ' .
                      'не хотите устанавливать в файле <code>~/.bashrc</code> на сервере.',
    'example'      => 'Например, вы можете захотеть установить переменную <code>COMPOSER_PROCESS_TIMEOUT</code>, ' .
                      'чтобы разрешить Composer-у работать дольше, или <code>SYMFONY_ENV</code>, если вы собираете ' .
                      'Symfony-проект',

];
