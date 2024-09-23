<?php return array(
    'root' => array(
        'name' => 'helpie/faq',
        'pretty_version' => 'dev-develop',
        'version' => 'dev-develop',
        'reference' => '4135b552a54c4c7bf52967658f667fcb9b71e09c',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'helpie/faq' => array(
            'pretty_version' => 'dev-develop',
            'version' => 'dev-develop',
            'reference' => '4135b552a54c4c7bf52967658f667fcb9b71e09c',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'pauple/pluginator' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'b3a31b02c300130b3d6554b993e17dc9555a5404',
            'type' => 'pauple-library',
            'install_path' => __DIR__ . '/../pauple/pluginator',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
    ),
);
