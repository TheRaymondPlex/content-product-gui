<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Shared\Payone;

use SprykerFeature\Shared\Library\ConfigInterface;

interface PayoneConfigConstants extends ConfigInterface
{

    const PAYONE_CREDENTIALS = 'PAYONE_CREDENTIALS';
    const PAYONE_CREDENTIALS_ENCODING = 'PAYONE_CREDENTIALS_ENCODING';
    const PAYONE_PAYMENT_GATEWAY_URL = 'PAYONE_PAYMENT_GATEWAY_URL';
    const PAYONE_CREDENTIALS_KEY = 'PAYONE_CREDENTIALS_KEY';
    const PAYONE_CREDENTIALS_MID = 'PAYONE_CREDENTIALS_MID';
    const PAYONE_CREDENTIALS_AID = 'PAYONE_CREDENTIALS_AID';
    const PAYONE_CREDENTIALS_PORTAL_ID = 'PAYONE_CREDENTIALS_PORTAL_ID';

}
