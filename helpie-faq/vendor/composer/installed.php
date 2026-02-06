<?php return array(
    'root' => array(
        'name' => 'helpie/faq',
        'pretty_version' => 'dev-develop',
        'version' => 'dev-develop',
        'reference' => '3c0e12231f82561917dafac932eb3c1b9c115538',
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
            'reference' => '3c0e12231f82561917dafac932eb3c1b9c115538',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'pauple/pluginator' => array(
            'pretty_version' => 'dev-release/0.3.9-redo',
            'version' => 'dev-release/0.3.9-redo',
            'reference' => '0e52f2a2e36d1ded67a37850c0bbbb351c06501d',
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
