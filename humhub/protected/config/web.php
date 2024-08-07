<?php

/**
 * This file provides to overwrite the default HumHub / Yii configuration by your local Web environments
 * @see http://www.yiiframework.com/doc-2.0/guide-concept-configurations.html
 * @see http://docs.humhub.org/admin-installation-configuration.html
 * @see http://docs.humhub.org/dev-environment.html
 */

return [
    'components' => [
        'request' => [
            'trustedHosts' => ['127.0.0.1/32']
        ],

        'response' => [
            'on beforeSend' => function () {

                # Disable Google topics
                Yii::$app->response->headers->add('permissions-policy', 'browsing-topics=()');

                # Disable Google FloC (Federated Learning of Cohorts)
                Yii::$app->response->headers->add('permissions-policy', 'interest-cohort=()');
            }
        ],
    ],

    /**
     * Javascript CSP nonces were introduced in HumHub 1.15 but have been creating a range
     * of issues since they require updated module code.
     *
     * For an easier migration they are now disabled and will be re-enabled when things have
     * stabalized.
     *
     * TODO: Remove this section/commit when GitHub complains about CSP have slowed down.
     */
    'modules' => [
        'web' => [
            'security' => [
                'csp' => [
                    'nonce' => false,
                ],
            ],
        ],
    ],
];
