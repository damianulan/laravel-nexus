<?php

namespace Nexus\Enums\Auth;

enum AuthType: string
{
    case LOCAL = 'local';

    case LDAP = 'ldap';

    case OAUTH2 = 'oauth2';

    case SSO = 'sso';
}
