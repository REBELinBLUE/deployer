<?php

return [

    'label'             => '环境变量',
    'create'            => '添加新变量',
    'edit'              => '编辑变量',
    'name'              => '变量名',
    'value'             => '值',
    'warning'           => '变量无法保存，请检查下面的表单',
    'description'       => '有时候，你可能需要在部署过程中定义某些环境变量 ' .
                           '但是你不想把它们设置在服务器的 <code>~/.bashrc</code> 文件中',
    'example'           => '例如，你可能想设置 <code>COMPOSER_PROCESS_TIMEOUT</code> 来允许 composer ' .
                           '运行得更久，或者在部署 symfony 项目时设置 <code>SYMFONY_ENV</code> ',

];
