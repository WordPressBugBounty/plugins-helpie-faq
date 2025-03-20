<?php return array(
    'root' => array(
        'name' => 'helpie/faq',
        'pretty_version' => 'dev-develop',
        'version' => 'dev-develop',
        'reference' => '0411c8172991a232f5b3ceebf49ec305984944ff',
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
            'reference' => '0411c8172991a232f5b3ceebf49ec305984944ff',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'pauple/pluginator' => array(
            'pretty_version' => 'dev-release/0.3.6',
            'version' => 'dev-release/0.3.6',
            'reference' => '26ad26a140fafe9c4c0d4513953953e519e74bf7',
            'type' => 'pauple-library',
            'install_path' => __DIR__ . '/../pauple/pluginator',
            'aliases' => array(),
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
