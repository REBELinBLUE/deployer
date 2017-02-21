<?php

return [

    'manage'            => 'Управление проектами',
    'warning'           => 'Проект не может быть сохunauthenticatedста, проверьте форму ниже.',
    'none'              => 'Сейчас не настроено ни одного проекта',
    'name'              => 'Название',
    'awesome'           => 'Мое великолепное приложение',
    'group'             => 'Группа',
    'repository'        => 'Репозиторий',
    'repository_url'    => 'URL репозитория',
    'builds'            => 'Хранить сборок',
    'build_options'     => 'Параметры сборки',
    'project_details'   => 'Подробности о проекте',
    'branch'            => 'Ветка по умолчанию',
    'image'             => 'Иконка сборки',
    'ci_image'          => 'Если вы используете CI-сервер, который предоставляет иконку статуса сборки, вы можете ' .
                           'поместить ее URL сюда, чтобы видеть статус сборки на странице проекта.',
    'latest'            => 'Последние сборки',
    'create'            => 'Добавить новый проект',
    'edit'              => 'Редактировать проект',
    'url'               => 'URL',
    'details'           => 'Подробности о проекте',
    'deployments'       => 'Сборки',
    'today'             => 'Сегодня',
    'last_week'         => 'Прошлая неделя',
    'latest_duration'   => 'Последняя продолжительность',
    'health'            => 'Health Check',
    'build_status'      => 'Статус сборки',
    'app_status'        => 'Статус приложения',
    'heartbeats_status' => 'Heartbeat Status',
    'view_ssh_key'      => 'Посмотреть SSH-ключ',
    'private_ssh_key'   => 'Приватный SSH-ключ',
    'public_ssh_key'    => 'Публичный SSH-ключ',
    'ssh_key'           => 'SSH-ключ',
    'ssh_key_info'      => 'Если у вас есть специальный приватный ключ, который вы хотите использовать, то вставьте ' .
                           'его сюда. Ключь не должен иметь passphrase пароля',
    'ssh_key_example'   => 'SSH-ключ будет сгенерирован автоматически, если вы оставите поле пустым. Это ' .
                           'рекомендуемое поведение.',
    'deploy_project'    => 'Собрать проект',
    'deploy'            => 'Собрать',
    'redeploy'          => 'Пересобрать',
    'server_keys'       => 'This key must be added to the server\'s <code>~/.ssh/authorized_keys</code> ' .
                           'for each user you wish to run commands as.',
    'git_keys'          => 'The key will also need to be added to the <strong>Deploy Keys</strong> ' .
                           'for you repository unless you are using a public/unauthenticated URL.',
    'finished'          => 'Закончена',
    'pending'           => 'Ожидает',
    'deploying'         => 'Собирается',
    'failed'            => 'Провалена',
    'not_deployed'      => 'Не собиралась',
    'options'           => 'Опции',
    'change_branch'     => 'Allow other branches to be deployed?',
    'include_dev'       => 'Install composer development packages?',
    'insecure'          => 'Your Deployer installation is not running over a secure connection, it is recommended ' .
                           'that you let Deployer generate an SSH key rather than supply one yourself so that the ' .
                           'private key is not transmitted over an insecure connection.',

];
